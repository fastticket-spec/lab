<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card class="ticket-design-page">
                    <template v-slot:headerTitle>
                        <h4 class="card-title">Ticket Design - Customize Event</h4>
                    </template>

                    <template v-slot:body>
                        <form class="mt-4" @submit.prevent="onSubmit">
                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="border-color">Border Color</label>
                                        <Field type="color" name="ticket_border_color" id="border-color"
                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                        <ErrorMessage name="ticket_border_color" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="bg-color">Background Color</label>
                                        <Field type="color" name="ticket_bgcolor" id="bg-color"
                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                        <ErrorMessage name="ticket_bgcolor" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="text-color">Text Color</label>
                                        <Field type="color" name="ticket_text_color" id="text-color"
                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                        <ErrorMessage name="ticket_text_color" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="sub-text-color">Sub Text Color</label>
                                        <Field type="color" name="ticket_sub_text_color" id="sub-text-color"
                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                        <ErrorMessage name="ticket_sub_text_color" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="barcode">Show 1D ticket Barcode on Ticket</label>
                                        <Field as="select" name="1d_barcode" id="barcode"
                                               :class="`form-control mb-0`" :validateOnInput="true">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </Field>
                                        <ErrorMessage name="1d_barcode" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-4">
                                <b-col sm="12">
                                    <ticket-preview
                                        :event="event"
                                        :ticket-sub-text-color="ticket_sub_text_color"
                                        :ticket-text-color="ticket_text_color"
                                        :ticket-bgcolor="ticket_bgcolor"
                                        :ticket-border-color="ticket_border_color"
                                        :bar-code="barCode"
                                    />
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-button type="submit" variant="primary">
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
import {useField, useForm, Field} from "vee-validate";
import {router} from "@inertiajs/vue3";
import TicketPreview from "./components/TicketPreview.vue";

const props = defineProps({
    event: {}
})

const initialValues = {
    '1d_barcode': props.event?.ticket_settings ? props.event?.ticket_settings['1d_barcode'] : '1',
    ticket_sub_text_color: props.event?.ticket_settings?.ticket_sub_text_color || '#000000',
    ticket_text_color: props.event?.ticket_settings?.ticket_text_color || '#000000',
    ticket_bgcolor: props.event?.ticket_settings?.ticket_bgcolor || '#ffffff',
    ticket_border_color: props.event?.ticket_settings?.ticket_border_color || '#cecece'
}

const {handleSubmit} = useForm({
    initialValues
});

const {value: ticket_sub_text_color} = useField("ticket_sub_text_color");
const {value: ticket_text_color} = useField("ticket_text_color");
const {value: ticket_bgcolor} = useField("ticket_bgcolor");
const {value: ticket_border_color} = useField("ticket_border_color");
const {value: barCode} = useField("1d_barcode");

const onSubmit = handleSubmit(values => {
    router.post(`/event/${props.event.id}/ticket-design`, values)
})

</script>
