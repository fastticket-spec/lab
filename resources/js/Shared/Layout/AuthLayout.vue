<template>
    <AlertHandler/>
    <section class="sign-in-page">
        <b-container class="mt-5 p-0">
            <div class="row no-gutters">
                <div class="col-12 col-xl-6 mx-auto align-self-center bg-white">
                    <div class="sign-in-from sign-in-form">
                        <slot/>
                    </div>
                </div>
            </div>
        </b-container>
    </section>
</template>
<script setup>
import AlertHandler from "../components/core/alert/AlertHandler.vue";
</script>
