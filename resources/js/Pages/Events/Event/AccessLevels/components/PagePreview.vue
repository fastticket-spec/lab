<template>
    <div class="page-preview-container" :style="{
            backgroundColor: data.background.type === 'color' && data.background.color,
            backgroundImage: data.background.type === 'image' ? 'url(' + data.background.bgImage?.full + ')' : '',

        }">
        <div class="bg-div m-5" :style="{backgroundColor: data.formBackground.color}">
            <div class="text-center">
                <img class="my-3 text-center img-fluid logo" :src="accessLevel?.page_design?.logo || event.event_image_url" alt="">
            </div>

            <p class="pt-3 px-4" v-html="accessLevel?.general_settings?.description"/>

            <div class="py-4 text-center">
                <b-btn size="lg" class="w-75" :style="{backgroundColor: data.btn.color, color: data.btn.font_color}">
                    {{ data.register.english }}
                </b-btn>
            </div>

            <div v-if="data.footer_logo" class="text-center">
                <img :src="data.footer_logo" alt="" class="img-fluid" :style="`height: ${data.footer_logo_height}px; margin-bottom: 15px; text-align: center`">
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    data: {},
    event: {},
    accessLevel: {}
})
</script>

<style scoped>
.page-preview-container {
    border: 2px solid var(--iq-primary);
    border-radius: 5px;
    max-height: 600px;
    overflow: auto;
}

.bg-div {
    height: 100%;
    width: 100%;
    border-radius: 10px;
}

.page-preview-container :deep(ul) {
    list-style: unset;
}

img.logo {
    height: 80px;
}
</style>
