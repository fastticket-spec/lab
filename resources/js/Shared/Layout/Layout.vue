<template>
    <div class="wrapper" :dir="locale === 'ar' ? 'rtl' : 'ltr'">
        <AlertHandler/>
        <SidebarStyle/>
        <HeaderStyle/>
        <div class="content-page" :class="{'ar': locale === 'ar'}" id="content-page">
            <slot/>
        </div>
    </div>
    <LayoutFooter :locale="locale"/>
</template>
<script setup>
import AlertHandler from '../../Shared/components/core/alert/AlertHandler.vue'
import HeaderStyle from './components/HeaderStyle.vue'
import SidebarStyle from './components/SidebarStyle.vue'
import LayoutFooter from './components/LayoutFooter.vue'

const locale = localStorage.getItem('locale') || 'en';
</script>

