<script setup>
import VueCropper from 'vue-cropperjs';
import 'cropperjs/dist/cropper.css';
import {ref} from "vue";

const props = defineProps({
  imgSrc: Object,
  onCrop: Function
})

const cropper = ref('');

const onCropImage = () => {
  props.onCrop(cropper.value.getCroppedCanvas().toDataURL());
}
</script>

<template>
  <vue-cropper
      ref="cropper"
      :aspect-ratio="9 / 9"
      :src="imgSrc"
      preview=".preview"
  />

  <div class="text-right">
    <b-btn variant="primary" class="mt-3 text-right" @click="onCropImage">Crop</b-btn>
  </div>
</template>
