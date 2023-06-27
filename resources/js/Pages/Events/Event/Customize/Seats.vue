<script setup>
import {onMounted, ref, watch} from "vue";
import axios from "axios";

const props = defineProps({
    event: Object,
    secret_key: String,
    chart_data: null | String,
    charts: Array,
    chart_event: null | Object
});

const enableSeatsIntegration = ref(false);
const showCreateSeatsChart = ref(0)
const isPublishing = ref(false);
const showChartsModal = ref(false);
const chosenChart = ref({})
const showDesigner = ref(false)
const chartId = ref('');
const chartPublished = ref(false);

onMounted(() => {
    enableSeatsIntegration.value = !!props.event.has_seats
    chartId.value = props.event.seats_chart_id;
    chartPublished.value = props.event.seats_chart_published;

    if (props.event.seats_chart_id && props.chart_data) {
        chosenChart.value = props.charts.find(x => x.key === props.chart_data.key);
    }

    if (props.event.seats_chart_id) {
        showCreateSeatsChart.value = 0;
    }
});

const publishChart = async (chartId = null) => {
    try {
        const {data} = await axios.post(`/event/${props.event.id}/publish-chart`, {
            chart_id: chartId || chartId.value
        });

        console.log({data})

        chartPublished.value = true;
    } catch (e) {
        console.log(e);
    }
}

watch(showDesigner, value => {
    if (value && !props.event.seats_chart_id) {
        new seatsio.SeatingChartDesigner({
            divId: 'chart-manager',
            onChartCreated: key => chartId.value = key,
            secretKey: props.secret_key
        }).render();
    } else if (value && props.event.seats_chart_id) {
        new seatsio.SeatingChartDesigner({
            divId: 'chart-manager',
            secretKey: props.secret_key,
            chartKey: chosenChart.value.key
        }).render();
    }
})
</script>

<template>
    <b-container fluid class="event-form">
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:headerTitle>
                        <h4 class="card-title">Seats.io Customisation</h4>
                    </template>
                    <template v-slot:body>
                        <b-row class="mt-3">
                            <b-col sm="6" class="mb-3">
                                <b-checkbox v-model="enableSeatsIntegration" id="showArabic"
                                            class="custom-checkbox-color"
                                            name="check-button" inline>
                                    Enable Seats Integration
                                </b-checkbox>
                                <br/>
                                <template v-if="enableSeatsIntegration">
                                    <template v-if="!chart_event">
                                        <b-radio v-model="showCreateSeatsChart" :value="0"
                                                 class="custom-checkbox-color mt-3"
                                                 name="create-seats-radio" inline>
                                            Choose Chart
                                        </b-radio>

                                        <b-radio v-model="showCreateSeatsChart" :value="1"
                                                 class="custom-checkbox-color mt-3"
                                                 name="create-seats-radio" inline>
                                            Or Create New Chart
                                        </b-radio>

                                        <div v-if="showCreateSeatsChart === 0" class="mt-2">
                                            <b-btn @click="showChartsModal = true" variant="primary">
                                                {{ chosenChart?.id ? `${chosenChart.name} chosen` : 'Choose Chart' }}
                                            </b-btn>
                                            <b-btn v-if="chosenChart?.id" @click="showDesigner = true" variant="primary" class="ml-2">
                                                Edit Chart Design
                                            </b-btn>
                                        </div>

                                        <div v-if="showCreateSeatsChart === 1">
                                            <b-btn @click="showDesigner = true" variant="primary">Create Chart</b-btn>
                                        </div>
                                    </template>

                                    <b-btn v-if="chart_event" @click="showDesigner = true" variant="primary" class="mt-2">
                                        Edit Chart Design
                                    </b-btn>
                                </template>
                            </b-col>
                        </b-row>

                        <b-row v-if="enableSeatsIntegration">
                            <b-col sm="12">
                                <div id="chart-manager" style="height: 800px"></div>
                            </b-col>
                        </b-row>

                        <b-btn v-if="!chartPublished" @click="publishChart" variant="primary" class="mt-3">
                            {{ isPublishing ? 'Publishing ...' : 'Publish Chart' }}
                        </b-btn>
                    </template>
                </iq-card>
            </b-col>
        </b-row>
    </b-container>

    <b-modal v-model="showChartsModal" size="lg" title="Seats.Io Charts">
        <div class="charts row">
            <div class="chart-container col-md-6" v-for="chart in charts" @click="chosenChart = chart">
                <div class="chart" :class="{chart: true, 'active': chosenChart.id === chart.id}">
                    <h5 class="chart--name">{{ chart.name }}</h5>
                    <span class="badge"
                          :class="{'badge-success': chart.status === 'PUBLISHED' || chart.status === 'PUBLISHED_WITH_DRAFT', 'badge-secondary': chart.status === 'NOT_USED'}">{{
                            chart.status
                        }}</span>
                    <img :src="chart.publishedVersionThumbnailUrl" class="chart--image"/>
                </div>
            </div>
        </div>

        <template #modal-footer>
            <div class="w-100">
                <b-button
                    :disabled="!chosenChart?.id"
                    variant="primary"
                    size="sm"
                    class="float-right"
                    @click="showChartsModal = false"
                >
                    Done
                </b-button>

<!--                <b-button-->
<!--                    :disabled="!chosenChart?.id"-->
<!--                    variant="primary"-->
<!--                    size="sm"-->
<!--                    class="float-right mr-2"-->
<!--                    @click="publishChart(chosenChart.id)"-->
<!--                >-->
<!--                    Use Chart-->
<!--                </b-button>-->
            </div>
        </template>
    </b-modal>
</template>

<style>
#chart {
    height: 100%;
}

.charts {
    max-height: 520px;
    overflow: auto;
}

.chart-container {

}

.chart {
    cursor: pointer;
    padding: 10px;
    margin-bottom: 10px;
}

.chart.active, .chart:hover {
    border: 1px solid #e3e3e3;
    border-radius: 7px;
    background-color: #e3e3e3;
}

.chart--image {
    width: 100%
}
</style>