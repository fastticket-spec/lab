<script setup>

import {ErrorMessage, Field, useForm} from "vee-validate";
import {ref} from "vue";
import {accessLevelSocialsSchema} from "../../../../../Shared/components/helpers/Validators.js";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    eventId: String,
    accessLevelId:String,
    data: Object
})

const initialValues = props.data || {}

const {handleSubmit, isSubmitting} = useForm({
    initialValues,
    validationSchema: accessLevelSocialsSchema,
});


const onSubmit = handleSubmit(values => {
    router.post(`/event/${props.eventId}/access-levels/${props.accessLevelId}/customize/socials`, values);
});
</script>

<template>
    <b-row>
        <b-col sm="12">
            <iq-card>
                <template v-slot:headerTitle>
                    <h4 class="card-title">Socials - Customize Event</h4>
                </template>

                <template v-slot:body>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form @submit.prevent="onSubmit" class="w-100">
                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="instagram">Instagram</label>
                                        <Field as="textarea" name="instagram" id="instagram"
                                               :class="`form-control mb-0`" :validateOnInput="true" />
                                        <ErrorMessage name="instagram" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <Field as="textarea" name="email" id="email"
                                               :class="`form-control mb-0`" :validateOnInput="true" />
                                        <ErrorMessage name="email" class="text-danger"/>
                                    </div>
                                </b-col>
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="phone_number">Phone Number</label>
                                        <Field as="textarea" name="phone_number" id="phone_number"
                                               :class="`form-control mb-0`" :validateOnInput="true" />
                                        <ErrorMessage name="phone_number" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-button :disabled="isSubmitting" type="submit" variant="primary">
                                        {{ $t('button.update') }}
                                    </b-button>
                                </b-col>
                            </b-row>
                        </form>
                    </div>
                </template>
            </iq-card>
        </b-col>
    </b-row>
</template>
