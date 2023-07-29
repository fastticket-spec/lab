<script setup>

import {ErrorMessage, Field, FieldArray, useForm} from "vee-validate";
import VueSelect from "vue-select";
import {accreditationFormSchema} from "../../Shared/components/helpers/Validators.js";
import {ref, watch} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    accessLevels: Array,
    eventId: String,
    surveys: Array,
    accessId: String,
    countries: Array,
});

const registerAccessLevelId = ref(props.accessId || '');

watch(registerAccessLevelId, value => {
    location.href = `/event/${props.eventId}/attendees/register-applicant?accessId=${value}`
})

const {handleSubmit, isSubmitting} = useForm({
    initialValues: {
        surveys: props.surveys.length > 0
            ? props.surveys.filter(x => !x.private).map(x => ({
                type: x.type,
                answer: x.title === 'Email Address' ? props.email : (x.type === '8' ? [] : ((x.type === '7') ? null : '')),
                title: x.title,
                title_arabic: x.title_arabic,
                id: x.id,
                question: x.title,
                is_private: x.private,
                options: x.options,
                required: x.required,
                disabled: x.title === 'Email Address' && props.email
            }))
            : {}
    },
    validationSchema: accreditationFormSchema,
});

const onSubmit = handleSubmit(values => {
    let answers = values.surveys.map(d => {
        if (d.type === '7') {
            d.answer = d.answer.map(x => {
                if (typeof x !== 'string') {
                    return x.name;
                }
                return x
            });
        }
        return d;
    });

    const privateAnswers = props.surveys.filter(x => x.private).map(x => ({
        type: x.type,
        answer: '',
        title: x.title,
        title_arabic: x.title_arabic,
        id: x.id,
        question: x.title,
        is_private: x.private,
        options: [],
        required: x.required
    }));

    answers = [...answers, ...privateAnswers];

    const data = {
        answers,
        lang: props.lang,
        reference: props.reference,
        route: `/event/${props.eventId}/attendees`
    };

    router.post(`/form/${props.eventId}/${props.accessId}/submit`, data, {
        forceFormData: true,
        preserveScroll: true
    })
});
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">{{ $t('sidebar.attendees') }}</h4>
                        </div>
                    </template>

                    <template v-slot:body>
                        <form @submit.prevent="onSubmit">
                            <b-row>
                                <b-col sm="12">
                                    <div class="form-group">
                                        <select v-model="registerAccessLevelId" class="form-control">
                                            <option value="">Select access level</option>
                                            <option v-for="accessLevel in accessLevels" :key="accessLevel.id"
                                                    :value="accessLevel.id">{{ accessLevel.title }}
                                            </option>
                                        </select>
                                    </div>
                                </b-col>
                            </b-row>
                            <b-row>
                                <div class="col-12 m-0 p-0">
                                    <div class="row m-0">
                                        <FieldArray name="surveys" v-slot="{ fields, insert, remove, swap }">
                                            <template v-for="(field, idx) in fields" :key="field.key">
                                                <b-col :sm="field.value.type !== '10' ? '6' : '12'" class="pb-2">
                                                    <div class="form-group mb-0">
                                                        <label :for="`surveys-${idx}`" v-if="field.value.type !== '10'">{{
                                                                lang === 'arabic' ? field.value.title_arabic : field.value.title
                                                            }}:</label>

                                                        <Field type="text"
                                                               v-if="field.value.type === '1'"
                                                               :name="`surveys[${idx}].answer`"
                                                               :id="`surveys-${idx}`"
                                                               :class="`form-control mb-0`" :validateOnInput="true"/>

                                                        <Field as="textarea"
                                                               v-if="field.value.type === '2'"
                                                               rows="5"
                                                               :name="`surveys[${idx}].answer`"
                                                               :id="`surveys-${idx}`"
                                                               :class="`form-control mb-0`" :validateOnInput="true"/>

                                                        <Field type="datetime-local"
                                                               v-else-if="field.value.type === '3'"
                                                               :name="`surveys[${idx}].answer`"
                                                               :id="`surveys-${idx}`"
                                                               :class="`form-control mb-0`" :validateOnInput="true"/>

                                                        <Field type="file"
                                                               v-else-if="field.value.type === '4'"
                                                               :name="`surveys[${idx}].answer`"
                                                               :id="`surveys-${idx}`"
                                                               :class="`form-control mb-0`" :validateOnInput="true"/>

                                                        <Field type="email"
                                                               v-else-if="field.value.type === '5'"
                                                               :name="`surveys[${idx}].answer`"
                                                               :id="`surveys-${idx}`"
                                                               :disabled="field.value.disabled"
                                                               :class="`form-control mb-0`" :validateOnInput="true"/>

                                                        <Field as="select"
                                                               v-else-if="field.value.type === '6'"
                                                               :name="`surveys[${idx}].answer`"
                                                               :id="`surveys-${idx}`"
                                                               :class="`form-control mb-0`" :validateOnInput="true">
                                                            <option v-for="option in field.value.options"
                                                                    :key="`${field.value.id}-${option.name}`"
                                                                    :value="lang === 'arabic' ? option.name_arabic : option.name">
                                                                {{
                                                                    lang === 'arabic' ? option.name_arabic : option.name
                                                                }}
                                                            </option>
                                                        </Field>

                                                        <vue-select v-model="field.value.answer"
                                                                    v-else-if="field.value.type === '7'"
                                                                    :class="{'text-right': lang === 'arabic'}"
                                                                    class="form-control mb-0"
                                                                    sm="6"
                                                                    :options="field.value.options"
                                                                    :label="lang === 'arabic' ? 'name_arabic' : 'name'"
                                                                    multiple/>

                                                        <template v-if="field.value.type === '8'">
                                                            <br>
                                                            <Field type="checkbox"
                                                                   v-for="option in field.value.options"
                                                                   :key="`${field.value.id}-${option.id}`"
                                                                   :name="`surveys[${idx}].answer`"
                                                                   v-slot="{field: boxField}"
                                                                   :class="`checkbox custom-checkbox-color`"
                                                                   :validateOnInput="true"
                                                                   :value="lang === 'arabic' ? option.name_arabic : option.name">
                                                                <label class="mr-3">
                                                                    <input type="checkbox"
                                                                           :name="`surveys[${idx}].answer`"
                                                                           v-bind="boxField"
                                                                           :value="lang === 'arabic' ? option.name_arabic : option.name"/>
                                                                    {{
                                                                        lang === 'arabic' ? option.name_arabic : option.name
                                                                    }}
                                                                </label>
                                                            </Field>
                                                        </template>

                                                        <template v-if="field.value.type === '9'">
                                                            <br>
                                                            <Field type="radio"
                                                                   v-for="option in field.value.options"
                                                                   :key="`${field.value.id}-${option.id}`"
                                                                   :name="`surveys[${idx}].answer`"
                                                                   v-slot="{field: boxField}"
                                                                   :class="`checkbox custom-radio-color`"
                                                                   :validateOnInput="true"
                                                                   :value="lang === 'arabic' ? option.name_arabic : option.name">
                                                                <label class="mr-3">
                                                                    <input type="radio" :name="`surveys[${idx}].answer`"
                                                                           v-bind="boxField"
                                                                           :value="lang === 'arabic' ? option.name_arabic : option.name"/>
                                                                    {{
                                                                        lang === 'arabic' ? option.name_arabic : option.name
                                                                    }}
                                                                </label>
                                                            </Field>
                                                        </template>

                                                        <h5 v-if="field.value.type === '10'" class="mt-5 mb-2">{{
                                                                lang === 'arabic' ? field.value.title_arabic : field.value.title
                                                            }}</h5>

                                                        <Field as="select"
                                                               v-else-if="field.value.type === '11'"
                                                               :name="`surveys[${idx}].answer`"
                                                               :id="`surveys-${idx}`"
                                                               :class="`form-control mb-0`" :validateOnInput="true">
                                                            <option v-for="country in countries"
                                                                    :key="`${field.value.id}-${country.country}`"
                                                                    :value="country.country">
                                                                {{ country.country }}
                                                            </option>
                                                        </Field>


                                                        <ErrorMessage :name="`surveys[${idx}].answer`"
                                                                      class="text-danger"/>
                                                    </div>
                                                </b-col>
                                            </template>
                                        </FieldArray>
                                    </div>
                                </div>
                                <div class="col-12 pb-5 mt-4">
                                    <b-btn type="submit" variant="primary" size="lg" class="px-5 py-2">
                                        Submit
                                    </b-btn>
                                </div>
                            </b-row>
                        </form>
                    </template>

                </iq-card>
            </b-col>
        </b-row>
    </b-container>
</template>
