<script setup>
import AlertHandler from "../components/core/alert/AlertHandler.vue";
</script>

<template>
  <AlertHandler/>
  <section class="print-section">
    <div class="print-header">
      <img src="../../../images/logo-dark.jpeg" class="print-header--logo" alt="">
    </div>

    <div class="print-body w-100">
      <slot/>
    </div>

    <div class="print-footer text-center">
      <img src="../../../images/logo-dark.jpeg" class="print-footer--logo" alt="">
    </div>
  </section>
</template>

<style scoped>
.print-section {
  background-color: #1B29D7;
  height: 100vh;
  display: flex;
  flex-direction: column;
}

.print-header--logo {
  width: 100px;
  margin: 15px;
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
