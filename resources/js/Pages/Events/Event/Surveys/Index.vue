<script setup>
import {ErrorMessage, Field, FieldArray, useField, useForm} from 'vee-validate';
import {createSurveySchema} from '../../../../Shared/components/helpers/Validators';
import {router} from "@inertiajs/vue3";
import {onMounted, ref} from "vue";

const props = defineProps({
    code: Number,
    message: String,
    editMode: Boolean,
    event_id: String,
    access_levels: Array,
    field_types: Array,
    event_survey: Object
});

const fillArabic = ref(false);
const selectedAccessLevels = ref([]);
const accessLevelError = ref('');

const initialValues = props.event_survey ? {
    surveys: props.event_survey.surveys.length > 0
        ? props.event_survey.surveys.map(x => ({
            ...x,
            required: !!x.required,
            options: (x.options).length > 0
                ? x.options
                : [{name: '', name_arabic: ''}],
            open: false
        }))
        : [{
            title: '',
            title_arabic: '',
            type: '1',
            required: false,
            options: [
                {name: '', name_arabic: ''}
            ],
            open: true
        }]
} : {
    surveys: [
        {
            title: '',
            title_arabic: '',
            type: '1',
            required: false,
            options: [
                {name: '', name_arabic: ''}
            ]
        }
    ]
}

onMounted(() => {
    if (props.event_survey) {
        selectedAccessLevels.value = props.event_survey.access_levels;
        if (props.event_survey.surveys.length > 0 && props.event_survey.surveys[0].title_arabic) {
            fillArabic.value = true
        }
    }
});

const {handleSubmit, isSubmitting, setFieldValue} = useForm({
    initialValues,
    validationSchema: createSurveySchema,
});

const {value: surveys} = useField('surveys');

const closeSurvey = surveyId => {
    setFieldValue(`surveys[${surveyId}].open`, false)
}

const onSubmit = handleSubmit((values) => {
    accessLevelError.value = '';

    if (selectedAccessLevels.value.length === 0) {
        return accessLevelError.value = 'Please choose an access level';
    }

    values.access_levels = selectedAccessLevels.value.filter(x => !!x);
    values.event_id = props.event_id;

    values.surveys = values.surveys.map(survey => {
        survey.options = survey.options.filter(option => (option.name || option.name_arabic));
        return survey;
    });

    router.post(`/event/${props.event_id}/surveys`, values, {
        preserveScroll: true
    })
})
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ editMode ? $t('survey.edit') : $t('sidebar.surveys') }}</h4>
                    </template>
                    <template v-slot:body>
                        <form @submit.prevent="onSubmit">
                            <b-row class="mt-3">
                                <b-col>
                                    <b-checkbox v-model="fillArabic" class="custom-checkbox-color"
                                                name="check-button" inline>
                                        Show Arabic Inputs
                                    </b-checkbox>
                                </b-col>
                            </b-row>

                            <FieldArray name="surveys" v-slot="{ fields, insert, remove, swap }">
                                <b-row v-for="(field, idx) in fields" :key="field.key" class="mt-3 border p-2">
                                    <b-col v-show="field?.value?.open" sm="6">
                                        <div class="form-group mb-0">
                                            <label for="question">{{ $t('input.question') }}</label>
                                            <Field type="text"
                                                   :name="`surveys[${idx}].title`"
                                                   :id="`question-${idx}`"
                                                   :class="`form-control mb-0`" :validateOnInput="true"/>
                                            <ErrorMessage :name="`surveys[${idx}].title`" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col v-show="field?.value?.open" sm="6" v-if="fillArabic">
                                        <div class="form-group mb-0">
                                            <label for="question_arabic">{{ $t('input.question_arabic') }}</label>
                                            <Field type="text"
                                                   :name="`surveys[${idx}].title_arabic`"
                                                   :id="`question_arabic-${idx}`"
                                                   :class="`form-control mb-0`"
                                                   :validateOnInput="true"/>
                                            <ErrorMessage :name="`surveys[${idx}].title_arabic`"
                                                          class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col v-show="field?.value?.open" sm="6" :class="{'mt-2': fillArabic}">
                                        <div class="form-group mb-0">
                                            <label for="question_type">{{ $t('input.question_type') }}</label>
                                            <Field as="select"
                                                   :name="`surveys[${idx}].type`"
                                                   :id="`question_type-${idx}`"
                                                   :class="`question-type form-control mb-0`"
                                                   :validateOnInput="true">
                                                <option v-for="(fieldType, k, i) in field_types"
                                                        :key="`field-type-option-${i}`"
                                                        :value="k">{{ fieldType.name }}
                                                </option>
                                            </Field>
                                            <ErrorMessage :name="`surveys[${idx}].type`" class="text-danger"/>
                                        </div>
                                    </b-col>

                                    <b-col v-show="field?.value?.open" sm="6">
                                        <Field
                                            v-slot="{ field }"
                                            :name="`surveys[${idx}].required`"
                                            type="checkbox"
                                            :value="true"
                                        >
                                            <label>
                                                <input type="checkbox" :name="`surveys[${idx}].required`"
                                                       v-bind="field"
                                                       :value="true"/>
                                                Required
                                            </label>
                                        </Field>
                                    </b-col>

                                    <b-col v-show="field?.value?.open" sm="12">
                                        <FieldArray :name="`surveys[${idx}].options`"
                                                    v-slot="{ fields: optionFields, insert: optionInsert, remove: optionRemove }">
                                            <b-row
                                                class="mt-3 bg-gray py-3"
                                                v-show="field_types[surveys[idx].type].has_options"
                                            >
                                                <b-col sm="12">
                                                    <h5>{{ $t('input.question_options') }}</h5>
                                                </b-col>

                                                <b-col sm="12">
                                                    <b-row v-for="(optionField, optionIdx) in optionFields"
                                                           :key="`options-${optionField.key}`"
                                                           class="mt-3">
                                                        <b-col sm="5">
                                                            <div class="form-group">
                                                                <label>Option {{ optionIdx + 1 }}</label>
                                                                <Field type="text"
                                                                       :name="`surveys[${idx}].options[${optionIdx}].name`"
                                                                       :id="`option-name-${optionIdx}`"
                                                                       :class="`form-control mb-0`"
                                                                       :validateOnInput="true"/>
                                                                <ErrorMessage
                                                                    :name="`surveys[${idx}].options[${optionIdx}].name`"
                                                                    class="text-danger"/>
                                                            </div>
                                                        </b-col>

                                                        <b-col sm="5" v-if="fillArabic">
                                                            <div class="form-group">
                                                                <label>Option {{ optionIdx + 1 }} (Arabic)</label>
                                                                <Field type="text"
                                                                       :name="`surveys[${idx}].options[${optionIdx}].name_arabic`"
                                                                       :id="`option-name_arabic-${optionIdx}`"
                                                                       :class="`form-control mb-0`"
                                                                       :validateOnInput="true"/>
                                                                <ErrorMessage
                                                                    :name="`surveys[${idx}].options[${optionIdx}].name_arabic`"
                                                                    class="text-danger"/>
                                                            </div>
                                                        </b-col>

                                                        <b-col>
                                                            <div class="form-group">
                                                                <label>&nbsp;</label>
                                                                <div class="form-control no-border">
                                                                    <b-btn variant="outline-primary"
                                                                           @click="optionInsert(optionIdx + 1, {name: '', name_arabic: ''})">
                                                                        <i class="ri-add-line p-0"></i>
                                                                    </b-btn>
                                                                    <b-btn v-if="optionFields.length > 1"
                                                                           variant="outline-danger"
                                                                           @click="optionRemove(optionIdx)"
                                                                           class="ml-2"><i
                                                                        class="ri-delete-bin-2-line p-0"></i>
                                                                    </b-btn>
                                                                </div>
                                                            </div>
                                                        </b-col>
                                                    </b-row>
                                                </b-col>
                                            </b-row>
                                        </FieldArray>
                                    </b-col>

                                    <b-col v-if="field?.value?.open" sm="12"
                                           class="my-3 d-flex justify-content-between">
                                        <div>
                                            <b-btn variant="primary" class="mr-2"
                                                   @click="insert(idx + 1, {title: '', title_arabic: '', type: '1', required: false, options: [{name: '', name_arabic: ''}], open: true})">
                                                <i class="ri-add-line"/> Add Section
                                            </b-btn>
                                            <b-btn variant="danger" class="mr-2" v-show="surveys.length > 1"
                                                   @click="remove(idx)"><i class="ri-subtract-line"></i> Remove
                                                Section
                                            </b-btn>
                                        </div>
                                        <b-btn
                                            v-show="surveys[idx].title"
                                            variant="secondary" class="mr-2"
                                            @click="closeSurvey(idx)"
                                        >
                                            <i class="ri-eye-close-line"/> Minimize
                                        </b-btn>
                                    </b-col>

                                    <b-col v-if="!field?.value?.open" sm="12"
                                           class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex">
                                            <h5>{{ surveys[idx].title }}</h5>
                                            <h5
                                                v-if="surveys[idx]?.title_arabic">&nbsp;({{
                                                    surveys[idx].title_arabic
                                                }})</h5>
                                        </div>
                                        <span>{{field_types[surveys[idx].type].name}}</span>
                                        <div>
                                            <b-btn variant="primary" class="mr-2"
                                                   @click="insert(idx + 1, {title: '', title_arabic: '', type: '1', required: false, options: [{name: '', name_arabic: ''}], open: true})">
                                                <i class="ri-add-line p-0"/>
                                            </b-btn>
                                            <b-btn variant="danger" class="mr-2" v-show="surveys.length > 1"
                                                   @click="remove(idx)"><i class="ri-subtract-line p-0"></i>
                                            </b-btn>
                                            <b-btn variant="secondary" class="mr-2"
                                                   @click="surveys[idx].open = 1"
                                            >
                                                <i class="ri-edit-2-line p-0"/>
                                            </b-btn>
                                            <b-btn variant="primary" class="mr-2"
                                                   :disabled="idx === 0"
                                                   @click="swap(idx, idx - 1)"
                                            >
                                                <i class="ri-arrow-up-line p-0"/>
                                            </b-btn>
                                            <b-btn variant="secondary" class="mr-2"
                                                   :disabled="idx === surveys.length - 1"
                                                   @click="swap(idx, idx + 1)"
                                            >
                                                <i class="ri-arrow-down-line p-0"/>
                                            </b-btn>
                                        </div>
                                    </b-col>

                                </b-row>
                            </FieldArray>

                            <b-row class="mt-3">
                                <b-col sm="12">
                                    <h5>{{ $t('input.require_for_levels') }}</h5>
                                </b-col>

                                <b-col sm="3" v-for="(accessLevel, i) in access_levels" class="mt-2">
                                    <b-checkbox :key="accessLevel.id" v-model="selectedAccessLevels[i]"
                                                class="custom-checkbox-color"
                                                :value="accessLevel.id"
                                                name="accessLevel_ids" inline>
                                        {{ accessLevel.title }}
                                    </b-checkbox>
                                </b-col>

                                <b-col sm="12">
                                    <div v-show="accessLevelError">
                                        <span class="text-danger">{{ accessLevelError }}</span>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-button type="submit" :disabled="isSubmitting" variant="primary">
                                        {{ $t(`button.${access_levels ? 'update' : 'create'}`) }}
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

<style scoped>
.no-border {
    border: 0;
    background: none;
}

.question-type {
    height: 45px;
}
</style>
