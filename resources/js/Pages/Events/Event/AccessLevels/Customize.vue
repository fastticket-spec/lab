<script setup>
import {ref} from "vue";
import SideLists from './components/SideLists.vue';
import General from "./components/General.vue";
import Design from "./components/Design.vue";
import RequestForm from "./components/RequestForm.vue";
import Socials from "./components/Socials.vue";

const props = defineProps({
    access_level: {},
    order: {},
    menuLists: [],
    currentMenu: String,
    event: Object,
    data: [],
    design_images: []
})

const selectedListName = ref(props.currentMenu);
const selectedList = ref(props.menuLists.find(x => x.id === props.currentMenu));

const selectList = item => {
    selectedListName.value = item.id;
    selectedList.value = item;
}
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <side-lists
                    :access-level-id="access_level.id"
                    :selected-list="selectedListName"
                    :project-lists="menuLists"
                    :on-select-list="selectList"/>
            </b-col>

            <b-col lg="12">
                <general :list="selectedList" :event-id="event.id" :data="data" :access-level="access_level" v-if="currentMenu === 'general'"/>
                <design :list="selectedList" :event="event" :data="data" :access-level="access_level" :design-images="design_images" v-if="currentMenu === 'design'"/>
                <request-form :list="selectedList" :event-id="event.id" :data="data" :access-level-id="access_level.id" v-if="currentMenu === 'request_form'"/>
                <socials :list="selectedList" :event-id="event.id" :data="data" :access-level-id="access_level.id" v-if="currentMenu === 'socials'"/>
            </b-col>
        </b-row>
    </b-container>
</template>
