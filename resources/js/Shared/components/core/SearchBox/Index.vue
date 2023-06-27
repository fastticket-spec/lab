<template>
    <div class="search-box" :class="{'ar': locale === 'ar'}">
        <input type="text" class="form-control" v-model="q" :placeholder="placeholder">
        <i class="search-icon ri-close-circle-line cursor-pointer" v-if="q" @click="cancelSearch" title="Clear Search"></i>
        <i class="search-icon ri-search-2-line" v-else></i>
    </div>
</template>

<script setup>
import {ref, watch} from "vue";
import debounce from "lodash.debounce";

const locale = localStorage.getItem('locale') || 'en';

const props = defineProps({
    placeholder: String,
    onSearch: Function,
    defaultValue: String
})

const q = ref(props.defaultValue)

watch(q, debounce((value) => {
    props.onSearch(value);
}, 500))

const cancelSearch = () => {
    q.value = '';
}
</script>

<style scoped>
.search-box {
    width: 400px;
    position: relative;
}

.search-box.ar .form-control {
    padding-right: 50px;
}

.search-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
}
</style>

