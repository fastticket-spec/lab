<template>
    <b-row class="page-cards">
        <b-col sm="6" v-for="event in events" :key="event.id">
            <b-card :title="event.title" class="iq-mb-3">
                <b-card-sub-title>
                    <div><small>By {{ event.organiser_name }}</small></div>
                </b-card-sub-title>

                <b-card-text class="d-flex w-100 justify-content-around my-5">
                    <div class="text-center">
                        <h5>{{ event.downloads }}</h5>
                        <span>Downloads</span>
                    </div>

                    <div class="text-center">
                        <h5>{{ event.no_of_attendees }}</h5>
                        <span>Attendees</span>
                    </div>
                </b-card-text>

                <div class="card-date d-flex flex-column text-center" :class="{'card-date-ar': locale === 'ar'}">
                    <span>{{ event.month }}</span>
                    <h5>{{ event.day }}</h5>
                </div>

                <div class="d-flex justify-content-around">
                    <a href="#" @click.prevent="goToEvent(event.id)"><i
                        class="ri-eye-line"></i> View</a>
                    <template v-if="(!userRole || userRole === 'Admin Users')">
                        <a href="#" @click.prevent.stop="visit(`/events/${event.id}/duplicate`, 'post')"><i
                            class="ri-file-copy-2-line"></i> Duplicate</a>
                        <a href="#" @click.prevent.stop="visit(`/events/${event.id}/edit`)"><i class="ri-edit-line"></i>
                            Edit</a>
                        <a href="#" @click.prevent.stop="visit(`/events/${event.id}/change-status`, 'post')"
                           :class="event.status === 'inactive' ? 'text-success' : 'text-danger'"><i
                            :class="event.status === 'inactive' ? 'ri-eye-fill' : 'ri-eye-close-line'"></i>
                            {{ event.status === 'inactive' ? 'Activate' : 'Deactivate' }}</a>
                        <a href="#" @click.prevent.stop="" class="text-danger"
                           :id="`popover-button-variants-${event.id}`"><i
                            class="ri-delete-bin-2-line"></i> Delete</a>
                        <b-popover :target="`popover-button-variants-${event.id}`"
                                   variant="default" triggers="focus">
                            <p><b>Are you sure you want to delete this event?</b></p>
                            <b-btn variant="outline-danger"
                                   @click.prevent.stop="visit(`/events/${event.id}`, 'delete')">Yes, Delete
                            </b-btn>
                        </b-popover>
                    </template>

                    <template v-if="userRole === 'Editors' || userRole === 'Viewers' || userRole === 'Operations'">
                        <a href="#" v-if="userRole !== 'Operations'" @click.prevent.stop="visit(`/event/${event.id}/access-levels`)"><i
                            class="ri-sound-module-line"></i>
                            Access Levels</a>

                        <a href="#" @click.prevent.stop="visit(`/event/${event.id}/attendees`)"><i
                            class="ri-group-2-line"></i>
                            Attendees</a>

                        <a href="#" v-if="userRole !== 'Operations'" @click.prevent.stop="visit(`/event/${event.id}/badges`)"><i
                            class="ri-pencil-ruler-line"></i>
                            Badges</a>
                    </template>

                    <template v-if="(!userRole || userRole === 'Admin Users' || userRole === 'Editors')">
                        <a href="#" @click.prevent.stop="visit(`/event/${event.id}/event-surveys`)"><i
                            class="ri-ticket-line"></i>
                            Surveys</a>
                    </template>
                </div>
            </b-card>
        </b-col>
    </b-row>

</template>

<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {computed, onMounted, ref} from "vue";

const props = defineProps({
    events: {}
})

const locale = ref('');

onMounted(() => {
    locale.value = localStorage.getItem('locale');
})

const userRole = computed(() => usePage().props.user_role);

const goToEvent = eventId => {
    router.get(`/event/${eventId}/dashboard`);
}

const visit = (link, method = 'get') => {
    if (method === 'get') {
        router.get(link);
    } else if (method === 'post') {
        router.post(link, null, {preserveScroll: true});
    } else {
        router.delete(link, {preserveScroll: true})
    }
}
</script>

<style>
.page-cards .card {
    border: 1px solid var(--iq-primary);
}

.card-body {
    box-shadow: 2px 4px 8px;
}

.card-date {
    position: absolute;
    top: 0;
    right: 0;
    padding: 4px 15px;
    margin: 5px;
    border: 1px solid var(--iq-primary);
    border-radius: 5px;
}

.card-date-ar {
    left: 0;
    right: unset;
}

</style>
