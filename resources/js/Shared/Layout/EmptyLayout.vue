<template>
    <AlertHandler/>
    <section class="">
        <slot/>
    </section>
</template>
<script setup>
import AlertHandler from "../components/core/alert/AlertHandler.vue";
</script>
