<script setup>
import {router} from "@inertiajs/vue3";

const props = defineProps({
    managers: Object
})

const fields = ['first_name', 'last_name', 'email', 'action'];

const onPaginate = page => {
    router.get(`/account-managers?page=${page}`)
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
                <no-data v-if="!managers.total" title="Managers" link="/managers/create"/>

                <iq-card v-if="managers.total">
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ $t('sidebar.accountManagers') }}</h4>
                    </template>

                    <template v-slot:body>
                        <b-row class="mt-3">
                            <b-col sm="12" class="table-responsive">
                                <b-table :items="managers.data" :fields="fields" class="table-responsive-sm table-borderless">
                                    <template #cell(email)="data">
                                        <a :href="`mailto:${data.item.email}`">{{data.item.email}}</a>
                                    </template>
                                    <template #cell(action)="data">
                                      <span>
                                        <b-button
                                            v-if="!data.item.is_current_user && data.item.parent_account_id"
                                            :id="`popover-button-variants-delete-${data.item.id}`"
                                            variant="outline-danger" class="mr-1"><i
                                            class="ri-delete-bin-2-line"></i>Delete</b-button>
                                          <b-popover :target="`popover-button-variants-delete-${data.item.id}`"
                                                     variant="default" triggers="focus">
                                                <p><b>Are you sure you want to delete this manager?</b></p>
                                                <b-btn variant="outline-danger"
                                                       @click.prevent.stop="visit(`/account-managers/${data.item.id}`, 'delete')">
                                                    Yes, Delete
                                                </b-btn>
                                            </b-popover>
                                      </span>
                                    </template>
                                </b-table>

                                <b-pagination v-if="managers.data && managers.data.length > 0"
                                              v-model="managers.current_page" @change="onPaginate"
                                              class="mt-2"
                                              :total-rows="managers.total" :per-page="managers.per_page" align="right"/>
                            </b-col>
                        </b-row>
                    </template>
                </iq-card>
            </b-col>
        </b-row>
    </b-container>
</template>
