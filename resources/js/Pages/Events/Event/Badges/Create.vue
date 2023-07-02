<script setup>
import {Form, Field, ErrorMessage, useForm} from 'vee-validate';
import {createBadgeSchema} from '../../../../Shared/components/helpers/Validators';
import {router} from "@inertiajs/vue3";
import {onMounted, ref} from "vue";
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";

const props = defineProps({
    code: Number,
    message: String,
    editMode: Boolean,
    badge: {},
    eventId: String,
    accessLevels: [],
    errors: Object,
    accessLevelIds: Array
});

const selectedAccessLevels = ref([]);

onMounted(() => {
    if (props.editMode && props.accessLevelIds) {
        selectedAccessLevels.value = props.accessLevels.filter(x => props.accessLevelIds.includes(x.id))
    }
})

const {handleSubmit, isSubmitting} = useForm({
    initialValues: props.editMode ? props.badge : {},
    validationSchema: createBadgeSchema
});

const onSubmit = handleSubmit(async values => {
    if (selectedAccessLevels.value.length > 0) {
        values.access_levels = selectedAccessLevels.value.map(x => x.id)
    }

    if (!props.editMode) {
        return router.post(`/event/${props.eventId}/badges`, values)
    }

    router.patch(`/event/${props.eventId}/badges/${props.badge.id}/update`, values)
});

</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ editMode ? $t('badges.edit') : $t('badges.create') }}</h4>
                    </template>
                    <template v-slot:body>
                        <form class="mt-4" @submit="onSubmit">
                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="titleInput">Title</label>
                                        <Field type="text" name="title" id="titleInput"
                                               :class="`form-control mb-0`"
                                               :validateOnInput="true"/>
                                        <ErrorMessage name="title" class="text-danger"/>
                                    </div>
                                </b-col>
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <Field as="textarea" rows="5" name="description" id="description"
                                               :class="`form-control mb-0`"
                                               :validateOnInput="true"/>
                                        <ErrorMessage name="description" class="text-danger"/>
                                    </div>
                                </b-col>
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="accessLevels">Access Levels</label>
                                        <vue-select class="form-control mb-0" v-model="selectedAccessLevels"
                                                    :options="accessLevels" label="title"
                                                    multiple/>
                                    </div>
                                </b-col>
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="width">Width</label>
                                        <Field type="number" name="width" id="width"
                                               step="0.01"
                                               :class="`form-control mb-0`"
                                               :validateOnInput="true"/>
                                        <ErrorMessage name="width" class="text-danger"/>
                                    </div>
                                </b-col>
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="height">Height</label>
                                        <Field type="number" name="height" id="height"
                                               step="0.01"
                                               :class="`form-control mb-0`"
                                               :validateOnInput="true"/>
                                        <ErrorMessage name="height" class="text-danger"/>
                                    </div>
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
                    </template>
                </iq-card>
            </b-col>
        </b-row>
    </b-container>
</template>
