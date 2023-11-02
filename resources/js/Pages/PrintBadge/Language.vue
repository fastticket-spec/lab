<script setup>
import {router} from "@inertiajs/vue3";
</script>

<script>
import PrintBadgeLayout from "../../Shared/Layout/PrintBadgeLayout.vue";

export default {
  layout: PrintBadgeLayout
}
</script>

<template>
  <div class="row print-language">
    <div class="col-6 offset-3 text-center">
      <p class="">WE ARE DELIGHTED TO WELCOME YOU TO THE GLOBAL CYBERSECURITY FORUM 2023</p>
      <p class="h4">CHARTING SHARED PRIORITIES IN CYPERSPACE</p>
    </div>

    <div class="col-6 offset-3 text-center print-language--action">
      <p class="h4">TO ASSIST YOU IN PRINTING YOUR BADGE, PLEASE SELECT YOUR PREFERRED LANGUAGE</p>

      <table class="lang-buttons">
        <tr>
          <td width="50%" @click="router.get('/print-badge/scan-qr?lang=arabic')">عربي </td>
          <td @click="router.get('/print-badge/scan-qr?lang=english')">English</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<style scoped>
.print-language {
  color: white;
  margin-top: 10%;
}

.print-language--action {
  margin-top: 35%;
}

.lang-buttons {
  width: 100%;
  margin-top: 20px;
  cursor: pointer;
}

.lang-buttons td {
  border: 1px solid #ffffff;
  padding: 7px;
}

.lang-buttons td:hover {
  border: 2px solid #ffffff;
  background-color: white;
  color: black;
}

</style>
