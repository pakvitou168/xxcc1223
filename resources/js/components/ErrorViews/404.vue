<template>
    <div class="wrapper fixed inset-0 z-100">
        <div class="error-page flex flex-col lg:flex-row items-center justify-center h-screen text-center lg:text-left">
            <div class="-intro-x lg:mr-20">
                <img alt="PGI" class="h-48 lg:h-auto" src="/images/error-illustration.svg">
            </div>
            <div class="text-white mt-10 lg:mt-0">
                <div class="intro-x text-6xl font-medium">404</div>
                <div class="intro-x text-xl lg:text-3xl font-medium">Oops. This page has gone missing.</div>
                <div class="intro-x text-lg mt-3">You may have mistyped the address or the page may have moved.</div>
                <router-link to="/">
                    <button class="intro-x button button--lg border border-white dark:border-dark-5 dark:text-gray-300 mt-10">Back to Dashboard</button>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    
}
</script>

<style scoped>
    .wrapper {
        background: rgb(28, 63, 170);
    }
    .z-100 {
        z-index: 100;
    }
    .button.button--lg {
        padding-top: 0.75rem;
        padding-bottom: 0.75rem;
        padding-left: 1rem;
        padding-right: 1rem;
    }
    .button {
        border-radius: 0.375rem;
        font-weight: 500;
        cursor: pointer;
    }
</style>