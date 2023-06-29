<script setup>
import {ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    access_levels: {},
    event_id: String
})

const selectedSort = ref('');

const sortAccessLevels = () => {
    visit(`/event/${props.event_id}/access-levels?sort=${selectedSort.value}`)
}

const visit = (link, method = 'get') => {
    if (method === 'get') {
        router.get(link);
    } else if (method === 'post') {
        router.post(link);
    } else {
        router.delete(link)
    }
}

</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <no-data v-if="!access_levels.total" title="Access Levels"
                         :link="`/event/${event_id}/access-levels/create`"/>

                <a :href="`/event/${event_id}/access-levels/create`" class="btn btn-primary mb-3">Create Access Level</a>

                <iq-card v-if="access_levels.total">
                    <template v-slot:headerTitle>
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">{{ $t('sidebar.access_levels') }}</h4>
                            <div class="form-group">
                                <select class="form-control" @change="sortAccessLevels" v-model="selectedSort">
                                    <option value="">{{ $t('sort.title') }}</option>
                                    <option value="sort_by_creation">{{ $t('sort.creation_date') }}</option>
                                    <option value="sort_by_title">{{ $t('input.title') }}</option>
                                </select>
                            </div>
                        </div>
                    </template>
                </iq-card>
            </b-col>
        </b-row>

        <b-row class="page-cards">
            <b-col sm="6" v-for="access_level in access_levels.data" :key="access_level.id">
                <b-card :title="access_level.title" class="iq-mb-3">

                    <b-card-text class="d-flex w-100 justify-content-around my-5">
                        <div class="text-center">
                            <h5>{{ access_level.quantity_filled || 0 }}</h5>
                            <span>Registered</span>
                        </div>

                        <div class="text-center">
                            <h5>{{ access_level.quantity_available ? (access_level.quantity_available - access_level.quantity_filled) : 'Unlimited'}}</h5>
                            <span>Remaining</span>
                        </div>
                    </b-card-text>

                    <div v-if="access_level.has_surveys" class="card-date d-flex flex-column text-center" :class="{'card-date-ar': locale === 'ar'}">
                        <a :href="`/e/${event_id}/a/${access_level.id}`" target="_blank">View Form</a>
                    </div>

                    <div class="d-flex justify-content-around">
                        <a href="#" @click.prevent.stop="visit(`/event/${event_id}/access-levels/${access_level.id}/edit`)"><i
                            class="ri-edit-line"></i>
                            Edit</a>
                        <a href="#"
                           @click.prevent.stop="visit(`/event/${event_id}/access-levels/${access_level.id}/change-status`, 'post')"
                           :class="access_level.status === 0 ? 'text-success' : 'text-danger'"><i
                            :class="access_level.status === 0 ? 'ri-play-line' : 'ri-pause-line'"></i>
                            {{ access_level.status === 0 ? 'Activate' : 'Pause' }}</a>
                    </div>
                </b-card>
            </b-col>
        </b-row>

        <b-pagination v-if="access_levels.data && access_levels.data.length > 0" v-model="access_levels.current_page" @change="onPaginate"
                      :total-rows="access_levels.total" :per-page="access_levels.per_page" align="center"/>
    </b-container>
</template>

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
