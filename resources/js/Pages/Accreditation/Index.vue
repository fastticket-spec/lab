<script setup>
import {ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    accessLevel: Object,
    status: Boolean,
    reference: String
})

const lang = ref('english');

const goToForm = () => {
    router.get(`/form/${props.accessLevel.id}?ref=${props.reference || ''}&lang=${lang.value}`)
}
</script>

<script>
import EmptyLayout from '../../Shared/Layout/EmptyLayout.vue';

export default {
    layout: EmptyLayout
}
</script>

<style>
.form-control {
    display: block;
    width: 100%;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #ffffff;
    background-color: #fff0;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}
.bg-white {
    //background-color: #7d4f4c61 !important;
}
label {
    color: #ffffff;
}
</style>

<template>
    <b-container fluid :style="{
                    backgroundColor: accessLevel?.page_design?.bg_type === 'color' && accessLevel?.page_design?.bg_color,
                    backgroundImage: accessLevel?.page_design?.bg_type === 'image'? 'url(' + accessLevel?.page_design?.bg_image + ')' : '',
                    backgroundSize: 'cover',
                    backgroundPositionX: '50%',
                    backgroundPositionY: '50%',
                    paddingTop: '100px',
                    paddingBottom: '100px',
                    paddingLeft: '200px',
                    paddingRight: '200px',
                    minHeight: '100vh'
                }">
        <div class="row no-gutters">
            <div class="col-12 align-self-center">
                <div class="bg-div" :style="{backgroundColor: accessLevel?.page_design?.form_bg_color}">
                    <div class="text-center p-5" v-if="!status">
                        <h3 class="p-5" v-if="lang === 'english'">Event is not active. Please contact administrator.</h3>
                        <h3 class="p-5" v-else>الحدث غير نشط. يرجى الاتصال بالمسؤول</h3>
                    </div>

                    <template v-else>
                        <div class="text-center">
                            <img class="my-3 text-center img-fluid logo" :src="accessLevel?.page_design?.logo || accessLevel?.event?.event_image_url" alt="">
                        </div>

                        <p class="pt-3 px-4" :class="{rtl: lang === 'arabic'}"
                           v-html="lang === 'english' ? accessLevel?.general_settings?.description : accessLevel?.general_settings?.description_arabic"/>

                        <div class="py-4 text-center">
                        <b-btn @click="goToForm" size="lg" class="px-5 py-2"
                               :style="{border:'none', backgroundColor: accessLevel?.page_design?.btn_color_code, color: accessLevel?.page_design?.btn_font_color_code}">
                            {{ accessLevel?.page_design?.register_btn_value || 'Register' }}
                        </b-btn>
                    </div>
                    </template>

                    <div class="lang-container">
                        <select v-model="lang">
                            <option value="english">English</option>
                            <option value="arabic">Arabic</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </b-container>
</template>

<style scoped>
.bg-div {
    height: 100%;
    width: 100%;
    position: relative;
    border-radius: 10px;
}

.bg-div :deep(ul) {
    list-style: unset;
}

img.logo {
    height: 100px;
}

.lang-container {
    position: absolute;
    top: 15px;
    right: 15px;
}

p :deep(.ql-direction-rtl) {
    direction: rtl;
    text-align: right;
}
</style>
