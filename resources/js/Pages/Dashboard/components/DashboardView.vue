<script setup>
import {computed, ref} from "vue";
import {usePage} from "@inertiajs/vue3";

const props = defineProps({
    data: Array,
    attendees: Array
})

const userRole = computed(() => usePage().props.user_role);
const activeOrganiser = computed(() => usePage().props.active_organiser);

const showItem = item => {
    if (!userRole.value) {
        return true;
    }
    if (item.disabled_for) {
        return !item.disabled_for?.includes(userRole.value);
    }
    return true;
}
</script>

<template>
    <b-container fluid>
        <b-row>
            <template v-for="item in data" :key="item.name">
                <b-col lg="3" md="6" v-if="showItem(item)">
                    <iq-card class="iq-card-block iq-card-stretch iq-card-height">
                        <template v-slot:body>
                            <div class="d-flex flex-column justify-content-center py-2">
                                <div class="text-left mb-2"><h6>{{ item.iI8 ? $t(item.iI8) : item.name }}</h6></div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="value-box">
                                        <h2 class="mb-0">{{ item.count }}</h2>
                                    </div>
                                    <div class="iq-iconbox iq-bg-primary">
                                        <i :class="item.icon"/>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </iq-card>
                </b-col>
            </template>
        </b-row>

        <!--        <b-row>-->
        <!--            <template v-if="ticketChartData">-->
        <!--                <b-col lg="6" v-for="(item,index) in charts" :key="index">-->
        <!--                    <iq-card>-->
        <!--                        <template v-slot:headerTitle>-->
        <!--                            <div class="d-flex align-items-center">-->
        <!--                                <h4 class="m-0">{{ item.title }}</h4>-->
        <!--                                <p class="ml-2 m-0"><i class="ri-information-line"></i>&nbsp;Per day of event.</p>-->
        <!--                            </div>-->
        <!--                        </template>-->

        <!--                        <template v-slot:body>-->
        <!--                            <HighChart :ref="item.type" :option="item.bodyData"/>-->
        <!--                        </template>-->
        <!--                    </iq-card>-->
        <!--                </b-col>-->
        <!--            </template>-->
        <!--        </b-row>-->

        <b-row>
            <b-col md="6">
                <iq-card class="iq-card-block iq-card-stretch iq-card-height">
                    <template v-slot:headerTitle>
                        <h4 class="card-title">Recent Requests</h4>
                    </template>
                    <template v-slot:headerAction>
                        <b-dropdown id="dropdownMenuButton1" right variant="none" data-toggle="dropdown">
                            <template v-slot:button-content>
                                <span class="text-primary"><i class="ri-more-fill"></i></span>
                            </template>
                            <Link class="dropdown-item" href="/attendees"><i
                                class="ri-eye-fill mr-2"></i>{{ $t('dashboard.orders_view') }}
                            </Link>
                        </b-dropdown>
                    </template>
                    <template v-slot:body>
                        <ul class="suggestions-lists m-0 p-0">
                            <li v-for="attendee in attendees" :key="attendee.id"
                                class="d-flex align-items-center py-2 px-1">

                                <div class="media-support-info ml-3">
                                    <h6 :class="{'text-secondary': attendee.status === 0, 'text-primary': attendee.status === 1, 'text-danger': attendee.status === 2}">
                                        <span>{{ attendee.ref }}</span> <br/>
                                        <span>{{ attendee.email }} - {{ attendee.status === 1 ? 'Registered' : (attendee.status === 2 ? 'Declined' : 'Pending') }}</span><br/>
                                    </h6>
                                </div>
                                <h5>
                                    <Link :href="`/event/${attendee.event_id}/dashboard`"
                                          :class="{'text-secondary': attendee.status === 0, 'text-primary': attendee.status === 1, 'text-danger': attendee.status === 2}">
                                        {{ attendee.event.title }}
                                    </Link>
                                </h5>
                            </li>
                        </ul>
                    </template>
                </iq-card>
            </b-col>
        </b-row>

        <!--        <b-row>-->


        <!--            &lt;!&ndash;            <b-col lg="6">&ndash;&gt;-->
        <!--            &lt;!&ndash;                <iq-card class="iq-card-block iq-card-stretch iq-card-height">&ndash;&gt;-->
        <!--            &lt;!&ndash;                    <template v-slot:headerTitle>&ndash;&gt;-->
        <!--            &lt;!&ndash;                        <h4 class="card-title">{{ $t('dashboard.events') }}</h4>&ndash;&gt;-->
        <!--            &lt;!&ndash;                    </template>&ndash;&gt;-->
        <!--            &lt;!&ndash;                    <template v-slot:headerAction>&ndash;&gt;-->
        <!--            &lt;!&ndash;                        <b-dropdown id="dropdownMenuButton1" right variant="none" data-toggle="dropdown">&ndash;&gt;-->
        <!--            &lt;!&ndash;                            <template v-slot:button-content>&ndash;&gt;-->
        <!--            &lt;!&ndash;                                <span class="text-primary"><i class="ri-more-fill"></i></span>&ndash;&gt;-->
        <!--            &lt;!&ndash;                            </template>&ndash;&gt;-->
        <!--            &lt;!&ndash;                            <Link class="dropdown-item" href="/events"><i&ndash;&gt;-->
        <!--            &lt;!&ndash;                                class="ri-eye-fill mr-2"></i>{{ $t('dashboard.orders_view') }}&ndash;&gt;-->
        <!--            &lt;!&ndash;                            </Link>&ndash;&gt;-->
        <!--            &lt;!&ndash;                        </b-dropdown>&ndash;&gt;-->
        <!--            &lt;!&ndash;                    </template>&ndash;&gt;-->
        <!--            &lt;!&ndash;                </iq-card>&ndash;&gt;-->
        <!--            &lt;!&ndash;            </b-col>&ndash;&gt;-->
        <!--        </b-row>-->


    </b-container>
</template>
