<script setup>
import QRScanner from '../../Shared/components/core/QRScanner/Index.vue'
import axios from "axios";
import {ref, watch} from "vue";

const props = defineProps({})

const checkInType = ref('rfid')
const attendee = ref(null);
const errorMessage = ref('');
const isCheckingIn = ref(false);
const isCheckingOut = ref(false);
const checkInMessage = ref({});
const attendeeRef = ref('');

watch(checkInType, () => {
    attendee.value = null
});

const onScan = async (decodedText, decodedResult = null) => {
    errorMessage.value = '';
    try {
        attendee.value = '';
        if (decodedText) {
            const {data: {data}} = await axios.post('/checkin-user/verify-attendee', {attendee_ref: decodedText})
            attendee.value = data;
        }
    } catch (e) {
        errorMessage.value = e.response.data.message;
    }
}

const checkIn = async type => {
    isCheckingIn.value = true;
    checkInMessage.value = {}
    try {
        const {data} = await axios.post(`/checkin-user/${type}`, {attendee_ref: attendee.value.reference})
        isCheckingIn.value = false;
        attendee.value.last_activity = data.last_activity;
        checkInMessage.value = {
            message: data.message,
            type: 'success'
        }
    } catch (e) {
        console.log(e);
        isCheckingIn.value = false;
        checkInMessage.value = {
            message: e.response.data.message,
            type: 'danger'
        }
    }
}
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ $t('sidebar.checkin_attendee') }}</h4>
                    </template>

                    <template v-slot:body>
                        <b-row class="mt-3">
                            <b-col sm="6">
                                <div class="form-group">
                                    <select v-model="checkInType" class="form-control">
                                        <option value="qr">QR Scanner</option>
                                        <option value="rfid">RFID</option>
                                    </select>
                                </div>
                            </b-col>
                        </b-row>

                        <b-row class="mt-3">
                            <b-col sm="6" v-if="checkInType === 'qr'">
                                <b-card class="card-bordered mb-3 text-center">
                                    <q-r-scanner
                                        :fps="10"
                                        :qrbox="250"
                                        style="width: 100%;"
                                        @result="onScan"
                                    />

                                    <span class="text-danger">{{ errorMessage }}</span>
                                </b-card>
                            </b-col>

                            <b-col sm="6" v-else>
                                <form @submit.prevent="onScan(attendeeRef)">
                                    <div class="form-group">
                                        <label for="attendee-ref-input">Attendee Ref</label>
                                        <input type="text" v-model="attendeeRef" id="attendee-ref-input"
                                               class="form-control" placeholder="Enter attendee ref">
                                    </div>

                                    <div class="text-danger mb-2">{{ errorMessage }}</div>

                                    <div class="form-group">
                                        <b-btn
                                            type="submit"
                                            :disabled="!attendeeRef" variant="primary"
                                            class="mr-2">Check
                                        </b-btn>
                                    </div>
                                </form>

                            </b-col>

                            <b-col v-if="attendee" sm="6">
                                <b-card class="card-bordered mb-3">
                                    <b-card class="card-bordered mb-2"><b>Attendee:</b> {{ attendee.email }}
                                    </b-card>
                                    <b-card class="card-bordered mb-2"><b>Reference:</b> {{ attendee.ref }}
                                    </b-card>
                                    <b-card class="card-bordered mb-2"><b>Category:</b> {{ attendee.category }}
                                    </b-card>
                                    <b-card class="card-bordered"><b>Access Level:</b> {{
                                            attendee.access_level
                                        }}
                                    </b-card>

                                    <!--                                    <div class="mt-3">-->
                                    <!--                                        <b-btn @click="checkIn('check-in')"-->
                                    <!--                                               v-if="!attendee.last_activity || attendee.last_activity?.type === 'checkout'"-->
                                    <!--                                               :disabled="isCheckingIn" variant="primary"-->
                                    <!--                                               class="mr-2">Check In-->
                                    <!--                                        </b-btn>-->
                                    <!--                                        <b-btn @click="checkIn('check-out')"-->
                                    <!--                                               v-if="attendee.last_activity?.type === 'checkin'"-->
                                    <!--                                               :disabled="isCheckingOut"-->
                                    <!--                                               variant="secondary">Check Out-->
                                    <!--                                        </b-btn>-->
                                    <!--                                    </div>-->

                                    <div class="mt-1"
                                         :class="checkInMessage.type === 'danger' ? 'text-danger' : 'text-success'"
                                         v-if="checkInMessage.message" v-text="checkInMessage.message"></div>
                                </b-card>
                            </b-col>
                        </b-row>
                    </template>
                </iq-card>
            </b-col>
        </b-row>
    </b-container>
</template>

<style scoped>
.card-bordered {
    border: 1px solid #e3e3e3;
}

.output {
    position: relative;
    cursor: pointer;
}

.copy-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 20px;
    cursor: pointer;
}

.affiliate-link {
    padding: 10px 12px;
    min-height: 45px;
    border: 1px solid var(--iq-dark-border);
    border-radius: 8px;
}
</style>
