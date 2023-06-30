<template>
    <AlertHandler/>
    <section class="">
        <b-container fluid class="m-0 p-0">
            <div class="row no-gutters">
                <div class="col-12 align-self-center">
                    <slot/>
                </div>
            </div>
        </b-container>
    </section>
</template>
<script setup>
import AlertHandler from "../components/core/alert/AlertHandler.vue";
</script>
