<template>
    <div class="iq-sidebar">
        <div class="iq-sidebar-logo d-flex justify-content-between">
            <a href="/" class="w-100">
                <div class="iq-light-logo w-100 text-center">
                    <img src="../../../../images/logo-dark.png" class="img-fluid mx-auto" alt="logo">
                </div>
                <div class="iq-dark-logo">
                    <img src="../../../../images/logo-dark.png" class="img-fluid" alt="logo">
                </div>
            </a>
            <div class="iq-menu-bt-sidebar">
                <div class="iq-menu-bt align-self-center">
                    <div class="wrapper-menu" @click="sidebarMini">
                        <div class="main-circle"><i class="ri-arrow-left-s-line"></i></div>
                        <div class="hover-circle"><i class="ri-arrow-right-s-line"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="sidebar-scrollbar">
            <nav class="iq-sidebar-menu">
                <a v-if="isEvent" href="#" @click="backTo('organiser')" class="outline-primary ml-2">
                    <i class="ri-arrow-left-line"></i> Back to Organiser
                </a>
                <a v-if="!isEvent && ((isOrganiser || state.setOrganiser) && !userRole)" href="#"
                   @click="backTo('account')"
                   class="outline-primary ml-2">
                    <i class="ri-arrow-left-line"></i> Back to Account Manager
                </a>
                <div v-if="isEvent" class="form-group form-group-sm px-2 mt-2">
                    <select @change="onChangeEvent" v-model="currentEvent" class="form-control form-control-sm">
                        <option v-for="event in events" :key="event.id" :value="event.id">{{event.title}}</option>
                    </select>
                </div>
                <CollapseMenu :items="menu" :open="true" :sidebarGroupTitle="true"/>
            </nav>
            <div class="p-3"></div>
        </div>
    </div>
</template>

<script setup>
import {core} from '../../config/pluginInit'
import CollapseMenu from '../../components/core/menus/CollapseMenu.vue'
import SideBarItems from './Sidebar.json'
import OrganiserSidebarItems from './OrganiserSidebar.json'
import {eventSideBar} from "./EventSidebar";
import {computed, onMounted, reactive, ref, watch} from "vue";
import {router, usePage} from "@inertiajs/vue3";
import axios from "axios";

core.SmoothScrollbar()

const sidebarMini = () => {
    core.triggerSet()
}

const state = reactive({
    setOrganiser: false,
})

const events = ref([])
const currentEvent = ref(null)

const isOrganiser = computed(() => usePage().props.active_organiser);
const userRole = computed(() => usePage().props.user_role);
const isEvent = computed(() => usePage().component.includes('Events/Event'));
const currentComponent = computed(() => usePage().component);

const setCurrentEvent = () => {
    if (isEvent.value) {
        currentEvent.value = usePage().url.split('event/')[1].split('/')[0]
    }
}

onMounted(async () => {
    try {
        const {data: {events: allEvents}} = await axios.get('/events/organiser-events');

        events.value = allEvents
        setCurrentEvent();
    } catch (e) {
        console.log(e);
    }
    console.log('here');
})

watch(currentComponent, (newVal) => {
    setCurrentEvent()
})

const onChangeEvent = () => {
    const urlArray = usePage().url.split('event/')[1].split('/');
    const urlEnd = urlArray.slice(1, urlArray.length).join('/');
    const url = `/event/${currentEvent.value}/${urlEnd}`;

    router.get(url);
}

const menu = computed(() => {
    const pageIsEvent = usePage().component.includes('Events/Event')
    const pageIsOrganiser = usePage().props.active_organiser;

    if (userRole.value === 'Ticket Sellers') {
        return [
            {
                "title": "Sales",
                "name": "sidebar.sales",
                "is_heading": false,
                "is_active": false,
                "link": "/dashboard",
                "class_name": "",
                "is_icon_class": true,
                "component": ["Dashboard/Index", "Dashboard/TicketSellerDashboard"],
                "icon": "ri-home-4-line"
            }
        ];
    } else if (userRole.value === 'Checkin Users') {
        return [
            {
                "title": "Checkin Attendee",
                "name": "sidebar.checkin_attendee",
                "is_heading": false,
                "is_active": false,
                "link": "/dashboard",
                "class_name": "",
                "is_icon_class": true,
                "component": ["Dashboard/Index", "Dashboard/TicketSellerDashboard", "Dashboard/CheckInUserDashboard"],
                "icon": "ri-home-4-line"
            }
        ]
    }

    if (pageIsEvent) {
        const eventId = usePage().url.split('/')[2];

        return filterItemsByRole(eventSideBar(eventId))
    } else if (!pageIsEvent && (pageIsOrganiser || state.setOrganiser)) {
        return filterItemsByRole(OrganiserSidebarItems);
    }

    return SideBarItems;
})

const filterItemsByRole = sidebar => {
    if (userRole.value) {
        return sidebar.filter(x => {
            if (x.disabled_for) {
                let shouldDisable = x.disabled_for?.includes(userRole.value);

                if (!shouldDisable && x.children) {
                    x.children = x.children.filter(y => y.disabled_for ? !y.disabled_for?.includes(userRole.value) : y);
                    return x;
                }

                return !shouldDisable;
            } else if (x.children) {
                x.children = x.children.filter(y => y.disabled_for ? !y.disabled_for?.includes(userRole.value) : y);

                return x;
            }

            return x;
        })
    }
    return sidebar;
}

const backTo = (type) => {
    switch (type) {
        case 'account':
            router.post(`/organisers/${usePage().props.active_organiser}/unset-organiser`);
            state.setOrganiser = false;
            return;
        case 'organiser':
            state.setOrganiser = true;
            router.get('/dashboard');
            return;
    }
}

</script>
