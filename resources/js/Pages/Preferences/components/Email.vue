<script setup>
import {onUpdated, reactive} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    data: Object
})

const state = reactive({
    bg_color: props.data?.email_bg_color || '#ffffff',
    font_color: props.data?.email_font_color || '#000000',
    qr_color: props.data?.email_qr_color || '#000000',
    logo: props.data?.email_logo_url || '',
    logo_width: props.data?.email_logo_width || 200,
    logo_height: props.data?.email_logo_height || 100,
    header_image: props.data?.email_header_image_url || '',
    footer_image: props.data?.email_footer_image_url || '',
})

onUpdated(() => {
    state.logo = props.data?.email_logo_url;
    state.header_image = props.data?.email_header_image_url;
    state.footer_image = props.data?.email_footer_image_url;
})

const uploadLogo = async ({target: {files}}) => {
    router.post(`/organiser-preferences/logo`, {
        logo: files[0],
        image_type: 'logo'
    }, {
        preserveScroll: true,
        preserveState: true
    })
}

const uploadHeaderImage = async ({target: {files}}) => {
    router.post(`/organiser-preferences/logo`, {
        logo: files[0],
        image_type: 'headerImage'
    }, {
        preserveScroll: true,
        preserveState: true
    })
}

const uploadFooterImage = async ({target: {files}}) => {
    router.post(`/organiser-preferences/logo`, {
        logo: files[0],
        image_type: 'footerImage'
    }, {
        preserveScroll: true,
        preserveState: true
    })
}

const onSubmit = () => {
    router.post(`/organiser-preferences`, {
        email_bg_color: state.bg_color,
        email_font_color: state.font_color,
        email_qr_color: state.qr_color,
        email_logo_url: state.logo,
        email_header_image_url: state.header_image,
        email_footer_image_url: state.footer_image,
        email_logo_width: state.logo_width,
        email_logo_height: state.logo_height,
    }, {
        preserveScroll: true,
        preserveState: true
    })
};
</script>

<template>
    <b-row>
        <b-col sm="12">
            <iq-card>
                <template v-slot:headerTitle>
                    <h4 class="card-title">Customize Email</h4>
                </template>

                <template v-slot:body>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <b-row class="mt-3 w-100">
                            <b-col md="6">
                                <h5 class="mb-3">Options</h5>

                                <b-form-group label="Background Color" label-for="bg-color">
                                    <b-form-input v-model="state.bg_color" type="color" size="sm"
                                                  id="bg-color" placeholder=""></b-form-input>
                                </b-form-group>

                                <b-form-group label="Font Color" label-for="font-color">
                                    <b-form-input v-model="state.font_color" type="color" size="sm"
                                                  id="font-color" placeholder=""></b-form-input>
                                </b-form-group>

                                <b-form-group label="QR Color" label-for="qr-color">
                                    <b-form-input v-model="state.qr_color" type="color" size="sm"
                                                  id="qr-color" placeholder=""></b-form-input>
                                </b-form-group>

                                <div v-if="state.logo" class="delete-container">
                                    <b-img thumbnail :src="state.logo"/>
                                    <Link href="/organiser-preferences/logo/logo" method="delete"><i class="ri-delete-bin-2-fill delete-icon"></i></Link>
                                </div>

                                <b-form-group label="Logo" label-for="logo-file">
                                    <b-form-file type="file" size="sm" id="logo-file" :multiple="false"
                                                 accept="image/*" @change="uploadLogo"/>
                                </b-form-group>

                                <div v-if="state.header_image" class="delete-container">
                                    <b-img thumbnail :src="state.header_image"/>
                                    <Link href="/organiser-preferences/logo/headerImage" method="delete"><i class="ri-delete-bin-2-fill delete-icon"></i></Link>
                                </div>

                                <b-form-group label="Header Image" label-for="header_image-file">
                                    <b-form-file type="file" size="sm" id="header_image-file" :multiple="false"
                                                 accept="image/*" @change="uploadHeaderImage"/>
                                </b-form-group>

                                <div v-if="state.footer_image" class="delete-container">
                                    <b-img thumbnail :src="state.footer_image"/>
                                    <Link href="/organiser-preferences/logo/footerImage" method="delete"><i class="ri-delete-bin-2-fill delete-icon"></i></Link>
                                </div>

                                <b-form-group label="Footer Image" label-for="footer_image-file">
                                    <b-form-file type="file" size="sm" id="footer_image-file" :multiple="false"
                                                 accept="image/*" @change="uploadFooterImage"/>
                                </b-form-group>

                                <b-form-group label="Logo Width" label-for="logo-width">
                                    <b-form-input v-model="state.logo_width" type="number" size="sm"
                                                  id="logo-width" placeholder=""></b-form-input>
                                </b-form-group>

                                <b-form-group label="Logo Height" label-for="logo-height">
                                    <b-form-input v-model="state.logo_height" type="number" size="sm"
                                                  id="logo-height" placeholder=""></b-form-input>
                                </b-form-group>

                                <b-button type="button" @click="onSubmit" variant="primary">
                                    {{ $t(`button.update`) }}
                                </b-button>
                            </b-col>
                        </b-row>

                    </div>
                </template>
            </iq-card>
        </b-col>
    </b-row>
</template>

<style scoped>
.delete-container {
    position: relative;
}

.delete-icon {
    padding: 1px 6px;
    font-size: 20px;
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: #FFFFFF;
}
</style>
