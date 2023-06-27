<template>
    <order-details-view :order="order" :project_lists="project_lists" :current_list="current_list" :event-id="event_id" />
</template>

<script setup>
import OrderDetailsView from "../../../../Pages/Orders/components/OrderDetailsView.vue";

const props = defineProps({
    order: {},
    project_lists: [],
    current_list: String,
    event_id: String,
})
</script>
