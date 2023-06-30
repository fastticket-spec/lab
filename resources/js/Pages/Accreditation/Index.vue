<script setup>
import {ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    accessLevel: Object
})

const lang = ref('english');

const goToForm = () => {
    router.get(`/form/${props.accessLevel.id}?lang=${lang.value}`)
}
</script>

<script>
import EmptyLayout from '../../Shared/Layout/EmptyLayout.vue';

export default {
    layout: EmptyLayout
}
</script>

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
                <div class="bg-div bg-white">
                    <div class="text-center">
                        <img class="my-3 text-center img-fluid logo" :src="accessLevel?.event?.event_image_url" alt="">
                    </div>

                    <p class="pt-3 px-4" :class="{rtl: lang === 'arabic'}"
                       v-html="lang === 'english' ? accessLevel?.general_settings?.description : accessLevel?.general_settings?.description_arabic"/>

                    <div class="py-4 text-center">
                        <b-btn @click="goToForm" size="lg" class="px-5 py-2"
                               :style="{border:'none', backgroundColor: accessLevel?.page_design?.btn_color_code, color: accessLevel?.page_design?.btn_font_color_code}">
                            {{ accessLevel?.page_design?.register_btn_value || 'Register' }}
                        </b-btn>
                    </div>

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
