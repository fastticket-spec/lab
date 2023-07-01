<script setup>
import {router} from "@inertiajs/vue3";

const props = defineProps({
    eventId: String,
    zones: Object
})

const fields = ['zone', 'event', 'status', 'date_created'];

const onPaginate = page => {
    router.get(`/event/${props.eventId}/zones?page=${page}`);
}
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <Link :href="`/event/${eventId}/zones/create`" class="btn btn-primary mb-3">{{zones.total === 0 ? 'Create' : 'Update'}} Zones</Link>

                <no-data v-if="!zones.total" title="Zones" :link="`/event/${eventId}/zones/create`" :sub-text="true"/>

                <iq-card v-if="zones.total">
                    <template v-slot:body>
                        <b-row class="mt-3">
                            <b-col sm="12" class="table-responsive">
                                <b-table :items="zones.data" :fields="fields"
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
                                    <b-pagination v-if="zones.data && zones.data.length > 0"
                                                  v-model="zones.current_page" @change="onPaginate"
                                                  class="mt-2"
                                                  :total-rows="zones.total" :per-page="zones.per_page"
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
