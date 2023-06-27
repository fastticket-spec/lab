<script setup>
import {ErrorMessage, Field, Form, useForm} from "vee-validate";
import {router} from "@inertiajs/vue3";
import {createAccessLevelSchema} from "../../../../Shared/components/helpers/Validators.js";

const props = defineProps({
    code: Number,
    message: String,
    editMode: Boolean,
    event_id: String,
    access_level: Object
})

const initialValues = props.editMode ? props.access_level : {}

const {handleSubmit, isSubmitting} = useForm({
    initialValues,
    validationSchema: createAccessLevelSchema,
});

const onSubmit = handleSubmit(async (values) => {
    props.editMode
        ? router.patch(`/event/${props.event_id}/access-levels/${props.access_level.id}/update`, values)
        : router.post(`/event/${props.event_id}/access-levels`, values)
});
</script>

<template>
    <b-container fluid class="event-form">
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ editMode ? $t('access_levels.edit') : $t('access_levels.create') }}</h4>
                    </template>
                    <template v-slot:body>
                        <form class="mt-4" @submit="onSubmit">
                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="titleInput">{{ $t('input.title') }}</label>
                                        <Field type="text" name="title" id="titleInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.title')" :validateOnInput="true"/>
                                        <ErrorMessage name="title" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
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
                                        <label for="quantityInput">{{ $t('input.quantity') }}</label>
                                        <Field type="number" name="quantity_available" id="quantityInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.quantity')" :validateOnInput="true"/>
                                        <ErrorMessage name="quantity_available" class="text-danger"/>
                                    </div>
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
    </b-container>
</template>
