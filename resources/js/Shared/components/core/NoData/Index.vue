<script setup>
defineProps({
    title: {type: String, default: 'Data'},
    link: {type: String, default: '#'},
    subText: {type: Boolean, default: true}
})
</script>

<template>
    <div class="row justify-content-center align-items-center">
        <div class="col-sm-9 text-center">
            <div class="iq-comingsoon-info">
                <h2 class="mt-4 mb-1">No {{ title }}</h2>
                <p v-if="subText">Click <Link :href="link">here</Link>
                    to create one
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.row {
    height: 50vh;
}
</style>
