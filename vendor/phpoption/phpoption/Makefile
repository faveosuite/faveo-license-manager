install:
	@docker run -it -w /data -v ${PWD}:/data:delegated -v ~/.composer:/root/.composer:delegated --entrypoint composer --rm registry.gitlab.com/grahamcampbell/php:7.4-base update
	@docker run -it -w /data -v ${PWD}:/data:delegated -v ~/.composer:/root/.composer:delegated --entrypoint composer --rm registry.gitlab.com/grahamcampbell/php:7.4-base bin all update

phpunit:
	@rm -f bootstrap/cache/*.php && docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/phpunit --rm registry.gitlab.com/grahamcampbell/php:7.4-cli

phpstan-analyze-src:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/phpstan --rm registry.gitlab.com/grahamcampbell/php:7.4-cli analyze src -c phpstan.src.neon.dist

phpstan-analyze-tests:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/phpstan --rm registry.gitlab.com/grahamcampbell/php:7.4-cli analyze tests -c phpstan.tests.neon.dist

psalm-analyze:
<<<<<<< HEAD
<<<<<<< HEAD
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/psalm.phar --rm registry.gitlab.com/grahamcampbell/php:7.4-cli

psalm-show-info:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/psalm .phar--rm registry.gitlab.com/grahamcampbell/php:7.4-cli --show-info=true
=======
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/psalm --rm registry.gitlab.com/grahamcampbell/php:7.4-cli

psalm-show-info:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/psalm --rm registry.gitlab.com/grahamcampbell/php:7.4-cli --show-info=true
>>>>>>> 22c0e54 (table changes)
=======
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/psalm.phar --rm registry.gitlab.com/grahamcampbell/php:7.4-cli

psalm-show-info:
	@docker run -it -w /data -v ${PWD}:/data:delegated --entrypoint vendor/bin/psalm .phar--rm registry.gitlab.com/grahamcampbell/php:7.4-cli --show-info=true
>>>>>>> f330c64 (optimization in progress)

test: phpunit phpstan-analyze-src phpstan-analyze-tests psalm-analyze

clean:
	@rm -rf .phpunit.result.cache composer.lock vendor vendor-bin/*/composer.lock vendor-bin/*/vendor
