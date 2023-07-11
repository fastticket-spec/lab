<script setup>
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import {onMounted, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";
import {ErrorMessage, Field, FieldArray, useForm} from "vee-validate";
import {accreditationFormSchema} from "../../Shared/components/helpers/Validators.js";

const props = defineProps({
    accessLevel: Object,
    lang: String,
    surveys: Array
})

const formData = reactive({});

onMounted(() => {
    props.surveys.forEach(survey => {
        formData[survey.id] = {
            type: survey.type,
            answer: survey.type === '8' ? [] : '',
            id: survey.id,
            question: survey.title
        }
    })
});

const {handleSubmit, isSubmitting} = useForm({
    initialValues: {
        surveys: props.surveys.map(x => ({
            type: x.type,
            answer: (x.type === '8' || x.type === '7') ? null : '',
            title: x.title,
            title_arabic: x.title_arabic,
            id: x.id,
            question: x.title,
            is_private: x.private,
            options: x.options,
            required: x.required
        }))
    },
    validationSchema: accreditationFormSchema,
});

const onSubmit = handleSubmit(values => {
    const answers = values.surveys.map(d => {
        if (d.type === '7') {
            d.answer = d.answer.map(x => x.name);
        }
        return d;
    });

    const data = {
        answers,
        lang: props.lang
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
    background: #f2f2f2;
    background-color: #f2f2f2;
}

.form-control {
    display: block;
    width: 100%;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #ffffff;
    background-color: #fff0;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
}

.bg-white {
    background-color: #7d4f4c61 !important;
}

label {
    color: #ffffff;
}
</style>

<template>
    <b-container fluid :style="{
                    backgroundColor: accessLevel?.page_design?.bg_type === 'color' && accessLevel?.page_design?.bg_color,
                    backgroundImage: accessLevel?.page_design?.bg_type === 'image'? 'url(' + accessLevel?.page_design?.bg_image + ')' : '',
                    backgroundSize: 'cover',
                    backgroundPositionX: '50%',
                    backgroundPositionY: '50%',
                    paddingTop: '100px',
                    paddingBottom: '100px',
                    paddingLeft: '200px',
                    paddingRight: '200px',
                    minHeight: '100vh'
                }">
        <div class="row no-gutters accreditation-form" :class="{'rtl text-right': lang === 'arabic'}">
            <div class="col-12 align-self-center">
                <div class="bg-div bg-white">
                    <div class="text-center">
                        <img class="my-3 text-center img-fluid logo" :src="accessLevel?.event?.event_image_url" alt="">
                    </div>

                    <form @submit.prevent="onSubmit" class="mx-5">
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
                                                       :class="`form-control mb-0`" :validateOnInput="true"/>

                                                <Field as="select"
                                                       v-else-if="field.value.type === '6'"
                                                       :name="`surveys[${idx}].answer`"
                                                       :id="`surveys-${idx}`"
                                                       :class="`form-control mb-0`" :validateOnInput="true">
                                                    <option v-for="option in field.value.options"
                                                            :key="`${field.value.id}-${option.name}`"
                                                            :value="lang === 'arabic' ? option.name_arabic : option.name">
                                                        {{ lang === 'arabic' ? option.name_arabic : option.name }}
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
                                                            <input type="checkbox" :name="`surveys[${idx}].answer`"
                                                                   v-bind="boxField"
                                                                   :value="lang === 'arabic' ? option.name_arabic : option.name"/>
                                                            {{ lang === 'arabic' ? option.name_arabic : option.name }}
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
                                                            {{ lang === 'arabic' ? option.name_arabic : option.name }}
                                                        </label>
                                                    </Field>
                                                </template>

                                                <h5 v-if="field.value.type === '10'" class="mt-5 mb-2">{{
                                                        lang === 'arabic' ? field.value.title_arabic : field.value.title
                                                    }}</h5>


                                                <ErrorMessage :name="`surveys[${idx}].answer`" class="text-danger"/>
                                            </div>
                                        </b-col>
                                    </template>
                                </FieldArray>
                            </div>
                        </div>
                        <div class="col-12 pb-5 text-center">
                            <b-btn type="submit" size="lg" class="px-5 py-2"
                                   :style="{border:'none', backgroundColor: accessLevel?.page_design?.btn_color_code, color: accessLevel?.page_design?.btn_font_color_code}">
                                {{ accessLevel?.page_design?.form_btn_value || 'Submit' }}
                            </b-btn>
º                        </div>
                    </form>
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
