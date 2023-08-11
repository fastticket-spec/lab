<script setup>
import {ErrorMessage, Field, useForm} from "vee-validate";
import {createManagerSchema} from "../../Shared/components/helpers/Validators.js";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    editMode: Boolean,
    errors: {},
    roles: [],
    manager: {},
    events: []
})

const initialValues = {
    first_name: '',
    last_name: '',
    email: ''
}

const {handleSubmit, isSubmitting, setFieldValue} = useForm({
    initialValues,
    validationSchema: createManagerSchema,
});

const onSubmit = handleSubmit(values => {
    props.editMode
        ? router.patch(`/account-managers/${props.manager.id}`, values)
        : router.post('/account-managers', values);

})
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ editMode ? $t('users.edit') : $t('users.create') }}</h4>
                    </template>
                    <template v-slot:body>
                        <form @submit.prevent="onSubmit">
                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="first_name">{{ $t('input.first_name') }}</label>
                                        <Field type="text" name="first_name" id="first_name"
                                               class="form-control mb-0"
                                               :placeholder="$t('input.first_name')" :validateOnInput="true"/>
                                        <ErrorMessage name="first_name" class="text-danger"/>
                                        <span v-if="errors.first_name" class="text-danger">{{
                                                errors.first_name
                                            }}</span>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="last_name">{{ $t('input.last_name') }}</label>
                                        <Field type="text" name="last_name" id="last_name"
                                               class="form-control mb-0"
                                               :placeholder="$t('input.last_name')" :validateOnInput="true"/>
                                        <ErrorMessage name="last_name" class="text-danger"/>
                                        <span v-if="errors.last_name" class="text-danger">{{ errors.last_name }}</span>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="email">{{ $t('input.email') }}</label>
                                        <Field type="email" name="email" id="email"
                                               class="form-control mb-0"
                                               :placeholder="$t('input.email')" :validateOnInput="true"/>
                                        <ErrorMessage name="email" class="text-danger"/>
                                        <span v-if="errors.email" class="text-danger">{{ errors.email }}</span>
                                    </div>
                                </b-col>
                            </b-row>

                            <div>
                                <span v-if="$page.props.code !== 200" class="text-danger">{{
                                        $page.props.message
                                    }}</span>
                            </div>

                            <b-row class="mt-3">
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
