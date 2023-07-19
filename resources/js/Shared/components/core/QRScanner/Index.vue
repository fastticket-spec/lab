<script setup>
import {onMounted} from "vue";

const props = defineProps({
    qrbox: {
        type: Number,
        default: 250
    },
    fps: {
        type: Number,
        default: 10
    },
})

const emit = defineEmits(['result'])

const onScanSuccess = (decodedText, decodedResult) => {
    emit('result', decodedText, decodedResult)
}

onMounted(() => {
    const config = {
        fps: props.fps,
        qrbox: props.qrbox,
    };
    const html5QrcodeScanner = new Html5QrcodeScanner('qr-code-full-region', config);
    html5QrcodeScanner.render(onScanSuccess);
})
</script>

<template>
    <div id="qr-code-full-region"></div>
</template>
