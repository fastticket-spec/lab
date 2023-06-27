<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ editMode ? $t('survey.edit') : $t('survey.create') }}</h4>
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

                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="question">{{ $t('input.question') }}</label>
                                        <Field type="text" name="title" id="question"
                                               class="form-control mb-0"
                                               :placeholder="$t('input.question')" :validateOnInput="true"/>
                                        <ErrorMessage name="title" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6" v-if="fillArabic">
                                    <div class="form-group">
                                        <label for="question_arabic">{{ $t('input.question_arabic') }}</label>
                                        <Field type="text" name="title_arabic" id="question_arabic"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.question_arabic')" :validateOnInput="true"/>
                                        <ErrorMessage name="title_arabic" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="question_type">{{ $t('input.question_type') }}</label>
                                        <Field as="select" name="type" id="question_type"
                                               :class="`form-control mb-0`"
                                               :validateOnInput="true">
                                            <option v-for="(survey, k, i) in survey_types" :key="`survey-option-${i}`"
                                                    :value="k">{{ survey.name }}
                                            </option>
                                        </Field>
                                        <ErrorMessage name="type" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3 bg-gray py-3" v-show="showQuestionOptions">
                                <b-col sm="12">
                                    <h5>{{ $t('input.question_options') }}</h5>
                                </b-col>

                                <b-col sm="12">
                                    <b-row v-for="(option, idx) in options" :key="`options-${idx}`" class="mt-3">
                                        <b-col sm="5">
                                            <div class="form-group">
                                                <label>Option {{ idx + 1 }}</label>
                                                <input type="text" v-model="option.name"
                                                       :class="`form-control mb-0`"/>
                                            </div>
                                        </b-col>

                                        <b-col sm="5" v-if="fillArabic">
                                            <div class="form-group">
                                                <label>Option {{ idx + 1 }} (Arabic)</label>
                                                <input type="text" v-model="option.name_arabic"
                                                       :class="`form-control mb-0`"/>
                                            </div>
                                        </b-col>

                                        <b-col>
                                            <div class="form-group">
                                                <label>&nbsp;</label>
                                                <div class="form-control no-border">
                                                    <b-btn variant="outline-primary"
                                                           @click="addOption(idx)">
                                                        <i class="ri-add-line p-0"></i>
                                                    </b-btn>
                                                    <b-btn v-if="options.length > 1" variant="outline-danger"
                                                           @click="removeOption(idx)"
                                                           class="ml-2"><i
                                                        class="ri-delete-bin-2-line p-0"></i></b-btn>
                                                </div>
                                            </div>
                                        </b-col>

                                        <b-col sm="12" v-if="optionsError">
                                            <span class="text-danger">{{ optionsError }}</span>
                                        </b-col>
                                    </b-row>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-checkbox v-model="has_parent" class="custom-checkbox-color"
                                                name="check_required" inline>
                                        {{ $t('input.has_parent') }}
                                    </b-checkbox>
                                </b-col>

                                <b-col>
                                    <b-checkbox v-model="required" class="custom-checkbox-color"
                                                name="check_required" inline>
                                        {{ $t('input.make_required') }}
                                    </b-checkbox>
                                </b-col>

                                <b-col>
                                    <b-checkbox v-model="post_order" class="custom-checkbox-color"
                                                name="check_post_order" inline>
                                        {{ $t('input.post_order') }}
                                    </b-checkbox>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3 bg-gray py-3" v-if="has_parent">
                                <b-col sm="12">
                                    <h5>{{ $t('input.parent') }}</h5>
                                </b-col>

                                <b-col sm="6" class="mt-3">
                                    <div class="form-group">
                                        <label for="parent_survey">{{ $t('input.parent_survey') }}</label>
                                        <Field as="select" name="parent_survey" id="parent_survey"
                                               :class="`form-control mb-0`"
                                               :validateOnInput="true">
                                            <option value="">{{ $t('input.parent_survey_placeholder') }}</option>
                                            <option v-for="(survey, i) in surveys_with_options"
                                                    :key="`survey-${survey.id}`" :value="survey.id">{{ survey.title }}
                                            </option>
                                        </Field>
                                        <ErrorMessage name="parent_survey" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6" class="mt-3" v-show="parent_survey">
                                    <div class="form-group">
                                        <label for="parent_survey_options">{{
                                                $t('input.parent_survey_options')
                                            }}</label>
                                        <Field as="select" name="parent" id="parent_survey_options"
                                               :class="`form-control mb-0`"
                                               :validateOnInput="true">
                                            <option value="">{{
                                                    $t('input.parent_survey_options_placeholder')
                                                }}
                                            </option>
                                            <option v-for="(option, i) in getParentSurveyOptions" :key="option.id"
                                                    :value="option.id">{{ option.name }}
                                            </option>
                                        </Field>
                                        <ErrorMessage name="parent" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col sm="12">
                                    <h5>{{ $t('input.require_for_tickets') }}</h5>
                                </b-col>

                                <b-col sm="3" v-for="(ticket, i) in tickets" class="mt-2">
                                    <b-checkbox :key="ticket.id" v-model="selectedTickets[i]"
                                                class="custom-checkbox-color"
                                                :value="ticket.id"
                                                name="ticket_ids" inline>
                                        {{ ticket.title }}
                                    </b-checkbox>
                                </b-col>

                                <b-col sm="12">
                                    <div v-show="ticketError">
                                        <span class="text-danger">{{ ticketError }}</span>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-button type="submit" :disabled="isSubmitting" variant="primary">
                                        {{ $t(`button.${editMode ? 'update' : 'create'}`) }}
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
import {Field, FieldArray, ErrorMessage, useForm, useField} from 'vee-validate';
import {createSurveySchema} from '../../../../Shared/components/helpers/Validators';
import {router} from "@inertiajs/vue3";
import {computed, onMounted, ref} from "vue";

const props = defineProps({
    code: Number,
    message: String,
    editMode: Boolean,
    event: {},
    survey: {},
    tickets: [],
    survey_types: [],
    surveys_with_options: []
});

const fillArabic = ref(false);
const selectedTickets = ref([]);
const options = ref([
    {name: '', name_arabic: ''}
])
const ticketError = ref('');
const optionsError = ref('');

const initialValues = props.editMode ? {
    ...props.survey,
    post_order: !!props.survey.post_order,
    has_parent: !!props.survey.has_parent,
    required: !!props.survey.required,
    parent_survey: props.survey.survey_parent[0] ? props.survey.survey_parent[0].event_surveys_id : '',
    parent: props.survey.survey_parent[0] ? props.survey.survey_parent[0].id : '',
    type: props.survey.type.toString()
} : {
    type: 1,
    required: false,
    post_order: false,
    has_parent: false,
    parent_survey: '',
    parent: ''
}

onMounted(() => {
    if (props.editMode) {
        let y = [];
        props.survey.tickets.map(x => x.id).forEach(x => {
            const ticketIndex = props.tickets.findIndex(ticket => ticket.id === x);
            y[ticketIndex] = x;
        })

        selectedTickets.value = y;

        if (props.survey.title_arabic) {
            fillArabic.value = true;
        }

        if(props.survey.survey_options.length > 0) {
            options.value = props.survey.survey_options.map(x => ({
                name: x.name,
                name_arabic: x.name_arabic
            }))
        }
    }
});

const {handleSubmit, isSubmitting, setFieldValue} = useForm({
    initialValues,
    validationSchema: createSurveySchema,
});

const {value: type} = useField("type");
const {value: required} = useField("required");
const {value: post_order} = useField("post_order");
const {value: has_parent} = useField("has_parent");
const {value: parent_survey} = useField("parent_survey");

const showQuestionOptions = computed(() => {
    return ['5', '6', '7', '8'].includes(type.value)
});

const addOption = index => {
    options.value.splice(index + 1, 0, {name: '', name_arabic: ''});
}

const removeOption = index => {
    options.value.splice(index, 1)
}

const getParentSurveyOptions = computed(() => {
    return props.surveys_with_options.find(x => x.id === parent_survey.value)?.survey_options || []
})

const onSubmit = handleSubmit((values) => {
    optionsError.value = '';
    ticketError.value = '';

    if (showQuestionOptions.value && options.value[0].name === '') {
        return optionsError.value = 'Options are required when this question type is selected';
    }

    if (selectedTickets.value.length === 0) {
        return ticketError.value = 'Please choose a ticket';
    }
    if (showQuestionOptions.value) {
        if (!fillArabic) {
            values.options = options.value.map(x => x.name_arabic = '');
        } else {
            values.options = options.value;
        }
    }

    values.tickets = selectedTickets.value.filter(x => !!x);
    values.event_id = props.event.id;

    props.editMode
        ? router.patch(`/event/${props.event.id}/surveys/${props.survey.id}`, values)
        : router.post(`/event/${props.event.id}/surveys`, values)
})
</script>

<style scoped>
.no-border {
    border: 0;
    background: none;
}
</style>
