<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">

                <iq-card v-if="tickets.total">
                    <template v-slot:headerTitle>
                        <div class="d-flex justify-content-between">
                            <h4 class="card-title">{{ $t('sidebar.tickets') }}</h4>
                            <div class="form-group">
                                <select class="form-control" @change="sortEvents" v-model="selectedSort">
                                    <option value="">Sort tickets by:</option>
                                    <option value="sort_by_creation">Creation Date</option>
                                    <option value="sort_by_title">Ticket Title</option>
                                    <option value="sort_by_quantity_sold">Quantity Sold</option>
                                    <option value="sort_by_volume">Sales Volume</option>
                                </select>
                            </div>
                        </div>
                    </template>
                </iq-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col sm="12">
                <Link v-if="(!userRole || userRole === 'Admin Users')" :href="`/event/${event.id}/tickets/create`" class="btn btn-primary"><i
                    class="ri-add-line"></i> Create Ticket
                </Link>
            </b-col>
        </b-row>

        <draggable v-model="tickets.data" class="page-cards mt-3 row" item-key="id" @change="sortEnded">
            <template #item="{element}">
                <div class="col-md-6">
                    <b-card :title="element.title" class="iq-mb-3">
                        <div class="card-date d-flex flex-column text-center mt-3"
                             :class="{'card-date-ar': locale === 'ar'}">
                            <h5>{{ element.price > 0 ? `${(+element.price).toLocaleString()} SAR` : (element.contact_to_purchase ? 'CONTACT' : 'FREE') }}</h5>
                        </div>

                        <div v-if="element.mulidates.length > 0" class="row">
                            <div class="col-8 offset-2 my-3">
                                <select class="form-control" v-model="element.chosen_multidate"
                                        @change="element.show_total = 0">
                                    <option value="">Choose Date</option>
                                    <option v-for="ticketDate in element.mulidates" :key="ticketDate.id"
                                            :value="ticketDate.id">{{ ticketDate.schedule_date }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <b-card-text
                            v-if="(element.mulidates.length === 0 && !element.show_total) || (element.mulidates.length > 0 && element.chosen_multidate && !element.show_total)"
                            class="d-flex w-100 justify-content-around my-2">
                            <div class="text-center">
                                <h5>{{ (+dateStats(element.chosen_multidate, element).quantity_sold) }}</h5>
                                <span>Sold</span>
                            </div>

                            <div class="text-center">
                                <h5>{{
                                        (+dateStats(element.chosen_multidate, element).quantity_available).toLocaleString()
                                    }}</h5>
                                <span>Remaining</span>
                            </div>

                            <div class="text-center">
                                <h5>SAR {{ dateStats(element.chosen_multidate, element).sales.toLocaleString() }}</h5>
                                <span>Revenue</span>
                            </div>
                        </b-card-text>

                        <b-card-text v-if="element.mulidates.length > 0 && element.show_total"
                                     class="d-flex w-100 justify-content-around my-2">
                            <div class="text-center">
                                <h5>{{ totalStats(element).sold.toLocaleString() }}</h5>
                                <span>Sold</span>
                            </div>

                            <div class="text-center">
                                <h5>{{ totalStats(element).available.toLocaleString() }}</h5>
                                <span>Remaining</span>
                            </div>

                            <div class="text-center">
                                <h5>SAR {{ totalStats(element).sales.toLocaleString(2) }}</h5>
                                <span>Revenue</span>
                            </div>
                        </b-card-text>

                        <div class="d-flex justify-content-around mt-3">
                            <Link v-if="!userRole || userRole === 'Admin Users'" :href="`/event/${event.id}/tickets/${element.id}`"><i
                                class="ri-edit-line"></i>
                                Edit
                            </Link>
                            <a v-if="event.is_multiple" href="#"
                               @click="element.show_total = (element.show_total ? 0 : 1)"><i
                                class="ri-bar-chart-2-line"></i>
                                {{ element.show_total ? 'Hide' : 'Show' }} Total Statistics</a>
                            <a v-if="!userRole || userRole === 'Admin Users'" href="#" @click.prevent.stop="" class="text-danger"
                               :id="`popover-button-variants-${element.id}`"><i
                                :class="{'ri-play-line': element.pause_ticket, 'ri-pause-line': !element.pause_ticket}"></i>
                                {{ element.pause_ticket ? 'Activate' : 'Pause' }}</a>
                            <b-popover :target="`popover-button-variants-${element.id}`"
                                       variant="default" triggers="focus">
                                <p><b>Are you sure you want to {{ element.pause_ticket ? 'activate' : 'pause' }} this
                                    event?</b></p>
                                <b-btn variant="outline-danger"
                                       @click.prevent.stop="visit(`/event/${event.id}/tickets/${element.id}/toggle-pause`, 'post')">
                                    Yes, {{ element.pause_ticket ? 'Activate' : 'Pause' }}
                                </b-btn>
                            </b-popover>
                        </div>
                    </b-card>
                </div>
            </template>
        </draggable>

        <no-data v-if="!tickets.total" title="Tickets" :link="`/event/${event.id}/tickets/create`"/>

        <div v-if="tickets.data && tickets.data.length > 0" class="d-flex row align-items-center justify-content-center my-4">
            <b-select class="form-control col-md-1 col-sm-3" v-model="tickets.per_page" @change="onPerPageChange">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="30">30</option>
                <option value="40">40</option>
                <option value="50">50</option>
            </b-select>

            <span class="ml-1 mr-4">per page</span>

            <b-pagination v-model="tickets.current_page"
                          @change="onPaginate"
                          class="mb-0"
                          :total-rows="tickets.total"
                          :per-page="tickets.per_page"
                          align="center"/>
        </div>
    </b-container>
</template>

<script setup>
import {router, usePage} from "@inertiajs/vue3";
import {computed, onMounted, ref} from "vue";
import draggable from 'vuedraggable';
import axios from "axios";

draggable.compatConfig = {MODE: 3}

const props = defineProps({
    tickets: {},
    event: {},
    query: ''
})

const locale = ref('');

onMounted(() => {
    locale.value = localStorage.getItem('locale');
})

const selectedSort = ref(props.query || '');

const userRole = computed(() => usePage().props.user_role);

const sortEvents = () => {
    visit(`/event/${props.event.id}/tickets?sort=${selectedSort.value}`)
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

const onPaginate = page => {
    router.get(`/event/${props.event.id}/tickets?sort=${selectedSort.value}&page=${page}&per_page=${props.tickets.per_page}`)
}

const onPerPageChange = () => {
    router.get(`/event/${props.event.id}/tickets?sort=${selectedSort.value}&page=${props.tickets.current_page}&per_page=${props.tickets.per_page}`)
}

const dateStats = computed(() => (dateId, ticket) => {
    if (!dateId && !ticket) return {}

    const ticketQuantity = ticket.quantity;

    if (!props.event.is_multiple || !ticket?.mulidates) {
        const singleDateStat = ticket.singleDate;

        const quantitySold = +singleDateStat.quantity_sold + (singleDateStat?.time_slot_stats?.reduce((a, b) => +a + b.quantity_sold, 0) || 0);
        const salesVolume = +singleDateStat.sales_volume + (singleDateStat?.time_slot_stats?.reduce((a, b) => +a + (b?.sales_volume || 0), 0) || 0);
        const ticketRemaining = ticketQuantity - quantitySold;

        return {
            quantity_sold: quantitySold,
            quantity_available: ticketRemaining,
            sales: salesVolume
        }
    }

    const ticketMultiDates = ticket?.mulidates;
    const pivot = ticketMultiDates.find(x => x.id === dateId)?.pivot;

    const quantitySold = +pivot?.quantity_sold + (pivot?.time_slot_stats?.reduce((a, b) => +a + b.quantity_sold, 0) || 0);
    const ticketRemaining = ticketQuantity - quantitySold;
    const salesVolume = +pivot?.sales_volume + (pivot?.time_slot_stats?.reduce((a, b) => +a + (b?.sales_volume || 0), 0) || 0);

    return {
        quantity_sold: quantitySold || 0,
        quantity_available: ticketRemaining || 0,
        sales: salesVolume || 0
    }
})

const totalStats = computed(() => (ticket) => {
    if (!ticket) return {}

    const ticketStats = ticket?.mulidates.map(x => {
        const quantitySold = +x.pivot?.quantity_sold + (x.pivot?.time_slot_stats?.reduce((a, b) => +a + +b.quantity_sold, 0) || 0);
        const ticketRemaining = ticket.quantity - quantitySold;
        const salesVolume = +x.pivot?.sales_volume + (x.pivot?.time_slot_stats?.reduce((a, b) => +a + (b?.sales_volume || 0), 0) || 0);

        return {
            sold: quantitySold, available: ticketRemaining, sales: salesVolume
        }
    });

    return {
        sold: ticketStats.reduce((a, b) => a + (b.sold || 0), 0),
        available: ticketStats.reduce((a, b) => a + (b.available || 0), 0),
        sales: ticketStats.reduce((a, b) => a + (+b.sales || 0), 0)
    }
})

const sortEnded = async () => {
    if (selectedSort.value) return false;

    const tickets = props.tickets.data.map((ticket, i) => ({
        ticket_id: ticket.id,
        i
    }));

    try {
        const {data} = await axios.post(`/event/${props.event.id}/tickets/update-indices`, {tickets})
        console.log({data})
    } catch (e) {
        console.log(e);
    }
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
