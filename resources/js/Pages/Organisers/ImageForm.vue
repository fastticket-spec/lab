<template>


    <form class="mt-4" @submit="onUpdateLogo" v-if="editMode">
        <h6>{{ $t('logo') }}</h6>
        <b-row :class="{'mt-3': (organiser?.logo_url || organiser?.logo_arabic_url)}">
            <b-col>
                <div v-if="organiser?.logo_url">
                    <b-img thumbnail :src="organiser.logo_url"
                           alt="Organiser Logo"></b-img>
                </div>
            </b-col>

            <b-col>
                <div v-if="organiser?.logo_arabic_url">
                    <b-img thumbnail :src="organiser.logo_arabic_url"
                           alt="Organiser Logo Arabic"></b-img>
                </div>
            </b-col>
        </b-row>

        <b-row class="mt-3">
            <b-col>
                <b-form-group
                    label-for="orgLogo"
                    class="mt-2"
                >
                    <Field name="organiser_logo" v-slot="{ handleChange, handleBlur }">
                        <b-form-file
                            :placeholder="`${organiser.organiser_logo ? 'Change' : 'Upload'} Logo`"
                            id="orgLogo"
                            @change="handleChange"
                            @blur="handleBlur"
                        ></b-form-file>
                        <span v-if="errors.organiser_logo" class="text-danger">{{ errors.organiser_logo }}</span>
                    </Field>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group
                    class="mt-2"
                >
                    <Field name="organiser_logo_arabic" v-slot="{ handleChange, handleBlur }">
                        <b-form-file
                            :placeholder="`${organiser.organiser_logo_arabic ? 'Change' : 'Upload'} Arabic Logo`"
                            @change="handleChange"
                            @blur="handleBlur"
                        ></b-form-file>
                        <span v-if="errors.organiser_logo_arabic"
                              class="text-danger">{{ errors.organiser_logo_arabic }}</span>
                    </Field>
                </b-form-group>
            </b-col>
        </b-row>

        <h6 class="mt-3">{{ $t('banner') }}</h6>
        <b-row :class="{'mt-3': (organiser?.banner_url || organiser?.banner_arabic_url)}">
            <b-col>
                <div v-if="organiser?.banner_url">
                    <b-img thumbnail :src="organiser.banner_url"
                           alt="Organiser Logo"></b-img>
                </div>
            </b-col>

            <b-col>
                <div v-if="organiser?.banner_arabic_url">
                    <b-img thumbnail :src="organiser.banner_arabic_url"
                           alt="Organiser Logo Arabic"></b-img>
                </div>
            </b-col>
        </b-row>

        <b-row class="mt-3">
            <b-col>
                <b-form-group
                    label-for="orgBanner"
                    class="mt-2"
                >
                    <Field name="organiser_banner" v-slot="{ handleChange, handleBlur }">
                        <b-form-file
                            :placeholder="`${organiser.organiser_banner ? 'Change' : 'Upload'} Banner`"
                            id="orgBanner"
                            @change="handleChange"
                            @blur="handleBlur"
                        ></b-form-file>
                        <span v-if="errors.organiser_banner" class="text-danger">{{ errors.organiser_banner }}</span>
                    </Field>
                </b-form-group>
            </b-col>
            <b-col>
                <b-form-group
                    class="mt-2"
                >
                    <Field name="organiser_banner_arabic" v-slot="{ handleChange, handleBlur }">
                        <b-form-file
                            :placeholder="`${organiser.organiser_banner_arabic ? 'Change' : 'Upload'} Arabic Banner`"
                            @change="handleChange"
                            @blur="handleBlur"
                        ></b-form-file>
                        <span v-if="errors.organiser_banner_arabic"
                              class="text-danger">{{ errors.organiser_banner_arabic }}</span>
                    </Field>
                </b-form-group>
            </b-col>
        </b-row>

        <b-row>
            <b-col>
                <b-button type="submit" :disabled="isSubmitting" variant="primary">
                    {{ $t(`button.${editMode ? 'update' : 'create'}`) }}
                </b-button>
            </b-col>
        </b-row>
    </form>

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
</template>
<script setup>
import {Field, Form, useField, useForm} from "vee-validate";
import {router} from "@inertiajs/vue3";
import {convertDataToFile} from "../../Shared/components/helpers/File";
import {reactive, ref, watch} from "vue";
import Cropper from "../../Shared/components/core/images/Cropper.vue";

const props = defineProps({
    organiser: Object,
    editMode: Boolean,
    errors: Object
})

const showCropperModal = ref(false);

const {handleSubmit, isSubmitting, setFieldValue} = useForm();

const {value: organiserBanner} = useField("organiser_banner");
const {value: organiserBannerArabic} = useField("organiser_banner_arabic");

const selectedImageType = reactive({
    type: '',
    croppedImage: '',
    image: null,
    aspectRatio: 1
})

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

const onUpdateLogo = handleSubmit(async values => {
    if (typeof values.organiser_banner === 'string') {
        values.organiser_banner = await convertDataToFile(values.organiser_banner);
    }
    if (typeof values.organiser_banner_arabic === 'string') {
        values.organiser_banner_arabic = await convertDataToFile(values.organiser_banner_arabic);
    }

    router.post(`/organisers/${props.organiser.id}/edit-logo`, values, {
        preserveState: true
    });
});

</script>
