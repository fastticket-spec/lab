<script setup>
import {onMounted, ref} from "vue";
import {router} from "@inertiajs/vue3";
import Loader from "./Loader.vue";

const props = defineProps({
    language: String
})

const scanning = ref(false);

const onScanSuccess = (decodedText, decodedResult) => {
    if (decodedText && !scanning.value) {
        scanning.value = true
        router.get(`/print-badge/view-badge?ref=${decodedText}&lang=${props.language}`)
    }
}

const errorMessage = ref('');

onMounted(() => {
    const config = {
        fps: 10,
        qrbox: 250,
    };
    const html5QrcodeScanner = new Html5QrcodeScanner('qr-code-div', config);
    html5QrcodeScanner.render(onScanSuccess);
})
</script>

<script>
import PrintBadgeLayout from "../../Shared/Layout/PrintBadgeLayout.vue";

export default {
    layout: PrintBadgeLayout
}
</script>

<template>
    <div class="row print-scan">
        <div class="col-6 offset-3 text-center">
            <p class="" v-if="language === 'english'">Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                Blanditiis commodi eaque ex praesentium quo sequi similique! Animi blanditiis debitis ducimus illo
                magnam necessitatibus possimus quisquam ullam voluptatem. Expedita, fugit, sapiente!</p>
            <p class="" v-if="language === 'arabic'">جُل وقامت رجوعهم الأرضية في. الإكتفاء الإيطالية نفس ٣٠, مع بشكل
                الطرفين والمانيا حيث. تعداد مواقعها إيو أي, أن الحكم رجوعهم لها. وتم بـ نقطة وإقامة تكاليف, من نهاية
                اكتوبر وقوعها، هذه. كلا وبعض الأولى اتفاقية قد, وعلى الإتفاقية ما ذات.</p>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-6 offset-3">
            <div class="card card-bordered mb-3 text-center qr-card">
                <div id="qr-code-div" class="qr-code-div" v-if="!scanning"></div>
                <div v-else>
                    <Loader />
                </div>
                <span class="text-danger">{{ errorMessage }}</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-6 offset-3 text-center print-scan--bottom">
            <p class="" v-if="language === 'english'">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
            <p class="" v-if="language === 'arabic'">جُل وقامت رجوعهم الأرضية في. الإكتفاء الإيطالية نفس ٣٠, مع بشكل
                الطرفين والمانيا حيث. تعداد مواقعها إيو أي, أن الحكم رجوعهم لها</p>
        </div>
    </div>
</template>


<style scoped>
.print-scan {
    color: white;
    margin-top: 10%;
}

.print-scan--bottom {
    margin-top: 80px;
    color: white;
}

.qr-card {
    background-color: transparent;
    border: none;
}

.qr-code-div {
    border: none !important;
}

.btn {
  border: 1px solid #ffffff;
  color: white;
}

.btn:hover {
  color: black;
  background-color: white;
}
</style>

<style>
#qr-code-div__scan_region img {
    display: none;
}

.qr-code-div div div + img {
    display: none;
}

#html5-qrcode-anchor-scan-type-change {
    color: white;
}
</style>
