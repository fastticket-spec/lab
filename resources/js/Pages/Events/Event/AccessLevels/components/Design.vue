<script setup>
import {router} from "@inertiajs/vue3";
import {onMounted, onUpdated, reactive, watch} from "vue";
import PagePreview from "./PagePreview.vue";

const props = defineProps({
    event: Object,
    accessLevel: Object,
    data: Object,
    designImages: Array
})

watch(() => props.design_images, (value, oldValue) => {
    if (value.length !== oldValue.length) {
        const imgs = value.map(image => ({
            full: image.design_image_url,
            thumbnail: image.design_image_url
        }))

        const defImages = state.backgroundImages.filter(x => x.type === 'default');

        state.backgroundImages = [...defImages, ...imgs];
    }
})

onMounted(() => {
    initImages();

    if (props.data?.bg_type && props.data?.bg_image) {
        const img = props.data?.bg_image;
        const selectedImage = state.backgroundImages.find(x => x.full === img);

        if (selectedImage) {
            state.background.bgImage = selectedImage
        } else {
            img ? state.background.bgImage = {
                full: img,
                thumbnail: img
            } : state.background.bgImage = state.backgroundImages[0]
        }
    }
});

onUpdated(() => {
    initImages()
});

const initImages = () => {
    if (props.designImages.length > 0) {
        const imgs = props.designImages.map(image => ({
            full: image.design_image_url,
            thumbnail: image.design_image_url
        }))

        state.backgroundImages = [...state.backgroundImages.filter(x => x.type === 'default'), ...imgs];
    }
}

const state = reactive({
    btn: {
        color: props.data?.btn_color_code || '#6546d2',
        font_color: props.data?.btn_font_color_code || '#ffffff'
    },
    register: {
        english: props.data?.register_btn_value || 'Register',
        arabic: props.data?.regiser_btn_value_ar || 'يسجل'
    },
    formButton: {
        english: props.data?.form_btn_value || 'Submit',
        arabic: props.data?.form_btn_value_ar || 'يُقدِّم'
    },
    formBackground: {
        color: props.data?.form_bg_color || '#ffffff',
    },
    background: {
        type: props.data?.bg_type || 'image',
        color: props.data?.bg_color || '#1682d4',
        bgImage: {}
    },
    backgroundImages: [
        {
            full: 'https://img.freepik.com/free-photo/moon-light-shine-through-window-into-islamic-mosque-interior_1217-2597.jpg?w=2000&t=st=1680706788~exp=1680707388~hmac=003db5592c0f6bd411f53337537d46d0d967930748990ce50c0e8f1407c4f068',
            thumbnail: 'https://img.freepik.com/free-photo/moon-light-shine-through-window-into-islamic-mosque-interior_1217-2597.jpg?w=200&t=st=1680706788~exp=1680707388~hmac=003db5592c0f6bd411f53337537d46d0d967930748990ce50c0e8f1407c4f068',
            type: 'default'
        },
        {
            full: 'https://img.freepik.com/free-vector/golden-luxury-mandala-background_23-2147885212.jpg?w=1480&t=st=1680706906~exp=1680707506~hmac=edb5949eef6c8d388f53f3f7ba6c43028a0257955938e8db9cd3342d20869365',
            thumbnail: "https://img.freepik.com/free-vector/golden-luxury-mandala-background_23-2147885212.jpg?w=200&t=st=1680706906~exp=1680707506~hmac=edb5949eef6c8d388f53f3f7ba6c43028a0257955938e8db9cd3342d20869365",
            type: 'default'
        },
        {
            full: 'https://img.freepik.com/free-photo/moon-light-shine-through-window-into-islamic-mosque-interior_1217-2597.jpg?w=2000&t=st=1680706788~exp=1680707388~hmac=003db5592c0f6bd411f53337537d46d0d967930748990ce50c0e8f1407c4f068',
            thumbnail: 'https://img.freepik.com/free-photo/moon-light-shine-through-window-into-islamic-mosque-interior_1217-2597.jpg?w=200&t=st=1680706788~exp=1680707388~hmac=003db5592c0f6bd411f53337537d46d0d967930748990ce50c0e8f1407c4f068',
            type: 'default'
        },
        {
            full: 'https://img.freepik.com/free-vector/golden-luxury-mandala-background_23-2147885212.jpg?w=1480&t=st=1680706906~exp=1680707506~hmac=edb5949eef6c8d388f53f3f7ba6c43028a0257955938e8db9cd3342d20869365',
            thumbnail: "https://img.freepik.com/free-vector/golden-luxury-mandala-background_23-2147885212.jpg?w=200&t=st=1680706906~exp=1680707506~hmac=edb5949eef6c8d388f53f3f7ba6c43028a0257955938e8db9cd3342d20869365",
            type: 'default'
        },
        {
            full: 'https://img.freepik.com/free-photo/moon-light-shine-through-window-into-islamic-mosque-interior_1217-2597.jpg?w=2000&t=st=1680706788~exp=1680707388~hmac=003db5592c0f6bd411f53337537d46d0d967930748990ce50c0e8f1407c4f068',
            thumbnail: 'https://img.freepik.com/free-photo/moon-light-shine-through-window-into-islamic-mosque-interior_1217-2597.jpg?w=200&t=st=1680706788~exp=1680707388~hmac=003db5592c0f6bd411f53337537d46d0d967930748990ce50c0e8f1407c4f068',
            type: 'default'
        },
        {
            full: 'https://img.freepik.com/free-vector/golden-luxury-mandala-background_23-2147885212.jpg?w=1480&t=st=1680706906~exp=1680707506~hmac=edb5949eef6c8d388f53f3f7ba6c43028a0257955938e8db9cd3342d20869365',
            thumbnail: "https://img.freepik.com/free-vector/golden-luxury-mandala-background_23-2147885212.jpg?w=200&t=st=1680706906~exp=1680707506~hmac=edb5949eef6c8d388f53f3f7ba6c43028a0257955938e8db9cd3342d20869365",
            type: 'default'
        }
    ]
});

const uploadFiles = ({target: {files}}) => {

    router.post(`/event/${props.event.id}/access-levels/${props.accessLevel.id}/customize/design-images`, {
        design_images: files
    }, {
        preserveScroll: true
    })
}

const onSubmit = () => {
    const data = {
        btn_color_code: state.btn.color,
        btn_font_color_code: state.btn.font_color,
        register_btn_value: state.register.english,
        register_btn_value_ar: state.register.arabic,
        form_bg_color: state.formBackground.color,
        bg_color: state.background.color,
        bg_type: state.background.type,
        bg_image: state.background.bgImage?.full,
        form_btn_value: state.formButton.english,
        form_btn_value_ar: state.formButton.arabic,
    }

    router.post(`/event/${props.event.id}/access-levels/${props.accessLevel.id}/customize/page-design`, data);
}
</script>

<template>
    <b-row>
        <b-col sm="12">
            <iq-card>
                <template v-slot:headerTitle>
                    <h4 class="card-title">Page Design - Customize Event</h4>
                </template>

                <template v-slot:body>
                    <div class="d-flex justify-content-between align-items-center mb-3 w-100">
                        <b-row class="mt-3 w-100">
                            <b-col md="6">
                                <h5 class="mb-3">Background Options</h5>

                                <div class="accordion" role="tablist">
                                    <b-card no-body class="mb-3">
                                        <b-card-header header-tag="header" class="p-1" role="tab"
                                                       header-bg-variant="primary">
                                            <div v-b-toggle.button-color class="py-1 px-2">Button Color</div>
                                        </b-card-header>
                                        <b-collapse id="button-color" visible accordion="my-accordion" role="tabpanel">
                                            <b-card-body>
                                                <b-form-group label="Color" label-for="btn-color">
                                                    <b-form-input v-model="state.btn.color" type="color" size="sm"
                                                                  id="btn-color" placeholder=""></b-form-input>
                                                </b-form-group>

                                                <b-form-group label="Font Color" label-for="font-color">
                                                    <b-form-input v-model="state.btn.font_color" type="color" size="sm"
                                                                  id="font-color" placeholder=""></b-form-input>
                                                </b-form-group>
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>

                                    <b-card no-body class="mb-3">
                                        <b-card-header header-tag="header" class="p-1" role="tab"
                                                       header-bg-variant="primary">
                                            <div v-b-toggle.register-value class="py-1 px-2">Application Button Value</div>
                                        </b-card-header>
                                        <b-collapse id="register-value" visible accordion="my-accordion"
                                                    role="tabpanel">
                                            <b-card-body>
                                                <b-form-group label="English" label-for="register-english">
                                                    <b-form-input v-model="state.register.english" type="text" size="sm"
                                                                  id="register-english" placeholder=""></b-form-input>
                                                </b-form-group>

                                                <b-form-group label="Arabic" label-for="register-arabic">
                                                    <b-form-input v-model="state.register.arabic" type="text" size="sm"
                                                                  id="register-arabic" placeholder=""></b-form-input>
                                                </b-form-group>
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>

                                    <b-card no-body class="mb-3">
                                        <b-card-header header-tag="header" class="p-1" role="tab"
                                                       header-bg-variant="primary">
                                            <div v-b-toggle.form-button class="py-1 px-2">Form Button Value</div>
                                        </b-card-header>
                                        <b-collapse id="form-button" visible accordion="my-accordion"
                                                    role="tabpanel">
                                            <b-card-body>
                                                <b-form-group label="English" label-for="form-english">
                                                    <b-form-input v-model="state.formButton.english" type="text" size="sm"
                                                                  id="form-english" placeholder=""></b-form-input>
                                                </b-form-group>

                                                <b-form-group label="Arabic" label-for="form-arabic">
                                                    <b-form-input v-model="state.formButton.arabic" type="text" size="sm"
                                                                  id="form-arabic" placeholder=""></b-form-input>
                                                </b-form-group>
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>

                                    <b-card no-body class="mb-3">
                                        <b-card-header header-tag="header" class="p-1" role="tab"
                                                       header-bg-variant="primary">
                                            <div v-b-toggle.background class="py-1 px-2">Background</div>
                                        </b-card-header>
                                        <b-collapse id="background" visible accordion="my-accordion"
                                                    role="tabpanel">
                                            <b-card-body>
                                                <b-form-group>
                                                    <b-form-radio inline v-model="state.background.type"
                                                                  name="background_type" value="color">Use Color
                                                    </b-form-radio>
                                                    <b-form-radio inline v-model="state.background.type"
                                                                  name="background_type" value="image">Use Image
                                                    </b-form-radio>
                                                </b-form-group>

                                                <b-form-group label="Background Color" label-for="background-color"
                                                              v-if="state.background.type === 'color'">
                                                    <b-form-input v-model="state.background.color" type="color"
                                                                  size="sm" id="background-color"
                                                                  placeholder=""></b-form-input>
                                                </b-form-group>

                                                <b-form-group v-if="state.background.type === 'image'">
                                                    <b-row>
                                                        <b-col class="mb-3" sm="3"
                                                               v-for="(image) in state.backgroundImages"
                                                               :key="image.full">
                                                            <a href="#" @click.prevent="state.background.bgImage = image">
                                                                <b-img thumbnail :src="image.thumbnail"/>
                                                            </a>
                                                        </b-col>
                                                    </b-row>
                                                </b-form-group>

                                                <b-form-group label="Upload Image(s)" label-for="upload-file"
                                                              v-if="state.background.type === 'image'">
                                                    <b-form-file type="file" size="sm" id="upload-file"
                                                                 accept="image/*" multiple @change="uploadFiles"/>
                                                </b-form-group>
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>

                                    <b-card no-body class="mb-3">
                                        <b-card-header header-tag="header" class="p-1" role="tab"
                                                       header-bg-variant="primary">
                                            <div v-b-toggle.formBgColor class="py-1 px-2">Form Background Color</div>
                                        </b-card-header>
                                        <b-collapse id="formBgColor" visible accordion="my-accordion"
                                                    role="tabpanel">
                                            <b-card-body>
                                                <b-form-group label="Background Color" label-for="form-background-color">
                                                    <b-form-input v-model="state.formBackground.color" type="color"
                                                                  size="sm" id="form-background-color"
                                                                  placeholder=""></b-form-input>
                                                </b-form-group>
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>

                                </div>

                                <b-row class="mt-3">
                                    <b-col>
                                        <b-button type="button" @click="onSubmit" variant="primary">
                                            {{ $t(`button.update`) }}
                                        </b-button>
                                    </b-col>
                                </b-row>
                            </b-col>

                            <b-col md="6">
                                <h5 class="mb-3">Page Preview</h5>
                                <page-preview :data="state" :event="event" :access-level="accessLevel"/>
                            </b-col>
                        </b-row>
                    </div>
                </template>
            </iq-card>
        </b-col>
    </b-row>
</template>

<style>
.page-design-page .card-body {
    border: 1px solid var(--iq-primary);
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
}
</style>
