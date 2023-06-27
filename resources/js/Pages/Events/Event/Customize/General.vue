<template>
    <b-container fluid class="event-form">
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">General - Customize Event</h4>
                    </template>
                    <template v-slot:body>
                        <Form class="mt-4" @submit="onSubmit" :initialValues="initialValues">
                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="visibilityInput">Event visibility</label>
                                        <Field as="select" name="visibility" id="visibilityInput"
                                               :class="`form-control mb-0`" :validateOnInput="true">
                                            <option value="0">Hide event from the public.</option>
                                            <option value="1">Make event visible to the public.</option>
                                            <option value="2">Access event via link only.</option>
                                            <option value="3">Visible in organiser.</option>
                                        </Field>
                                        <ErrorMessage name="visibility" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="featureInput">Accept/Reject Feature</label>
                                        <Field as="select" name="accept_reject" id="featureInput"
                                               :class="`form-control mb-0`" :validateOnInput="true">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </Field>
                                        <ErrorMessage name="accept_reject" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="waitingInput">Waiting List Option Feature</label>
                                        <Field as="select" name="waiting_list" id="waitingInput"
                                               :class="`form-control mb-0`" :validateOnInput="true">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </Field>
                                        <ErrorMessage name="waiting_list" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="termsInput">Send Terms and Conditions?</label>
                                        <Field as="select" name="send_tc" id="termsInput"
                                               :class="`form-control mb-0`" :validateOnInput="true">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </Field>
                                        <ErrorMessage name="send_tc" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-button type="submit" variant="primary">
                                        {{ $t(`button.update`) }}
                                    </b-button>
                                </b-col>
                            </b-row>
                        </Form>
                    </template>
                </iq-card>
            </b-col>
        </b-row>
    </b-container>

    <order-page-form :event="event" />

    <social-form :event="event" :errors="errors" />
</template>

<script setup>
import {Form, Field, ErrorMessage} from 'vee-validate';
import {router} from "@inertiajs/vue3";
import {ref} from "vue";
import OrderPageForm from "./components/OrderPageForm.vue";
import SocialForm from "./components/SocialForm.vue";

const props = defineProps({
    code: Number,
    message: String,
    event: {},
    errors: Object
});

const initialValues = ref({
    visibility: props.event?.settings?.visibility.toString() || '1',
    is_live: props.event?.settings?.is_live.toString() || '1',
    accept_reject: props.event?.settings?.accept_reject.toString() || '0',
    waiting_list: props.event?.settings?.waiting_list.toString() || '0',
    send_tc: props.event?.settings?.send_tc.toString() || '0'
})

const onSubmit = (values) => {
    router.post(`/event/${props.event.id}/customize`, values)
};
</script>
