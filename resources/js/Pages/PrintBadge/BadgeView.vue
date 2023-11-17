<script setup>
const props = defineProps({
  badge: Array,
  language: String,
  error: String
})

const printBadge = () => {
  let header_str = '<html><head><title>Badge Print</title></head><body>';
  let footer_str = '</body></html>';
  let new_str = document.getElementById('badgeContainer').innerHTML;
  let old_str = document.body.innerHTML;
  document.body.innerHTML = header_str + new_str + footer_str;
  window.print();
  document.body.innerHTML = old_str;
  return false;
}
</script>

<script>
import PrintBadgeLayout from "../../Shared/Layout/PrintBadgeLayout.vue";

export default {
  layout: PrintBadgeLayout
}
</script>

<template>
  <template v-if="badge">
    <div class="row print-badge--view">
      <div class="col-6 offset-3 text-center">
        <p class="h3" v-if="language === 'english'">Badge View</p>
        <p class="h3" v-if="language === 'arabic'">عرض الشارة</p>
      </div>
    </div>

    <div class="row">
      <div class="col-8 offset-2 print-badge--container" v-html="badge.original.html_data">
      </div>
    </div>

    <div class="row mt-5">
      <div class="col-4 offset-4">
        <button class="btn" @click="printBadge">{{ language === 'english' ? 'Print' : 'مطبعة' }}</button>
      </div>
    </div>
  </template>

  <div v-else>
    <div class="row print-badge--view">
      <div class="col-6 offset-3 text-center h3 mt-5 text-danger">
        {{ error }}
      </div>
    </div>
  </div>
</template>

<style scoped>
.print-badge--view {
  color: white;
}

.print-badge--container {
  display: flex;
  justify-content: center;
  margin-top: 100px;
}

.btn {
  width: 100%;
  background-color: transparent;
  border: 1px solid #ffffff;
  color: white;
}

.btn:hover {
  color: black;
  background-color: white;
}
</style>

<style>
#badgeContainer {
  position: relative!important;
}
</style>

