<template>
    <b-container fluid class="order-page-form">
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">Order Page - Customize Event</h4>
                    </template>
                    <template v-slot:body>
                        <Form class="mt-4" @submit="onSubmit" :initialValues="initialValues">
                            <b-row class="mt-3">
                                <b-col sm="12" class="mb-3">
                                    <b-checkbox v-model="showArabic" id="showArabic" class="custom-checkbox-color"
                                                name="check-button" inline>
                                        Show Arabic Messages
                                    </b-checkbox>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="preOrderInput">Message to Display to Attendees before They Complete
                                            Their Order
                                            <i v-b-tooltip.top="'This message will be displayed to attendees immediately before they finalize their order.'"
                                               class="mt-3 mr-1 ri-information-fill"/>
                                        </label>
                                        <Field as="textarea" name="pre_order_display_message" id="preOrderInput"
                                               :class="`form-control mb-0`" :validateOnInput="true" rows="3"/>
                                        <ErrorMessage name="pre_order_display_message" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6" v-if="showArabic">
                                    <div class="form-group">
                                        <label for="preOrderArabicInput">Message to Display to Attendees before They
                                            Complete Their Order (Arabic)</label>
                                        <Field as="textarea" name="pre_order_display_message_ar"
                                               id="preOrderArabicInput"
                                               :class="`form-control mb-0`" :validateOnInput="true" rows="3"/>
                                        <ErrorMessage name="pre_order_display_message_ar" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="postOrderInput">Message to Display to Attendees after They Complete
                                            Their Order
                                            <i v-b-tooltip.top="'This message will be displayed to attendees once they have successfully completed the checkout process.'"
                                               class="mt-3 mr-1 ri-information-fill"/>
                                        </label>
                                        <Field as="textarea" name="post_order_display_message" id="postOrderInput"
                                               :class="`form-control mb-0`" :validateOnInput="true" rows="3"/>
                                        <ErrorMessage name="post_order_display_message" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6" v-if="showArabic">
                                    <div class="form-group">
                                        <label for="postOrderArabicInput">Message to Display to Attendees after They
                                            Complete Their Order (Arabic)</label>
                                        <Field as="textarea" name="post_order_display_message_ar"
                                               id="postOrderArabicInput"
                                               :class="`form-control mb-0`" :validateOnInput="true" rows="3"/>
                                        <ErrorMessage name="post_order_display_message_ar" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col md="12">
                                    <h5>Offline Payment Settings</h5>
                                </b-col>

                                <b-col sm="12" class="mb-3">
                                    <b-checkbox v-model="enableOfflinePayment" id="enableOfflinePayment" class="custom-checkbox-color"
                                                name="enable-offline" inline>
                                       Enable Offline Payment
                                    </b-checkbox>
                                </b-col>

                                <b-col sm="6" v-if="enableOfflinePayment">
                                    <div class="form-group">
                                        <label for="offlinePaymentInput">Enter instructions on how attendees can make payment offline.</label>
                                        <Field as="textarea" name="offline_payment_instruction"
                                               id="offlinePaymentInput"
                                               :class="`form-control mb-0`" :validateOnInput="true" rows="3"/>
                                        <ErrorMessage name="offline_payment_instruction" class="text-danger"/>
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
</template>

<script setup>
import {ref} from "vue";
import {Form, Field, ErrorMessage} from 'vee-validate';
import {router} from "@inertiajs/vue3";

const props = defineProps({
    event: {}
})

const showArabic = ref(!!props.event?.settings?.order_message_ar);
const enableOfflinePayment = ref(!!props.event?.settings?.enable_offline_payment);

const initialValues = ref({
    ...props.event?.settings
})

const onSubmit = values => {
    values.order_message_ar = showArabic.value;
    values.enable_offline_payment = enableOfflinePayment.value;
    router.post(`/event/${props.event.id}/customize`, values)
}
</script>
