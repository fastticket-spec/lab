<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ editMode ? $t('organisers.edit') : $t('organisers.create') }}</h4>
                    </template>
                    <template v-slot:body>
                        <form class="mt-4" @submit="onSubmit">
                            <h6>{{ $t('details') }}</h6>
                            <b-row class="mt-3">
                                <b-col>
                                    <div class="form-group">
                                        <label for="nameInput">{{ $t('input.name') }}</label>
                                        <Field type="text" name="name" id="nameInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.name')" :validateOnInput="true"/>
                                        <ErrorMessage name="name" class="text-danger"/>
                                    </div>
                                </b-col>
                                <b-col>
                                    <div class="form-group">
                                        <label for="nameArabicInput">{{ $t('input.nameArabic') }}</label>
                                        <Field type="text" name="name_arabic" id="nameArabicInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.nameArabic')" :validateOnInput="true"/>
                                        <ErrorMessage name="name_arabic" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>
                            <b-row class="mt-3">
                                <b-col>
                                    <div class="form-group">
                                        <label for="emailInput">{{ $t('input.email') }}</label>
                                        <Field type="email" name="email" id="emailInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.email')" :validateOnInput="true"/>
                                        <ErrorMessage name="email" class="text-danger"/>
                                    </div>
                                </b-col>
                                <b-col>
                                    <div class="form-group">
                                        <label for="phoneInput">{{ $t('input.phone') }}</label>
                                        <Field type="text" name="phone" id="phoneInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.phone')" :validateOnInput="true"/>
                                        <ErrorMessage name="phone" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <h6>{{ $t('social_details') }}</h6>
                            <b-row class="mt-3">
                                <b-col>
                                    <div class="form-group">
                                        <label for="facebookInput">{{ $t('input.facebook') }}</label>
                                        <Field type="text" name="facebook" id="facebookInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.facebook')" :validateOnInput="true"/>
                                        <ErrorMessage name="facebook" class="text-danger"/>
                                    </div>
                                </b-col>
                                <b-col>
                                    <div class="form-group">
                                        <label for="twitterInput">{{ $t('input.twitter') }}</label>
                                        <Field type="text" name="twitter" id="twitterInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.twitter')" :validateOnInput="true"/>
                                        <ErrorMessage name="twitter" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>
                            <b-row class="mt-3">
                                <b-col>
                                    <div class="form-group">
                                        <label for="snapchatInput">{{ $t('input.snapchat') }}</label>
                                        <Field type="text" name="snapchat" id="snapchatInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.snapchat')" :validateOnInput="true"/>
                                        <ErrorMessage name="snapchat" class="text-danger"/>
                                    </div>
                                </b-col>
                                <b-col>
                                    <div class="form-group">
                                        <label for="instagramInput">{{ $t('input.instagram') }}</label>
                                        <Field type="text" name="instagram" id="instagramInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.instagram')" :validateOnInput="true"/>
                                        <ErrorMessage name="instagram" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>
                            <b-row class="mt-3">
                                <b-col>
                                    <div class="form-group">
                                        <label for="youtubeInput">{{ $t('input.youtube') }}</label>
                                        <Field type="text" name="youtube" id="youtubeInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.youtube')" :validateOnInput="true"/>
                                        <ErrorMessage name="youtube" class="text-danger"/>
                                    </div>
                                </b-col>
                                <b-col></b-col>
                            </b-row>

                            <template v-if="!editMode">
                                <h6>{{ $t('logo') }}</h6>
                                <b-row class="mt-3">
                                    <b-col>
                                        <b-form-group
                                            label-for="customFile"
                                        >
                                            <Field name="organiser_logo" v-slot="{ handleChange, handleBlur }">
                                                <b-form-file
                                                    placeholder="Upload Logo"
                                                    id="customFile"
                                                    @change="handleChange"
                                                    @blur="handleBlur"
                                                ></b-form-file>
                                                <span v-if="errors.organiser_logo" class="text-danger">{{errors.organiser_logo}}</span>
                                            </Field>
                                        </b-form-group>
                                    </b-col>
                                    <b-col>
                                        <b-form-group
                                            label-for="customFile"
                                        >
                                            <Field name="organiser_logo_arabic" v-slot="{ handleChange, handleBlur }">
                                                <b-form-file
                                                    placeholder="Upload Arabic Logo"
                                                    @change="handleChange"
                                                    @blur="handleBlur"
                                                ></b-form-file>
                                                <span v-if="errors.organiser_logo_arabic" class="text-danger">{{errors.organiser_logo_arabic}}</span>
                                            </Field>
                                        </b-form-group>
                                    </b-col>
                                </b-row>

                                <b-row class="mt-3">
                                    <b-col>
                                        <b-form-group>
                                            <Field name="organiser_banner" v-slot="{ handleChange, handleBlur }">
                                                <b-form-file
                                                    placeholder="Upload Banner"
                                                    @change="handleChange"
                                                    @blur="handleBlur"
                                                ></b-form-file>
                                            </Field>
                                            <span v-if="errors.organiser_banner" class="text-danger">{{errors.organiser_banner}}</span>
                                        </b-form-group>
                                    </b-col>
                                    <b-col>
                                        <b-form-group
                                            label-for="customFile"
                                        >
                                            <Field name="organiser_banner_arabic" v-slot="{ handleChange, handleBlur }">
                                                <b-form-file
                                                    placeholder="Upload Arabic Banner"
                                                    @change="handleChange"
                                                    @blur="handleBlur"
                                                ></b-form-file>
                                            </Field>
                                            <span v-if="errors.organiser_banner_arabic" class="text-danger">{{errors.organiser_banner_arabic}}</span>
                                        </b-form-group>
                                    </b-col>
                                </b-row>
                            </template>

                            <b-row>
                                <b-col>
                                    <b-button type="submit" :disabled="isSubmitting" variant="primary">
                                        {{ $t(`button.${editMode ? 'update' : 'create'}`) }}
                                    </b-button>
                                </b-col>
                            </b-row>

                        </form>

                        <ImageForm :organiser="organiser" :edit-mode="editMode" :errors="errors"/>
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
                    <p class="float-left">Organiser Image</p>
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

<script setup>
import {Form, Field, ErrorMessage, useForm, useField} from 'vee-validate';
import {createOrganiserSchema} from '../../Shared/components/helpers/Validators';
import {router} from "@inertiajs/vue3";
import {reactive, ref, watch} from "vue";
import Cropper from "../../Shared/components/core/images/Cropper.vue";
import ImageForm from "./ImageForm.vue";

const props = defineProps({
    code: Number,
    message: String,
    editMode: Boolean,
    organiser: {},
    errors: Object
});


const {handleSubmit, isSubmitting, setFieldValue} = useForm({
    initialValues: props.editMode ? props.organiser : {},
    validationSchema: createOrganiserSchema
});

const {value: organiserBanner} = useField("organiser_banner");
const {value: organiserBannerArabic} = useField("organiser_banner_arabic");

const selectedImageType = reactive({
    type: '',
    croppedImage: '',
    image: null,
    aspectRatio: 1
})

const showCropperModal = ref(false);

watch(organiserBanner, (newVal) => {
    if (newVal instanceof File) {
        selectedImageType.type = 'organiser_banner'
        selectedImageType.image = newVal;
        selectedImageType.aspectRatio = 4;
        showCropperModal.value = true;
    }
})

watch(organiserBannerArabic, (newVal) => {
    if (newVal instanceof File) {
        selectedImageType.type = 'organiser_banner_arabic'
        selectedImageType.image = newVal;
        selectedImageType.aspectRatio = 4;
        showCropperModal.value = true;
    }
})

const onCropped = async () => {
    setFieldValue(selectedImageType.type, selectedImageType.croppedImage)
    showCropperModal.value = false
}

const onSubmit = handleSubmit(async values => {
    if (!props.editMode) {
        return router.post('/organisers/create', values)
    }

    delete values.organiser_logo;
    delete values.organiser_logo_arabic;

    router.patch(`/organisers/${props.organiser.id}`, values)
});

</script>
