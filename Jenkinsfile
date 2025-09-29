pipeline {
    agent any

    environment {
        GITHUB_CREDENTIALS_ID = 'faveobot'
        MYSQL_CREDENTIALS_ID = 'mysql_credentials_id'
        WORKSPACE_DIR = '/home/jenkins/workspace'
        REPO_ID = 'faveo-license-manager.git'
        SONAR_HOST_URL = 'https://sonarqube.faveotools.com'
        SONAR_ADMIN_LOGIN = credentials('sonar-admin-login')
    }

    stages {
        stage('Checking out and getting your code...') {
            steps {
                withCredentials([usernamePassword(credentialsId: GITHUB_CREDENTIALS_ID, usernameVariable: 'GITHUB_USER', passwordVariable: 'GITHUB_TOKEN')]) {
                    checkout scm
                     script {
                       sh 'git remote set-url origin https://${GITHUB_USER}:${GITHUB_TOKEN}@github.com/faveosuite/${REPO_ID}'
            }
                }
            }
        }

 stage('Checking what are your code changes...') {
    steps {
        withCredentials([usernamePassword(credentialsId: GITHUB_CREDENTIALS_ID, usernameVariable: 'GITHUB_USER', passwordVariable: 'GITHUB_TOKEN')]) {
            script {
                def repoName = scm.userRemoteConfigs[0].url.tokenize('/').last().replace('.git', '')
                // Resetting the remote URL with credentials
                sh 'git remote set-url origin https://${GITHUB_USER}:${GITHUB_TOKEN}@github.com/faveosuite/${REPO_ID}'
                sh 'git fetch origin development:refs/remotes/origin/development'
                def changedFiles = sh(script: "git diff --name-only origin/development...HEAD", returnStdout: true).trim()
                echo "Changed Files:\n${changedFiles}"

                // Check for frontend file changes
                def frontendChanged = changedFiles.contains('.vue') || changedFiles.contains('.js') || changedFiles.contains('.css')

                def backendChanged = changedFiles.contains('.php')

                env.FRONTEND_CHANGED = frontendChanged.toString()
                env.BACKEND_CHANGED = backendChanged.toString()

            }
        }
    }
}


        stage('Running vue tests...') {
            when {
                expression { env.FRONTEND_CHANGED == 'true' }
            }
            steps {
                sh 'rm -rf node_modules'
                sh 'yarn install'
                sh 'yarn test'
            }
        }

        stage('Setting up backend environment... and testing') {
            when {
                expression { env.BACKEND_CHANGED == 'true' }
            }
            steps {
                sh 'composer dump-autoload'
                script {
                    def buildNumber = env.BUILD_NUMBER
                    def prNumber = env.CHANGE_ID ?: ''
                    def repoName = scm.userRemoteConfigs[0].url.tokenize('/').last().replace('.git', '')

                    withCredentials([usernamePassword(credentialsId: GITHUB_CREDENTIALS_ID, usernameVariable: 'GITHUB_USER', passwordVariable: 'GITHUB_TOKEN')]) {
                        if (!prNumber) {
                            prNumber = sh(script: """
                                curl -s -u ${GITHUB_USER}:${GITHUB_TOKEN} \
                                "https://api.github.com/repos/faveosuite/${repoName}/pulls?head=${GITHUB_USER}:${env.BRANCH_NAME}" \
                                | jq '.[0].number' | tr -d '"'
                            """, returnStdout: true).trim()
                        }

                        def uniqueDatabaseName = "license_${buildNumber}_${prNumber}"

                        withCredentials([
                            usernamePassword(credentialsId: MYSQL_CREDENTIALS_ID, usernameVariable: 'DB_USER', passwordVariable: 'DB_PASS')
                        ]) {
                            sh """
                            echo "Creating database ${uniqueDatabaseName}" && \
                            mysql -u ${DB_USER} -p${DB_PASS} -e "DROP DATABASE IF EXISTS ${uniqueDatabaseName}; CREATE DATABASE ${uniqueDatabaseName};" && \
                            php artisan optimize:clear && \
                            php artisan testing-setup --username=${DB_USER} --password=${DB_PASS} --database=${uniqueDatabaseName} && \
                            COMPOSER_MEMORY_LIMIT=-1 php artisan test --coverage-clover=storage/sonarqube/clover.xml
                            """
                        }
                    }
                }
            }
        }
        stage('Create SonarQube Project and Token') {
            steps {
                script {
                    def prIdentifier = env.CHANGE_ID ?: env.BUILD_NUMBER
                    def projectKey = "license-manager-${prIdentifier}"
                    def projectName = "License Manager - Build #${prIdentifier}"

                    withCredentials([string(credentialsId: 'sonar-admin-token', variable: 'SONAR_ADMIN_TOKEN')]) {
                        sh """
                            curl -X POST "${SONAR_HOST_URL}/api/projects/delete" \\
                                 -u ${SONAR_ADMIN_TOKEN}: \\
                                 -d "project=${projectKey}" || true
                        """
                        sh """
                            curl -X POST "${SONAR_HOST_URL}/api/projects/create" \\
                                 -u ${SONAR_ADMIN_TOKEN}: \\
                                 -H "Content-Type: application/x-www-form-urlencoded" \\
                                 -d "project=${projectKey}" \\
                                 -d "name=${projectName}" \\
                                 -d "visibility=private" \\
                                 -d "mainBranch=development" \\
                                 -d "creationMode=manual" \\
                                 -d "newCodeDefinitionType=VERSION" \\
                                 -d "newCodeDefinitionReferenceBranch=PREVIOUS_VERSION"
                        """
                    }
                }
            }
        }

        stage('SonarQube Analysis') {
            steps {
                script {
                    def prIdentifier = env.CHANGE_ID ?: env.BUILD_NUMBER
                    def projectKey = "license-manager-${prIdentifier}"
                    echo "Running SonarQube Analysis for ${projectKey}"

                    withSonarQubeEnv('local-sonar') {
                        withCredentials([string(credentialsId: 'sonar-admin-token', variable: 'SONAR_ADMIN_TOKEN')]) {
                            sh 'git fetch origin development:development'
                            sh 'git stash'
                            sh 'git checkout development'
                            sh """
                                sonar-scanner \\
                                  -Dsonar.projectKey=${projectKey} \\
                                  -Dsonar.sources=. \\
                                  -Dsonar.php.coverage.reportPaths=storage/sonarqube/clover.xml \\
                                  -Dsonar.javascript.lcov.reportPaths=coverage/lcov.info \\
                                  -Dsonar.token=${SONAR_ADMIN_TOKEN} \\
                                  -Dsonar.branch.base=development \\
                                  -Dsonar.exclusions=resources/css/app.css \\
                                  -Dsonar.inclusions=app/**,resources/**,routes/** \\
                                  -Dsonar.projectVersion="1.0.0" \\
                                  -Dsonar.sourceEncoding=UTF-8
                            """
                            checkout scm
                            sh """
                                sonar-scanner \\
                                  -Dsonar.projectKey=${projectKey} \\
                                  -Dsonar.sources=. \\
                                  -Dsonar.php.coverage.reportPaths=storage/sonarqube/clover.xml \\
                                  -Dsonar.javascript.lcov.reportPaths=coverage/lcov.info \\
                                  -Dsonar.token=${SONAR_ADMIN_TOKEN} \\
                                  -Dsonar.branch.base=development \\
                                  -Dsonar.exclusions=resources/css/app.css \\
                                  -Dsonar.inclusions=app/**,resources/**,routes/** \\
                                  -Dsonar.projectVersion="1.1.1" \\
                                  -Dsonar.sourceEncoding=UTF-8
                            """
                        }
                    }
                }
            }
        }

        stage('Wait for SonarQube Quality Gate') {
            steps {
                script {
                    def prIdentifier = env.CHANGE_ID ?: env.BUILD_NUMBER
                    def projectKey = "license-manager-${prIdentifier}"

                    timeout(time: 2, unit: 'MINUTES') {
                        def qualityGate = waitForQualityGate()
                        if (qualityGate.status != 'OK') {
                            echo "Quality Gate failed: ${qualityGate.status}"

                            withCredentials([
                                string(credentialsId: 'sonar-admin-token', variable: 'SONAR_TOKEN'),
                                usernamePassword(credentialsId: GITHUB_CREDENTIALS_ID, usernameVariable: 'GITHUB_USER', passwordVariable: 'GITHUB_TOKEN')
                            ]) {
                                def issuesUrl = "${SONAR_HOST_URL}/api/issues/search?componentKeys=${projectKey}&resolved=false&inNewCodePeriod=true"

                                echo "Fetching issues from: ${issuesUrl}"

                                def response = sh(
                                    script: "curl -s -u \"${SONAR_TOKEN}:\" \"${issuesUrl}\"",
                                    returnStdout: true
                                ).trim()

                                def issues = readJSON text: response

                                def formattedIssues = issues.issues.collect { issue ->
                                    """<br>
                                    - [Issue Link](${SONAR_HOST_URL}/project/issues?id=${projectKey}&open=${issue.key})<br>
                                      - **Issue:** ${issue.message} <br>
                                      - **Severity:** ${issue.severity} <br>
                                      - **File Path:** ${issue.component} <br>
                                      - **Line Number:** ${issue.line} <br>
                                      - **Rule:** ${issue.rule} <br>

                                    """
                                }.join('\n')

                                def commentBody = "SonarQube Quality Gate failed.\n\n**Issues:**\n\n${formattedIssues}"

                                def jsonPayload = """
                                    {
                                        "body": "${commentBody.replaceAll('"', '\\\\"')}"
                                    }
                                """.stripIndent()

                                writeFile file: 'comment.json', text: jsonPayload

                                sh """
                                    curl -s -X POST \\
                                    -H "Authorization: token ${GITHUB_TOKEN}" \\
                                    -H "Content-Type: application/json" \\
                                    -d @comment.json \\
                                    https://api.github.com/repos/faveosuite/${REPO_ID.replace('.git', '')}/issues/${env.CHANGE_ID}/comments
                                """
                            }
// Commenting out the error to prevent build failure as per request don;t close the pr just post the issues as comment
//                             error("Aborting pipeline due to failed quality gate.")
                        } else {
                            echo "Quality Gate passed. Deleting project from SonarQube..."
                            withCredentials([string(credentialsId: 'sonar-admin-token', variable: 'SONAR_TOKEN')]) {
                                sh """
                                    curl -s -X POST -u "${SONAR_TOKEN}:" \\
                                    "${SONAR_HOST_URL}/api/projects/delete?project=${projectKey}"
                                """
                            }
                        }
                    }
                }
            }
        }
    }

    post {
        always {
            script {
                def commitSha = env.GIT_COMMIT
                def repoName = scm.userRemoteConfigs[0].url.tokenize('/').last().replace('.git', '')
                def prNumber = env.CHANGE_ID ?: ''

                withCredentials([usernamePassword(credentialsId: GITHUB_CREDENTIALS_ID, usernameVariable: 'GITHUB_USER', passwordVariable: 'GITHUB_TOKEN')]) {
                    if (!prNumber) {
                        prNumber = sh(script: """
                            curl -s -u ${GITHUB_USER}:${GITHUB_TOKEN} \
                            "https://api.github.com/repos/faveosuite/${repoName}/pulls?head=${GITHUB_USER}:${env.BRANCH_NAME}" \
                            | jq '.[0].number' | tr -d '"'
                        """, returnStdout: true).trim()
                    }

                    def status = currentBuild.currentResult == 'SUCCESS' ? 'success' : 'failure'
                    def statusUrl = "${env.BUILD_URL}"
                    def description = currentBuild.currentResult == 'SUCCESS' ? 'Build successful' : 'Build failed'

                    sh """
                    curl -u ${GITHUB_USER}:${GITHUB_TOKEN} \
                         -d '{"state": "${status}", "target_url": "${statusUrl}", "description": "${description}", "context": "Jenkins"}' \
                         https://api.github.com/repos/faveosuite/${repoName}/statuses/${commitSha}
                    """

                    if (currentBuild.currentResult != 'SUCCESS' && prNumber) {
                        echo "Closing the PR due to build failure..."
                        sh """
                        curl -X PATCH -u ${GITHUB_USER}:${GITHUB_TOKEN} \
                             -d '{"state": "closed"}' \
                             https://api.github.com/repos/faveosuite/${repoName}/pulls/${prNumber}
                        """
                    }
                }
            }

            script {
                def buildNumber = env.BUILD_NUMBER
                def prNumber = env.CHANGE_ID ?: ''
                def repoName = scm.userRemoteConfigs[0].url.tokenize('/').last().replace('.git', '')

                withCredentials([usernamePassword(credentialsId: GITHUB_CREDENTIALS_ID, usernameVariable: 'GITHUB_USER', passwordVariable: 'GITHUB_TOKEN')]) {
                    if (!prNumber) {
                        prNumber = sh(script: """
                            curl -s -u ${GITHUB_USER}:${GITHUB_TOKEN} \
                            "https://api.github.com/repos/faveosuite/${repoName}/pulls?head=${GITHUB_USER}:${env.BRANCH_NAME}" \
                            | jq '.[0].number' | tr -d '"'
                        """, returnStdout: true).trim()
                    }

                    def uniqueDatabaseName = "license_${buildNumber}_${prNumber}"

                    withCredentials([
                        usernamePassword(credentialsId: MYSQL_CREDENTIALS_ID, usernameVariable: 'DB_USER', passwordVariable: 'DB_PASS')
                    ]) {
                        sh """
                        echo "Dropping database ${uniqueDatabaseName}" && \
                        mysql -u ${DB_USER} -p${DB_PASS} -e "DROP DATABASE IF EXISTS ${uniqueDatabaseName};" && \
                        rm -rf /home/jenkins/automation/${uniqueDatabaseName}.sql
                        """
                        cleanWs()
                    }
                }
            }
        }
    }
}
