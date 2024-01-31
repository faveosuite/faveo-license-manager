<template>
    <div v-if="shouldShowProgressBar" class="progress color-shift-progress-bar">
        <div class="progress-bar" role="progressbar" :style="{ width: progressBarWidth }"></div>
    </div>
    <router-view :versioning="versioning"></router-view>
</template>

<script>
export default {
    props: {
        versioning: { type: String, default: '' },
    },
    data() {
        return {
            shouldShowProgressBar: false,
            progressBarInterval: null, // Interval variable to control the progress bar animation
        };
    },
    watch: {
        $route(to, from) {
            this.$store.dispatch('unsetAlert');
            this.$store.dispatch('unsetValidationError');
        },
    },
    beforeMount() {
        this.$store.dispatch('setApiKey');
    },
    computed: {
        progressBarWidth() {
            return this.$store.state.progressBarValue + '%';
        },
    },
    methods: {
        startProgressBarAnimation() {
            let progress = 0;
            this.progressBarInterval = setInterval(() => {
                progress += 1; // Adjust the increment as needed
                this.$store.dispatch('updateProgressBar', progress);
                if (progress >= 100) {
                    clearInterval(this.progressBarInterval);
                }
            }, 100); // Adjust the interval as needed
        },
        stopProgressBarAnimation() {
            clearInterval(this.progressBarInterval);
            this.$store.dispatch('updateProgressBar', 0);
        },
    },
    created() {
        const store = this.$store;
        let activeRequests = 0;

        const showProgressBar = () => {
            activeRequests++;
            if (activeRequests === 1) {
                this.shouldShowProgressBar = true;
                this.startProgressBarAnimation();
            }
        };

        const hideProgressBar = () => {
            activeRequests--;
            if (activeRequests === 0) {
                this.shouldShowProgressBar = false;
                this.stopProgressBarAnimation();
            }
        };

        const onRequestSuccess = (response) => {
            hideProgressBar();
            return response;
        };

        const onRequestError = (error) => {
            hideProgressBar();
            return Promise.reject(error);
        };

        axios.interceptors.request.use((config) => {
            showProgressBar();
            return config;
        });

        axios.interceptors.response.use(onRequestSuccess, onRequestError);
    },
};
</script>

<style scoped>
.color-shift-progress-bar {
    width: 100%;
    height: 3px;
    background: linear-gradient(to right, transparent, #007bff, transparent); /* Updated */
    background-size: 200% 100%;
    animation: color-move-animation 2s linear infinite;
}

@keyframes color-move-animation {
    0% {
        background-position: 200% 0; /* Updated */
    }
    100% {
        background-position: -200% 0; /* Updated */
    }
}
</style>
