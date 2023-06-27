<template>
    <b-collapse tag="ul" :class="className" :visible="open" :id="idName" :accordion="accordianName">
        <li v-for="(item,index) in items" :key="index"
            :class="activeClass(item)">
            <i v-if="item.is_heading && hideListMenuTitle" class="ri-subtract-line"/>
            <span v-if="item.is_heading && hideListMenuTitle">{{ $t(item.name) }}</span>
            <Link :href="item.link" v-if="!item.is_heading"
                  :class="`iq-waves-effect ${activeClass(item) && item.children ? 'active' : activeClass(item) ? 'active' : ''}`"
                  v-b-toggle="item.name">
                <i :class="item.icon" v-if="item.is_icon_class"/>
                <template v-else v-html="item.icon">
                </template>
                <span>{{ $t(item.name) }}</span>
                <i v-if="item.children" class="ri-arrow-right-s-line iq-arrow-right"/>
                <small v-html="item.append" v-if="hideListMenuTitle" :class="item.append_class"/>
            </Link>
            <List v-if="item.children" :items="item.children" :sidebarGroupTitle="hideListMenuTitle"
                  :open="openMenu(item)"
                  :idName="item.name" :accordianName="`sidebar-accordion-${item.class_name}`"
                  :className="`iq-submenu ${item.class_name}`"/>
        </li>
    </b-collapse>
</template>

<script setup>
import List from './CollapseMenu.vue'
import {computed, onMounted, onUpdated, ref, watch} from "vue";
import {usePage} from "@inertiajs/vue3";

let props = defineProps({
    items: Array,
    className: {type: String, default: 'iq-menu'},
    open: {type: Boolean, default: false},
    idName: {type: String, default: 'sidebar'},
    accordianName: {type: String, default: 'sidebar'},
    sidebarGroupTitle: {type: Boolean, default: true}
});

const page = usePage();

const openMenu = computed(() => item =>
    item.link.name !== '' && !!activeClass.value(item) && item.children
        ? true
        : !!(item.link.name !== '' && activeClass.value(item))
)

const activeClass = computed(() => item => {
    if (item.is_heading) {
        return 'iq-menu-title'
    }

    const component = item.component;
    const pageComponent = page.component;

    if (Array.isArray(component)) {
        return component.includes(pageComponent) ? 'active' : '';
    }

    return pageComponent === component ? 'active' : '';
})

const hideListMenuTitle = ref(props.sidebarGroupTitle);

</script>
