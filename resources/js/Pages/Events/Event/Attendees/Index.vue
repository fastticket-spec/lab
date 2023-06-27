<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <no-data v-if="!attendees.total" title="Attendees" :sub-text="false" link="/attendees/create"/>

                <iq-card v-if="attendees.total">
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ $t('sidebar.attendees') }}</h4>
                    </template>

                    <template v-slot:headerAction>
                        <search-box
                            placeholder="Search Attendees by ticket, ticket ref or order ref"
                            :on-search="searchAttendees"
                            :default-value="q"
                        />
                    </template>

                    <template v-slot:body>
                        <b-row class="mt-3">
                            <b-col sm="12" class="mb-3">
                                <b-btn v-show="checkedRows.length > 0" @click="downloadMultiTickets"
                                       variant="outline-primary" class="mr-2">
                                    <i class="ri-save-2-line"></i> Download Ticket{{
                                        checkedRows.length !== 1 ? 's' : ''
                                    }}
                                    for selected attendees
                                </b-btn>
                            </b-col>

                            <b-col sm="12" class="table-responsive">
                                <b-table :items="attendees.data" :fields="fields"
                                         class="table-responsive-sm table-borderless">
                                    <template #cell(check)="data">
                                        <b-form-checkbox v-model="checkedRows" :value="data.item.id" inline/>
                                    </template>

                                    <template #cell(contact)="data">
                                        <span>
                                            <i class="ri-user-2-line"></i> {{ data.item.owner_name }} <br/>
                                            <a :href="`mailto:${data.item.email}`"><i
                                                class="ri-mail-line"></i> {{ data.item.email }}</a> <br/>
                                            <a :href="`tel:${data.item.country_code}${data.item.phone}`"><i
                                                class="ri-phone-line"></i> ({{
                                                    data.item.country_code
                                                }}) {{ data.item.phone }}</a>
                                        </span>
                                    </template>

                                    <template #cell(order_reference)="data">
                                        <Link :href="`/event/${event_id}/orders/${data.item.order_id}`">
                                            {{ data.item.order_reference }}
                                        </Link>
                                    </template>

                                    <template #cell(status)="data">
                                        <span :class="{'badge badge-danger': data.item.status === 'cancelled'}">
                                            {{ data.item.status === 'cancelled' ? 'Cancelled' : '' }}
                                        </span>
                                    </template>

                                    <template #cell(action)="data">
                                        <b-dropdown id="dropdown-right" right text="Actions" size="sm"
                                                    variant="primary" v-if="data.item.status !== 'cancelled'">
                                            <Link :href="`/event/${event_id}/attendees/${data.item.id}/edit`"
                                                  class="dropdown-item">Edit
                                            </Link>
                                            <b-dropdown-item @click="sendTicket(data.item)">Resend
                                                Ticket
                                            </b-dropdown-item>

                                            <b-dropdown-item @click="downloadSingleTicket(data.item.id)"
                                                             variant="outline-primary">Download Ticket
                                            </b-dropdown-item>

                                            <b-dropdown-item v-b-modal.modal-1
                                                             @click="surveyAnswers = data.item.survey_answers"
                                                             variant="outline-primary">Survey Answers
                                            </b-dropdown-item>

                                            <b-dropdown-item @click="cancelTicket(data.item.id)"
                                                             variant="outline-primary">Cancel Ticket
                                            </b-dropdown-item>
                                        </b-dropdown>
                                    </template>
                                </b-table>

                                <b-pagination v-if="attendees.data && attendees.data.length > 0"
                                              v-model="attendees.current_page" @change="onPaginate"
                                              :total-rows="attendees.total" :per-page="attendees.per_page"
                                              class="mt-2"
                                              align="right"/>
                            </b-col>
                        </b-row>
                    </template>
                </iq-card>
            </b-col>
        </b-row>
    </b-container>

    <b-modal id="modal-1" title="Survey Answers">
        <div class="order-details full" v-if="surveyAnswers.length === 0">
            <div class="order-detail-item full mb-2">
                No survey answers.
            </div>
        </div>

        <div class="order-details full">
            <div class="order-detail-item full mb-2" v-for="(survey, i) in surveyAnswers" :key="`survey-${i}`">
                <h6 class="title">{{ survey.question }} </h6>
                <template
                    v-if="['textbox', 'textarea', 'single_select', 'radio', 'multi_select', 'checkbox'].includes(survey.type)">
                    <span class="answer" v-if="typeof survey.answer === 'string'">{{ survey.answer }}</span>

                    <template v-else>
                        <span class="answer" v-for="answer in survey.answer">- {{ answer }}</span>
                    </template>
                </template>
                <template v-if="survey.type === 'file'">
                    <a :href="survey.answer" target="_blank" class="answer">- {{ survey.answer }}</a>
                </template>
            </div>
        </div>
    </b-modal>
</template>

<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import SearchBox from "../../../../Shared/components/core/SearchBox/Index.vue";

const props = defineProps({
    attendees: {},
    event_id: String,
    q: String
})

const fields = ['check', 'contact', 'ticket_reference', {ticket_title: 'ticket'}, 'order_reference', 'event_title', 'attend_date', 'status', {'download_count': 'downloads'}, 'action'];

const userRole = computed(() => usePage().props.user_role);

const checkedRows = ref([]);

const surveyAnswers = ref([]);

const searchAttendees = searchString => {
    goTo(props.attendees.current_page, props.attendees.per_page, searchString)
}

const onPaginate = page => {
    goTo(page, 10, props.q)
}

const goTo = (page, perPage, q) => {
    router.get(`/event/${props.event_id}/attendees?q=${q || ''}&page=${page}&per_page=${perPage}`, {}, {
        replace: true,
        preserveState: true
    })
}

const visit = (link, method = 'get', data = {}) => {
    if (method === 'get') {
        router.get(link);
    } else {
        router.delete(link, data);
    }
}

const sendTicket = data => {
    const url = `/event/${props.event_id}/attendees/${data.id}/send-ticket`;

    router.post(url, {
        ticket_reference: data.ticket_reference,
        email: data.email,
        ticket_id: data.ticket_id,
        attend_date: data.attend_date,
        start_time: data.time_slot?.start_time,
        end_time: data.time_slot?.end_time,
        order_id: data.order_id
    });
}

const downloadSingleTicket = id => {
    const attendeeIds = [id];

    location.href = `/event/${props.event_id}/attendees/download-multi-tickets?attendee_ids=${attendeeIds.join(',')}`;
}

const cancelTicket = attendeeId => {
    router.patch(`/event/${props.event_id}/attendees/${attendeeId}/cancel`);
}

const downloadMultiTickets = async () => {
    const attendeeIds = Object.values(checkedRows.value);

    location.href = `/event/${props.event_id}/attendees/download-multi-tickets?attendee_ids=${attendeeIds.join(',')}`;
}
</script>
