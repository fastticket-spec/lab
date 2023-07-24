<script setup>
import {reactive, ref, watch} from "vue";
import {router} from "@inertiajs/vue3";
import {Form, Field, ErrorMessage, useForm, useField} from 'vee-validate';
import {createEventSchema} from '../../../Shared/components/helpers/Validators'
import {QuillEditor} from '@vueup/vue-quill';
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import Cropper from "../../../Shared/components/core/images/Cropper.vue";

const props = defineProps({
    code: Number,
    message: String,
    editMode: Boolean,
    event: {},
    errors: Object,
});

const showArabicInputs = ref(true)

const initialValues = props.editMode ? props.event : {}

const {handleSubmit, isSubmitting, setFieldValue} = useForm({
    initialValues,
    validationSchema: createEventSchema,
});

const {value: description} = useField("description");
const {value: description_arabic} = useField("description_arabic");
const {value: eventImageUpload} = useField("event_image_upload");
const {value: eventBannerUpload} = useField("event_banner_upload");

const selectedImageType = reactive({
    type: '',
    croppedImage: '',
    image: null,
    aspectRatio: 1
})

const showCropperModal = ref(false);

watch(eventBannerUpload, (newVal) => {
    if (newVal instanceof File) {
        selectedImageType.type = 'event_banner_upload'
        selectedImageType.image = newVal;
        selectedImageType.aspectRatio = 3;
        showCropperModal.value = true;
    }
})

const onCropped = async () => {
    setFieldValue(selectedImageType.type, selectedImageType.croppedImage)
    showCropperModal.value = false
}

const onSubmit = handleSubmit(async (values, actions) => {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const returnUrl = urlParams.get('return')

    if (props.editMode) {
        delete values.event_image;
        delete values.event_attachment;

        if (returnUrl) {
            values.return_url = returnUrl
        }
    }

    props.editMode
        ? router.post(`/events/${props.event.id}/update`, values)
        : router.post('/events', values)
});
</script>

<template>
    <b-container fluid class="event-form">
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ editMode ? $t('event.edit') : $t('event.create') }}</h4>
                    </template>
                    <template v-slot:body>
                        <form class="mt-4" @submit="onSubmit">
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
                                        <label for="titleInput">{{ $t('input.title') }}</label>
                                        <Field type="text" name="title" id="titleInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.title')" :validateOnInput="true"/>
                                        <ErrorMessage name="title" class="text-danger"/>
                                        <span class="text-danger" v-if="errors.title">{{ errors.title }}</span>
                                    </div>
                                </b-col>

                                <b-col sm="6" v-if="showArabicInputs">
                                    <div class="form-group">
                                        <label for="titleArabicInput">{{ $t('input.titleArabic') }}</label>
                                        <Field type="text" name="title_arabic" id="titleArabicInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.titleArabic')" :validateOnInput="true"/>
                                        <ErrorMessage name="title_arabic" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="descriptionInput">{{ $t('input.description') }}</label>
                                        <quill-editor theme="snow" v-model:content="description"
                                                      content-type="html"></quill-editor>
                                        <ErrorMessage name="description" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6" v-if="showArabicInputs">
                                    <div class="form-group">
                                        <label for="descriptionArabicInput">{{ $t('input.descriptionArabic') }}</label>
                                        <quill-editor theme="snow" v-model:content="description_arabic"
                                                      content-type="html"></quill-editor>
                                        <ErrorMessage name="description_arabic" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>


                            <b-row :class="{'mt-3': (event?.event_image_url || event?.event_banner_url)}">
                                <b-col>
                                    <div v-if="event?.event_image_url">
                                        <b-img thumbnail :src="event.event_image_url"
                                               alt="Event Image"></b-img>
                                    </div>
                                </b-col>

                                <b-col>
                                    <div v-if="event?.event_banner_url">
                                        <b-img thumbnail :src="event.event_banner_url"
                                               alt="Event Banner"></b-img>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-form-group
                                        label-for="customFile"
                                    >
                                        <label for="eventImage">{{ $t('input.eventImage') }}</label>
                                        <Field id="eventImage" name="event_image_upload"
                                               v-slot="{ handleChange, handleBlur }">
                                            <b-form-file
                                                placeholder=""
                                                id="customFile"
                                                @change="handleChange"
                                                @blur="handleBlur"
                                            ></b-form-file>
                                        </Field>
                                        <span v-if="errors.event_image_upload"
                                              class="text-danger">{{ errors.event_image_upload }}</span>
                                    </b-form-group>
                                </b-col>

                                <b-col>
                                    <b-form-group
                                        label-for="eventBanner"
                                    >
                                        <label for="eventBanner">{{ $t('input.eventBanner') }}</label>
                                        <Field id="eventBanner" name="event_banner_upload"
                                               v-slot="{ handleChange, handleBlur }">
                                            <b-form-file
                                                placeholder=""
                                                id="eventBanner"
                                                @change="handleChange"
                                                @blur="handleBlur"
                                            ></b-form-file>
                                        </Field>
                                        <span v-if="errors.event_banner_upload"
                                              class="text-danger">{{ errors.event_banner_upload }}</span>
                                    </b-form-group>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-button :disabled="isSubmitting" type="submit" variant="primary">
                                        {{ $t(`button.${editMode ? 'update' : 'create'}`) }}
                                    </b-button>
                                </b-col>
                            </b-row>
                        </form>
                    </template>
                </iq-card>
            </b-col>
        </b-row>

        <b-modal v-model="showCropperModal" size="lg" title="Crop Image">
            <Cropper
                v-if="selectedImageType.image"
                :image="selectedImageType.image"
                :aspect-ratio="selectedImageType.aspectRatio"
                @change="x => selectedImageType.croppedImage = x"
            />

            <template #modal-footer>
                <div class="w-100">
                    <p class="float-left">Event Image</p>
                    <b-button
                        variant="primary"
                        size="sm"
                        class="float-right"
                        @click="onCropped"
                    >
                        Done
                    </b-button>
                </div>
            </template>
        </b-modal>
    </b-container>
</template>
