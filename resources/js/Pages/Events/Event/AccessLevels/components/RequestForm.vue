<script setup>

import {ErrorMessage, Field, useForm} from "vee-validate";
import {accessLevelRequestFormSchema} from "../../../../../Shared/components/helpers/Validators.js";
import {router} from "@inertiajs/vue3";
import {ref} from "vue";
import {QuillEditor} from "@vueup/vue-quill";


const props = defineProps({
    eventId: String,
    accessLevelId:String,
    data: Object
})

const initialValues = props.data || {}

const showArabicInputs = ref(true)

const {handleSubmit, isSubmitting} = useForm({
    initialValues,
    validationSchema: accessLevelRequestFormSchema,
});


const onSubmit = handleSubmit(values => {
    router.post(`/event/${props.eventId}/access-levels/${props.accessLevelId}/customize/request-form`, values);
});
</script>

<template>
    <b-row>
        <b-col sm="12">
            <iq-card>
                <template v-slot:headerTitle>
                    <h4 class="card-title">Request Form - Customize Event</h4>
                </template>

                <template v-slot:body>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form @submit.prevent="onSubmit" class="w-100">
                            <b-row class="mt-3">
                                <b-col>
                                    <b-checkbox v-model="showArabicInputs" class="custom-checkbox-color"
                                                name="check-button" inline>
                                        Show Arabic Inputs
                                    </b-checkbox>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="message_before">Message to Display to Attendees before they Complete Their Request.</label>
                                        <quill-editor toolbar="full" theme="snow" v-model:content="message_before"  name="message_before" id="message_before"
                                                      content-type="html"></quill-editor>

                                        <!-- <Field as="textarea" name="message_before" id="message_before"
                                               :class="`form-control mb-0`" :validateOnInput="true" /> -->

                                        <ErrorMessage name="message_before" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col v-if="showArabicInputs" sm="6">
                                    <div class="form-group">
                                        <label for="message_before_arabic">Message to Display to Attendees before they Complete Their Request. (Arabic)</label>

                                        <quill-editor toolbar="full" theme="snow" v-model:content="message_before_arabic"  name="message_before_arabic" id="message_before_arabic"
                                                      content-type="html"></quill-editor>

                                        <!-- <Field as="textarea" name="message_before_arabic" id="message_before_arabic"
                                               :class="`form-control mb-0`" :validateOnInput="true" /> -->

                                        <ErrorMessage name="message_before_arabic" class="text-danger"/>
                                    </div>
                                </b-col>
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="message_after">Message to Display to Attendees before they Complete Their Request.</label>

                                        <quill-editor toolbar="full" theme="snow" v-model:content="message_after"  name="message_after" id="message_after"
                                                      content-type="html"></quill-editor>

                                        <!-- <Field as="textarea" name="message_after" id="message_after"
                                               :class="`form-control mb-0`" :validateOnInput="true" /> -->


                                        <ErrorMessage name="message_after" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col v-if="showArabicInputs" sm="6">
                                    <div class="form-group">
                                        <label for="message_after_arabic">Message to Display to Attendees before they Complete Their Request. (Arabic)</label>

                                        <quill-editor toolbar="full" theme="snow" v-model:content="message_after_arabic"  name="message_after_arabic" id="message_after_arabic"
                                                      content-type="html"></quill-editor>

                                        <!-- <Field as="textarea" name="message_after_arabic" id="message_after_arabic"
                                               :class="`form-control mb-0`" :validateOnInput="true" /> -->
                                        <ErrorMessage name="message_after_arabic" class="text-danger"/>
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
