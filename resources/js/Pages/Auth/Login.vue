<template>
    <div class="img-container text-center">
        <img src="../../../images/logo-dark.jpeg" class="img-fluid" alt="logo">
    </div>

    <h1 class="mb-0">{{ $t('sign-in.header') }}</h1>
    <p>{{ $t('sign-in.description') }}</p>
    <sign-in-form/>
</template>

<script setup>
import SignInForm from './components/SignInForm.vue'
</script>

<script>
import AuthLayout from '../../Shared/Layout/AuthLayout.vue';

export default {
    layout: AuthLayout
}
</script>

<style scoped>
.img-container {
    width: 100%;
    display: flex;
    justify-items: center;
    align-items: center;
    margin-bottom: 25px;
}

.img-container img {
    width: 150px;
    margin: auto;
}
</style>
