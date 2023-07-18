<script setup>
import {router} from "@inertiajs/vue3";

const props = defineProps({
    users: {}
})

const fields = ['name', 'email', {
    key: 'event_access',
    label: 'Event Access',
    thStyle: { width: "250px" },
}, 'role', 'action'];

const onPaginate = page => {
    router.get(`/users?page=${page}`)
}

const visit = (link, method = 'get', data = {}) => {
    if (method === 'get') {
        router.get(link);
    } else {
        router.delete(link, data);
    }
}
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <no-data v-if="!users.total" title="Users" link="/users/create"/>

                <iq-card v-if="users.total">
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ $t('sidebar.users') }}</h4>
                    </template>

                    <template v-slot:body>
                        <b-row class="mt-3">
                            <b-col sm="12" class="table-responsive">
                                <b-table :items="users.data" :fields="fields" class="table-responsive-sm table-borderless">
                                    <template #cell(email)="data">
                                        <a :href="`mailto:${data.item.email}`">{{data.item.email}}</a>
                                    </template>
                                    <template #cell(event_access)="data">
                                        <span class="badge badge-primary" v-if="data.item.access_all_events">All Events</span>
                                        <span v-else class="badge badge-primary mr-1" v-for="event in data.item.event_access" :key="event">{{event}}</span>
                                    </template>
                                    <template #cell(action)="data">
                                      <span>
                                        <b-button @click="visit(`/users/${data.item.id}/edit`)"
                                                  variant="outline-primary" class="mr-1"><i
                                            class="ri-edit-line"></i>Edit</b-button>
                                        <b-button
                                            v-if="data.item.role"
                                            :id="`popover-button-variants-delete-${data.item.id}`"
                                            variant="outline-danger" class="mr-1"><i
                                            class="ri-delete-bin-2-line"></i>Delete</b-button>
                                          <b-popover :target="`popover-button-variants-delete-${data.item.id}`"
                                                     variant="default" triggers="focus">
                                                <p><b>Are you sure you want to delete this user?</b></p>
                                                <b-btn variant="outline-danger"
                                                       @click.prevent.stop="visit(`/users/${data.item.id}`, 'delete')">
                                                    Yes, Delete
                                                </b-btn>
                                            </b-popover>
                                      </span>
                                    </template>
                                </b-table>

                                <b-pagination v-if="users.data && users.data.length > 0"
                                              v-model="users.current_page" @change="onPaginate"
                                              class="mt-2"
                                              :total-rows="users.total" :per-page="users.per_page" align="right"/>
                            </b-col>
                        </b-row>
                    </template>
                </iq-card>
            </b-col>
        </b-row>
    </b-container>
</template>
