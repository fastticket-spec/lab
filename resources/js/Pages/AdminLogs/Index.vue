<script setup>
import {router} from "@inertiajs/vue3";

const props = defineProps({
  logs: []
})

const fields = ['description', 'subject_type', 'action_date'];

const onPaginate = page => {
  router.get(`/admin-logs?page=${page}`)
}

const visit = (link, method = 'get', data = {}) => {
  if (method === 'get') {
    router.get(link);
  } else {
    router.delete(link, data);
  }
}

const getSubjectType = subjectType => {
  if (subjectType) {
    const typeArray = subjectType.split('\\');
    return typeArray[typeArray.length - 1];
  }

  return '';
}
</script>

<template>
  <b-container fluid>
    <b-row>
      <b-col sm="12">
        <iq-card v-if="logs.total">
          <template v-slot:headerTitle>
            <h4 class="card-title">{{ $t('sidebar.logs') }}</h4>
          </template>

          <template v-slot:body>
            <b-row class="mt-3">
              <b-col sm="12" class="table-responsive">
                <b-table :items="logs.data" :fields="fields" class="table-responsive-sm table-borderless">
                  <template #cell(subject_type)="data">
                    {{getSubjectType(data.item.subject_type)}}
                  </template>
                </b-table>

                <b-pagination v-if="logs.data && logs.data.length > 0"
                              v-model="logs.current_page" @change="onPaginate"
                              class="mt-2"
                              :total-rows="logs.total" :per-page="logs.per_page" align="right"/>
              </b-col>
            </b-row>
          </template>
        </iq-card>
      </b-col>
    </b-row>
  </b-container>
</template>
