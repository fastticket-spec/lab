<template>
    <b-container fluid class="event-form">
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ editMode ? $t('service.edit') : $t('service.create') }}</h4>
                    </template>
                    <template v-slot:body>
                        <form class="mt-4" @submit="onSubmit">
                            <b-row class="mt-3">
                                <b-col>
                                    <b-checkbox v-model="fillArabic" class="custom-checkbox-color"
                                                name="check-button" inline>
                                        Show Arabic Inputs
                                    </b-checkbox>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col sm="12">
                                    <div class="form-group">
                                        <label for="tickets">{{ $t('input.ticket_id') }}</label>
                                        <vue-select class="form-control mb-0" v-model="chosenTickets" :options="tickets"
                                                    label="title" multiple/>
                                        <span class="text-danger"
                                              v-if="ticketErrorMessage">{{ ticketErrorMessage }}</span>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="service_title">{{ $t('input.service_title') }}</label>
                                        <Field type="text" name="service" id="service_title"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.service_title')" :validateOnInput="true"/>
                                        <ErrorMessage name="service" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6" v-if="fillArabic">
                                    <div class="form-group">
                                        <label for="service_title_arabic">{{ $t('input.service_title_arabic') }}</label>
                                        <Field type="text" name="service_arabic" id="service_title_arabic"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.service_title_arabic')" :validateOnInput="true"/>
                                        <ErrorMessage name="service_arabic" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="service_price">{{ $t('input.service_price') }}</label>
                                        <Field type="number" name="price" id="service_price"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.service_price')" :validateOnInput="true"/>
                                        <ErrorMessage name="price" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="quantityInput">{{ $t('input.quantity') }}</label>
                                        <Field type="number" name="quantity_available" id="quantityInput"
                                               :class="`form-control mb-0`"
                                               placeholder="Leave blank for unlimited" :validateOnInput="true"/>
                                        <ErrorMessage name="quantity_available" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="service_description">{{ $t('input.service_description') }}</label>
                                        <quill-editor theme="snow" v-model:content="description"
                                                      content-type="html"/>
                                        <ErrorMessage name="description" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6" v-if="fillArabic">
                                    <div class="form-group">
                                        <label for="service_description_arabic">{{
                                                $t('input.service_description_arabic')
                                            }}</label>
                                        <quill-editor theme="snow" v-model:content="description_arabic"
                                                      content-type="html"/>
                                        <ErrorMessage name="description_arabic" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-checkbox v-model="showMoreOptions" class="custom-checkbox-color"
                                                name="check-more" inline>
                                        Show More
                                    </b-checkbox>
                                </b-col>
                            </b-row>

                            <b-row v-if="showMoreOptions" class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="minimum_ticket">{{ $t('input.minimum_ticket') }}</label>
                                        <Field as="select" name="min_person_per_service" id="minimum_ticket"
                                               :class="`form-control mb-0`" :validateOnInput="true">
                                            <option v-for="n in 100" :key="`minimum-ticket-option-${n}`"
                                                    :value="n.toString()">{{
                                                    n
                                                }}
                                            </option>
                                        </Field>
                                        <ErrorMessage name="min_person_per_service" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="maximum_ticket">{{ $t('input.maximum_ticket') }}</label>
                                        <Field as="select" name="max_person_per_service" id="maximum_ticket"
                                               :class="`form-control mb-0`" :validateOnInput="true">
                                            <option v-for="n in 100" :key="`maximum-ticket-option-${n}`"
                                                    :value="n.toString()">{{
                                                    n
                                                }}
                                            </option>
                                        </Field>
                                        <ErrorMessage name="max_person_per_service" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="startDate">{{ $t('input.startDate') }}</label>
                                        <Field id="startDate" name="start_sale" v-slot="{ field }">
                                            <VueDatePicker v-bind="field" format="YYYY-MM-DD"
                                                           placeholder="Choose date" class="form-control mb-0"/>
                                        </Field>
                                        <ErrorMessage name="start_sale" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="endDate">{{ $t('input.endDate') }}</label>
                                        <Field id="endDate" name="end_sale" v-slot="{ field }">
                                            <VueDatePicker v-bind="field" format="YYYY-MM-DD"
                                                           placeholder="Choose date" class="form-control mb-0"/>
                                        </Field>
                                        <ErrorMessage name="end_sale" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-checkbox v-model="hide_service" class="custom-checkbox-color"
                                                name="check-hide" inline>
                                        {{ $t('input.hide_ticket') }}
                                    </b-checkbox>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-button :disabled="isSubmitting" type="submit" variant="primary">
                                        {{ $t(`button.${editMode ? 'update' : 'create'}`) }}
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
import {Form, Field, ErrorMessage, useForm, useField} from 'vee-validate';
import {createServiceSchema} from "../../../../Shared/components/helpers/Validators";
import {onMounted, ref} from "vue";
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import {VueDatePicker} from '@mathieustan/vue-datepicker';
import '@mathieustan/vue-datepicker/dist/vue-datepicker.min.css';
import {QuillEditor} from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import {router} from "@inertiajs/vue3";

const props = defineProps({
    event: {},
    service: {},
    editMode: Boolean,
    tickets: []
})

const fillArabic = ref(false);
const showMoreOptions = ref(false);
let chosenTickets = ref([]);
let ticketErrorMessage = ref('')

onMounted(() => {
    if (props.editMode) {
        chosenTickets.value = props.service.tickets.map(x => ({
            id: x.id,
            title: x.title,
            title_arabic: x.title_arabic
        }))

        if (props.service.service_arabic) {
            fillArabic.value = true;
        }

    }
})

const initialValues = props.editMode
    ? {
        ...props.service,
        hide_service: !!props.service.hide_service
    }
    : {
        min_person_per_service: '1',
        max_person_per_service: '30',
        hide_service: false,
    }

const {handleSubmit, isSubmitting, setFieldValue} = useForm({
    initialValues,
    validationSchema: createServiceSchema,
});

const {value: hide_service} = useField("hide_service");
const {value: description} = useField("description");
const {value: description_arabic} = useField("description_arabic");

const onSubmit = handleSubmit((values) => {
    ticketErrorMessage.value = '';
    if (chosenTickets.value.length === 0) {
        ticketErrorMessage.value = 'Please choose a ticket'
        return
    }

    if (!props.editMode) {
        values.start_sale = (values.start_sale || props.event.start_date) + ' ' + props.event.start_time;
        values.end_sale = (values.end_sale || props.event.end_date) + ' ' + props.event.end_time;
    } else {
        values.start_sale = values.start_sale.split(' ').length > 1
            ? values.start_sale || props.event.start_date
            : (values.start_sale || props.event.start_date) + ' ' + props.event.start_time;

        values.end_sale = values.end_sale.split(' ').length > 1
            ? values.end_sale || props.event.end_date
            : (values.end_sale || props.event.end_date) + ' ' + props.event.end_time;
    }

    values.tickets = chosenTickets.value.map(x => x.id);
    values.event_id = props.event.id;

    props.editMode
        ? router.patch(`/event/${props.event.id}/services/${props.service.id}`, values)
        : router.post(`/event/${props.event.id}/services`, values)
});
</script>

<style>

.vd-picker__input {
    margin-top: -5px;
}
</style>
