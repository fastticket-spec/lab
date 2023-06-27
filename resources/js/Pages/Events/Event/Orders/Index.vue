<template>
    <order-view :orders="orders"
                :q="q"
                :event-id="event_id"
                :export-url="`/event/${event_id}/orders/export`"
                :attendee-export-url="`/event/${event_id}/attendees/export`"
                :survey-export-url="`/event/${event_id}/orders/export-surveys`"
    />
</template>


<script setup>
import OrderView from '../../../../Pages/Orders/components/OrderView.vue'

const props = defineProps({
    orders: {},
    q: String,
    event_id: String
})
</script>
