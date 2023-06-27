<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ $t('attendees.edit') }}</h4>
                    </template>
                    <template v-slot:body>
                        <form @submit.prevent="onSubmit">
                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="ticket_id">{{ $t('input.ticket_id') }}</label>
                                        <Field as="select" name="ticket_id" id="ticket_id"
                                               class="form-control mb-0"
                                               :placeholder="$t('input.ticket_id')" :validateOnInput="true">
                                            <option value="">Choose Ticket</option>
                                            <option v-for="ticket in tickets" :key="ticket.id" :value="ticket.id">
                                                {{ ticket.title }}
                                            </option>
                                        </Field>
                                        <ErrorMessage name="ticket_id" class="text-danger"/>
                                        <span v-if="errors.ticket_id" class="text-danger">{{
                                                errors.ticket_id
                                            }}</span>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="first_name">{{ $t('input.first_name') }}</label>
                                        <Field type="text" name="first_name" id="first_name"
                                               class="form-control mb-0"
                                               :placeholder="$t('input.first_name')" :validateOnInput="true"/>
                                        <ErrorMessage name="first_name" class="text-danger"/>
                                        <span v-if="errors.first_name" class="text-danger">{{
                                                errors.first_name
                                            }}</span>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="last_name">{{ $t('input.last_name') }}</label>
                                        <Field type="text" name="last_name" id="last_name"
                                               class="form-control mb-0"
                                               :placeholder="$t('input.last_name')" :validateOnInput="true"/>
                                        <ErrorMessage name="last_name" class="text-danger"/>
                                        <span v-if="errors.last_name" class="text-danger">{{ errors.last_name }}</span>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="email">{{ $t('input.email') }}</label>
                                        <Field type="email" name="email" id="email"
                                               class="form-control mb-0"
                                               :placeholder="$t('input.email')" :validateOnInput="true"/>
                                        <ErrorMessage name="email" class="text-danger"/>
                                        <span v-if="errors.email" class="text-danger">{{ errors.email }}</span>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="attend_date">{{ $t('input.attend_date') }}</label>
                                        <Field id="attend_date" name="attend_date" v-slot="{ field }">
                                            <VueDatePicker v-bind="field" format="YYYY-MM-DD"
                                                           :allowed-dates="allowedDates"
                                                           placeholder="Choose date" class="form-control mb-0"/>
                                        </Field>
                                        <ErrorMessage name="attend_date" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="note">{{ $t('input.note') }}</label>
                                        <Field as="textarea" rows="5" name="note" id="note"
                                               class="form-control mb-0"
                                               :placeholder="$t('input.note')" :validateOnInput="true"/>
                                        <ErrorMessage name="note" class="text-danger"/>
                                        <span v-if="errors.note" class="text-danger">{{ errors.note }}</span>
                                    </div>
                                </b-col>
                            </b-row>

                            <div>
                                <span v-if="$page.props.code !== 200" class="text-danger">{{
                                        $page.props.message
                                    }}</span>
                            </div>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-button type="submit" :disabled="isSubmitting" variant="primary">
                                        {{ $t(`button.update`) }}
                                    </b-button>
                                </b-col>
                            </b-row>
                        </form>
                    </template>
                </iq-card>
            </b-col>
        </b-row>
    </b-container>
</template>
<script setup>
import {ErrorMessage, Field, useForm} from "vee-validate";
import {editAttendeeSchema} from "../../../../Shared/components/helpers/Validators";
import {router} from "@inertiajs/vue3";
import {VueDatePicker} from '@mathieustan/vue-datepicker';
import '@mathieustan/vue-datepicker/dist/vue-datepicker.min.css';

const props = defineProps({
    code: Number,
    message: String,
    errors: Object,
    attendee: Object,
    event_id: String,
    tickets: Array,
    attend_dates: Array
});

const initialValues = {
    first_name: props.attendee.first_name,
    last_name: props.attendee.last_name,
    email: props.attendee.email,
    ticket_id: props.attendee.ticket_id,
    attend_date: props.attendee.attend_date,
    note: props.attendee.note,
}

const {handleSubmit, isSubmitting, setFieldValue} = useForm({
    initialValues,
    validationSchema: editAttendeeSchema,
});

const allowedDates = date => props.attend_dates.includes(date)

const onSubmit = handleSubmit(values => {
    router.patch(`/event/${props.event_id}/attendees/${props.attendee.id}`, values);
})
</script>

<style>

.vd-picker__input {
    margin-top: -5px;
}
</style>
