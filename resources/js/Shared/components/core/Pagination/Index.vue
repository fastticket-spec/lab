<template>
    <div>
        <Component
            :is="link.url ? 'b-btn' : 'span'"
            v-for="link in links"
            :href="link.url" v-html="link.label"
            class="px-1"
            variant="outline-primary"
            :class="{'text-gray-500' : !link.url, 'font-bold' : link.active}"
        />
    </div>
</template>

<script>
export default {
    props: {
        links: []
    }
}
</script>
