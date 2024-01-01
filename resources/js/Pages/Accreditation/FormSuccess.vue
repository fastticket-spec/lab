<script setup>
import {onMounted} from "vue";
import Facebook from "../Events/Event/AccessLevels/components/Facebook.vue";
import Twitter from "../Events/Event/AccessLevels/components/Twitter.vue";
import LinkedIn from "../Events/Event/AccessLevels/components/LinkedIn.vue";
import Whatsapp from "../Events/Event/AccessLevels/components/Whatsapp.vue";

const props = defineProps({
    accessLevel: Object,
    lang: String,
    success_message: String,
    success_message_arabic: String,
    social_share: Boolean,
    socials: Array,
    social_share_message: String,
    social_share_message_arabic: String,
    link: String
})

const share = ({value}) => {
  let navUrl = '';
  const text = props.lang === 'arabic' ? props.social_share_message_arabic : props.social_share_message;
  if (value === 'facebook') {
    navUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + props.link
  } else if (value === 'twitter') {
    navUrl = `https://twitter.com/intent/tweet?text=${text}&url=${props.link}`
  } else if (value === 'linkedin') {
    navUrl = `https://www.linkedin.com/shareArticle?mini=true&url=${props.link}&title=${text}&summary=${text}`
  } else if (value === 'whatsapp') {
    navUrl = `https://wa.me/?text=${text} ${props.link}`
  }
  window.open(navUrl, '_blank');
}


onMounted(() => {
    document.querySelector('title').textContent = `${props.accessLevel.title} - ${props.accessLevel?.event?.organiser?.name}`
})
</script>

<script>
import EmptyLayout from '../../Shared/Layout/EmptyLayout.vue';

export default {
    layout: EmptyLayout
}
</script>
<style>
.form-control {
    display: block;
    width: 100%;
    height: calc(1.5em + 0.75rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #ffffff;
    background-color: #fff0;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
}

</style>


<template>
    <b-container fluid  style="width: 80%;" :style="{
                    backgroundColor: accessLevel?.page_design?.bg_type === 'color' && accessLevel?.page_design?.bg_color,
                    backgroundImage: accessLevel?.page_design?.bg_type === 'image'? 'url(' + accessLevel?.page_design?.bg_image + ')' : '',
                    backgroundSize: 'cover',
                    backgroundPositionX: '50%',
                    backgroundPositionY: '50%',
                    paddingTop: '100px',
                    paddingBottom: '100px',
                    minHeight: '100vh',
                    textAlign: lang != 'english' ? 'right' : '',
                    direction:  lang != 'english' ? 'rtl' : ''
                }" class="mx-auto vag d-flex align-items-center">
        <div class="row no-gutters accreditation-form w-100" :class="{'rtl text-right': lang === 'arabic'}">
            <div class="col-12 align-self-center">
                <div class="bg-div bg-white" :style="{backgroundColor: accessLevel?.page_design?.form_bg_color + ' !important'}">
                    <div class="text-center">
                        <img class="my-3 text-center img-fluid logo" :src="accessLevel?.page_design?.logo || accessLevel?.event?.event_image_url" alt="">
                    </div>
                    <p :style="{ color: accessLevel?.page_design?.font_color}"
                        v-html="lang === 'arabic' ? success_message_arabic : success_message"
                        class="text-center p-5"/>

                    <div class="d-flex align-items-center justify-content-center" v-if="social_share">
                      <a href="#" v-for="social in socials" :key="social.value" class="mr-2 mb-3" @click.prevent="share(social)">
                        <Facebook :color="social.color" v-if="social.value === 'facebook' && social.enabled"/>
                        <Twitter :color="social.color" v-if="social.value === 'twitter' && social.enabled"/>
                        <LinkedIn :color="social.color" v-if="social.value === 'linkedin' && social.enabled"/>
                        <Whatsapp :color="social.color" v-if="social.value === 'whatsapp' && social.enabled"/>
                      </a>
                    </div>

                    <div v-if="accessLevel?.page_design?.footer_logo" class="text-center">
                        <img :src="accessLevel?.page_design?.footer_logo" alt="" class="img-fluid" :style="`height: ${accessLevel?.page_design?.footer_logo_height}px; margin-bottom: 15px; text-align: center`">
                    </div>
                </div>
                <!-- <div class="text-center">
                    <a class="btn btn-primary" onclick="history.back()"> Add Another</a>
                </div> -->
            </div>
        </div>
    </b-container>
</template>

<style scoped>
.bg-div {
    height: 100%;
    width: 100%;
    position: relative;
    border-radius: 10px;
}

.bg-div :deep(ul) {
    list-style: unset;
}

img.logo {
    height: 100px;
}

p :deep(.ql-direction-rtl) {
    direction: rtl;
    //text-align: center;
}
</style>
