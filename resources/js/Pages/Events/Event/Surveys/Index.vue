<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <Link v-if="!userRole || userRole === 'Admin Users'" :href="`/event/${event.id}/surveys/create`" class="btn btn-primary"><i class="ri-add-line"></i>
                    Add Survey
                </Link>
            </b-col>
        </b-row>

        <b-row class="mt-3">
            <b-col sm="12">

                <iq-card v-if="surveys.total">
                    <template v-slot:headerTitle>
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">{{ $t('sidebar.surveys') }}</h4>
                            <div class="form-group">
                                <select class="form-control" @change="sortSurveys" v-model="selectedSort">
                                    <option value="">Sort surveys by:</option>
                                    <option value="sort_by_creation">Creation Date</option>
                                    <option value="sort_by_title">Survey Title</option>
                                    <option value="sort_by_sold">Quantity Sold</option>
                                    <option value="sort_by_volume">Sales Volume</option>
                                </select>
                            </div>
                        </div>
                    </template>

                    <template v-slot:body>
                        <b-row class="mt-3">
                            <b-col sm="12" class="table-responsive">
                                <b-table :items="surveys.data" :fields="fields" class="table-responsive-sm table-borderless">
                                    <template #cell(action)="data">
                                        <span v-if="!userRole || userRole === 'Admin Users'">
                                            <b-button
                                                @click="visit(`/event/${event.id}/surveys/${data.item.id}`)"
                                                variant="outline-primary"
                                                class="mr-1"><i
                                                class="ri-edit-line"></i>Edit
                                            </b-button>

                                            <a href="#" @click.prevent.stop="" class="btn btn-outline-danger mr-1"
                                               :id="`popover-button-variants-deactivate-${data.item.id}`">
                                                <i :class="{'ri-play-line': data.item.status, 'ri-pause-line': !data.item.status}"/>
                                                &nbsp; {{ data.item.status === 'Disabled' ? 'Activate' : 'Deactivate' }}
                                            </a>
                                            <b-popover :target="`popover-button-variants-deactivate-${data.item.id}`"
                                                       variant="default" triggers="focus">
                                                <p><b>Are you sure you want to {{
                                                        data.item.status === 'Disabled' ? 'activate' : 'deactivate'
                                                    }} this survey?</b></p>
                                                <b-btn variant="outline-danger"
                                                       @click.prevent.stop="visit(`/event/${event.id}/surveys/${data.item.id}/toggle-status`, 'post', {survey_id: data.item.id, event_id: props.event.id})">Yes, {{
                                                        data.item.status === 'Disabled' ? 'Activate' : 'Deactivate'
                                                    }}
                                                </b-btn>
                                            </b-popover>

                                            <a href="#" @click.prevent.stop="" class="btn btn-outline-danger mr-1"
                                               :id="`popover-button-variants-delete-${data.item.id}`">
                                                <i class="ri-delete-bin-2-line"></i> Delete Question
                                            </a>

                                            <b-popover :target="`popover-button-variants-delete-${data.item.id}`"
                                                       variant="default" triggers="focus">
                                                <p><b>Are you sure you want to delete this question?</b></p>
                                                <b-btn variant="outline-danger"
                                                       @click.prevent.stop="visit(`/event/${event.id}/surveys/${data.item.id}`, 'delete')">Yes, Delete</b-btn>
                                            </b-popover>
                                        </span>
                                    </template>
                                </b-table>

                                <b-pagination v-if="surveys.data && surveys.data.length > 0" v-model="surveys.current_page" @change="onPaginate"
                                              class="mt-2"
                                              :total-rows="surveys.total" :per-page="surveys.per_page" align="right"/>
                            </b-col>
                        </b-row>
                    </template>
                </iq-card>
            </b-col>
        </b-row>


        <no-data v-if="!surveys.total" title="Surveys" :link="`/event/${event.id}/surveys/create`"/>

    </b-container>
</template>

<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";

const props = defineProps({
    surveys: {},
    event: {}
})

const fields = ['title', 'required', 'post_order', 'status', 'no_of_responses', 'action'];

const userRole = computed(() => usePage().props.user_role);

const selectedSort = ref('');

const sortSurveys = () => {
    visit(`/event/${props.event.id}/surveys?sort=${selectedSort.value}`)
}

const visit = (link, method = 'get', data = {}) => {
    if (method === 'get') {
        router.get(link);
    } else if (method === 'post') {
        router.post(link, data);
    } else {
        router.delete(link)
    }
}

const onPaginate = page => {
    router.get(`/event/${props.event.id}/surveys?sort=${selectedSort.value}&page=${page}`)
}
</script>
