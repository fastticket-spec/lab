<script setup>
import {router} from "@inertiajs/vue3";
import {ErrorMessage, Field, Form} from "vee-validate";
import {changePasswordSchema} from "../../Shared/components/helpers/Validators";

const props = defineProps({
    errors: Object,
    token: String
})

const onSubmit = (values) => {
    router.post('/change-password', {...values, token: props.token});
}
</script>

<template>
    <div class="img-container text-center">
        <img src="../../../images/logo-dark.png" class="img-fluid" alt="logo">
    </div>

    <h1 class="mb-0">{{ $t('reset-password.header') }}</h1>
    <p>{{ $t('reset-password.change_description') }}</p>

    <Form :validation-schema="changePasswordSchema" class="mt-4" @submit="onSubmit" v-slot="{errors}">
        <div class="form-group">
            <label for="passwordInput">{{ $t('sign-in.password') }}</label>
            <Field type="password" name="password" :class="`form-control mb-0 ${(!errors.password?'':'is-invalid')}`"
                   id="passwordInput" placeholder="Enter your new password" :validateOnInput="true"/>
            <ErrorMessage name="password" class="text-danger"/>
            <span v-if="$page.props.errors?.password" v-text="$page.props.errors?.password" class="text-danger"/>
        </div>

        <div class="form-group">
            <label for="confirmPasswordInput">{{ $t('sign-in.confirm-password') }}</label>
            <Field type="password" name="confirm_password" :class="`form-control mb-0 ${(!errors.confirm_password?'':'is-invalid')}`"
                   id="confirmPasswordInput" placeholder="Re enter your password" :validateOnInput="true"/>
            <ErrorMessage name="confirm_password" class="text-danger"/>
            <span v-if="$page.props.errors?.confirm_password" v-text="$page.props.errors?.confirm_password" class="text-danger"/>
        </div>

        <div>
            <span v-if="$page.props.code !== 200" class="text-danger">{{ $page.props.message }}</span>
        </div>

        <button type="submit"
                class="btn btn-primary float-right">{{ $t('reset-password.header') }}
        </button>

        <a href="/login">Back to Login</a>
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
