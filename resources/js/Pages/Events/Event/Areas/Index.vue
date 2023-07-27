<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {computed} from "vue";

const props = defineProps({
    eventId: String,
    areas: Object
})

const fields = ['area', 'area_id','event', 'status', 'date_created'];

const userRole = computed(() => usePage().props.user_role);

const onPaginate = page => {
    router.get(`/event/${props.eventId}/areas?page=${page}`);
}
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <Link v-if="userRole !== 'Operations' && userRole !== 'Viewers'" :href="`/event/${eventId}/areas/create`"
                      class="btn btn-primary mb-3">{{ areas.total === 0 ? 'Create' : 'Update' }} Areas
                </Link>

                <no-data v-if="!areas.total" title="Areas" :link="`/event/${eventId}/areas/create`" :sub-text="true"/>

                <iq-card v-if="areas.total">
                    <template v-slot:headerTitle>
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">{{ $t('sidebar.areas') }}</h4>
                        </div>
                    </template>
                </iq-card>

                <iq-card v-if="areas.total">
                    <template v-slot:body>
                        <b-row class="mt-3">
                            <b-col sm="12" class="table-responsive">
                                <b-table :items="areas.data" :fields="fields"
                                         class="table-responsive-sm table-borderless">
                                    <template #cell(event)="data">
                                        <Link :href="`/event/${data.item.event?.id}/dashboard`">
                                            {{
                                                data.item.event?.title
                                            }} {{
                                                data.item.event?.title_arabic ? `(${data.item.event?.title_arabic})` : ''
                                            }}
                                        </Link>
                                    </template>
                                </b-table>

                                <div class="mt-5">
                                    <b-pagination v-if="areas.data && areas.data.length > 0"
                                                  v-model="areas.current_page" @change="onPaginate"
                                                  class="mt-2"
                                                  :total-rows="areas.total" :per-page="areas.per_page"
                                                  align="center"/>

                                </div>
                            </b-col>
                        </b-row>
                    </template>
                </iq-card>
            </b-col>
        </b-row>
    </b-container>
</template>
