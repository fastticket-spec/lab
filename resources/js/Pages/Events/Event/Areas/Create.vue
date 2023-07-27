<script setup>
import {ErrorMessage, Field, FieldArray, useForm} from "vee-validate";
import {areaSchema} from "../../../../Shared/components/helpers/Validators.js";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    eventId: String,
    areas: Array
});

const {handleSubmit, isSubmitting} = useForm({
    initialValues: props.areas.length > 0
        ? {
            areas: props.areas.map(area => ({area: area.area}))
        }
        : {
            areas: [{area: ''}]
        },
    validationSchema: areaSchema,
});

const onSubmit = handleSubmit(values => {
    console.log(values)
    router.post(`/event/${props.eventId}/areas`, values);
})
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">Update Areas</h4>
                    </template>
                    <template v-slot:body>
                        <form @submit.prevent="onSubmit">
                            <b-row>
                                <FieldArray name="areas" v-slot="{ fields, insert, remove, swap }">
                                    <template v-for="(field, idx) in fields" :key="field.key">
                                        <b-col sm="4" class="mb-3">
                                            <div class="form-group mb-0">
                                                <label for="area">Area</label>
                                                <Field type="text"
                                                       :name="`areas[${idx}].area`"
                                                       :id="`area-${idx}`"
                                                       :class="`form-control mb-0`" :validateOnInput="true"/>
                                                <ErrorMessage :name="`areas[${idx}].area`" class="text-danger"/>
                                            </div>
                                        </b-col>

                                        <b-col sm="2" class="mb-3 pt-5">
                                            <b-btn variant="outline-primary"
                                                   @click="insert(idx + 1, {area: ''})">
                                                <i class="ri-add-line p-0"></i>
                                            </b-btn>
                                            <b-btn v-if="fields.length > 1"
                                                   variant="outline-danger"
                                                   @click="remove(idx)"
                                                   class="ml-2"><i
                                                class="ri-delete-bin-2-line p-0"></i>
                                            </b-btn>
                                        </b-col>
                                    </template>
                                </FieldArray>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-button type="submit" :disabled="isSubmitting" variant="primary">
                                        {{ $t('button.update') }}
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
