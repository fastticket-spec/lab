<template>
    <b-container fluid class="event-form">
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ editMode ? $t('ticket.edit') : $t('ticket.create') }}</h4>
                    </template>
                    <template v-slot:body>
                        <form class="mt-4" @submit="onSubmit">
                            <b-row class="mt-3">
                                <b-col sm="3">
                                    <b-checkbox v-model="fillArabic" class="custom-checkbox-color"
                                                name="check-button" inline>
                                        Show Arabic Inputs
                                    </b-checkbox>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="titleInput">{{ $t('input.ticketTitle') }}</label>
                                        <Field type="text" name="title" id="titleInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.ticketTitle')" :validateOnInput="true"/>
                                        <ErrorMessage name="title" class="text-danger"/>
                                        <span class="text-danger" v-if="errors.title">{{ errors.title }}</span>
                                    </div>
                                </b-col>

                                <b-col sm="6" v-if="fillArabic">
                                    <div class="form-group">
                                        <label for="titleInputArabic">{{ $t('input.ticketTitleArabic') }}</label>
                                        <Field type="text" name="title_arabic" id="titleInputArabic"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.ticketTitleArabic')" :validateOnInput="true"/>
                                        <ErrorMessage name="title_arabic" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6" v-if="!contactToPurchase">
                                    <div class="form-group">
                                        <label for="ticket_price">{{ $t('input.ticketPrice') }}</label>
                                        <Field type="number" name="price" id="ticket_price"
                                               :class="`form-control mb-0`"
                                               step="0.01"
                                               :placeholder="$t('input.ticketPrice')" :validateOnInput="true"/>
                                        <ErrorMessage name="price" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="quantityInput">{{ $t('input.quantity') }}</label>
                                        <Field type="number" name="quantity" id="quantityInput"
                                               :class="`form-control mb-0`"
                                               placeholder="Leave blank for unlimited" :validateOnInput="true"/>
                                        <ErrorMessage name="quantity" class="text-danger"/>
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

                            <template v-if="showMoreOptions">
                                <b-row class="mt-3">
                                    <b-col sm="6">
                                        <div class="form-group">
                                            <label>{{ $t('input.ticketDescription') }}</label>
                                            <quill-editor theme="snow" v-model:content="description"
                                                          content-type="html"/>
                                            <ErrorMessage name="description" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="6" v-if="fillArabic">
                                        <div class="form-group">
                                            <label>{{ $t('input.ticketDescriptionArabic') }}</label>
                                            <quill-editor theme="snow" v-model:content="description_arabic"
                                                          content-type="html"/>
                                            <ErrorMessage name="description_arabic" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="6">
                                        <div class="form-group">
                                            <label>{{ $t('input.note') }}</label>
                                            <quill-editor theme="snow" v-model:content="notes"
                                                          content-type="html"/>
                                            <ErrorMessage name="notes" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="6" v-if="fillArabic">
                                        <div class="form-group">
                                            <label>{{ $t('input.noteArabic') }}</label>
                                            <quill-editor theme="snow" v-model:content="notes_arabic"
                                                          content-type="html"/>
                                            <ErrorMessage name="notes_arabic" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="6">
                                        <div class="form-group">
                                            <label for="checkin_limit">{{ $t('input.checkin_limit') }}</label>
                                            <Field type="number" name="checkin_limit" id="checkin_limit"
                                                   :class="`form-control mb-0`"
                                                   :placeholder="$t('input.checkin_limit')" :validateOnInput="true"/>
                                            <ErrorMessage name="checkin_limit" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="6">
                                        <div class="form-group">
                                            <label for="checkout_limit">{{ $t('input.checkout_limit') }}</label>
                                            <Field type="number" name="checkout_limit" id="checkout_limit"
                                                   :class="`form-control mb-0`"
                                                   :placeholder="$t('input.checkout_limit')" :validateOnInput="true"/>
                                            <ErrorMessage name="checkout_limit" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="6">
                                        <div class="form-group">
                                            <label for="minimum_ticket">{{ $t('input.minimum_ticket') }}</label>
                                            <Field as="select" name="minimum_ticket_per_order" id="minimum_ticket"
                                                   :class="`form-control mb-0`" :validateOnInput="true">
                                                <option v-for="n in 100" :key="`minimum-ticket-option-${n}`"
                                                        :value="n.toString()">{{
                                                        n
                                                    }}
                                                </option>
                                            </Field>
                                            <ErrorMessage name="minimum_ticket_per_order" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="6">
                                        <div class="form-group">
                                            <label for="maximum_ticket">{{ $t('input.maximum_ticket') }}</label>
                                            <Field as="select" name="maximum_ticket_per_order" id="maximum_ticket"
                                                   :class="`form-control mb-0`" :validateOnInput="true">
                                                <option v-for="n in 100" :key="`maximum-ticket-option-${n}`"
                                                        :value="n.toString()">{{
                                                        n
                                                    }}
                                                </option>
                                            </Field>
                                            <ErrorMessage name="maximum_ticket_per_order" class="text-danger"/>
                                        </div>
                                    </b-col>
                                </b-row>

                                <b-row class="mt-3">
                                    <b-col>
                                        <b-checkbox v-model="for_ticket_seller" class="custom-checkbox-color"
                                                    name="check-button" inline>
                                            {{ $t('input.ticket_seller') }}
                                        </b-checkbox>
                                    </b-col>

                                    <b-col v-if="!contactToPurchase">
                                        <b-checkbox v-model="tax_included" class="custom-checkbox-color"
                                                    name="check-button" inline>
                                            {{ $t('input.tax_included') }}
                                        </b-checkbox>
                                    </b-col>

                                    <b-col v-if="!contactToPurchase">
                                        <b-checkbox v-model="request_visa" class="custom-checkbox-color"
                                                    name="check-button" inline>
                                            {{ $t('input.visa_request') }}
                                        </b-checkbox>
                                    </b-col>

                                    <b-col>
                                        <b-checkbox v-model="hide_ticket" class="custom-checkbox-color"
                                                    name="check-button" inline>
                                            {{ $t('input.hide_ticket') }}
                                        </b-checkbox>
                                    </b-col>
                                </b-row>

                                <b-row class="mt-3" v-if="!contactToPurchase">
                                    <b-col sm="12">
                                        <b-checkbox v-model="auto_discount" class="custom-checkbox-color"
                                                    name="check-button" inline>
                                            {{ $t('input.auto_discount') }}
                                        </b-checkbox>
                                    </b-col>

                                    <b-col sm="12" v-show="auto_discount">
                                        <FieldArray name="auto_discounts" v-slot="{ fields, push, remove }">
                                            <b-row v-for="(field, idx) in fields" :key="field.key">
                                                <b-col sm="5">
                                                    <div class="form-group">
                                                        <label
                                                            :for="`no_of_tickets-${idx}`">{{
                                                                $t('input.no_of_tickets')
                                                            }}</label>
                                                        <Field as="select"
                                                               :name="`auto_discounts[${idx}].no_of_ticket`"
                                                               :id="`no_of_tickets-${idx}`"
                                                               :class="`form-control mb-0`" :validateOnInput="true">
                                                            <option v-for="n in 100" :key="`maximum-ticket-option-${n}`"
                                                                    :value="n.toString()">{{
                                                                    n
                                                                }}
                                                            </option>
                                                        </Field>
                                                        <ErrorMessage :name="`auto_discounts[${idx}].no_of_ticket`"
                                                                      class="text-danger"/>
                                                    </div>
                                                </b-col>

                                                <b-col sm="3">
                                                    <div class="form-group">
                                                        <label
                                                            :for="`discount_type-${idx}`">{{
                                                                $t('input.discount_type')
                                                            }}</label>
                                                        <Field as="select"
                                                               :name="`auto_discounts[${idx}].type`"
                                                               :id="`discount_type-${idx}`"
                                                               :class="`form-control mb-0`" :validateOnInput="true">
                                                            <option value="percentage">Percentage</option>
                                                            <option value="value">Fixed value</option>
                                                        </Field>
                                                        <ErrorMessage :name="`auto_discounts[${idx}].type`"
                                                                      class="text-danger"/>
                                                    </div>
                                                </b-col>

                                                <b-col sm="2">
                                                    <div class="form-group">
                                                        <label :for="`amount-${idx}`">{{ $t('input.amount') }}</label>
                                                        <Field type="text"
                                                               :name="`auto_discounts[${idx}].value`"
                                                               :id="`amount-${idx}`"
                                                               step="0.01"
                                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                                        <ErrorMessage :name="`auto_discounts[${idx}].value`"
                                                                      class="text-danger"/>
                                                    </div>
                                                </b-col>

                                                <b-col>
                                                    <div class="form-group">
                                                        <label>&nbsp;</label>
                                                        <div class="form-control no-border">
                                                            <b-btn variant="outline-primary"
                                                                   @click="push({no_of_ticket: '1', type: 'percentage', value: ''})">
                                                                <i class="ri-add-line p-0"></i>
                                                            </b-btn>
                                                            <b-btn variant="outline-danger" @click="remove(idx)"
                                                                   class="ml-2"><i
                                                                class="ri-delete-bin-2-line p-0"></i></b-btn>
                                                        </div>
                                                    </div>
                                                </b-col>
                                            </b-row>


                                        </FieldArray>
                                    </b-col>

                                </b-row>

                                <b-row class="mt-3">
                                    <b-col sm="6">
                                        <div class="form-group">
                                            <label for="sale_start">{{ $t('input.sale_start') }}</label>
                                            <Field type="datetime-local" name="start_sale" id="sale_start"
                                                   :class="`form-control mb-0`"
                                                   :placeholder="$t('input.sale_start')" :validateOnInput="true"/>
                                            <ErrorMessage name="start_sale" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="6">
                                        <div class="form-group">
                                            <label for="sale_end">{{ $t('input.sale_end') }}</label>
                                            <Field type="datetime-local" name="end_sale" id="sale_end"
                                                   :class="`form-control mb-0`"
                                                   :placeholder="$t('input.sale_end')" :validateOnInput="true"/>
                                            <ErrorMessage name="end_sale" class="text-danger"/>
                                        </div>
                                    </b-col>
                                </b-row>

                                <b-row class="mt-3" v-if="!contactToPurchase">
                                    <b-col sm="12" class="mb-3">
                                        <h6>Event Times</h6>
                                    </b-col>

                                    <b-col sm="12">
                                        <b-checkbox v-model="all_day" class="custom-checkbox-color"
                                                    name="check-more" inline>
                                            All Day
                                        </b-checkbox>
                                    </b-col>

                                    <b-col sm="6" v-if="!all_day">
                                        <div class="form-group">
                                            <label for="time_slots_minutes">{{ $t('input.time_slots_minutes') }}</label>
                                            <input type="number" step="5" v-model="defaultTimeInterval"
                                                   id="time_slots_minutes"
                                                   :class="`form-control mb-0`"/>
                                            <ErrorMessage name="time_slot" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="6" v-if="!all_day">
                                        <div class="form-group">
                                            <label for="break_minutes">{{ $t('input.break_minutes') }}</label>
                                            <input type="number" step="5" v-model="breakTime"
                                                   id="break_minutes"
                                                   :class="`form-control mb-0`"/>
                                            <ErrorMessage name="break_minutes" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col sm="12" class="mb-3 d-flex" v-if="!all_day">
                                        <h6>Edit Available Timeslots</h6>
                                        <a href="#" @click.prevent="resetTimeSlots" class="ml-3"><i
                                            class="ri-arrow-go-back-fill"></i> Reset</a>
                                    </b-col>

                                    <FieldArray v-if="!all_day" name="time_slots" v-slot="{ fields, push, remove }">
                                        <template v-for="(field, idx) in fields" :key="field.key">
                                            <b-col sm="2">
                                                <div class="form-group">
                                                    <Field as="select" :name="`time_slots[${idx}].value`"
                                                           :id="`time_slots[${idx}]`"
                                                           :class="`form-control mb-0`" :validateOnInput="true">
                                                        <option
                                                            v-for="(time, i) in availableTimeSlotsOptions(idx, field.isLast)"
                                                            :key="`available-time-slot-options-${time.value}`">
                                                            {{ time.value }}
                                                        </option>
                                                    </Field>
                                                </div>
                                            </b-col>

                                            <b-col sm="1">
                                                <div class="form-group mt-2 text-center justify-content-center">
                                                    -
                                                </div>
                                            </b-col>

                                            <b-col sm="2">
                                                <div class="form-group">
                                                    <input class="form-control mb-0" type="text"
                                                           :value="addMinutes(field.value.value, +time_slot)"
                                                           disabled>
                                                </div>
                                            </b-col>

                                            <b-col sm="1">
                                                <div class="form-group">
                                                    <b-btn variant="outline-danger" @click="deleteTimeSlot(idx)"><i
                                                        class="ri-delete-bin-2-line p-0"></i></b-btn>
                                                </div>
                                            </b-col>
                                        </template>

                                    </FieldArray>

                                </b-row>
                            </template>

                            <b-row class="mt-3" v-if="!contactToPurchase">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="seatsioChannelId">Seats.io Channel ID</label>
                                        <Field type="text" name="seatsio_channel_id" id="seatsioChannelId"
                                               :class="`form-control mb-0`"
                                               placeholder="Seats.io Channel ID"/>
                                        <ErrorMessage name="seatsio_channel_id" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-checkbox v-model="contactToPurchase" class="custom-checkbox-color"
                                                name="check-button" inline>
                                        Contact Us to Purchase
                                    </b-checkbox>
                                </b-col>

                                <b-col v-if="contactToPurchase">
                                    <div class="form-group">
                                        <label for="contactEmail">Contact Email</label>
                                        <Field type="email" name="contact_email" id="contactEmail"
                                               :class="`form-control mb-0`"
                                               placeholder="Contact Email" :validateOnInput="true"/>
                                        <ErrorMessage name="contact_email" class="text-danger"/>
                                        <span class="text-danger" v-if="errors.contact_email">{{
                                                errors.contact_email
                                            }}</span>
                                    </div>
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

        <ticekts-availability v-if="editMode" :ticket="ticket" />
    </b-container>
</template>

<script setup>
import {Form, Field, FieldArray, ErrorMessage, useForm, useField} from 'vee-validate';
import {createTicketSchema} from "../../../../Shared/components/helpers/Validators";
import {ref, watch, computed, onMounted} from "vue";
import {QuillEditor} from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import {router} from "@inertiajs/vue3";
import TicektsAvailability from "./TicektsAvailability.vue";

const props = defineProps({
    event: {},
    ticket: {},
    start_time: String,
    end_time: String,
    time_slot_start: String,
    time_slot_end: String,
    editMode: Boolean,
    errors: {}
})

const fillArabic = ref(false);
const contactToPurchase = ref(false);
const showMoreOptions = ref(true);
const defaultTimeInterval = ref(60);
const breakTime = ref(0);
const reset = ref(!props.editMode || !(props.editMode && props.time_slot_start));

const getHourMin = time => {
    return time.split(':');
}

const addMinutes = (time, minutes) => {
    time = getHourMin(time || '00:00');
    const date = new Date();
    date.setHours(time[0], time[1], 0);
    date.setMinutes(date.getMinutes() + minutes);

    return date.toLocaleString('en-US', {hour: '2-digit', minute: '2-digit', hour12: false});
}

const getTimeIntervals = (startTime, endTime, interval, breakVal = 0) => {
    startTime = getHourMin(startTime);
    endTime = getHourMin(endTime);
    const start = new Date();
    start.setHours(startTime[0], startTime[1], 0);
    const end = new Date();
    end.setHours(+endTime[0] + (startTime > endTime ? 24 : 0), endTime[1], 0);

    const timeArray = [];

    while (start <= end) {
        timeArray.push(start.toLocaleString('en-US', {hour: '2-digit', minute: '2-digit', hour12: false}))
        start.setMinutes(start.getMinutes() + interval + breakVal);
    }

    return timeArray.map(x => ({value: x, status: true}));
}

const timeIntervalsBetweenStartEndTimes = computed(() => (reset, startTime = null, endTime = null) => {
    if (!reset) {
        const timeStart = startTime || (props.ticket?.start_sale?.split(' ')[1] || props.start_time);
        const timeEnd = endTime || (props.ticket?.end_sale?.split(' ')[1] || props.end_time);

        return getTimeIntervals(timeStart, timeEnd, defaultTimeInterval.value, breakTime.value);
    }

    return getTimeIntervals(props.start_time, props.end_time, defaultTimeInterval.value, breakTime.value);
});

const initialValues = props.editMode ? {
    ...props.ticket,
    for_ticket_seller: !!props.ticket.for_ticket_seller,
    tax_included: !!props.ticket.tax_included,
    hide_ticket: !!props.ticket.hide_ticket,
    request_visa: !!props.ticket.request_visa,
    auto_discount: !!props.ticket.auto_discount,
    all_day: !!props.ticket.all_day,
    start_sale: props.ticket.start_sale,
    end_sale: props.ticket.end_sale,
} : {
    checkin_limit: '1',
    checkout_limit: '1',
    minimum_ticket_per_order: '1',
    maximum_ticket_per_order: '30',
    auto_discounts: [
        {no_of_ticket: '1', type: 'percentage', value: '0'}
    ],
    badge_ticket_design: false,
    for_ticket_seller: false,
    tax_included: false,
    hide_ticket: false,
    request_visa: false,
    auto_discount: true,
    all_day: true,
    time_slot: defaultTimeInterval.value,
    time_slots: timeIntervalsBetweenStartEndTimes.value(reset.value)
}

const {handleSubmit, isSubmitting, setFieldValue} = useForm({
    initialValues,
    validationSchema: createTicketSchema,
});

const {value: description} = useField("description");
const {value: description_arabic} = useField("description_arabic");
const {value: notes} = useField("notes");
const {value: notes_arabic} = useField("notes_arabic");
const {value: badge_ticket_design} = useField("badge_ticket_design");
const {value: for_ticket_seller} = useField("for_ticket_seller");
const {value: tax_included} = useField("tax_included");
const {value: request_visa} = useField("request_visa");
const {value: hide_ticket} = useField("hide_ticket");
const {value: auto_discount} = useField("auto_discount");
const {value: all_day} = useField("all_day");
const {value: time_slot} = useField("time_slot");
const {value: time_slots} = useField("time_slots");
const {value: start_sale} = useField("start_sale");
const {value: end_sale} = useField("end_sale");

onMounted(() => {
    if (props.editMode) {
        breakTime.value = props.ticket.time_slot_break;
        defaultTimeInterval.value = props.ticket.time_slot;
        setFieldValue('time_slots', timeIntervalsBetweenStartEndTimes.value(reset.value));
        if (props.ticket.auto_discounts.length > 0) {
            setFieldValue('auto_discounts', props.ticket.auto_discounts)
        } else {
            setFieldValue('auto_discounts', [
                {no_of_ticket: '1', type: 'percentage', value: ''}
            ])
        }
        contactToPurchase.value = !!props.ticket.contact_to_purchase;
    } else {
        setFieldValue('contact_to_purchase', false);
    }
})

watch(start_sale, newVal => {
    setFieldValue('time_slots', timeIntervalsBetweenStartEndTimes.value(false, newVal.split('T')[1], end_sale.value?.split('T')[1]));
})

watch(end_sale, newVal => {
    setFieldValue('time_slots', timeIntervalsBetweenStartEndTimes.value(false, start_sale.value?.split('T')[1], newVal.split('T')[1]));
})

watch(showMoreOptions, (newVal) => {
    if (newVal) {
        setFieldValue('auto_discounts', [
            {no_of_ticket: '1', type: 'percentage', value: ''}
        ])
    }
})

watch(defaultTimeInterval, (newVal) => {
    setFieldValue('time_slot', newVal)

    setFieldValue('time_slots', timeIntervalsBetweenStartEndTimes.value(reset.value));
})

watch(breakTime, (newVal) => {
    setFieldValue('time_slots', timeIntervalsBetweenStartEndTimes.value(reset.value));
})

watch(reset, (newVal) => {
    setFieldValue('time_slots', timeIntervalsBetweenStartEndTimes.value(newVal));
})

watch(contactToPurchase, newVal => {
    setFieldValue('contact_to_purchase', newVal);
})

const availableTimeSlotsOptions = (index, isLastOption) => {
    if (isLastOption) {
        return [{...time_slots.value[index], status: false}];
    }

    return getTimeIntervals(time_slots.value[index].value || '02:00', time_slots.value[index + 1].value || '07:00', 15);
}

const deleteTimeSlot = index => {
    time_slots.value.splice(index, 1);
}

const formatTime = time => {
    if (!!time && time.includes('24:')) {
        time = `00:${time.split('24:')[1]}`;
    }

    return time;
}

const formatDateTime = dateTime => {
    const d = new Date(dateTime);

    return d.getFullYear() + "-" +
        ("00" + (d.getMonth() + 1)).slice(-2) + "-" +
        ("00" + d.getDate()).slice(-2) + " " +
        ("00" + d.getHours()).slice(-2) + ":" +
        ("00" + d.getMinutes()).slice(-2) + ":" +
        ("00" + d.getSeconds()).slice(-2);
}

const onSubmit = handleSubmit((values) => {
    values.time_slot_type = 'minutes';
    values.time_slot_break = breakTime.value;

    if (values.contact_to_purchase) {
        values.auto_discount = 0
    }

    if (!values.auto_discount) {
        values.auto_discounts = []
    }

    if (values.all_day) {
        delete values.time_slots;
    } else {
        values.time_slots = values.time_slots
            .filter(x => !!x.status)
            .map((timeSlot, i) => {
                let endTime = addMinutes(timeSlot.value, (values.time_slot));

                return {
                    start_time: formatTime(timeSlot.value),
                    end_time: formatTime(endTime)
                }
            })
            .filter(timeSlot => !!timeSlot.end_time)
    }

    values.start_sale = formatDateTime(values.start_sale);
    values.end_sale = formatDateTime(values.end_sale);
    // values.start_sale = (values.start_sale || props.event.start_date) + ' ' + props.start_time + ':00';
    // values.end_sale = (values.end_sale || props.event.end_date) + ' ' + props.end_time + ':00';

    props.editMode
        ? router.patch(`/tickets/${props.ticket.id}`, values)
        : router.post(`/event/${props.event.id}/tickets`, values);
});

const resetTimeSlots = () => {
    reset.value = false;
    setTimeout(() => {
        reset.value = true;
    }, 100)
}
</script>

<style scoped>
.no-border {
    border: 0;
}
</style>

<style>
.dp__input_wrap .dp__icon {
    width: 15px;
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
}
</style>
