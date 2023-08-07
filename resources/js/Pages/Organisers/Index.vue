<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <no-data v-if="!organisers.total" title="Organisers" link="/organisers/create" />

                <iq-card v-if="organisers.total">
                    <template v-slot:headerTitle>
                        <h4 class="card-title mb-2">{{ $t('sidebar.organisers') }}</h4>
                    </template>
                </iq-card>
            </b-col>
        </b-row>

        <b-row class="page-cards">
            <b-col sm="6" v-for="organiser in organisers.data" :key="organiser.id">
                <b-card :title="lang === 'ar' ? (organiser.name_arabic || organiser.name) : organiser.name" class="iq-mb-3">
                    <b-card-sub-title>
                        <div><small><a :href="`mailto:${organiser.email}`"><i class="ri-mail-line"></i> {{ organiser.email }}</a></small></div>
                        <div><small><a :href="`tel:${organiser.phone}`"><i class="ri-phone-line"></i> {{ organiser.phone }}</a></small></div>
                    </b-card-sub-title>

                    <div class="d-flex justify-content-around mt-5">
                        <a href="#" class="text-secondary" @click.prevent="visit(`/organisers/${organiser.id}/set-organiser`, 'post')"><i
                            class="ri-key-2-line"></i> {{ $t('login_as_organiser') }}</a>
                        <a href="#" @click.prevent.stop="visit(`/organisers/${organiser.id}/edit`)" class="text-primary"><i class="ri-edit-line"></i>
                            {{ $t('button.edit') }}</a>
                    </div>
                </b-card>
            </b-col>
        </b-row>

        <b-pagination v-if="organisers.data && organisers.data.length > 0" v-model="organisers.current_page" @change="onPaginate"
                      :total-rows="organisers.total" :per-page="organisers.per_page" align="center"/>
    </b-container>
</template>

<script setup>

import {router, usePage} from "@inertiajs/vue3";
import {onUpdated, ref} from "vue";

onUpdated(() => {
    if (usePage().props.active_organiser) {
        location.href = '/dashboard';
    }
})

const lang = ref(localStorage.getItem('locale'));

let props = defineProps({organisers: {}, filters: {}})

const fields = ['name', 'email', 'phone', 'action'];

const visit = (link, method = 'get') => {
    if (method === 'get') {
        router.get(link);
    } else {
        router.post(link);
    }
}

const onPaginate = page => {
    router.get(`/organisers?page=${page}`)
}
</script>

<style>
.page-cards .card {
    border: 1px solid var(--iq-primary);
}

.card-body {
    box-shadow: 2px 4px 8px;
}
</style>
