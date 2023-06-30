<script setup>
const props = defineProps({
    accessLevel: Object,
    lang: String
})
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
                }" class="d-flex align-items-center">
        <div class="row no-gutters accreditation-form w-100" :class="{'rtl text-right': lang === 'arabic'}">
            <div class="col-12 align-self-center">
                <div class="bg-div bg-white">
                    <p
                        v-html="lang === 'arabic' ? accessLevel?.general_settings?.success_message_arabic : accessLevel?.general_settings?.success_message"
                        class="text-center p-5"/>
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
