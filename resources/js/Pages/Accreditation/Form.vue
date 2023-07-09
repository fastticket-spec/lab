<script setup>
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import {onMounted, reactive, ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    accessLevel: Object,
    lang: String,
    surveys: Array
})

const formData = reactive({});

onMounted(() => {
    props.surveys.forEach(survey => {
        formData[survey.id] = {type: survey.type, answer: survey.type === '8' ? [] : '', id: survey.id, question: survey.title}
    })
})

const onSubmit = () => {
    const fData= Object.keys(formData).map(d => {
        let keyVal = formData[d];
        if (formData[d].type === '7') {
            keyVal.answer = keyVal.answer.map(x => (`${x.name}${x.name_arabic ? ` (${x.name_arabic})` : ''}`));
        }
        return formData[d];
    });

    const data = {
        answers: fData,
        lang: props.lang
    };

    console.log({data})

    router.post(`/form/${props.accessLevel.event_id}/${props.accessLevel.id}/submit`, data, {
        forceFormData: true,
        preserveScroll: true
    })
}
</script>

<script>
import EmptyLayout from '../../Shared/Layout/EmptyLayout.vue';

export default {
    layout: EmptyLayout
}
</script>

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
                <div class="bg-div bg-white" style="background-color: #7d4f4c61 !important;">
                    <div class="text-center">
                        <img class="my-3 text-center img-fluid logo" :src="accessLevel?.event?.event_image_url" alt="">
                    </div>

                    <form @submit.prevent="onSubmit" class="mx-5" v-if="Object.keys(formData).length > 0">
                        <div class="row justify-content-md-right">
                            <div v-for="survey in surveys.filter(x => !x.private)" :key="survey.id" class="col  p-2" :class="survey.type === '10' ? 'col-md-12' : 'col-md-6'">
                                <label :for="survey.id" v-if="survey.type !== '10'">{{ lang === 'arabic' ? survey.title_arabic : survey.title }}:</label>
                                <template v-if="survey.type === '1'">
                                    <input v-model="formData[survey.id].answer" type="text" :class="{'text-right': lang === 'arabic'}" class="form-control" sm="6" :id="survey.id" :required="survey.required">
                                </template>

                                <template v-if="survey.type === '2'">
                                    <textarea v-model="formData[survey.id].answer" rows="5" :class="{'text-right': lang === 'arabic'}" class="form-control"  sm="6" :id="survey.id" :required="survey.required"/>
                                </template>

                                <template v-if="survey.type === '3'">
                                    <input v-model="formData[survey.id].answer" type="datetime-local" :class="{'text-right': lang === 'arabic'}" sm="6" class="form-control" :id="survey.id"
                                        :required="survey.required">
                                </template>

                                <template v-if="survey.type === '4'">
                                    <input type="file" @input="formData[survey.id].answer = $event.target.files[0]" :class="{'text-right': lang === 'arabic'}" sm="6" class="form-control" :id="survey.id" :required="survey.required">
                                </template>

                                <template v-if="survey.type === '5'">
                                    <input v-model="formData[survey.id].answer" type="email" :class="{'text-right': lang === 'arabic'}" class="form-control" sm="6" :id="survey.id" :required="survey.required">
                                </template>

                                <template v-if="survey.type === '6'">
                                    <select v-model="formData[survey.id].answer" :class="{'text-right': lang === 'arabic'}" class="form-control" :id="survey.id" sm="6" :required="survey.required">
                                        <option value=""></option>
                                        <option v-for="option in survey.options" :key="`${survey.id}-${option.name}`"
                                                :value="lang === 'arabic' ? option.name_arabic : option.name">{{ lang === 'arabic' ? option.name_arabic : option.name }}
                                        </option>
                                    </select>
                                </template>

                                <template v-if="survey.type === '7'">
                                    <vue-select v-model="formData[survey.id].answer" :class="{'text-right': lang === 'arabic'}" class="form-control mb-0" sm="6"
                                                :options="survey.options" :label="lang === 'arabic' ? 'name_arabic' : 'name'"
                                                multiple/>
                                </template>

                                <template v-if="survey.type === '8'">
                                    <br/>
                                    <b-checkbox v-model="formData[survey.id].answer" v-for="option in survey.options" :key="`${survey.id}-${option.name}`"
                                                class="custom-checkbox-color"
                                                :value="lang === 'arabic' ? option.name_arabic : option.name"
                                                :name="`check-button-for-${survey.id}`" inline>
                                        {{ lang === 'arabic' ? option.name_arabic : option.name }}
                                    </b-checkbox>
                                </template>

                                <template v-if="survey.type === '9'">
                                    <br/>
                                    <b-radio v-model="formData[survey.id].answer" v-for="option in survey.options" :key="`${survey.id}-${option.name}`"
                                            class="custom-radio-color"
                                            :value="lang === 'arabic' ? option.name_arabic : option.name"
                                            :name="`check-button-for-${survey.id}`" inline>
                                        {{ lang === 'arabic' ? option.name_arabic :  option.name }}
                                    </b-radio>
                                </template>

                                <template v-if="survey.type === '10'">
                                    <h5 class="mt-5 mb-2">{{ lang === 'arabic' ? survey.title_arabic : survey.title }}</h5>
                                </template>

                            </div>
                        </div>

                        <div class="py-4 text-center">
                            <b-btn type="submit" size="lg" class="px-5 py-2"
                                   :style="{border:'none', backgroundColor: accessLevel?.page_design?.btn_color_code, color: accessLevel?.page_design?.btn_font_color_code}">
                                {{ accessLevel?.page_design?.form_btn_value || 'Submit' }}

                            </b-btn>
                        </div>
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
