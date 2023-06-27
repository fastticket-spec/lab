<template>
    <h5>Ticket Preview</h5>

    <div class="ticket-container d-flex align-items-center mt-3" :style="{
        border: `1px solid ${ticketBorderColor}`,
        backgroundColor: ticketBgcolor
    }">
        <div class="ticket-bar-code d-flex flex-column justify-content-between align-items-center">
            <p :style="{color: ticketTextColor}">#Y884884</p>
            <img :src="imgSrc" alt="">
            <p :style="{color: ticketSubTextColor}">#Y884884-1</p>
        </div>

        <div class="ticket-details d-flex flex-column" :style="{
        borderLeft: `1px solid ${ticketBorderColor}`,
        borderLeftStyle: 'dashed',
        borderLeftWidth: '6px',
        color: ticketTextColor
    }">
            <h3 class="mb-4" :style="{color: ticketTextColor}">{{event.title}}</h3>
            <span>Venue: {{event.venue_name}}, {{event.address}}</span>
            <span>Start Time: {{event.start_time}}</span>
            <span>End Time: {{event.end_time}}</span>
            <span>Name: Bill Blogs</span>
            <span>Ticket Type: General admission</span>
            <span>Price: $XX.XX</span>
        </div>
    </div>
</template>

<script setup>
defineProps({
    event: {},
    ticketSubTextColor: String,
    ticketTextColor: String,
    ticketBgcolor: String,
    ticketBorderColor: String,
    barCode: String
})

const imgSrc = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAH4AAAB+AQMAAADY7TfLAAAABlBMVEX///8AAABVwtN+AAAAAXRSTlMAQObYZgAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAPdJREFUSIm9lNENwyAMRC1lAEbKakgsyhh8VLi+M6ZK+xmnqJHQI+rh8zmiWF2KzoLdSx4E9gSYIlIM9Fm6bZMABF0FOICUB8CPig75AzieADrODejplLNeTL4HvPvzOw6mmQ9ciMCXVXtd98DK8Dgbf3UBqzYfmIptGucFx0LQj+jcXbCnMlSszoNn9qqBmgL8z1uoOECR6x6SBhQWcmPF2cRWFowgIoV5AFOJXklkLBmsvlijGICddR6brTURuJGKvvjc0s1EEF/LrUKw7gFbEwEHX8cXaJjedIAS4x40tKQCb71ShZ763KaBT8aYgR0HGOqeJoA3WWrZBx7GPSUAAAAASUVORK5CYII=';
</script>

<style scoped>
.ticket-bar-code {
    width: 27%;
}

.ticket-details {
    padding: 30px 60px;
}

.ticket-details span {
    margin-top: 7px;
    font-size: 16px;
}
</style>
