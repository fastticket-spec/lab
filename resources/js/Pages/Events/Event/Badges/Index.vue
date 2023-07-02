<script setup>
import {router} from "@inertiajs/vue3";

const props = defineProps({
    badges: Object,
    eventId: String
});

const fields = ['title', 'event', 'description', 'width', 'height', 'status', 'access_levels', 'date_created', 'action']

const visit = (link, method = 'get', useInertia = true) => {
    if (method === 'get') {
        useInertia ? router.get(link) : location.href = link;
    } else {
        router.post(link);
    }
}

const onPaginate = page => {
    router.get(`/event/${props.eventId}/badges?page=${page}`)
}
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <Link :href="`/event/${eventId}/badges/create`" class="btn btn-primary mb-3">Add Badge</Link>
                <no-data v-if="!badges.total" title="Badges" :link="`/event/${eventId}/badges/create`"
                         :sub-text="true"/>

                <iq-card v-if="badges.total">
                    <template v-slot:body>
                        <b-row class="mt-3">
                            <b-col sm="12" class="table-responsive">
                                <b-table :items="badges.data" :fields="fields"
                                         class="table-responsive-sm table-borderless">
                                    <template #cell(access_levels)="data">
                                        <span>
                                            {{ data.item.access_levels ? data.item.access_levels.join(', ') : '' }}
                                        </span>
                                    </template>

                                    <template #cell(event)="data">
                                        <Link :href="`/event/${data.item.event.id}/dashboard`">
                                            {{
                                                data.item.event?.title
                                            }} {{
                                                data.item.event?.title_arabic ? `(${data.item.event?.title_arabic})` : ''
                                            }}
                                        </Link>
                                    </template>

                                    <template #cell(action)="data">
                                      <span>
                                          <b-dropdown id="dropdown-right" right text="Actions" size="sm"
                                                      variant="primary">
                                            <b-dropdown-item
                                                @click.prevent="visit(`/event/${eventId}/badges/${data.item.id}/edit`, 'get')">Edit</b-dropdown-item>
                                            <b-dropdown-item
                                                @click.prevent="visit(`/event/${eventId}/badges/${data.item.id}/customize`, 'get', false)">Customize</b-dropdown-item>
                                        </b-dropdown>
                                      </span>
                                    </template>
                                </b-table>

                                <div class="mt-5">
                                    <b-pagination v-if="badges.data && badges.data.length > 0"
                                                  v-model="badges.current_page" @change="onPaginate"
                                                  class="mt-2"
                                                  :total-rows="badges.total" :per-page="badges.per_page"
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
