<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">

                <iq-card v-if="services.total">
                    <template v-slot:headerTitle>
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">{{ $t('sidebar.services') }}</h4>
                            <div class="form-group">
                                <select class="form-control" @change="sortServices" v-model="selectedSort">
                                    <option value="">Sort services by:</option>
                                    <option value="sort_by_creation">Creation Date</option>
                                    <option value="sort_by_service">Service Title</option>
                                    <option value="sort_by_creation">Quantity Sold</option>
                                    <option value="sort_by_creation">Sales Volume</option>
                                </select>
                            </div>
                        </div>
                    </template>
                </iq-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col sm="12">
                <Link v-if="!userRole || userRole === 'Admin Users'" :href="`/event/${event.id}/services/create`" class="btn btn-primary"><i class="ri-add-line"></i> Create Service</Link>
            </b-col>
        </b-row>

        <b-row class="page-cards mt-3">
            <b-col sm="6" v-for="(service, i) in services.data" :key="service.id">
                <b-card :title="service.service" class="iq-mb-3">
                    <div class="card-date d-flex flex-column text-center mt-3" :class="{'card-date-ar': locale === 'ar'}">
                        <h5>{{ service.price > 0 ? `${(+service.price).toLocaleString()} SAR` : 'FREE' }}</h5>
                    </div>

                    <div v-if="service.multidates.length > 0" class="row">
                        <div class="col-8 offset-2 my-3">
                            <select class="form-control" v-model="service.chosen_multidate" @change="service.show_total = 0">
                                <option value="">Choose Date</option>
                                <option v-for="serviceDate in service.multidates" :key="serviceDate.id" :value="serviceDate.id">{{ serviceDate.schedule_date}}</option>
                            </select>
                        </div>
                    </div>

                    <b-card-text v-if="(service.multidates.length === 0 && !service.show_total) || (service.multidates.length > 0 && service.chosen_multidate && !service.show_total)" class="d-flex w-100 justify-content-around my-2">
                        <div class="text-center">
                            <h5>{{ (+dateStats(service.chosen_multidate, service).quantity_sold).toLocaleString() }}</h5>
                            <span>Sold</span>
                        </div>

                        <div class="text-center">
                            <h5>{{ (+dateStats(service.chosen_multidate, service).quantity_available).toLocaleString() }}</h5>
                            <span>Remaining</span>
                        </div>

                        <div class="text-center">
                            <h5>SAR {{ dateStats(service.chosen_multidate, service).sales_volume }}</h5>
                            <span>Revenue</span>
                        </div>
                    </b-card-text>

                    <b-card-text v-if="service.multidates.length > 0 && service.show_total" class="d-flex w-100 justify-content-around my-2">
                        <div class="text-center">
                            <h5>{{ totalStats(service).sold }}</h5>
                            <span>Sold</span>
                        </div>

                        <div class="text-center">
                            <h5>{{ totalStats(service).available }}</h5>
                            <span>Remaining</span>
                        </div>

                        <div class="text-center">
                            <h5>SAR {{ totalStats(service).sales.toLocaleString(2, {minimumFractionDigits: 2}) }}</h5>
                            <span>Revenue</span>
                        </div>
                    </b-card-text>

                    <div class="d-flex justify-content-around mt-3">
                        <Link v-if="!userRole || userRole === 'Admin Users'" :href="`/event/${event.id}/services/${service.id}`" ><i class="ri-edit-line"></i>
                            Edit</Link>
                        <a href="#" v-if="event.is_multiple" @click="service.show_total = (service.show_total ? 0 : 1)"><i class="ri-bar-chart-2-line"></i>
                            Total Statistics</a>
                        <a v-if="!userRole || userRole === 'Admin Users'" href="#" @click.prevent.stop="" class="text-danger"
                           :id="`popover-button-variants-${service.id}`"><i
                            :class="{'ri-play-line': service.pause_service, 'ri-pause-line': !service.pause_service}"></i>
                            {{ service.pause_service ? 'Activate' : 'Pause' }}</a>
                        <b-popover :target="`popover-button-variants-${service.id}`"
                                   variant="default" triggers="focus">
                            <p><b>Are you sure you want to {{ service.pause_service ? 'activate' : 'pause' }} this service?</b></p>
                            <b-btn variant="outline-danger"
                                   @click.prevent.stop="visit(`/event/${event.id}/services/${service.id}/toggle-pause`, 'post')">Yes, {{ service.pause_service ? 'Activate' : 'Pause' }}
                            </b-btn>
                        </b-popover>
                    </div>
                </b-card>
            </b-col>
        </b-row>

        <no-data v-if="!services.total" title="Services" :link="`/event/${event.id}/services/create`"/>

        <b-pagination v-if="services.data && services.data.length > 0" v-model="services.current_page" @change="onPaginate"
                      :total-rows="services.total" :per-page="services.per_page" align="center"/>
    </b-container>
</template>

<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {computed, onMounted, ref} from "vue";

const props = defineProps({
    services: {},
    event: {}
})

const locale = ref('');

const userRole = computed(() => usePage().props.user_role);

onMounted(() => {
    locale.value = localStorage.getItem('locale');
})


const selectedSort = ref('');

const sortServices = () => {
    visit(`/event/${props.event.id}/services?sort=${selectedSort.value}`)
}

const visit = (link, method = 'get') => {
    if (method === 'get') {
        router.get(link);
    } else if (method === 'post') {
        router.post(link);
    } else {
        router.delete(link)
    }
}

const dateStats = computed(() => (dateId, service) => {
    if (!dateId && !service) return {}

    if (!props.event.is_multiple) {
        return service.singleDate || {};
    }

    const serviceMultiDates = service?.multidates;

    return serviceMultiDates.find(x => x.id === dateId).pivot;
})

const totalStats = computed(() => (service) => {
    if (!service) return {}

    const serviceStats = service?.multidates.map(x => x.pivot);

    return {
        sold: serviceStats.reduce((a, b) => a + (b.quantity_sold || 0), 0),
        available: serviceStats.reduce((a, b) => a + (b.quantity_available || 0), 0),
        sales: serviceStats.reduce((a, b) => +a + (+b.sales_volume || 0), 0)
    }
})

const onPaginate = page => {
    router.get(`/event/${props.event.id}/services?sort=${selectedSort.value}&page=${page}`)
}
</script>

<style>
.page-cards .card {
    border: 1px solid var(--iq-primary);
}

.card-body {
    box-shadow: 2px 4px 8px;
}

.card-date {
    position: absolute;
    top: 0;
    right: 0;
    padding: 4px 15px;
    margin: 5px;
    border: 1px solid var(--iq-primary);
    border-radius: 5px;
}

.card-date-ar {
    left: 0;
    right: unset;
}

</style>
