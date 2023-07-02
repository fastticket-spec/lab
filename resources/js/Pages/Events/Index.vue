<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <no-data v-if="!events.total" title="Categories" link="/events/create"/>

                <iq-card v-if="events.total">
                    <template v-slot:headerTitle>
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">{{ $t('sidebar.events') }}</h4>
                            <div class="form-group">
                                <select class="form-control" @change="sortEvents" v-model="selectedSort">
                                    <option value="">{{$t('sort.title')}}</option>
                                    <option value="sort_by_creation">{{ $t('sort.creation_date') }}</option>
                                    <option value="sort_by_title">{{ $t('input.title') }}</option>
                                </select>
                            </div>
                        </div>
                    </template>
                </iq-card>
            </b-col>
        </b-row>

        <page-cards :events="events.data"/>

        <b-pagination v-if="events.data && events.data.length > 0" v-model="events.current_page" @change="onPaginate"
                      :total-rows="events.total" :per-page="events.per_page" align="center"/>
    </b-container>
</template>

<script setup>

import {router} from "@inertiajs/vue3";
import {ref} from "vue";
import PageCards from "./component/PageCards.vue";

defineProps({
    events: {}
})

const selectedSort = ref('');

const sortEvents = () => {
    visit(`/events?sort=${selectedSort.value}`)
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

const onPaginate = page => {
    router.get(`/events?sort=${selectedSort.value}&page=${page}`)
}
</script>
