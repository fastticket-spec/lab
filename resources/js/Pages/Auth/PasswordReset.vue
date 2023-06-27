<script setup>
import {passwordResetSchema} from "../../Shared/components/helpers/Validators";
import {Form, Field, ErrorMessage} from 'vee-validate';
import {router} from "@inertiajs/vue3";

defineProps({
    errors: Object
})

const onSubmit = (values) => {
    router.post('/send-password-reset-token', values);
}
</script>

<template>
    <div class="img-container text-center">
        <img src="../../../images/logo-dark.png" class="img-fluid" alt="logo">
    </div>

    <h1 class="mb-0">{{ $t('reset-password.header') }}</h1>
    <p>{{ $t('reset-password.description') }}</p>

    <Form :validation-schema="passwordResetSchema" class="mt-4" @submit="onSubmit" v-slot="{errors}">
        <div class="form-group">
            <label for="emailInput">{{ $t('sign-in.email-address') }}</label>
            <Field type="email" name="email" :class="`form-control mb-0 ${(!errors.email?'':'is-invalid')}`"
                   id="emailInput" :placeholder="$t('sign-in.enter-email')" :validateOnInput="true"/>
            <ErrorMessage name="email" class="text-danger"/>
            <span v-if="$page.props.errors?.email" v-text="$page.props.errors?.email" class="text-danger"/>
        </div>

        <div>
            <span v-if="$page.props.code !== 200" class="text-danger">{{ $page.props.message }}</span>
        </div>

        <button type="submit"
                class="btn btn-primary float-right">{{ $t('reset-password.header') }}
        </button>
    </Form>
</template>

<script>
import AuthLayout from '../../Shared/Layout/AuthLayout.vue';

export default {
    layout: AuthLayout
}
</script>

<style scoped>
.img-container {
    width: 100%;
    display: flex;
    justify-items: center;
    align-items: center;
    margin-bottom: 25px;
}

.img-container img {
    width: 150px;
    margin: auto;
}
</style>
