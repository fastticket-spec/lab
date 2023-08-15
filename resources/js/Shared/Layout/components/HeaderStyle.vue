<template>
    <div class="iq-top-navbar">
        <div class="iq-navbar-custom">
            <div class="iq-sidebar-logo">
                <div class="top-logo">
                    <a href="/dashboard.home-1" class="logo">
                        <div class="iq-light-logo">
                            <img src="../../../../images/logo.gif" class="img-fluid" alt="logo">
                        </div>
                        <div class="iq-dark-logo">
                            <img src="../../../../images/logo-dark.gif" class="img-fluid" alt="logo">
                        </div>
                        <span>{{ appName }}</span>
                    </a>
                </div>
            </div>
            <nav class="navbar navbar-expand-lg navbar-light p-0">
                <b-navbar-toggle target="nav-collapse" class="">
                    <i class="ri-menu-3-line"></i>
                </b-navbar-toggle>
                <div class="iq-menu-bt align-self-center">
                    <div class="wrapper-menu" @click="sidebarMini">
                        <div class="main-circle"><i class="ri-arrow-left-s-line"></i></div>
                        <div class="hover-circle"><i class="ri-arrow-right-s-line"></i></div>
                    </div>
                </div>
                <b-collapse id="nav-collapse" is-nav>
                    <ul class="navbar-nav ml-auto navbar-list">
                        <li class="nav-item" v-nav-toggle v-if="$page.props.active_organiser">
                            <a class="iq-waves-effect language-title" v-if="!$page.component.includes('Events/Event')"
                               :href="$page.component.includes('Events/Event')
                               ? eventUrl($page.url)
                               : organiserUrl()"
                               target="_blank"><i class="ri-eye-line"></i></a>
                        </li>
                        <li class="nav-item" v-nav-toggle>
                            <a class="search-toggle iq-waves-effect language-title" href="#"><img
                                :src="lang.image" alt="img-flaf" class="img-fluid mr-1"
                                style="height: 16px; width: 16px;"/><i class="ri-arrow-down-s-line"></i></a>
                            <div class="iq-sub-dropdown">
                                <a class="iq-sub-card" href="#" v-for="(lang, i) in langOptions"
                                   :key="`Lang${i}`" @click="langChange(lang)">
                                    <img :src="lang.image" alt="img-flaf" class="img-fluid mr-2" style="width: 20px"/>{{ lang.title }}
                                </a>
                            </div>
                        </li>
                    </ul>
                </b-collapse>
                <ul class="navbar-list">
                    <li class="rounded" v-nav-toggle>
                        <a href="#" class="search-toggle iq-waves-effect d-flex align-items-center rounded profile">
                            <img src="../../../../images/user/1.png" class="img-fluid rounded mr-3" alt="user">
                            <div class="caption">
                                <h6 class="mb-0 line-height text-white">Admin</h6>
                                <span class="font-size-12 text-white">{{ $t('header.available') }}</span>
                            </div>
                        </a>
                        <div class="iq-sub-dropdown iq-user-dropdown">
                            <div class="iq-card shadow-none m-0">
                                <div class="iq-card-body p-0 ">
                                    <div class="bg-primary p-3">
                                        <h5 class="mb-0 text-white line-height">Hello Admin</h5>
                                        <span class="text-white font-size-12">{{ $t('header.available') }}</span>
                                    </div>
                                    <a href="#" class="iq-sub-card iq-bg-primary-hover">
                                        <div class="media align-items-center">
                                            <div class="rounded iq-card-icon iq-bg-primary">
                                                <i class="ri-file-user-line"></i>
                                            </div>
                                            <div class="media-body ml-3">
                                                <h6 class="mb-0 ">{{ $t('header.myProfile') }}</h6>
                                                <p class="mb-0 font-size-12">{{ $t('header.viewPersonalDetails') }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="iq-sub-card iq-bg-primary-hover">
                                        <div class="media align-items-center">
                                            <div class="rounded iq-card-icon iq-bg-primary">
                                                <i class="ri-profile-line"></i>
                                            </div>
                                            <div class="media-body ml-3">
                                                <h6 class="mb-0 ">{{ $t('header.editProfile') }}</h6>
                                                <p class="mb-0 font-size-12">{{
                                                        $t('header.modifyPersonalDetails')
                                                    }}</p>
                                            </div>
                                        </div>
                                    </a>
                                    <Link href="/organiser-preferences" v-if="isOrganiser"
                                       class="iq-sub-card iq-bg-primary-hover">
                                        <div class="media align-items-center">
                                            <div class="rounded iq-card-icon iq-bg-primary">
                                                <i class="ri-account-box-line"></i>
                                            </div>
                                            <div class="media-body ml-3">
                                                <h6 class="mb-0 ">{{ $t('header.organiserPreferences') }}</h6>
                                                <p class="mb-0 font-size-12">{{ $t('header.manageAccount') }}</p>
                                            </div>
                                        </div>
                                    </Link>
                                    <div class="d-inline-block w-100 text-center p-3">
                                        <Link href="/logout" method="post" as="button"
                                              class="btn btn-primary dark-btn-primary">{{ $t('header.logout') }}<i
                                            class="ri-login-box-line ml-2"></i></Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script setup>
import {computed, onMounted, ref, watch} from "vue";
import {router, usePage} from "@inertiajs/vue3";

const page = usePage();

const isOrganiser = computed(() => page.props.active_organiser);
const userRole = computed(() => page.props.user_role);
const component = usePage().component;
const isEvent = computed(() => page.component.includes('Events/Event'));
const taifOrgId = page.props.taif_organiser_id;

const eventUrl = (url) => {
    const eventID = url.split('/')[2];
    if (taifOrgId !== isOrganiser.value) {
        return `/home/${eventID}`;
    }
    return `https://tickets.jcsa.sa/events/${eventID}`;
}

const organiserUrl = () => {
    if (taifOrgId !== isOrganiser.value) {
        return `/home/${isOrganiser.value}`;
    }
    return `https://tickets.jcsa.sa/o/${isOrganiser.value}`;
}
</script>

<script>
import {core, APPNAME} from '../../config/pluginInit'
import Lottie from '../../components/core/lottie/Lottie.vue'
import EngFlag from '../../../../images/small/flag-01.png'
import AraFlag from '../../../../images/small/flag-500.png'
import EmmaImg from '../../../../images/user/user-01.jpg'
import {computed} from "vue";
import {usePage} from "@inertiajs/vue3";

export default {
    name: 'HeaderStyle',
    components: {
        Lottie
    },
    data() {
        return {
            appName: APPNAME,
            globalSearch: '',
            notification: [
                {
                    image: EmmaImg,
                    name: 'Emma Watson Nik',
                    date: 'Just Now',
                    description: '95 MB'
                }
            ],
            message: [
                {image: import('../../../../images/user/user-01.jpg'), name: 'Nik Emma Watson', date: '13 jan'},
            ],
            lang: {title: 'English', value: 'en', image: EngFlag},
            langOptions: [
                {title: 'English', value: 'en', image: EngFlag},
                {title: 'Arabic', value: 'ar', image: AraFlag},
            ],
        }
    },
    mounted() {
        const chosenLocale = localStorage.getItem('locale') || 'en';
        this.lang = this.langOptions.find(locale => locale.value === chosenLocale);
    },

    methods: {
        sidebarMini() {
            core.triggerSet()
        },

        langChange(lang) {
            // this.$i18n.locale = lang.value
            localStorage.setItem('locale', lang.value);
            document.getElementsByClassName('iq-show')[0].classList.remove('iq-show')
            location.reload()
        }
    },

}
</script>
