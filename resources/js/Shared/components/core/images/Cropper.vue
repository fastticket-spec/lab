<script setup>
import {Cropper} from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';

import {computed, onMounted, ref, watch} from "vue";

const props = defineProps({
    image: String,
    aspectRatio: Number,
    modelValue: File,
    resizable: Boolean
})

const emits = defineEmits(['change']);

const image = ref('');
const mimeType = ref('');

onMounted(() => {
    setImageAsData(props.image);
});

const uploadedImage = computed(() => props.image);

watch(uploadedImage, async val => {
    setImageAsData(val);
}, {deep: true})

const setImageAsData = file => {
    if (file) {
        mimeType.value = file.type

        let reader = new FileReader();

        reader.onload = function (e) {
            image.value = e.target.result;
        };

        reader.readAsDataURL(file);
    }
}

const change = ({coordinates, canvas}) => {
    canvas.toBlob((b) => {
        emits('change', b);
    }, mimeType.value)
}

</script>

<template>
    <div style="width: 100%">
        <cropper
            :src="image"
            @change="change"
            :stencil-props="{
                handlers: {},
                movable: true,
                resizable: true,
                aspectRatio: aspectRatio,
            }"
            :resize-image="{
                adjustStencil: false
            }"
            image-restriction="stencil"
        />
    </div>
</template>
