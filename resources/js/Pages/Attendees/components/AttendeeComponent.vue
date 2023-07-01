<script setup>
import {router} from "@inertiajs/vue3";
import {ref, onUpdated} from "vue";

const props = defineProps({
    attendees: Object,
    eventId: String,
    sort: String
})

const selectedAttendee = ref({})
const answerModalShow = ref(false)

const sortEvents = () => {
    visit(`/attendees?sort=${selectedSort.value}`)
}

onUpdated(() => {
    answerModalShow.value = false
})

const visit = (link, method = 'get') => {
    if (method === 'get') {
        router.get(link);
    } else if (method === 'post') {
        router.post(link);
    } else {
        router.delete(link)
    }
}

const selectedSort = ref(props.sort || '');

const fields = ['access_level', 'event', 'ref', 'email', 'status', 'accept_status', 'date_submitted', 'action']

const answerFields = ['question', 'answers'];

const onPaginate = page => {
    props.eventId
        ? router.get(`/event/${props.eventId}/attendees?sort=${selectedSort.value}&page=${page}`)
        : router.get(`/attendees?sort=${selectedSort.value}&page=${page}`)
}

const approveAttendee = () => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/${selectedAttendee.value.id}/approve`)
        : router.post(`/attendees/${selectedAttendee.value.id}/approve`)
}
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <no-data v-if="!attendees.total" title="Attendees" link="#" :sub-text="false"/>

                <iq-card v-if="attendees.total">
                    <template v-slot:headerTitle>
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">{{ $t('sidebar.attendees') }}</h4>
                            <div class="form-group">
                                <select class="form-control" @change="sortEvents" v-model="selectedSort">
                                    <option value="">{{ $t('sort.title') }}</option>
                                    <option value="sort_by_creation">{{ $t('sort.creation_date') }}</option>
                                    <option value="accept_status">Accept Status</option>
                                    <option value="sort_by_ref">Ref</option>
                                </select>
                            </div>
                        </div>
                    </template>

                    <template v-slot:body>
                        <b-row class="mt-3">
                            <b-col sm="12" class="table-responsive">
                                <b-table :items="attendees.data" :fields="fields"
                                         class="table-responsive-sm table-borderless">
                                    <template #cell(access_level)="data">
                                        <span>
                                            {{
                                                data.item.access_level?.title
                                            }} {{
                                                data.item.access_level?.title_arabic ? `(${data.item.access_level?.title_arabic})` : ''
                                            }}
                                        </span>
                                    </template>

                                    <template #cell(event)="data">
                                        <Link :href="`/event/${data.item.event.id}/dashboard`">
                                            {{
                                                data.item.event?.title
                                            }} {{
                                                data.item.event?.title_arabic ? `(${data.item.event?.title_arabic})` : ''
                                            }}
                                        </Link>
                                    </template>

                                    <template #cell(email)="data">
                                        <a :href="`mailto:${data.item.email}`">{{ data.item.email }}</a>
                                    </template>
                                    <template #cell(action)="data">
                                      <span>
                                          <b-dropdown id="dropdown-right" right text="Actions" size="sm"
                                                      variant="primary">
                                            <b-dropdown-item v-b-modal.answer-modal
                                                             @click.prevent="selectedAttendee = data.item; answerModalShow = true">Answer</b-dropdown-item>
                                            <b-dropdown-item @click="message(data.item.id)">Message</b-dropdown-item>
                                            <b-dropdown-item>Reinstate</b-dropdown-item>
                                        </b-dropdown>
                                      </span>
                                    </template>
                                </b-table>

                                <div class="mt-5">
                                    <b-pagination v-if="attendees.data && attendees.data.length > 0"
                                                  v-model="attendees.current_page" @change="onPaginate"
                                                  class="mt-2"
                                                  :total-rows="attendees.total" :per-page="attendees.per_page"
                                                  align="center"/>

                                </div>
                            </b-col>
                        </b-row>
                    </template>
                </iq-card>
            </b-col>
        </b-row>

        <b-modal v-model="answerModalShow" id="answer-modal" size="xl" title="Answers">
            <b-row class="mt-3">
                <b-col sm="12" class="table-responsive">
                    <b-table :items="selectedAttendee.answers" :fields="answerFields"
                             class="table-responsive-sm table-borderless">
                        <template #cell(answers)="data">
                            <span v-if="data.item.type !== '4'">
                                {{ Array.isArray(data.item.answer) ? data.item.answer.join(', ') : data.item.answer }}
                            </span>
                            <a v-else-if="data.item.type === '4'" :href="data.item.answer" target="_blank">View File</a>
                        </template>
                    </b-table>
                </b-col>
            </b-row>

            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="danger"
                        class="float-right ml-2"
                        @click="answerModalShow = false"
                    >
                        Close
                    </b-button>
                    <b-button
                        variant="primary"
                        class="float-right ml-2"
                        @click="approveAttendee"
                        v-if="selectedAttendee.accept_status === 'not accepted'"
                    >
                        Approve
                    </b-button>
                    <span v-else class="badge badge-primary float-right ml-2">Approved</span>
                </div>
            </template>
        </b-modal>
    </b-container>
</template>
