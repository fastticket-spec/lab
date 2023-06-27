<template>
    <coupon-form
        :code="code"
        :message="message"
        :errors="errors"
        :edit-mode="editMode"
        :coupon="coupon"
        :eventId="eventId"
        :event="event"
        :tickets="tickets"
        :coupon-tickets="coupon_tickets"
    />
</template>

<script setup>
import CouponForm from '../../../Coupons/components/CouponForm.vue';

const props = defineProps({
    code: Number,
    message: String,
    errors: Object,
    editMode: Boolean,
    coupon: {},
    eventId: String,
    event: Object,
    tickets: Array,
    coupon_tickets: Array
})
</script>
