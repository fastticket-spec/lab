<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {ref, onUpdated, reactive, computed} from "vue";
import SearchBox from '../../../Shared/components/core/SearchBox/Index.vue';
import axios from "axios";
import * as XLSX from "xlsx";

const props = defineProps({
    attendees: Object,
    eventId: String,
    sort: String,
    zones: Array,
    q: String,
    accessLevels: Array,
    errors: Object
})

const selectedAttendee = ref({})
const uploadModalShow = ref(false)
const answerModalShow = ref(false)
const messageModalShow = ref(false)
const zonesModalShow = ref(false)
const zonesForBulk = ref(false)
const selectedSort = ref(props.sort || '');
const message = reactive({
    subject: '',
    content: ''
})
const selectedZones = ref([]);

const userRole = computed(() => usePage().props.user_role);

const fields = [(userRole.value !== 'Viewers' && 'check'), 'access_level', 'category', 'ref', 'email', 'status', 'accept_status', 'date_submitted', (userRole.value !== 'Viewers' && 'action')]

const answerFields = ['question', 'answers', '⠀'];

const checkedRows = ref([]);

const upload = reactive({
    access_level_id: '',
    approve: false
});

const searchAttendees = searchString => {
    goTo(props.attendees.current_page, props.attendees.per_page, searchString);
}

const goTo = (page, perPage, q) => {
    router.get(`/attendees?q=${q || ''}&page=${page}&per_page=${perPage}`, {}, {
        replace: true,
        preserveState: true
    })
}

const sortEvents = () => {
    visit(`/attendees?sort=${selectedSort.value}`)
}

onUpdated(() => {
    answerModalShow.value = false;
    zonesModalShow.value = false;
    messageModalShow.value = false;
    if (Object.keys(props.errors).length === 0) {
        uploadModalShow.value = false;
    }
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

const onPaginate = page => {
    props.eventId
        ? router.get(`/event/${props.eventId}/attendees?sort=${selectedSort.value}&page=${page}`)
        : router.get(`/attendees?sort=${selectedSort.value}&page=${page}`)
}

const approveAttendee = () => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/${selectedAttendee.value.id}/approval/1`)
        : router.post(`/attendees/${selectedAttendee.value.id}/approval/1`)
}

const declineAttendee = (attendeeId) => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/${attendeeId}/approval/2`)
        : router.post(`/attendees/${attendeeId}/approval/2`)
}

const reinstateAttendee = (attendeeId) => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/${attendeeId}/approval/0`)
        : router.post(`/attendees/${attendeeId}/approval/0`)
}

const downloadBadge = (attendeeId, badgeId) => {
    props.eventId
        ? router.get(`/event/${props.eventId}/attendees/${attendeeId}/download-badge/${badgeId}?type=full`)
        : router.get(`/attendees/${attendeeId}/download-badge/${badgeId}?type=full`)
}

const onSubmitMessage = () => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/${selectedAttendee.value.id}/send-message`, message)
        : router.post(`/attendees/${selectedAttendee.value.id}/send-message`, message)
};

const eventZones = computed(() => {
    if (selectedAttendee.value) {
        return props.zones.filter(zone => zone.event_id === selectedAttendee.value.category.id)
    }

    if (checkedRows.value.length > 0 && props.eventId) {
        return props.zones.filter(zone => zone.event_id === props.eventId)
    }

    return props.zones;
});

const onAssignZones = () => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/${selectedAttendee.value.id}/assign-zones`, {zones: selectedZones.value})
        : router.post(`/attendees/${selectedAttendee.value.id}/assign-zones`, {zones: selectedZones.value})
}

const approveAttendees = () => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/bulk-approval/1`, {attendee_ids: checkedRows.value})
        : router.post(`/attendees/bulk-approval/1`, {attendee_ids: checkedRows.value})
}
const declineAttendees = () => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/bulk-approval/2`, {attendee_ids: checkedRows.value})
        : router.post(`/attendees/bulk-approval/2`, {attendee_ids: checkedRows.value})
}
const assignZonesToAttendees = () => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/bulk-assign-zones`, {
            attendee_ids: checkedRows.value,
            zones: selectedZones.value
        })
        : router.post(`/attendees/bulk-assign-zones`, {attendee_ids: checkedRows.value, zones: selectedZones.value})
}

const showEdit = answer => {
    selectedAttendee.value.answers = selectedAttendee.value.answers.map(x => ({
        ...x,
        edit: false,
        answer: x.prev_answer || x.answer
    }))
    const currentAnswerIndex = selectedAttendee.value.answers.findIndex(x => (x.question === answer.question && x.type === answer.type));

    if (currentAnswerIndex !== '-1') {
        let selectedAnswer = selectedAttendee.value.answers[currentAnswerIndex];

        selectedAnswer.edit = true;
        selectedAnswer.prev_answer = selectedAnswer.answer;
    }
}

const saveAnswer = async answerIndex => {
    try {
        const {data} = await axios.post(`/attendees/${selectedAttendee.value.id}/update-answers`, {
            answers: selectedAttendee.value.answers
        })

        console.log(data);

        selectedAttendee.value.answers[answerIndex].edit = false;
        // answerModalShow.value = false;
    } catch (e) {
        console.log(e);
    }

}

const cancelAnswer = (answerIndex) => {
    let selectedAnswer = selectedAttendee.value.answers[answerIndex];
    selectedAnswer.edit = true;
    selectedAnswer.answer = selectedAnswer.prev_answer || selectedAnswer.answer;
    selectedAnswer.edit = false;
}

const uploadedAttendees = ref([]);

const onUploadFile = e => {
    const file = e.target.files ? e.target.files[0] : null;
    if (file) {
        const reader = new FileReader();

        reader.onload = (e) => {
            const bstr = e.target.result;
            const wb = XLSX.read(bstr, {type: 'binary'});
            const wsName = wb.SheetNames[0];
            const ws = wb.Sheets[wsName];
            let data = XLSX.utils.sheet_to_json(ws, {header: 1});
            data.splice(0, 1);
            uploadedAttendees.value = data.map(x => {
                return {first_name: x[0], 'last_name': x[1], 'email': x[2]}
            })
        }

        reader.readAsBinaryString(file);
    }
}

const onUploadAttendees = () => {
    router.post(`/event/${props.eventId}/attendees/upload-attendees`, {
        attendees: uploadedAttendees.value,
        access_level_id: upload.access_level_id,
        approve: upload.approve
    })
}
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <no-data v-if="!attendees.total && !q" title="Attendees" link="#" :sub-text="false"/>

                <iq-card v-if="attendees.total || (!attendees.total && q)">
                    <template v-slot:headerTitle>
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">{{ $t('sidebar.attendees') }}</h4>
                        </div>
                    </template>

                    <template v-slot:headerAction>
                        <search-box
                            placeholder="Search by email, ref, event"
                            :on-search="searchAttendees"
                            :default-value="q"
                        />

                        <div class="form-group ml-4 mt-2">
                            <select class="form-control" @change="sortEvents" v-model="selectedSort">
                                <option value="">{{ $t('sort.title') }}</option>
                                <option value="sort_by_creation">{{ $t('sort.creation_date') }}</option>
                                <option value="status">Status</option>
                                <option value="sort_by_ref">Ref</option>
                            </select>
                        </div>
                    </template>

                    <template v-slot:body>
                        <b-row v-if="eventId && userRole !== 'Viewers'">
                            <b-col sm="12">
                                <a href="#" @click="uploadModalShow = true" class="btn btn-outline-primary"><i
                                    class="ri-upload-2-line"></i>Upload Attendees</a>
                            </b-col>
                        </b-row>

                        <b-row class="mt-3">
                            <b-col sm="12" class="mb-3" v-show="checkedRows.length > 0">
                                <b-btn @click="approveAttendees"
                                       variant="outline-primary" class="mr-2">Approve attendee{{
                                        checkedRows.length !== 1 ? 's' : ''
                                    }}
                                </b-btn>

                                <b-btn @click="declineAttendees"
                                       variant="outline-danger" class="mr-2">Decline attendee{{
                                        checkedRows.length !== 1 ? 's' : ''
                                    }}
                                </b-btn>

                                <b-btn @click="zonesModalShow = true; zonesForBulk = true; selectedAttendee = null"
                                       variant="outline-primary" class="mr-2">Assign zones to attendee{{
                                        checkedRows.length !== 1 ? 's' : ''
                                    }}
                                </b-btn>
                            </b-col>

                            <b-col sm="12" class="table-responsive">
                                <b-table :items="attendees.data" :fields="fields"
                                         class="table-responsive-sm table-borderless">
                                    <template #cell(check)="data">
                                        <b-form-checkbox v-model="checkedRows" :value="data.item.id" inline/>
                                    </template>
                                    <template #cell(access_level)="data">
                                        <span>
                                            {{
                                                data.item.access_level?.title
                                            }} {{
                                                data.item.access_level?.title_arabic ? `(${data.item.access_level?.title_arabic})` : ''
                                            }}
                                        </span>
                                    </template>

                                    <template #cell(category)="data">
                                        <Link :href="`/event/${data.item.category.id}/dashboard`">
                                            {{
                                                data.item.category?.title
                                            }} {{
                                                data.item.category?.title_arabic ? `(${data.item.category?.title_arabic})` : ''
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
                                            <b-dropdown-item
                                                @click.prevent="selectedAttendee = data.item; answerModalShow = true">Answer</b-dropdown-item>
                                            <b-dropdown-item
                                                @click.prevent="selectedAttendee = data.item; messageModalShow = true">Message</b-dropdown-item>
                                            <b-dropdown-item v-if="data.item.status !== 'declined'"
                                                             @click.prevent="declineAttendee(data.item.id)">Decline</b-dropdown-item>
                                            <b-dropdown-item v-if="data.item.status === 'declined'"
                                                             @click.prevent="reinstateAttendee(data.item.id)">Reinstate</b-dropdown-item>
                                            <b-dropdown-item
                                                @click.prevent="selectedAttendee = data.item; checkedRows = []; zonesModalShow = true; zonesForBulk = false; selectedZones = (data.item.zones || [])">Assign Zones</b-dropdown-item>
                                            <b-dropdown-item
                                                v-if="data.item.badge"
                                                @click.prevent="downloadBadge(data.item.id, data.item.badge.id)">Download Badge</b-dropdown-item>
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
                            <div v-if="data.item.edit">
                                <input v-model="data.item.answer"
                                       :type="data.item.type === '3' ? 'datetime-local' : 'text'">
                                <b-btn @click="saveAnswer(data.index)" class="ml-2" size="sm"
                                       variant="primary"><i
                                    class="ri-save-2-line p-0"></i></b-btn>
                                <b-btn @click="cancelAnswer(data.index)" class="ml-2" size="sm"
                                       variant="danger"><i
                                    class="ri-close-line p-0"></i></b-btn>
                            </div>

                            <template v-else>
                                <span v-if="data.item.type !== '4'">
                                    {{
                                        Array.isArray(data.item.answer) ? data.item.answer.join(', ') : data.item.answer
                                    }}
                                </span>
                                <a v-else-if="data.item.type === '4'" :href="data.item.answer" target="_blank">View
                                    File</a>
                            </template>
                        </template>
                        <template #cell(⠀)="data">
                            <b-btn v-if="data.item.type !== '10' && !data.item.edit" @click="showEdit(data.item)"
                                   size="sm" variant="primary"><i
                                class="ri-edit-2-line p-0"></i></b-btn>
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
                        v-if="selectedAttendee.status !== 'approved'"
                    >
                        Approve
                    </b-button>
                    <span v-else-if="selectedAttendee.status === 'approved'"
                          class="badge badge-primary float-right ml-2">Approved</span>
                    <span v-else-if="selectedAttendee.status === 'declined'"
                          class="badge badge-danger float-right ml-2">Declined</span>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="messageModalShow" id="message-modal" title="Message Attendee">
            <b-row class="mt-3">
                <b-col sm="12">
                    <div class="form-group">
                        <label for="subject">Message Subject</label>
                        <input type="text" v-model="message.subject"
                               :class="`form-control mb-0`"
                               id="subject"/>
                    </div>

                    <div class="form-group">
                        <label for="content">Message Content</label>
                        <textarea v-model="message.content"
                                  rows="5"
                                  :class="`form-control mb-0`"
                                  id="content"/>
                    </div>

                    <span>The attendee will be instructed to send any reply to media@thesaudicup.com.sa</span>
                </b-col>
            </b-row>

            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="primary"
                        @click="onSubmitMessage"
                        :disabled="!message.content || !message.subject"
                        class="btn btn-primary float-right ml-2">Send Message
                    </b-button>
                    <b-button
                        type="button"
                        variant="danger"
                        class="float-right ml-2"
                        @click="messageModalShow = false"
                    >
                        Close
                    </b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="zonesModalShow" id="zones-modal" title="Assign Zones">
            <b-row class="mt-3">
                <b-col sm="6" v-for="zone in eventZones" :key="zone.id">
                    <div class="form-group">
                        <b-checkbox v-model="selectedZones" :value="zone.id">{{ zone.zone }}</b-checkbox>
                    </div>
                </b-col>
            </b-row>

            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="primary"
                        @click="(checkedRows.length > 0 && !selectedAttendee) ? assignZonesToAttendees() : onAssignZones()"
                        :disabled="selectedZones.length === 0"
                        class="btn btn-primary float-right ml-2">Assign Zones
                    </b-button>
                    <b-button
                        type="button"
                        variant="danger"
                        class="float-right ml-2"
                        @click="zonesModalShow = false"
                    >
                        Close
                    </b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="uploadModalShow" id="upload-modal" title="Upload Attendees">
            <b-row class="mt-3">
                <b-col sm="12">
                    <div class="form-group">
                        <label for="access-level">Access Level</label>
                        <select class="form-control" v-model="upload.access_level_id" id="access-level">
                            <option value="">Select Access level</option>
                            <option v-for="level in accessLevels" :key="level.id" :value="level.id">{{ level.title }}
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="upload-content">Upload File (.xlsx, .xls, .csv)</label>
                        <input type="file" class="form-control" @change="onUploadFile" id="upload-content"
                               accept=".xlsx, .xls, .csv">
                        <span class="text-danger" v-if="Object.keys(errors).length > 0">Ensure all fields are filled correctly.</span>
                    </div>

                    <div class="form-group">
                        <b-checkbox v-model="upload.approve" class="custom-checkbox-color"
                                    name="approve-check" inline>
                            Approve Attendees
                        </b-checkbox>
                    </div>
                </b-col>
            </b-row>

            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="primary"
                        @click="onUploadAttendees"
                        :disabled="uploadedAttendees.length === 0 || !upload.access_level_id"
                        class="btn btn-primary float-right ml-2">Upload
                    </b-button>
                    <b-button
                        type="button"
                        variant="danger"
                        class="float-right ml-2"
                        @click="uploadModalShow = false"
                    >
                        Close
                    </b-button>
                </div>
            </template>
        </b-modal>

    </b-container>
</template>
