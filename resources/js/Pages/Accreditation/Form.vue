<script setup>
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import {computed, onMounted, reactive, ref, watch} from "vue";
import {router} from "@inertiajs/vue3";
import {ErrorMessage, Field, FieldArray, useField, useForm} from "vee-validate";
import {accreditationFormSchema} from "../../Shared/components/helpers/Validators.js";
import VueCountryCode from "vue-country-code";
import VuePhoneNumberInput from 'vue-phone-number-input';
import 'vue-phone-number-input/dist/vue-phone-number-input.css';
import debounce from "lodash.debounce";


const props = defineProps({
    accessLevel: Object,
    status: Boolean,
    lang: String,
    surveys: Array,
    reference: String,
    answers: Array,
    countries: Array,
    countryCodes: Array,
    email: String,
    isProccssing: Boolean,
    attendee: Object
})

const formData = reactive({});
const buttonDisabled = ref(false)


const isProccssing = false

onMounted(() => {
    document.querySelector('title').textContent = `${props.accessLevel.title} - ${props.accessLevel?.event?.organiser?.name}`
    props.surveys.forEach(survey => {
        formData[survey.id] = {
            type: survey.type,
            answer: survey.type === '8' ? [] : '',
            id: survey.id,
            question: survey.title,
            parent_index: survey.parent_index,
            parent_answer: survey.parent_answer,
            required: survey.parent_index ? 0 : survey.required,
        }
    })
});

const defaultAnswer = (type, inputType) => {
  if (type === '8' || type === '7') return [];
  if (['1', '2', '10'].includes(type) && inputType === 'number') return null;
  return '';
}


const {handleSubmit, isSubmitting} = useForm({
    initialValues: (props.reference && props.answers)
        ? {
            surveys: props.surveys.filter(x => !x.private).map(x => {
                const answer = props.answers.find(y => y.question === x.title)?.answer || '';

                return {
                    type: x.type,
                    answer: answer || defaultAnswer(x.type, x.input_type),
                    title: x.title,
                    title_arabic: x.title_arabic,
                    id: x.id,
                    question: x.title,
                    is_private: x.private,
                    parent_index: x.parent_index,
                    parent_answer: x.parent_answer,
                    options: x.options,
                    required: x.parent_index ? 0 : x.required,
                    disabled: x.title === 'Email Address' && props.email,
                    country_code: x.country_code ?  x.country_code  : '',
                    input_type: x.input_type,
                    input_length: x.input_length
                }
            })
        }
        : {
            surveys: props.surveys.filter(x => !x.private).map(x => ({
                type: x.type,
                answer: x.title === 'Email Address' ? props.email : defaultAnswer(x.type, x.input_type),
                title: x.title,
                title_arabic: x.title_arabic,
                id: x.id,
                question: x.title,
                is_private: x.private,
                parent_index: x.parent_index,
                parent_answer: x.parent_answer,
                options: x.options,
                required: x.parent_index ? 0 : x.required,
                disabled: x.title === 'Email Address' && props.email,
                country_code: x.country_code ?  x.country_code  : '',
                input_type: x.input_type,
                input_length: x.input_length
            }))
        },
    validationSchema: accreditationFormSchema(props.lang),
});

const {value: surveysFields} = useField('surveys');

const emailAddress = computed(() => {
    return surveysFields.value.find(x => x.title === 'Email Address')?.answer;
});

watch(emailAddress, debounce(async (value) => {
    try {
        router.post('/form-emails', {
            email: value,
            event_id: props.accessLevel.event_id,
            access_level_id: props.accessLevel.id,
            organiser_id: props.accessLevel?.event?.organiser?.id
        });
    } catch (e) {
        console.log(e);
    }
}, 3000));

const onSubmit = handleSubmit(values => {
    console.log(values)
    buttonDisabled.value = true;
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
    };

    router.post(`/form/${props.accessLevel.event_id}/${props.accessLevel.id}/submit`, data, {
        forceFormData: true,
        preserveScroll: true
    })
});
</script>

<script>
import EmptyLayout from '../../Shared/Layout/EmptyLayout.vue';

export default {
    layout: EmptyLayout
}
</script>


<style>
input.form-control, .form-control {
    line-height: 27px !important;
}

form-control:disabled, .form-control[readonly] {
    opacity: 1;
}
select {
    height: 46px !important;
}

.form-control {
    display: block;
    width: 100%;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    background-color: #fff0;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}
.mobile_number {
    display: flex;
}

.mobile_number select{
    width: 30%;
    height: auto;
}

.mobile_number input{
    width: 80%;
    border-top-right-radius: 0px;
    border-bottom-right-radius: 0px;
}

select {
    height: 48px;
    height: 46px !important;
    border-radius: 6px !important;
}
select option {
  margin: 40px;
  background: rgba(0, 0, 0, 0.3);
  color: #fff;
  text-shadow: 0 1px 0 rgba(0, 0, 0, 0.4);
}

@media screen and (max-width: 600px) {
    .vag {
        width: 100% !important;
    }
}

</style>

<template>
    <b-container fluid class="mx-auto vag" style="width: 80%; margin-top: -77px;" :style="{
                    backgroundColor: accessLevel?.page_design?.bg_type === 'color' && accessLevel?.page_design?.bg_color,
                    backgroundImage: accessLevel?.page_design?.bg_type === 'image'? 'url(' + accessLevel?.page_design?.bg_image + ')' : '',
                    backgroundSize: 'cover',
                    backgroundPositionX: '50%',
                    backgroundPositionY: '50%',
                    paddingTop: '100px',
                    paddingBottom: '100px',
                    minHeight: '100vh',
                    textAlign: lang != 'english' ? 'right' : '',
                    direction:  lang != 'english' ? 'rtl' : '',
                }">
        <div class="row no-gutters accreditation-form" :class="{'rtl text-right': lang === 'arabic'}">
            <div class="col-12 align-self-center">
                <div class="bg-div" :style="{backgroundColor: accessLevel?.page_design?.form_bg_color}">
                    <div class="text-center p-5" v-if="!status">
                        <h3 style="color: white" class="p-5" v-if="lang === 'english'">Link is not active/Quantity Exceeded.</h3>
                        <h3 style="color: white" class="p-5" v-else>الحدث غير نشط. يرجى الاتصال بالمسؤول</h3>
                    </div>

                    <template v-else>
                        <div class="text-center" style="">
                            <img class="my-3 text-center img-fluid logo"
                                 :src="accessLevel?.page_design?.logo || accessLevel?.event?.event_image_url" alt="">
                        </div>

                        <form @submit.prevent="onSubmit" class="mx-5">
                            <div class="col-12 m-0 p-0">
                                <div class="row m-0">

                                    <FieldArray name="surveys" v-slot="{ fields, insert, remove, swap }">
                                        <template v-for="(field, idx) in fields" :key="field.key">
                                            <b-col :sm="field.value.type !== '10' ? '6' : '12'" class="pb-2" v-if="!field.value.parent_index">
                                                <div class="form-group mb-0">
                                                    <label :for="`surveys-${idx}`" v-if="field.value.type !== '10'" :style="{ color: accessLevel?.page_design?.font_color}">{{
                                                            lang === 'arabic' ? field.value.title_arabic : field.value.title
                                                        }}:</label>

                                                    <div class="mobile_number"  v-if="field.value.type === '12'">
                                                        <Field as="select" :name="`surveys[${idx}].country_code`" :class="`mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color }">
                                                          <option v-for="code in countryCodes" :key="code.id" :value="'+' + code.code">{{ lang === 'english' ? code.name_en : code.name_ar }}</option>
                                                        </Field>
                                                        <Field type="number"
                                                           :name="`surveys[${idx}].answer`"
                                                           :disabled="!!attendee"
                                                           :id="`surveys-${idx}`"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color }" :validateOnInput="true"/>

                                                    </div>

                                                    <Field type="text"
                                                           v-if="field.value.type === '1'"
                                                           :name="`surveys[${idx}].answer`"
                                                           :disabled="!!attendee"
                                                           :id="`surveys-${idx}`"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color }" :validateOnInput="true"/>

                                                    <Field as="textarea"
                                                           v-if="field.value.type === '2'"
                                                           rows="5"
                                                           :name="`surveys[${idx}].answer`"
                                                           :disabled="!!attendee"
                                                           :id="`surveys-${idx}`"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}" :validateOnInput="true"/>

                                                    <Field type="date"
                                                           v-else-if="field.value.type === '3'"
                                                           :name="`surveys[${idx}].answer`"
                                                           :disabled="!!attendee"
                                                           :id="`surveys-${idx}`"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}" :validateOnInput="true"/>

                                                    <Field type="file" title=" "
                                                           v-else-if="field.value.type === '4'"
                                                           :name="`surveys[${idx}].answer`"
                                                           :disabled="!!attendee"
                                                           :id="`surveys-${idx}`"
                                                           accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}" :validateOnInput="true"/>

                                                    <Field type="email"
                                                           v-else-if="field.value.type === '5'"
                                                           :name="`surveys[${idx}].answer`"
                                                           :id="`surveys-${idx}`"
                                                           :disabled="field.value.disabled || !!attendee"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}" :validateOnInput="true"/>

                                                    <Field as="select"
                                                           v-else-if="field.value.type === '6'"
                                                           :name="`surveys[${idx}].answer`"
                                                           :disabled="!!attendee"
                                                           :id="`surveys-${idx}`"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}" :validateOnInput="true">
                                                        <option v-for="option in field.value.options"
                                                                :key="`${field.value.id}-${option.name}`"
                                                                :value="lang === 'arabic' ? option.name_arabic : option.name">
                                                            {{ lang === 'arabic' ? option.name_arabic : option.name }}
                                                        </option>
                                                    </Field>

                                                    <vue-select v-model="field.value.answer"
                                                                v-else-if="field.value.type === '7'"
                                                                :class="{'text-right': lang === 'arabic'}"
                                                                :disabled="!!attendee"
                                                                class="form-control mb-0"
                                                                :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}"
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
                                                               :disabled="!!attendee"
                                                               :class="`checkbox custom-checkbox-color`"
                                                               :validateOnInput="true"
                                                               :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}"
                                                               :value="lang === 'arabic' ? option.name_arabic : option.name">
                                                            <label class="mr-3">
                                                                <input type="checkbox" :name="`surveys[${idx}].answer`"
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
                                                               :disabled="!!attendee"
                                                               v-slot="{field: boxField}"
                                                               :class="`checkbox custom-radio-color`"
                                                               :validateOnInput="true"
                                                               :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}"
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

                                                    <h5 v-if="field.value.type === '10'" class="mt-5 mb-2" :style="{color: accessLevel?.page_design?.font_color }">{{
                                                            lang === 'arabic' ? field.value.title_arabic : field.value.title
                                                        }}</h5>

                                                    <Field as="select"
                                                           v-else-if="field.value.type === '11'"
                                                           :name="`surveys[${idx}].answer`"
                                                           :id="`surveys-${idx}`"
                                                           :disabled="!!attendee"
                                                           :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}"
                                                           :class="`form-control mb-0`" :validateOnInput="true">
                                                        <option v-for="country in countries"
                                                                :key="`${field.value.id}-${country.country}`"
                                                                :value="lang === 'arabic' ?  country.country_ar : country.country">
                                                            {{ country.country }}
                                                        </option>
                                                    </Field>


                                                    <ErrorMessage :name="`surveys[${idx}].answer`" class="text-danger"/>
                                                </div>
                                            </b-col>

                                            <b-col :sm="field.value.type !== '10' ? '6' : '12'" class="pb-2" v-else-if="field.value.parent_index && (surveysFields[field.value.parent_index]?.answer?.includes(field.value.parent_answer?.split(', ')[lang === 'english' ? 0 : 1]))">
                                                <div class="form-group mb-0">
                                                    <label :for="`surveys-${idx}`" v-if="field.value.type !== '10'" :style="{ color: accessLevel?.page_design?.font_color}">{{
                                                            lang === 'arabic' ? field.value.title_arabic : field.value.title
                                                        }}:</label>

                                                    <div class="mobile_number"  v-if="field.value.type === '12'">
                                                        <b-select :name="`surveys[${idx}].country_code`"  :class="`mb-0`" :disabled="!!attendee" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color }" >
                                                            <b-select-option v-for="code in countryCodes" :key="code.id" :value="'+' + code.code"> {{ lang === 'english' ? code.name_en : code.name_ar}}</b-select-option>
                                                        </b-select>
                                                        <Field type="number"
                                                           :name="`surveys[${idx}].answer`"
                                                           :disabled="!!attendee"
                                                           :id="`surveys-${idx}`"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color }" :validateOnInput="true"/>

                                                    </div>

                                                    <Field type="text"
                                                           v-if="field.value.type === '1'"
                                                           :name="`surveys[${idx}].answer`"
                                                           :disabled="!!attendee"
                                                           :id="`surveys-${idx}`"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color }" :validateOnInput="true"/>

                                                    <Field as="textarea"
                                                           v-if="field.value.type === '2'"
                                                           rows="5"
                                                           :name="`surveys[${idx}].answer`"
                                                           :disabled="!!attendee"
                                                           :id="`surveys-${idx}`"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}" :validateOnInput="true"/>

                                                    <Field type="date"
                                                           v-else-if="field.value.type === '3'"
                                                           :name="`surveys[${idx}].answer`"
                                                           :disabled="!!attendee"
                                                           :id="`surveys-${idx}`"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}" :validateOnInput="true"/>

                                                    <Field type="file" title=" "
                                                           v-else-if="field.value.type === '4'"
                                                           :name="`surveys[${idx}].answer`"
                                                           :disabled="!!attendee"
                                                           accept="image/jpeg,image/gif,image/png,application/pdf,image/x-eps"
                                                           :id="`surveys-${idx}`"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}" :validateOnInput="true"/>

                                                    <Field type="email"
                                                           v-else-if="field.value.type === '5'"
                                                           :name="`surveys[${idx}].answer`"
                                                           :id="`surveys-${idx}`"
                                                           :disabled="field.value.disabled || !!attendee"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}" :validateOnInput="true"/>

                                                    <Field as="select"
                                                           v-else-if="field.value.type === '6'"
                                                           :name="`surveys[${idx}].answer`"
                                                           :disabled="!!attendee"
                                                           :id="`surveys-${idx}`"
                                                           :class="`form-control mb-0`" :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}" :validateOnInput="true">
                                                        <option v-for="option in field.value.options"
                                                                :key="`${field.value.id}-${option.name}`"
                                                                :value="lang === 'arabic' ? option.name_arabic : option.name">
                                                            {{ lang === 'arabic' ? option.name_arabic : option.name }}
                                                        </option>
                                                    </Field>

                                                    <vue-select v-model="field.value.answer"
                                                                v-else-if="field.value.type === '7'"
                                                                :class="{'text-right': lang === 'arabic'}"
                                                                :disabled="!!attendee"
                                                                class="form-control mb-0"
                                                                :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}"
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
                                                               :disabled="!!attendee"
                                                               :validateOnInput="true"
                                                               :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}"
                                                               :value="lang === 'arabic' ? option.name_arabic : option.name">
                                                            <label class="mr-3">
                                                                <input type="checkbox" :name="`surveys[${idx}].answer`"
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
                                                               :disabled="!!attendee"
                                                               :class="`checkbox custom-radio-color`"
                                                               :validateOnInput="true"
                                                               :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}"
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

                                                    <h5 v-if="field.value.type === '10'" class="mt-5 mb-2" :style="{color: accessLevel?.page_design?.font_color }">{{
                                                            lang === 'arabic' ? field.value.title_arabic : field.value.title
                                                        }}</h5>

                                                    <Field as="select"
                                                           v-else-if="field.value.type === '11'"
                                                           :name="`surveys[${idx}].answer`"
                                                           :id="`surveys-${idx}`"
                                                           :disabled="!!attendee"
                                                           :style="{backgroundColor: accessLevel?.page_design?.field_color, color: accessLevel?.page_design?.font_color}"
                                                           :class="`form-control mb-0`" :validateOnInput="true">
                                                        <option v-for="country in countries"
                                                                :key="`${field.value.id}-${country.country}`"
                                                                :value="lang === 'arabic' ?  country.country_ar : country.country">
                                                            {{ country.country }}
                                                        </option>
                                                    </Field>


                                                    <ErrorMessage :name="`surveys[${idx}].answer`" class="text-danger"/>
                                                </div>
                                            </b-col>
                                        </template>
                                    </FieldArray>
                                </div>
                            </div>

                            <div class="col-12 pb-5 text-center" style="margin-top: 40px;">
                                <b-btn type="submit" size="lg" class="px-5 py-2" :disabled="buttonDisabled" v-if="!isSubmitting"
                                       :style="{border:'none', backgroundColor: accessLevel?.page_design?.btn_color_code, color: accessLevel?.page_design?.btn_font_color_code}">
                                    {{
                                        lang === 'english' ? accessLevel?.page_design?.form_btn_value : accessLevel?.page_design?.form_btn_value_ar
                                    }}
                                </b-btn>
                            </div>
                        </form>

                        <div v-if="accessLevel?.page_design?.footer_logo" class="text-center">
                            <img :src="accessLevel?.page_design?.footer_logo" alt="" class="img-fluid" :style="`height: ${accessLevel?.page_design?.footer_logo_height}px; margin-bottom: 15px; text-align: center`">
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </b-container>
</template>

<style scoped>
.bg-div {
    height: 100%;
    width: 100%;
    position: relative;
    border-radius: 10px;
}

.bg-div :deep(ul) {
    list-style: unset;
}

img.logo {
    height: 100px;
}

.lang-container {
    position: absolute;
    top: 15px;
    right: 15px;
}

p :deep(.ql-direction-rtl) {
    direction: rtl;
    text-align: right;
}
</style>
