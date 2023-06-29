<template>
    <div class="page-preview-container">
        <div class="bg-div" :style="{
            backgroundColor: data.background.type === 'color' && data.background.color,
            backgroundImage: data.background.type === 'image' ? 'url(' + data.background.bgImage?.full + ')' : '',

        }">
            <img class="my-3 text-center" :src="event.event_image_url" alt="">
            <p class="pt-3 px-4" v-html="accessLevel?.general_settings?.description" />

            <div class="py-4 text-center">
                <b-btn size="lg" class="w-75" :style="{backgroundColor: data.btn.color, color: data.btn.font_color}">
                    {{ data.register.english }}
                </b-btn>
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
}

.page-preview-container :deep(ul) {
    list-style: unset;
}
</style>
