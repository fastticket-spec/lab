<script setup>
import {onMounted, ref} from "vue";
import {router} from "@inertiajs/vue3";
import Form from "./Form.vue";
import {ErrorMessage, Field, useForm} from "vee-validate";
import * as yup from 'yup';
import axios from "axios";

const props = defineProps({
    accessLevel: Object,
    status: Boolean,
    reference: String
})

const lang = ref('english');

onMounted(() => {
    document.querySelector('title').textContent = `${props.accessLevel.title} - ${props.accessLevel?.event?.organiser?.name}`
})

const {handleSubmit, isSubmitting} = useForm({
    initialValues: {email: '', registration_number: ''},
    validationSchema: yup.object({
        email: yup.string().required().email(),
        registration_number: yup.string().required()
    })
});


const goToForm = () => {
    router.get(`/form/${props.accessLevel.id}?ref=${props.reference || ''}&lang=${lang.value}`)
}

const loginStatus = ref(true);

const onSubmit = handleSubmit(async values => {
    try {
        const {data: {status}} = await axios.post(`/form/${props.accessLevel.id}/accreditation-login`, values);
        if (status) {
            goToForm();
        } else {
            loginStatus.value = false;
        }
    } catch (e) {
        console.log(e);
    }
})
</script>

<script>
import EmptyLayout from '../../Shared/Layout/EmptyLayout.vue';

export default {
    layout: EmptyLayout
}
</script>

<style>
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

.bg-white {
//background-color: #7d4f4c61 !important;
}

label {
    color: #ffffff;
}

</style>

<template>
    <b-container fluid class="mx-auto vag" style="width: 80%;" :style="{
                    backgroundColor: accessLevel?.page_design?.bg_type === 'color' && accessLevel?.page_design?.bg_color,
                    backgroundImage: accessLevel?.page_design?.bg_type === 'image'? 'url(' + accessLevel?.page_design?.bg_image + ')' : '',
                    backgroundSize: 'cover',
                    backgroundPositionX: '50%',
                    backgroundPositionY: '50%',
                    paddingTop: '100px',
                    paddingBottom: '100px',
                    minHeight: '100vh'
                }">
        <div class="row no-gutters">
            <div class="col-12 align-self-center">
                <div class="bg-div" :style="{backgroundColor: accessLevel?.page_design?.form_bg_color}">
                    <div class="text-center p-5" v-if="!status">
                        <h3 class="p-5" v-if="lang === 'english'">Link is not active.</h3>
                        <h3 class="p-5" v-else>الحدث غير نشط. يرجى الاتصال بالمسؤول</h3>
                    </div>

                    <template v-else>
                        <div class="text-center">
                            <img class="my-3 text-center img-fluid logo"
                                 :src="accessLevel?.page_design?.logo || accessLevel?.event?.event_image_url" alt="">
                        </div>

                        <p class="pt-3 px-4" :class="{rtl: lang === 'arabic'}"
                           v-html="lang === 'english' ? accessLevel?.general_settings?.description : accessLevel?.general_settings?.description_arabic"/>

                        <div v-if="accessLevel.registration">
                            <h5 class="text-center" v-if="lang === 'english'">Your registration number will be found in
                                your invitation email.</h5>
                            <h5 class="text-center" v-else>سيتم العثور على رقم التسجيل الخاص بك في رسالة الدعوة الخاصة بك.</h5>
                            <form @submit.prevent="onSubmit" v-if="loginStatus" class="mx-5">
                                <div class="row reg-form">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="reg_no">Registration number</label>
                                            <Field type="text"
                                                   name="registration_number"
                                                   id="reg_no"
                                                   placeholder="Enter your registration number"
                                                   class="form-control mb-0" :validateOnInput="true"/>
                                            <ErrorMessage name="registration_number" class="text-danger"/>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email address</label>
                                            <Field type="email"
                                                   name="email"
                                                   id="email"
                                                   placeholder="Enter your email adderss"
                                                   class="form-control mb-0" :validateOnInput="true"/>
                                            <ErrorMessage name="email" class="text-danger"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="py-4 text-center">
                                    <b-btn type="submit" size="lg" class="px-5 py-2"
                                           :style="{border:'none', backgroundColor: accessLevel?.page_design?.btn_color_code, color: accessLevel?.page_design?.btn_font_color_code}">
                                        {{ accessLevel?.page_design?.register_btn_value || 'Register' }}
                                    </b-btn>
                                </div>
                            </form>

                            <div class="d-flex flex-column align-items-center" v-else>
                                <h4 class="mt-4 text-center" v-if="lang === 'english'">Invalid Confirmation Code or
                                    Email, Please try again</h4>
                                <h4 class="mt-4 text-center" v-else>رمز التأكيد أو البريد الإلكتروني غير صالح، يرجى المحاولة مرة أخرى.</h4>

                                <div>
                                    <b-btn @click="loginStatus = true" size="lg" class="mt-4 px-5 py-2 text-center"
                                           :style="{border:'none', backgroundColor: accessLevel?.page_design?.btn_color_code, color: accessLevel?.page_design?.btn_font_color_code}">
                                        Go Back
                                    </b-btn>
                                </div>
                            </div>
                        </div>

                        <div class="py-4 text-center" v-else>
                            <b-btn @click="goToForm" size="lg" class="px-5 py-2"
                                   :style="{border:'none', backgroundColor: accessLevel?.page_design?.btn_color_code, color: accessLevel?.page_design?.btn_font_color_code}">
                                {{ accessLevel?.page_design?.register_btn_value || 'Register' }}
                            </b-btn>
                        </div>
                    </template>

                    <div class="lang-container">
                        <select v-model="lang">
                            <option value="english">English</option>
                            <option value="arabic">Arabic</option>
                        </select>
                    </div>
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
