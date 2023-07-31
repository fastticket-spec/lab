<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {ref, onUpdated, reactive, computed, watch} from "vue";
import SearchBox from '../../../Shared/components/core/SearchBox/Index.vue';
import axios from "axios";
import * as XLSX from "xlsx";

const props = defineProps({
    attendees: Object,
    eventId: String,
    sort: String,
    filter_by: String,
    zones: Array,
    areas: Array,
    q: String,
    accessLevels: Array,
    errors: Object
})

const selectedAttendee = ref({})
const uploadModalShow = ref(false)
const exportModalShow = ref(false)
const answerModalShow = ref(false)
const messageModalShow = ref(false)
const badgeModalShow = ref(false)
const collectedModalShow = ref(false)
const zonesModalShow = ref(false)
const zonesForBulk = ref(false)
const moveAttendeeModal = ref(false)
const areasModalShow = ref(false)
const areasForBulk = ref(false)
const selectedSort = ref(props.sort || '');
const selectedFilter = ref(props.filter_by || '');
const message = reactive({
    subject: '',
    content: ''
})
const selectedZones = ref([]);
const selectedAreas = ref([]);
const badgeData = ref(null)

watch(badgeData, (val) => {
    if (val) {
        setTimeout(() => {
            let collection = document.querySelectorAll("#badgeContainer p");

            for (let i = 0; i < collection.length; i++) {
                collection[i].innerHTML = collection[i].innerHTML.replace('&nbsp;', ' ');
                collection[i].style.wordWrap = 'normal';
                collection[i].style.letterSpacing = 'normal';
            }
        }, 500)
    }
})

const userRole = computed(() => usePage().props.user_role);

const fields = [(userRole.value !== 'Viewers' && 'check'), 'access_level', 'category', 'ref', 'info', 'downloads', 'status', 'regulation_video', 'date_submitted', (userRole.value !== 'Viewers' && 'action')]

const answerFields = ['question', 'answers', '⠀'];

const checkedRows = ref([]);

const upload = reactive({
    access_level_id: '',
    approve: false
});

const exportData = reactive({
    access_level_id: '',
    message: ''
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
    visit(props.eventId ? `/event/${props.eventId}/attendees?sort=${selectedSort.value}` : `/attendees?sort=${selectedSort.value}`)
}

const filterEvents = () => {
    visit(props.eventId ? `/event/${props.eventId}/attendees?filter=${selectedFilter.value}` : `/attendees?filter=${selectedFilter.value}`)
}

onUpdated(() => {
    answerModalShow.value = false;
    zonesModalShow.value = false;
    areasModalShow.value = false;
    messageModalShow.value = false;
    moveAttendeeModal.value = false;
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

const viewBadge = async (attendeeId, badgeId, status) => {
    selectedAttendee.value = attendeeId
    try {
        const {data} = await axios.get(`/attendees/${attendeeId}/download-badge/${badgeId}?type=full`);

        badgeData.value = data;
        badgeData.value['status'] = status;
        badgeModalShow.value = true;
    } catch (e) {
        console.log(e);
    }
}

const downloadBadge = () => {
    try {
        let source = document.getElementById('badgeContainer')
        source.classList.remove("container");

        html2canvas(source, {useCORS: true, allowTaint: true, scale: 5}).then(async canvas => {
            const imgWidth = badgeData.value.badge.width;
            const imgHeight = badgeData.value.badge.height

            const contentDataURL = canvas.toDataURL('image/jpeg')

            document.body.appendChild(canvas);
            const {jsPDF} = window.jspdf;

            let pdf = new jsPDF('p', 'cm', [imgWidth, imgHeight]); // A4 size page of PDF
            pdf.addImage(contentDataURL, 'JPEG', 0, 0, imgWidth, imgHeight)

            pdf.save('MYPdf.pdf');

            await axios.post(`/attendees/${selectedAttendee.value}/download-badge-increment`)

            const attendeeIndex = props.attendees.data.findIndex(x => x.id === selectedAttendee.value);

            if (attendeeIndex >= 0) {
                props.attendees.data[attendeeIndex].downloads = props.attendees.data[attendeeIndex].downloads + 1
            }
        });
    } catch (e) {
        console.log(e);
    }
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

const eventAreas = computed(() => {
    if (selectedAttendee.value) {
        return props.areas.filter(area => area.event_id === selectedAttendee.value.category.id)
    }

    if (checkedRows.value.length > 0 && props.eventId) {
        return props.areas.filter(area => area.event_id === props.eventId)
    }

    return props.areas;
});

const onAssignZones = () => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/${selectedAttendee.value.id}/assign-zones`, {zones: selectedZones.value})
        : router.post(`/attendees/${selectedAttendee.value.id}/assign-zones`, {zones: selectedZones.value})
}

const onAssignAreas = () => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/${selectedAttendee.value.id}/assign-areas`, {areas: selectedAreas.value})
        : router.post(`/attendees/${selectedAttendee.value.id}/assign-areas`, {areas: selectedAreas.value})
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
const assignAreasToAttendees = () => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/bulk-assign-areas`, {
            attendee_ids: checkedRows.value,
            areas: selectedAreas.value
        })
        : router.post(`/attendees/bulk-assign-areas`, {attendee_ids: checkedRows.value, areas: selectedAreas.value})
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
        await axios.post(`/attendees/${selectedAttendee.value.id}/update-answers`, {
            answers: selectedAttendee.value.answers
        })

        selectedAttendee.value.answers[answerIndex].edit = false;
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

const markAsPrinted = (printed = true) => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/mark-as-printed`, {attendee_ids: checkedRows.value, printed})
        : router.post(`/attendees/mark-as-printed`, {attendee_ids: checkedRows.value, printed})
}

const markAsCollected = (collected = true) => {
    props.eventId
        ? router.post(`/event/${props.eventId}/attendees/mark-as-collected`, {attendee_ids: checkedRows.value, collected})
        : router.post(`/attendees/mark-as-collected`, {attendee_ids: checkedRows.value, collected})
}

const markBadgeAsPrinted = async () => {
    try {
        await axios.post(`/attendees/mark-as-printed`, {attendee_ids: [selectedAttendee.value], printed: true})
        badgeData.value.downloaded = 1;

        const attendeeIndex = props.attendees.data.findIndex(x => x.id === selectedAttendee.value);

        if (attendeeIndex >= 0) {
            props.attendees.data[attendeeIndex].printed = 1
        }
    } catch (e) {
        console.log(e);
    }
}

const markBadgeAsCollected = async () => {
    try {
        await axios.post(`/attendees/mark-as-collected`, {attendee_ids: [selectedAttendee.value], collected: true})
        badgeData.value.collected = 1;

        const attendeeIndex = props.attendees.data.findIndex(x => x.id === selectedAttendee.value);

        if (attendeeIndex >= 0) {
            props.attendees.data[attendeeIndex].collected = 1
        }

        collectedModalShow.value = false;
    } catch (e) {
        console.log(e);
    }
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
            uploadedAttendees.value = XLSX.utils.sheet_to_json(ws);
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

const onExportTemplate = async () => {
    exportData.message = '';

    try {
        let {data: {surveys}} = await axios.get(`/event/${props.eventId}/access-levels/${exportData.access_level_id}/surveys`)
        if (!surveys) {
            exportData.message = 'There are no surveys for selected access level!';
            setTimeout(() => {exportData.message = ''}, 3000);
            return;
        }

        surveys = surveys.filter(x => x.type !== '10').map(x => x.title);
        const worksheet = XLSX.utils.json_to_sheet([]);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Survey Template");
        XLSX.utils.sheet_add_aoa(worksheet, [surveys], { origin: "A1" });

        XLSX.writeFile(workbook, "template.xlsx", { compression: true });
        exportModalShow.value = false;
    } catch (e) {
        console.log(e);
    }
}

const moveToAccessLevelId = ref('');
const selectedAttendeeAccessLevelId = ref('');

const otherAccessLevels = computed(() => excludeAccessLevelId => {
    return props.accessLevels.filter(x => x.id !== excludeAccessLevelId);
})

const moveToAccessLevel = () => {
    router.post(`/event/${props.eventId}/attendees/${selectedAttendee.value.id}/change-access-level`, {
        access_level_id: moveToAccessLevelId.value
    });
}
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card v-if="attendees.total || (!attendees.total && q) || (!attendees.total && filter_by)">
                    <template v-slot:headerTitle>
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">{{ $t('sidebar.attendees') }}</h4>
                        </div>
                    </template>

                    <template v-slot:headerAction>
                        <div class="d-flex justify-content-center align-items-center" style="width: 670px">
                            <search-box
                                placeholder="Search by first name, last name, email, ref or event"
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

                            <div class="form-group ml-4 mt-2">
                                <select class="form-control" @change="filterEvents" v-model="selectedFilter">
                                    <option value="">Filter by</option>
                                    <option value="1">Approved</option>
                                    <option value="0">Pending</option>
                                    <option value="2">Declined</option>
                                </select>
                            </div>
                        </div>
                    </template>

                    <template v-slot:body v-if="attendees.total">
                        <b-row v-if="eventId && userRole !== 'Viewers' && userRole !== 'Operations'">
                            <b-col sm="12">
                                <a href="#" @click="uploadModalShow = true" class="btn btn-outline-primary"><i
                                    class="ri-upload-2-line"></i>Upload Attendees</a>
                                <a href="#" @click="exportModalShow = true" class="btn btn-outline-primary ml-2"><i
                                    class="ri-upload-2-line"></i>Export Template</a>
                                <Link :href="`/event/${eventId}/attendees/register-applicant`" class="btn btn-outline-primary ml-2"><i
                                    class="ri-user-3-line"></i>Register Applicant</Link>
                            </b-col>
                        </b-row>

                        <b-row class="mt-3">
                            <b-col sm="12" class="mb-3" v-show="checkedRows.length > 0">
                                <b-btn @click="approveAttendees"
                                       variant="outline-primary" class="mr-2">Approve attendee{{
                                        checkedRows.length !== 1 ? 's' : ''
                                    }}
                                </b-btn>

                                <b-btn @click="declineAttendees" v-if="!userRole || (!userRole === 'Admin Users')"
                                       variant="outline-danger" class="mr-2">Decline attendee{{
                                        checkedRows.length !== 1 ? 's' : ''
                                    }}
                                </b-btn>

                                <b-btn @click="zonesModalShow = true; zonesForBulk = true; selectedAttendee = null"
                                       variant="outline-primary" class="mr-2">Assign zones to attendee{{
                                        checkedRows.length !== 1 ? 's' : ''
                                    }}
                                </b-btn>

                                <b-btn @click="areasModalShow = true; areasForBulk = true; selectedAttendee = null"
                                       variant="outline-primary" class="mr-2">Assign areas to attendee{{
                                        checkedRows.length !== 1 ? 's' : ''
                                    }}
                                </b-btn>

                                <b-dropdown id="dropdown-right-2" right text="More Actions" variant="primary">
                                    <b-dropdown-item @click.prevent="markAsPrinted()">Mark as Printed</b-dropdown-item>
                                    <b-dropdown-item @click.prevent="markAsPrinted(false)">Mark as Not Printed</b-dropdown-item>
                                    <b-dropdown-item @click.prevent="markAsCollected()">Mark as Collected</b-dropdown-item>
                                    <b-dropdown-item @click.prevent="markAsCollected(false)">Mark as Not Collected</b-dropdown-item>
                                </b-dropdown>
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

                                    <template #cell(info)="data">
                                        <div v-if="data.item.first_name"><i class="ri-user-3-line"></i> {{data.item.first_name}} {{data.item.last_name}}</div>
                                        <a :href="`mailto:${data.item.email}`"><i class="ri-mail-line"></i> {{ data.item.email }}</a>
                                    </template>

                                    <template #cell(status)="data">
                                        <div class="d-flex flex-column">
                                            <div>{{ data.item.status }}</div>
                                            <div v-if="data.item.printed" :class="{'mb-1': true, badge: true, 'badge-success' : data.item.printed}">Printed</div>
                                            <div v-if="data.item.collected" :class="{'mb-1': true, badge: true, 'badge-success' : data.item.collected}">Collected</div>
                                        </div>
                                    </template>

                                    <template #cell(action)="data">
                                      <span>
                                          <b-dropdown id="dropdown-right" right text="Actions" size="sm"
                                                      variant="primary">
                                            <b-dropdown-item
                                                v-if="userRole !== 'Operations'"
                                                @click.prevent="selectedAttendee = data.item; answerModalShow = true">Answer</b-dropdown-item>
                                            <b-dropdown-item
                                                v-if="userRole !== 'Operations'"
                                                @click.prevent="selectedAttendee = data.item; messageModalShow = true">Message</b-dropdown-item>
                                            <b-dropdown-item v-if="data.item.status !== 'declined' && userRole !== 'Operations'"
                                                             @click.prevent="declineAttendee(data.item.id)">Decline</b-dropdown-item>
                                            <b-dropdown-item v-if="data.item.status === 'declined' && userRole !== 'Operations'"
                                                             @click.prevent="reinstateAttendee(data.item.id)">Reinstate</b-dropdown-item>
                                            <b-dropdown-item
                                                v-if="userRole !== 'Operations'"
                                                @click.prevent="selectedAttendee = data.item; checkedRows = []; zonesModalShow = true; zonesForBulk = false; selectedZones = (data.item.zones || [])">Assign Zones</b-dropdown-item>
                                            <b-dropdown-item
                                                v-if="userRole !== 'Operations'"
                                                @click.prevent="selectedAttendee = data.item; checkedRows = []; areasModalShow = true; areasForBulk = false; selectedAreas = (data.item.areas || [])">Assign Areas</b-dropdown-item>
                                            <b-dropdown-item
                                                v-if="data.item.badge && userRole !== 'Editors'"
                                                @click.prevent="viewBadge(data.item.id, data.item.badge.id, data.item.status)">View Badge</b-dropdown-item>
                                            <b-dropdown-item
                                                v-if="eventId && userRole !== 'Operations'"
                                                @click.prevent="selectedAttendee = data.item; selectedAttendeeAccessLevelId = data.item.access_level.id;  moveAttendeeModal = true">Move to Access Level</b-dropdown-item>
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

                <no-data v-if="!attendees.total && !q" title="Attendees" link="#" :sub-text="false"/>
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
                                <a v-if="data.item.answer.includes('http')" :href="data.item.answer" target="_blank">View
                                    File</a>
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

        <b-modal v-model="areasModalShow" id="areas-modal" title="Assign Areas">
            <b-row class="mt-3">
                <b-col sm="6" v-for="area in eventAreas" :key="area.id">
                    <div class="form-group">
                        <b-checkbox v-model="selectedAreas" :value="area.id">{{ area.area }}</b-checkbox>
                    </div>
                </b-col>
            </b-row>

            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="primary"
                        @click="(checkedRows.length > 0 && !selectedAttendee) ? assignAreasToAttendees() : onAssignAreas()"
                        :disabled="selectedAreas.length === 0"
                        class="btn btn-primary float-right ml-2">Assign Areas
                    </b-button>
                    <b-button
                        type="button"
                        variant="danger"
                        class="float-right ml-2"
                        @click="areasModalShow = false"
                    >
                        Close
                    </b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="moveAttendeeModal" id="move-attendee-modal" title="Move Attendee">
            <b-row class="mt-3">
                <b-col sm="12">
                    <div class="form-group">
                        <select class="form-control" v-model="moveToAccessLevelId">
                            <option value="">Select Access Level</option>
                            <option v-for="accessLevel in otherAccessLevels(selectedAttendeeAccessLevelId)" :key="accessLevel.id" :value="accessLevel.id">{{accessLevel.title}}</option>
                        </select>
                    </div>
                </b-col>
            </b-row>

            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="primary"
                        @click="moveToAccessLevel"
                        :disabled="!moveToAccessLevelId"
                        class="btn btn-primary float-right ml-2">Move
                    </b-button>
                    <b-button
                        type="button"
                        variant="danger"
                        class="float-right ml-2"
                        @click="moveAttendeeModal = false"
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

        <b-modal v-model="exportModalShow" id="export-modal" title="Export Survey Template">
            <b-row class="mt-3">
                <b-col sm="12">
                    <div class="form-group">
                        <label for="access-level">Access Level</label>
                        <select class="form-control" v-model="exportData.access_level_id" id="access-level">
                            <option value="">Select Access level</option>
                            <option v-for="level in accessLevels" :key="level.id" :value="level.id">{{ level.title }}
                            </option>
                        </select>
                        <span v-if="exportData.message" class="text-danger">{{exportData.message}}</span>
                    </div>
                </b-col>
            </b-row>

            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="primary"
                        @click="onExportTemplate"
                        :disabled="!exportData.access_level_id"
                        class="btn btn-primary float-right ml-2">Export
                    </b-button>
                    <b-button
                        type="button"
                        variant="danger"
                        class="float-right ml-2"
                        @click="exportModalShow = false"
                    >
                        Close
                    </b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="badgeModalShow" id="badge-modal" size="xl" title="Badge" :scrollable="true">
            <b-row class="mt-3 badge-view" v-if="badgeData">
                <b-col sm="12" v-html="badgeData.html_data" :style="{height: `${badgeData.badge.height * 38}px`}" />
            </b-row>

            <template #modal-footer>
                <div class="w-100" v-if="badgeData.status === 'approved'">
                    <b-button
                        type="button"
                        variant="primary"
                        class="float-right ml-2"
                        :disabled="badgeData.collected === 1"
                        @click="collectedModalShow = true;"
                    >
                        Collected
                    </b-button>
                    <b-button
                        type="button"
                        variant="primary"
                        class="float-right ml-2"
                        :disabled="badgeData.downloaded === 1"
                        @click="markBadgeAsPrinted"
                    >
                        Downloaded
                    </b-button>
                    <b-button
                        variant="primary"
                        @click="downloadBadge"
                        :disabled="badgeData.downloaded === 1"
                        class="btn btn-primary float-right ml-2">Download
                    </b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="collectedModalShow" id="collected-modal" title="Collected">
            <b-row class="mt-3">
                <b-col sm="12">
                    <p>Are you sure the badge has been downloaded and collected?</p>
                </b-col>
            </b-row>

            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="primary"
                        @click="markBadgeAsCollected"
                        class="btn btn-primary float-right ml-2">Yes
                    </b-button>
                    <b-button
                        type="button"
                        variant="danger"
                        class="float-right ml-2"
                    >
                        Close
                    </b-button>
                </div>
            </template>
        </b-modal>


    </b-container>
</template>
