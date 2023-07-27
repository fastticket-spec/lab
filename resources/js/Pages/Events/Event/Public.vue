<script setup>

defineProps({
    data: Array,
})
</script>

<style>
.vag {
    padding-right: 300px;
    padding-left: 300px;
}
.logo{
    width: 38%;
}
</style>

<template>
        <b-container fluid class="mx-auto vag" style="width: 50%;" :style="{
                    backgroundImage: 'url(https://eticket.fra1.cdn.digitaloceanspaces.com/spl/BackG.jpg)',
                    backgroundSize: 'cover',
                    backgroundPositionX: '50%',
                    backgroundPositionY: '50%',
                    paddingTop: '100px',
                    paddingBottom: '100px',
                    minHeight: '100vh'
                }">
        <div class="row no-gutters">
            <div class="col-12 align-self-center">
                <div class="bg-div" :style="{backgroundColor: 'transparent'}">
                    <div class="text-center">
                            <img class="my-3 text-center img-fluid logo" src="https://eticket.fra1.cdn.digitaloceanspaces.com/spl/Logo.png" alt="">
                        </div>
                        <b-row class="page-cards">
                            <b-col sm="4" v-for="access_level in data.data" :key="access_level.id">
                                <b-card :title="access_level.title" class="iq-mb-3 text-center" style=" height: 154px;">
                                    <div v-if="access_level.has_surveys && access_level.status == 1" class="card-date d-flex flex-column text-center"
                                        :class="{'card-date-ar': locale === 'ar'}">
                                        <a :href="`/e/${access_level.event.id}/a/${access_level.id}`" class="btn btn-primary" target="_blank" style="margin-top: 45px;">Register</a>
                                    </div>

                                </b-card>
                            </b-col>
                        </b-row>

                </div>
            </div>
        </div>
    </b-container>




</template>

<script>
import EmptyLayout from '../../../Shared/Layout/EmptyLayout.vue';

export default {
    layout: EmptyLayout
}
</script>
