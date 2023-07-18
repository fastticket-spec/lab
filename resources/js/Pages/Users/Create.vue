<script setup>
import {router} from "@inertiajs/vue3";
import {onMounted, ref, watch} from "vue";
import {useForm, Field, ErrorMessage} from "vee-validate";
import {createUserSchema} from "../../Shared/components/helpers/Validators";
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";

const props = defineProps({
    editMode: Boolean,
    errors: {},
    roles: [],
    user: {},
    events: []
})

const chosenEvents = ref([]);
const allEvents = ref(false);

onMounted(() => {
    if (props.editMode) {
        allEvents.value = !!props.user?.all_events;

        if (!allEvents.value) {
            chosenEvents.value = props.user?.events_access;
            console.log(chosenEvents.value);
        }
    }
})

const initialValues = props.editMode ? {
    ...props.user,
    all_events: !!props.user?.all_events
} : {
    first_name: '',
    last_name: '',
    email: '',
    role_id: '',
    all_events: false
}

const {handleSubmit, isSubmitting, setFieldValue} = useForm({
    initialValues,
    validationSchema: createUserSchema,
});

watch(chosenEvents, (value) => {
    if (value.length > 0) {
        allEvents.value = false;
    }
});

watch(allEvents, (value) => {
    if (value) {
        chosenEvents.value = []
    }
})

const onSubmit = handleSubmit(values => {
    props.errors.all_events = ''
    if (chosenEvents.value === 0) {
        return props.errors.all_events = 'Please choose a category';
    }
    values.all_events = allEvents.value;
    values.event_ids = values.all_events ? props.events.map(x => x.id) : chosenEvents.value.map(x => x.id);

    props.editMode
        ? router.patch(`/users/${props.user.id}`, values)
        : router.post('/users', values);

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

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="role">{{ $t('input.role') }}</label>
                                        <Field as="select" name="role_id" id="role"
                                               class="form-control mb-0"
                                               :placeholder="$t('input.role')" :validateOnInput="true">
                                            <option value="">Select Role</option>
                                            <option v-for="role in roles" :key="role.id" :value="role.id">
                                                {{ role.role }}
                                            </option>
                                        </Field>
                                        <ErrorMessage name="role" class="text-danger"/>
                                        <span v-if="errors.role" class="text-danger">{{ errors.role }}</span>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="events">{{ $t('sidebar.events') }}</label>
                                        <vue-select class="form-control mb-0" v-model="chosenEvents"
                                                    :options="events" label="title"
                                                    multiple/>
                                        <ErrorMessage name="events" class="text-danger"/>
                                        <span v-if="errors.events" class="text-danger">{{
                                                errors.events
                                            }}</span>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <b-checkbox v-model="allEvents"
                                                    class="form-control custom-checkbox-color no-border"
                                                    name="check-button" inline>
                                            {{ $t('input.all_events') }}
                                        </b-checkbox>
                                        <span v-if="errors.all_events" class="text-danger">{{
                                                errors.all_events
                                            }}</span>
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
