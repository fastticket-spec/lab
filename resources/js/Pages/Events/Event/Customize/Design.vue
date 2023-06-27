<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card class="page-design-page">
                    <template v-slot:headerTitle>
                        <h4 class="card-title">Page Design - Customize Event</h4>
                    </template>

                    <template v-slot:body>
                        <b-row class="mt-3">
                            <b-col md="6">
                                <h5 class="mb-3">Background Options</h5>

                                <div class="accordion" role="tablist">
                                    <b-card no-body class="mb-3">
                                        <b-card-header header-tag="header" class="p-1" role="tab"
                                                       header-bg-variant="primary">
                                            <div v-b-toggle.button-color class="py-1 px-2">Button color</div>
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
                                            <div v-b-toggle.register-value class="py-1 px-2">Register Value</div>
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
                                            <div v-b-toggle.title class="py-1 px-2">Title</div>
                                        </b-card-header>
                                        <b-collapse id="title" visible accordion="my-accordion"
                                                    role="tabpanel">
                                            <b-card-body>
                                                <b-form-group label="Title Color" label-for="title-color">
                                                    <b-form-input v-model="state.title.color" type="color" size="sm"
                                                                  id="title-color" placeholder=""></b-form-input>
                                                </b-form-group>

                                                <b-form-group label="Title Description Color"
                                                              label-for="title-desc-color">
                                                    <b-form-input v-model="state.title.desc_color" type="color"
                                                                  size="sm" id="title-desc-color"
                                                                  placeholder=""></b-form-input>
                                                </b-form-group>
                                            </b-card-body>
                                        </b-collapse>
                                    </b-card>

                                    <b-card no-body class="mb-3">
                                        <b-card-header header-tag="header" class="p-1" role="tab"
                                                       header-bg-variant="primary">
                                            <div v-b-toggle.icon class="py-1 px-2">Icon</div>
                                        </b-card-header>
                                        <b-collapse id="icon" visible accordion="my-accordion"
                                                    role="tabpanel">
                                            <b-card-body>
                                                <b-form-group label="Icon Color" label-for="icon-color">
                                                    <b-form-input v-model="state.icon.color" type="color" size="sm"
                                                                  id="icon-color" placeholder=""></b-form-input>
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

                                                <b-form-group label="Event Background Color"
                                                              label-for="event-background-color">
                                                    <b-form-input v-model="state.background.color2" type="color"
                                                                  size="sm" id="event-background-color"
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
                                <page-preview :data="state" :event="event"/>
                            </b-col>
                        </b-row>
                    </template>
                </iq-card>
            </b-col>
        </b-row>
    </b-container>
</template>

<script setup>
import {onMounted, reactive, watch} from "vue";
import PagePreview from "./components/PagePreview.vue";
import {router} from "@inertiajs/vue3";
import {getFullLogoUrl} from "../../../../Shared/components/helpers/ImageHelper";

const props = defineProps({
    event: {},
    design_images: []
})

watch(() => props.design_images, (value, oldValue) => {
    if (value.length !== oldValue.length) {
        const imgs = value.map(image => ({
            full: getFullLogoUrl(image.design_image),
            thumbnail: getFullLogoUrl(image.design_image)
        }))

        console.log({imgs})

        const defImages = state.backgroundImages.filter(x => x.type === 'default');

        state.backgroundImages = [...defImages, ...imgs];
    }
})

onMounted(() => {
    if (props.design_images.length > 0) {
        const imgs = props.design_images.map(image => ({
            full: getFullLogoUrl(image.design_image),
            thumbnail: getFullLogoUrl(image.design_image)
        }))

        state.backgroundImages = [...state.backgroundImages, ...imgs];
    }

    if (props.event?.design?.bg_type && props.event?.design?.bg_image) {
        const img = props.event?.design?.bg_image;
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
})

const state = reactive({
    btn: {
        color: props.event?.design?.btn_color_code || '#6546d2',
        font_color: props.event?.design?.btn_font_color_code || '#ffffff'
    },
    register: {
        english: props.event?.design?.register_btn_value || 'Register',
        arabic: props.event?.design?.regiser_btn_value_ar || 'يسجل'
    },
    title: {
        color: props.event?.design?.title_color_code || '#2e2138',
        desc_color: props.event?.design?.description_color_code || '#2e2138'
    },
    icon: {
        color: props.event?.design?.icon_color_code || '#2e2138'
    },
    background: {
        type: props.event?.design?.bg_type || 'image',
        color: props.event?.design?.bg_color || '#1682d4',
        color2: props.event?.design?.side_bg_color || '#ffff00',
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
    router.post(`/event/${props.event.id}/design-images`, {
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
        regiser_btn_value_ar: state.register.arabic,
        title_color_code: state.title.color,
        description_color_code: state.title.desc_color,
        icon_color_code: state.icon.color,
        bg_color: state.background.color,
        side_bg_color: state.background.color2,
        bg_type: state.background.type === 'color' ? 1 : 2,
        bg_image: state.background.bgImage?.full,
    }

    router.post(`/event/${props.event.id}/page-design`, data);
}
</script>

<style>
.page-design-page .card-body {
    border: 1px solid var(--iq-primary);
    border-bottom-left-radius: 5px;
    border-bottom-right-radius: 5px;
}
</style>
