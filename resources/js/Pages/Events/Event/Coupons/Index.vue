<template>
    <coupon-view :coupons="coupons" :event="event" />
</template>

<script setup>
import CouponView from "../../../Coupons/components/CouponView.vue";

defineProps({
    coupons: {},
    event: {}
})
</script>
