<script setup>

import {Field, FieldArray, Form, useForm} from "vee-validate";
import {onMounted} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    ticket: Object
})

const initialValues = {}

const {handleSubmit, isSubmitting, setFieldValue} = useForm({
    initialValues
});

onMounted(() => {
    const multiDates = props.ticket.multidate_events.map(x => ({
        date: x.schedule_date,
        available: !!x.pivot?.available,
        multidate_event_id: x.pivot?.multidate_event_id
    }));

    setFieldValue('availabilities', multiDates)
})

const onSubmit = handleSubmit((values) => {
    console.log({values})

    router.post(`/event/${props.ticket.event_id}/tickets/${props.ticket.id}/update-ticket-date-availability`, values)
})
</script>

<template>
    <b-row>
        <b-col sm="12">
            <iq-card>
                <template v-slot:headerTitle>
                    <h4 class="card-title">Tickets Availability</h4>
                </template>
                <template v-slot:body>
                    <form class="mt-4" @submit="onSubmit">
                        <b-row class="mt-3">
                            <b-col sm="12">
                                <FieldArray name="availabilities" v-slot="{ fields, push, remove }">
                                    <b-row>
                                        <b-col v-for="(field, idx) in fields" :key="field.key" sm="2">
                                            <label class="card availability-card">
                                                <div class="d-flex justify-content-between w-100 py-2 px-3">
                                                    <span>{{ field.value.date }}</span>

                                                    <Field type="checkbox"
                                                           :value="true"
                                                           :name="`availabilities[${idx}].available`"
                                                           :id="`availability-${idx}`"
                                                           class="mb-0" :validateOnInput="true"/>
                                                </div>
                                            </label>
                                        </b-col>
                                    </b-row>
                                </FieldArray>
                            </b-col>
                        </b-row>

                        <b-row class="mt-3">
                            <b-col>
                                <b-button :disabled="isSubmitting" type="submit" variant="primary">
                                    {{ $t(`button.update`) }}
                                </b-button>
                            </b-col>
                        </b-row>
                    </form>
                </template>
            </iq-card>
        </b-col>
    </b-row>
</template>

<style scoped>
.availability-card {
    border: 1px solid #eee;
}
</style>
