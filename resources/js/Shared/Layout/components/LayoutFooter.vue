<template>
<div>
  <footer class="iq-footer" :class="{'ar': locale === 'ar'}">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-6">
          <ul class="list-inline mb-0">
            <!-- <li class="list-inline-item"><a href="#">Privacy Policy</a></li>
            <li class="list-inline-item"><a href="#">Terms of Use</a></li> -->
          </ul>
        </div>
        <div class="col-lg-6 text-right">
          <!-- Copyright 2020 All Rights Reserved. -->
        </div>
      </div>
    </div>
  </footer>
<!--  <FloatingButton />-->
</div>
</template>
<script setup>
defineProps({
    locale: String
})
import FloatingButton from './FloatingButton.vue'
</script>
