<template>
    <div class="page-preview-container">
        <div class="bg-div" :style="{
            backgroundColor: data.background.type === 'color' && data.background.color,
            backgroundImage: data.background.type === 'image' ? 'url(' + data.background.bgImage?.full + ')' : '',
            backgroundSize: 'cover'
        }"></div>
        <div class="event-div px-4" :style="{backgroundColor: data.background.color2}">
            <h2 class="mb-5" :style="{color: data.title.color}">{{ event.title }}</h2>
            <div class="d-flex mb-2" :style="{color: data.title.desc_color}">
                <i class="ri-calendar-2-fill" :style="{color: data.icon.color}"></i> <span>{{
                    event.start_date
                }} - {{ event.end_date }}</span>
            </div>
            <div class="d-flex mb-2" :style="{color: data.title.desc_color}">
                <i class="ri-time-fill" :style="{color: data.icon.color}"></i> <span>{{
                    event.start_time
                }} - {{ event.end_time }}</span>
            </div>
            <div class="d-flex mb-2" :style="{color: data.title.desc_color}">
                <i class="ri-map-pin-2-fill" :style="{color: data.icon.color}"></i>
                <span>{{ event.venue_name }}, {{ event.address }}</span>
            </div>
            <p :style="{color: data.title.desc_color}" v-html="event.description" />

            <div class="mt-4">
                <b-btn size="lg" :style="{backgroundColor: data.btn.color, color: data.btn.font_color}">
                    {{ data.register.english }}
                </b-btn>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    data: {},
    event: {}
})
</script>

<style>
.page-preview-container {
    border: 2px solid var(--iq-primary);
    border-radius: 5px;
    height: 600px;
    position: relative;
}

.bg-div {
    height: 100%;
    width: 60%;
}

.event-div {
    height: 100%;
    width: 65%;
    position: absolute;
    top: 0;
    right: 0;
    border-top-left-radius: 100px;
    display: flex;
    flex-direction: column;
    justify-content: center;

}

.event-div span, .event-div i {
    font-size: 18px;
}

.event-div i {
    margin-right: 4px;
}


</style>
