<template>
    <iq-card>
        <template v-slot:body>
            <div class="iq-todo-page">
                <ul class="todo-task-list p-0 m-0 d-flex">
                    <li v-for="(item,index) in projectLists" :key="index" @click="onSelectList(item)"
                        :class="`${item.id === selectedList ? 'active' : ''}`">
                        <Link href="#" :data="{ page: item.id }" preserve-scroll>
                            <i class="ri-stack-fill mr-2"></i>
                            {{ item.iI8 ? $t(item.iI8) : item.name }}
                        </Link>
                    </li>
                </ul>
            </div>
        </template>
    </iq-card>
</template>

<script setup>
defineProps({
    selectedList: {},
    projectLists: [],
    onSelectList: Function,
    accessLevelId: String
})
</script>
