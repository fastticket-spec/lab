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
})

onUpdated(() => {
    state.logo = props.data?.email_logo_url;
})

const uploadLogo = async ({target: {files}}) => {
    router.post(`/organiser-preferences/logo`, {
        logo: files[0]
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

                                <b-img thumbnail v-if="state.logo" :src="state.logo" />

                                <b-form-group label="Logo" label-for="logo-file">
                                    <b-form-file type="file" size="sm" id="logo-file"
                                                 accept="image/*" multiple @change="uploadLogo"/>
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
