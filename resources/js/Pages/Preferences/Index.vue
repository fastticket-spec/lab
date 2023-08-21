<script setup>
import {ref} from "vue";
import Email from "./components/Email.vue";

const props = defineProps({
    pageLists: Array,
    currentPage: String,
    data: Object
})

const selectedPageName = ref(props.currentMenu);
const selectedPage = ref(props.pageLists.find(x => x.id === props.currentPage));

const selectPage = item => {
    selectedPageName.value = item.id;
    selectedPage.value = item;
}
</script>

<template>
    <b-container fluid>
        <b-row>
            <b-col sm="12">
                <iq-card>
                    <template v-slot:body>
                        <div class="iq-todo-page">
                            <ul class="todo-task-list p-0 m-0 d-flex">
                                <li v-for="(item,index) in pageLists" :key="index" @click="selectPage(item)"
                                    :class="`${item.id === selectedPage.id ? 'active' : ''}`">
                                    <Link href="#" :data="{ page: item.id }" preserve-scroll>
                                        <i class="ri-stack-fill mr-2"></i>
                                        {{ item.iI8 ? $t(item.iI8) : item.name }}
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </template>
                </iq-card>
            </b-col>

            <b-col lg="12">
                <email :list="selectedPage" :data="data" />
            </b-col>
        </b-row>
    </b-container>
</template>
