<script setup>
import AlertHandler from "../components/core/alert/AlertHandler.vue";
</script>

<template>
    <AlertHandler/>
    <section class="print-section">
        <div class="print-header">
            <img src="https://eticket.fra1.cdn.digitaloceanspaces.com/proptech/Asset%207.png" class="print-header--logo"
                 alt="">
        </div>

        <div class="print-body w-100">
            <slot/>
        </div>

        <div class="print-footer text-center text-white">
            <h3 class="text-white">Powered by AOne</h3>
        </div>
    </section>
</template>

<style scoped>
.print-section {
    background-image: url("https://eticket.fra1.cdn.digitaloceanspaces.com/proptech/Asset%209.png");
    background-size: cover;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.print-header {
    text-align: center;
}

.print-header--logo {
    width: 200px;
    margin-top: 20px;
    margin-bottom: 40px;
}

.print-body {
    flex: 1;
}

.print-footer {
    margin-bottom: 20px;
}

.print-footer--logo {
    width: 100px;
}
</style>
