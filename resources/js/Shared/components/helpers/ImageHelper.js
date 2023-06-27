import {usePage} from "@inertiajs/vue3";

export const getFullLogoUrl = url => {
    return `${usePage().props.app_url}/${url}`;
}
