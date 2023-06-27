<template>
    <b-container fluid class="order-page-form">
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">Social Settings - Customize Event</h4>
                        <p>Share buttons to show</p>
                    </template>
                    <template v-slot:body>
                        <b-row>
                            <b-col sm="6">
                                <div class="form-group">
                                    <label>Facebook</label>
                                    <b-input v-model="state.item.facebook" size="md" />
                                    <span class="text-danger">{{errors?.facebook}}</span>
                                </div>
                            </b-col>
                            <b-col sm="6">
                                <div class="form-group">
                                    <label>Twitter</label>
                                    <b-input v-model="state.item.twitter" size="md" />
                                    <span class="text-danger">{{errors?.twitter}}</span>
                                </div>
                            </b-col>
                            <b-col sm="6">
                                <div class="form-group">
                                    <label>Instagram</label>
                                    <b-input v-model="state.item.instagram" size="md" />
                                    <span class="text-danger">{{errors?.instagram}}</span>
                                </div>
                            </b-col>
                            <b-col sm="6">
                                <div class="form-group">
                                    <label>LinkedIn</label>
                                    <b-input v-model="state.item.linkedin" size="md" />
                                    <span class="text-danger">{{errors?.linkedin}}</span>
                                </div>
                            </b-col>
                        </b-row>

                        <b-row class="mt-3">
                            <b-col>
                                <b-button @click="onSubmit" type="button" variant="primary">
                                    {{ $t(`button.update`) }}
                                </b-button>
                            </b-col>
                        </b-row>
                    </template>
                </iq-card>
            </b-col>
        </b-row>
    </b-container>
</template>

<script setup>
import {onMounted, reactive, ref} from "vue";
import {Form, Field, ErrorMessage} from 'vee-validate';
import {router} from "@inertiajs/vue3";

const props = defineProps({
    event: {},
    errors: Object
})

const state = reactive({
    item: {
        facebook: '',
        twitter: '',
        instagram: '',
        linkedin: '',
    }
})

onMounted(() => {
    if (props.event.socials) {
        state.item = props.event.socials
    }
})

const onSubmit = () => {
    router.post(`/event/${props.event.id}/social-settings`, state.item, {
        preserveScroll: true
    })
}
</script>
