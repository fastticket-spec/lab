<template>
    <Form :validation-schema="signInSchema" class="mt-4" @submit="onSubmit" v-slot="{errors}">
        <div class="form-group">
            <label for="emailInput">{{ $t('sign-in.email-address') }}</label>
            <Field type="email" name="email" :class="`form-control mb-0 ${(!errors.email?'':'is-invalid')}`"
                   id="emailInput" :placeholder="$t('sign-in.enter-email')" :validateOnInput="true"/>
            <ErrorMessage name="email" class="text-danger"/>
        </div>

        <div class="form-group">
            <label for="passwordInput">{{ $t('sign-in.password') }}</label>
            <a href="/password-reset" class="float-right">{{ $t('sign-in.forgot-password') }}</a>
            <Field type="password" name="password" :class="`form-control mb-0 ${(!errors.password?'':'is-invalid')}`"
                   id="passwordInput" :placeholder="$t('sign-in.password')" :validateOnInput="true"/>
            <ErrorMessage name="password" class="text-danger"/>
        </div>

        <div class="d-inline-block w-100">
            <div class="">
                <Field v-slot="{ field }" name="remember" type="checkbox" :value="true">
                    <label>
                        <input type="checkbox" name="terms" v-bind="field"/>
                        {{ $t('sign-in.remember-me') }}
                    </label>
                </Field>
            </div>

            <div>
                <span v-if="$page.props.code !== 200" class="text-danger">{{ $page.props.message }}</span>
            </div>

            <button type="submit" :disabled="Object.keys(errors).length > 0"
                    class="btn btn-primary float-right">{{ $t('sign-in.header') }}
            </button>
        </div>
    </Form>
</template>

<script setup>
import {Form, Field, ErrorMessage} from 'vee-validate';
import {signInSchema} from "../../../Shared/components/helpers/Validators";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    code: Number,
    message: String
});

const onSubmit = (values) => {
    router.post('/login', values);
}

</script>
