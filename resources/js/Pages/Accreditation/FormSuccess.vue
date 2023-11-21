<script setup>
import {onMounted} from "vue";

const props = defineProps({
    accessLevel: Object,
    lang: String
})

onMounted(() => {
    document.querySelector('title').textContent = `${props.accessLevel.title} - ${props.accessLevel?.event?.organiser?.name}`
})
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

</style>


<template>
    <b-container fluid  style="width: 80%;" :style="{
                    backgroundColor: accessLevel?.page_design?.bg_type === 'color' && accessLevel?.page_design?.bg_color,
                    backgroundImage: accessLevel?.page_design?.bg_type === 'image'? 'url(' + accessLevel?.page_design?.bg_image + ')' : '',
                    backgroundSize: 'cover',
                    backgroundPositionX: '50%',
                    backgroundPositionY: '50%',
                    paddingTop: '100px',
                    paddingBottom: '100px',
                    minHeight: '100vh',
                    textAlign: lang != 'english' ? 'right' : '',
                    direction:  lang != 'english' ? 'rtl' : ''
                }" class="mx-auto vag d-flex align-items-center">
        <div class="row no-gutters accreditation-form w-100" :class="{'rtl text-right': lang === 'arabic'}">
            <div class="col-12 align-self-center">
                <div class="bg-div bg-white" :style="{backgroundColor: accessLevel?.page_design?.form_bg_color + ' !important'}">
                    <div class="text-center">
                        <img class="my-3 text-center img-fluid logo" :src="accessLevel?.page_design?.logo || accessLevel?.event?.event_image_url" alt="">
                    </div>
                    <p :style="{ color: accessLevel?.page_design?.font_color}"
                        v-html="lang === 'arabic' ? accessLevel?.general_settings?.success_message_arabic : accessLevel?.general_settings?.success_message"
                        class="text-center p-5"/>

                    <div v-if="accessLevel?.page_design?.footer_logo" class="text-center">
                        <img :src="accessLevel?.page_design?.footer_logo" alt="" class="img-fluid" :style="`height: ${accessLevel?.page_design?.footer_logo_height}px; margin-bottom: 15px; text-align: center`">
                    </div>
                </div>
                <div class="text-center">
                    <a class="btn btn-primary" onclick="history.back()"> Add Another</a>
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

p :deep(.ql-direction-rtl) {
    direction: rtl;
    //text-align: center;
}
</style>
