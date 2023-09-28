<script setup>

import {router, usePage} from "@inertiajs/vue3";
import {computed} from "vue";

let props = defineProps({event_surveys: {}, event_id: String})

const userRole = computed(() => usePage().props.user_role);

const visit = (link, method = 'get') => {
    if (method === 'get') {
        router.get(link);
    } else {
        router.post(link);
    }
}

const onPaginate = page => {
    router.get(`/event/${props.event_id}/event-surveys?page=${page}`)
}
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12" v-if="(userRole !== 'Operations' && userRole !== 'Viewers')">
                <b-btn variant="primary" class="mb-3" @click="visit(`/event/${event_id}/event-surveys/create/`)">Add Survey</b-btn>
            </b-col>
            <b-col sm="12">
                <no-data v-if="!event_surveys.total" title="Event Surveys" :link="(userRole !== 'Operations' && userRole !== 'Viewers') ? '/event_surveys/create' : '#'" />

                <iq-card v-if="event_surveys.total">
                    <template v-slot:headerTitle>
                        <h4 class="card-title mb-2">{{ $t('sidebar.event_surveys') }}</h4>
                    </template>
                </iq-card>
            </b-col>
        </b-row>

        <b-row class="page-cards">
            <b-col sm="6" v-for="event_survey in event_surveys.data" :key="event_survey.id">
                <b-card :title="event_survey.name" class="iq-mb-3">
                    <b-card-sub-title>
                        <div><small>{{ event_survey.surveys.length }} field{{event_survey.surveys.length === 1 ? '' : 's'}}</small></div>
                        <div><small>{{event_survey.access_levels.join(' | ')}}</small></div>
                    </b-card-sub-title>

                    <div class="d-flex justify-content-around mt-5" v-if="(userRole !== 'Operations' && userRole !== 'Viewers')">
                        <a href="#" :class="{'text-primary': !event_survey.status, 'text-danger': event_survey.status }" @click.prevent="visit(`/event/${event_id}/event-surveys/${event_survey.id}/status`, 'post')"><i
                            class="ri-key-2-line"></i> {{ event_survey.status ? 'Deactivate' : 'Activate' }} </a>
                        <a href="#" @click.prevent.stop="visit(`/event/${event_id}/event-surveys/${event_survey.id}/surveys`)" class="text-primary"><i class="ri-edit-line"></i>
                            {{ $t('button.edit') }}</a>
                    </div>
                    <div class="d-flex justify-content-around mt-5" v-else>
                        <span href="#" :class="{'badge': true, 'badge-primary': event_survey.status, 'badge-danger': !event_survey.status }"><i
                            class="ri-key-2-line"></i> {{ event_survey.status ? 'Activated' : 'Deactivated' }} </span>
                    </div>
                </b-card>
            </b-col>
        </b-row>

        <b-pagination v-if="event_surveys.data && event_surveys.data.length > 0" v-model="event_surveys.current_page" @change="onPaginate"
                      :total-rows="event_surveys.total" :per-page="event_surveys.per_page" align="center"/>
    </b-container>
</template>

<style>
.page-cards .card {
    border: 1px solid var(--iq-primary);
}

.card-body {
    box-shadow: 2px 4px 8px;
}
</style>
