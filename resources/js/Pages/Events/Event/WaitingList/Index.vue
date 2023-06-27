<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">{{ $t('sidebar.waitingList') }}</h4>
                    </template>
                    <template v-slot:body>
                        <b-row class="mt-3">
                            <b-col sm="12" class="table-responsive">
                                <b-table :items="waiting_lists.data" :fields="fields" class="table-responsive-sm table-borderless">
<!--                                    <template #cell(action)="data">-->
<!--                                      <span>-->
<!--                                        <b-button @click="visit(`/event-categories/${data.item.id}`)"-->
<!--                                                  variant="outline-primary" class="mb-3 mr-1"><i-->
<!--                                            class="ri-edit-line"></i>Edit</b-button>-->
<!--                                        <b-button @click="visit(`/event-categories/${data.item.id}`, 'patch', {status: data.item.status !== 1})"-->
<!--                                                  :variant="`outline-${data.item.status === 1 ? 'danger' : 'success'}`" class="mb-3 mr-1"><i-->
<!--                                            :class="{'ri-eye-line': data.item.status !== 1, 'ri-eye-off-line': data.item.status === 1}"></i>{{ data.item.status === 1 ? 'Deactivate' : 'Activate' }}</b-button>-->
<!--                                      </span>-->
<!--                                    </template>-->
                                </b-table>
                                <b-pagination value="1" :total-rows="50" align="right" class="mt-2"/>
                            </b-col>
                        </b-row>
                    </template>
                </iq-card>
            </b-col>
        </b-row>
    </b-container>

</template>

<script setup>
import {router} from "@inertiajs/vue3";

defineProps({
    waiting_lists: {}
})

const fields = ['name', 'email', 'ticket', 'number_of_tickets', 'number_of_tickets_remaining', 'status', 'action'];

const visit = (link, method = 'get', data = {}) => {
    if (method === 'get') {
        router.get(link);
    } else {
        router.patch(link, data);
    }
}
</script>
