<script setup>
import {computed, onUpdated, reactive, ref} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import axios from "axios";


const props = defineProps({
    access_levels: {},
    event_id: String
})

const selectedSort = ref('');

const locale = ref(localStorage.getItem('locale'));

const sortAccessLevels = () => {
    visit(`/event/${props.event_id}/access-levels?sort=${selectedSort.value}`)
}

const userRole = computed(() => usePage().props.user_role);

const visit = (link, method = 'get') => {
    if (method === 'get') {
        router.get(link);
    } else if (method === 'post') {
        router.post(link);
    } else {
        router.delete(link)
    }
}

const invitationModal = ref(false);
const invitesModal = ref(false);

const invitations = ref([{email: '', first_name: '', last_name: '', phone: ''}]);
const inviteAttachment = ref(null);
const isSubmittingInvite = ref(false);

const inviteType = ref('mail');

onUpdated(() => {
    invitationModal.value = false

    invitations.value = [{email: '', first_name: '', last_name: '', phone: ''}];
    inviteAttachment.value = null
    isSubmittingInvite.value = false
})

const selectedAccessLevel = ref({})

const onPaginate = page => {
    router.get(`/event/${props.event_id}/access-levels?sort=${selectedSort.value}&page=${page}`)
}

const sendInvite = () => {
    isSubmittingInvite.value = true;
    router.post(`/event/${props.event_id}/access-levels/${selectedAccessLevel.value.id}/send-invitation`, {
        invitations: invitations.value,
        invitation_type: inviteType.value,
        attachment: inviteAttachment.value ? inviteAttachment.value.files[0] : null
    })
}

const quantityAvailable = computed(() => (quantityAvailable, quantityFilled) => {
    const available = quantityAvailable - quantityFilled;
    return available >= 0 ? available : 0
})

const inviteFields = ['first_name', 'last_name', 'email', 'phone', 'attachment', 'date_sent'];
const accessLevelInvites = ref([]);

const viewInvitations = async (accessLevelId) => {
    try {
        const {data} = await axios.get(`/event/${props.event_id}/access-levels/${accessLevelId}/invites`);
        accessLevelInvites.value = data.invites;

        invitesModal.value = true;
        selectedAccessLevel.value.id = accessLevelId;
    } catch (e) {
        console.log(e);
    }
}

const insert = i => {
    invitations.value.splice(i, 0, {email: '', first_name: '', last_name: '', phone: ''})
}

const remove = i => {
    invitations.value.splice(i, 1)
}

const disableInviteSubmit = computed(() => {
    return !!invitations.value.find(x => inviteType.value === 'mail' ? (!x.email || !x.first_name) : (!x.first_name || !x.phone));
});
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <Link v-if="(userRole !== 'Operations' && userRole !== 'Viewers')"
                      :href="`/event/${event_id}/access-levels/create`" class="btn btn-primary mb-3">Add Access Level
                </Link>

                <no-data v-if="!access_levels.total" title="Access Levels"
                         :link="(userRole !== 'Operations' && userRole !== 'Viewers') ? `/event/${event_id}/access-levels/create` : '#'"/>

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
                <b-card
                    :title="locale === 'ar' ? (access_level.title_arabic || access_level.title) : access_level.title"
                    class="iq-mb-3">

                    <b-card-text class="d-flex w-100 justify-content-around my-5">
                        <div class="text-center">
                            <h5>{{ access_level.quantity_filled || 0 }}</h5>
                            <span>Registered</span>
                        </div>

                        <div class="text-center">
                            <h5>
                                {{
                                    access_level.quantity_available ? quantityAvailable(access_level.quantity_available, access_level.quantity_filled) : 'Unlimited'
                                }}</h5>
                            <span>Remaining</span>
                        </div>
                    </b-card-text>

                    <div v-if="access_level.has_surveys" class="card-date d-flex flex-column text-center"
                         :class="{'card-date-ar': locale === 'ar'}">
                        <a :href="`/e/${event_id}/a/${access_level.id}`" target="_blank">View Form</a>
                    </div>

                    <div class="d-flex justify-content-around">
                        <template v-if="(userRole !== 'Operations' && userRole !== 'Viewers')">
                            <a href="#"
                               @click.prevent.stop="visit(`/event/${event_id}/access-levels/${access_level.id}/edit`)"><i
                                class="ri-edit-line"></i>
                                Edit</a>
                            <a href="#"
                               v-if="userRole !== 'Editors'"
                               @click.prevent.stop="visit(`/event/${event_id}/access-levels/${access_level.id}/customize`)"><i
                                class="ri-settings-2-line"></i>
                                Customize</a>
                            <a href="#"
                               v-if="userRole !== 'Editors'"
                               @click.prevent.stop="visit(`/event/${event_id}/access-levels/${access_level.id}/change-status`, 'post')"
                               :class="access_level.status === 0 ? 'text-success' : 'text-danger'"><i
                                :class="access_level.status === 0 ? 'ri-play-line' : 'ri-pause-line'"></i>
                                {{ access_level.status === 0 ? 'Activate' : 'Deactivate' }}</a>
                            <a href="#"
                               v-if="userRole !== 'Editors'"
                               @click.prevent.stop="visit(`/event/${event_id}/access-levels/${access_level.id}/change-public-status`, 'post')"
                               :class="access_level.public_status === 0 ? 'text-success' : 'text-danger'"><i
                                :class="access_level.public_status === 0 ? 'ri-play-line' : 'ri-pause-line'"></i>
                                {{ access_level.public_status === 0 ? 'Activate(Public)' : 'Deactivate(Public)' }}</a>
                        </template>

                        <a href="#"
                           v-if="access_level.has_surveys && userRole !== 'Viewers' && userRole !== 'Editors'"
                           @click.prevent.stop="invitationModal = true; selectedAccessLevel = access_level"><i
                            class="ri-settings-2-line"></i>
                            Send Invitation</a>

                        <a href="#"
                           v-if="access_level.has_surveys && userRole !== 'Viewers' && userRole !== 'Editors'"
                           @click.prevent.stop="viewInvitations(access_level.id)"><i
                            class="ri-eye-line"></i>
                            View Invites</a>

                    </div>
                </b-card>
            </b-col>
        </b-row>

        <b-pagination v-if="access_levels.data && access_levels.data.length > 0" v-model="access_levels.current_page"
                      @change="onPaginate"
                      :total-rows="access_levels.total" :per-page="access_levels.per_page" align="center"/>

        <b-modal v-model="invitationModal" id="invitation-modal" title="Send Invitation" size="lg">
            <b-row class="mt-3">
                <b-col sm="6">
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select class="form-control" v-model="inviteType">
                            <option value="mail">Mail</option>
                            <option value="sms">SMS</option>
                        </select>
                    </div>
                </b-col>
            </b-row>

            <b-row class="mt-3" v-if="(inviteType === 'mail' && !selectedAccessLevel.has_mail_invite_content) || (inviteType === 'sms' && !selectedAccessLevel.has_sms_invite_content)">
                <b-col sm="12">
                    <h5>Invite {{ inviteType }} content is missing. Click <Link :href="`/event/${event_id}/access-levels/${selectedAccessLevel.id}/customize#${inviteType}-invite`">here</Link> to update content.</h5>
                </b-col>
            </b-row>

            <template v-if="(inviteType === 'mail' && selectedAccessLevel.has_mail_invite_content) || (inviteType === 'sms' && selectedAccessLevel.has_sms_invite_content)">
                <b-row class="mt-3" v-for="(invitation, i) in invitations" :key="`invitation-${i}`">
                    <b-col sm="3">
                        <div class="form-group">
                            <label for="subject">First Name</label>
                            <input type="text" class="form-control" v-model="invitation.first_name">
                        </div>
                    </b-col>
                    <b-col sm="3">
                        <div class="form-group">
                            <label for="subject">Last Name</label>
                            <input type="text" class="form-control" v-model="invitation.last_name">
                        </div>
                    </b-col>
                    <b-col sm="4" v-if="inviteType === 'mail'">
                        <div class="form-group">
                            <label for="subject">Email</label>
                            <input type="email" class="form-control" v-model="invitation.email">
                        </div>
                    </b-col>
                    <b-col sm="4" v-else>
                        <div class="form-group">
                            <label for="subject">Phone</label>
                            <input type="text" class="form-control" placeholder="+9660123456789" v-model="invitation.phone">
                        </div>
                    </b-col>
                    <b-col sm="2" class="mb-3 pt-5">
                        <b-btn variant="outline-primary"
                               @click="insert(i + 1)">
                            <i class="ri-add-line p-0"></i>
                        </b-btn>
                        <b-btn v-if="invitations.length > 1"
                               variant="outline-danger"
                               @click="remove(i)"
                               class="ml-2"><i
                            class="ri-delete-bin-2-line p-0"></i>
                        </b-btn>
                    </b-col>
                </b-row>

                <b-row v-if="inviteType === 'mail'">
                    <b-col size="12">
                        <div class="form-group">
                            <label>Attachment</label><br>
                            <input type="file" ref="inviteAttachment" accept="application/pdf">
                        </div>
                    </b-col>
                </b-row>
            </template>

            <template #modal-footer>
                <div class="w-100">
                    <b-button
                        variant="primary"
                        @click="sendInvite"
                        :disabled="disableInviteSubmit || isSubmittingInvite"
                        class="btn btn-primary float-right ml-2">Send Invite
                    </b-button>
                    <b-button
                        type="button"
                        variant="danger"
                        class="float-right ml-2"
                        @click="invitationModal = false"
                    >
                        Close
                    </b-button>
                </div>
            </template>
        </b-modal>

        <b-modal v-model="invitesModal" size="xl" id="message-modal" title="View Invites" scrollable>
            <b-row class="mt-3">
                <b-col sm="12">
                    <b-table v-if="accessLevelInvites.length > 0" :items="accessLevelInvites" :fields="inviteFields"
                             class="table-responsive-sm table-borderless">
                        <template #cell(attachment)="data">
                            <a v-if="data.item.attachment" :href="data.item.attachment" target="_blank">View</a>
                        </template>
                    </b-table>
                    <h5 v-else class="text-center mb-3">Invites will appear here</h5>
                </b-col>
            </b-row>

            <template #modal-footer>
            </template>
        </b-modal>
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

.vs__dropdown-toggle {
    border: none;
    margin: -0.3rem;
}

</style>
