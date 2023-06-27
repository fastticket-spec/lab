<script setup>
import {router} from "@inertiajs/vue3";
import {ErrorMessage, Field, Form} from "vee-validate";
import {passwordTokenSchema} from "../../Shared/components/helpers/Validators";

defineProps({
    errors: Object,
    code: Number
})

const onSubmit = (values) => {
    router.post('/verify-token', values);
}
</script>

<template>
    <div class="img-container text-center">
        <img src="../../../images/logo-dark.png" class="img-fluid" alt="logo">
    </div>

    <h1 class="mb-0">{{ $t('reset-password.header') }}</h1>
    <p>{{ $t('reset-password.enter-token') }}</p>

    <Form :validation-schema="passwordTokenSchema" class="mt-4" @submit="onSubmit" v-slot="{errors}">
        <div class="form-group">
            <label for="token">{{ $t('sign-in.token') }}</label>
            <Field type="text" name="token" :class="`form-control mb-0 ${(!errors.token?'':'is-invalid')}`"
                   id="token" placeholder="Enter token" :validateOnInput="true"/>
            <ErrorMessage name="token" class="text-danger"/>
            <span v-if="$page.props.errors?.token" v-text="$page.props.errors?.token" class="text-danger"/>
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
