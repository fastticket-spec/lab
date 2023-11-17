<script setup>
import Loader from "./Loader.vue";
import {router} from "@inertiajs/vue3";
</script>

<script>
import PrintBadgeLayout from "../../Shared/Layout/PrintBadgeLayout.vue";

export default {
  layout: PrintBadgeLayout
}
</script>

<template>
  <div class="row print-welcome" @click="router.get('/print-badge/choose-language')">
    <div class="col-6 offset-3 text-center">
      <p class="mb-0 h3">Welcome</p>
      <p>Click anywhere on the page to print your badge.</p>

      <Loader class="loader" />
    </div>
  </div>
</template>

<style scoped>
  .print-welcome {
    color: white;
  }

  .loader {
    margin-top: 200px;
  }
</style>
