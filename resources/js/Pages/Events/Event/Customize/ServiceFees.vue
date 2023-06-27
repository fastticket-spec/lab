<template>
    <b-container fluid class="services-fees-form">
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">Service Fees & Taxes - Customize Event</h4>
                        <p>These are optional fees you can include in the cost of each ticket. This charge will appear
                            on buyer's invoices as 'BOOKING FEES'.</p>
                    </template>

                    <template v-slot:body>
                        <Form class="mt-4" @submit="onSubmit" :initialValues="initialValues">
                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="site-fees">Site Fees
                                            <i v-b-tooltip.top="'e.g: enter 3.5 for 3.5%'"
                                               class="mt-3 mr-1 ri-information-fill"/>
                                        </label>
                                        <Field type="number" name="site_fee_percentage" id="site-fees"
                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                        <ErrorMessage name="site_fee_percentage" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="12" class="mb-3">
                                    <b-checkbox v-model="chargeTaxes" id="chargeTaxes" class="custom-checkbox-color"
                                                name="charge-taxes" inline>
                                        Do you want to charge taxes as your events?
                                    </b-checkbox>
                                </b-col>

                                <template v-if="chargeTaxes">
                                    <b-col sm="6">
                                        <div class="form-group">
                                            <label for="tax-id">Tax ID</label>
                                            <Field type="text" name="tax_id" id="tax-id"
                                                   :class="`form-control mb-0`" :validateOnInput="true"/>
                                            <ErrorMessage name="tax_id" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="6">
                                        <div class="form-group">
                                            <label for="tax-name">Tax Name</label>
                                            <Field type="text" name="tax_name" id="tax-name"
                                                   :class="`form-control mb-0`" :validateOnInput="true"/>
                                            <ErrorMessage name="tax_name" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="6">
                                        <div class="form-group">
                                            <label for="tax-name-arabic">Tax Name (Arabic)</label>
                                            <Field type="text" name="tax_name_ar" id="tax-name-arabic"
                                                   :class="`form-control mb-0`" :validateOnInput="true"/>
                                            <ErrorMessage name="tax_name_ar" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="6">
                                        <div class="form-group">
                                            <label for="tax-percentage">Tax Percentage</label>
                                            <Field type="number" name="tax_value" id="tax-percentage"
                                                   :class="`form-control mb-0`" :validateOnInput="true"/>
                                            <ErrorMessage name="tax_value" class="text-danger"/>
                                        </div>
                                    </b-col>
                                </template>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="service-fee-percentage">Service Fee Percentage</label>
                                        <Field type="number" name="organiser_fee_percentage" id="service-fee-percentage"
                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                        <ErrorMessage name="organiser_fee_percentage" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="service-fee-fixed">Service Fee Fixed Price</label>
                                        <Field type="number" name="organiser_fee_fixed" id="service-fee-fixed"
                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                        <ErrorMessage name="organiser_fee_fixed" class="text-danger"/>
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
import {Form, Field, ErrorMessage} from 'vee-validate';
import {ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    event: {}
})

const chargeTaxes = ref(!!props.event?.organiser_settings?.charge_tax)

const initialValues = props.event.organiser_settings

const onSubmit = values => {
    values.charge_tax = chargeTaxes.value;
    router.post(`/event/${props.event.id}/service-fees`, values);
}
</script>
