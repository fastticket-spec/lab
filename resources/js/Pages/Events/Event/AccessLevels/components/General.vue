<script setup>

import {useForm, Field, ErrorMessage, useField} from "vee-validate";
import {accessLevelGeneralSchema} from "../../../../../Shared/components/helpers/Validators.js";
import {QuillEditor} from "@vueup/vue-quill";
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import VueSelect from "vue-select";
import "vue-select/dist/vue-select.css";
import {ColorPicker} from "vue3-colorpicker";
import Facebook from "./Facebook.vue";
import LinkedIn from "./LinkedIn.vue";
import Twitter from "./Twitter.vue";
import Whatsapp from "./Whatsapp.vue";

import {onMounted, ref} from "vue";
import {router} from "@inertiajs/vue3";

const props = defineProps({
    eventId: String,
    accessLevel: Object,
    data: Object,
    socials: Array
})

const showArabicInputs = ref(true)
const showArabicInvitation = ref(false)
const declineInvitation = ref(false)
const shareLink = ref(false)
const gradientColor = ref("linear-gradient(0deg, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 1) 100%)");

const socialsData = ref(props.socials);

const enableVCard = ref(false)

const initialValues = props.data
    ? {
        ...props.data,
        title: props.accessLevel.title,
        title_arabic: props.accessLevel.title_arabic,
        quantity_available: props.accessLevel.quantity_available,
    }
    : {
        visibility: '1',
        accept_reject: '1',
        waiting_list: '1',
        send_tc: '1',
        title: props.accessLevel.title,
        title_arabic: props.accessLevel.title_arabic,
        quantity_available: props.accessLevel.quantity_available,
    }

const {handleSubmit, isSubmitting} = useForm({
    initialValues,
    validationSchema: accessLevelGeneralSchema,
});

onMounted(() => {
    showArabicInvitation.value = !!props.data?.arabic_invitation
    declineInvitation.value = !!props.data?.decline_invitation
    enableVCard.value = !!props.data?.enable_vcard
    shareLink.value = !!props.data?.share_link
})


const {value: description} = useField("description");
const {value: description_arabic} = useField("description_arabic");
const {value: success_message} = useField("success_message");
const {value: success_message_arabic} = useField("success_message_arabic");
const {value: social_share_message} = useField("social_share_message");
const {value: social_share_message_arabic} = useField("social_share_message_arabic");
const {value: link_address} = useField("link_address");
const {value: approval_message} = useField("approval_message");
const {value: email_message} = useField("email_message");
const {value: email_message_arabic} = useField("email_message_arabic");
const {value: invitation_message} = useField("invitation_message");
const {value: invitation_message_sms} = useField("invitation_message_sms");
const {value: decline_text} = useField("decline_text");

const onSubmit = handleSubmit(values => {
  router.post(`/event/${props.eventId}/access-levels/${props.accessLevel.id}/customize/general`, {
    ...values,
    arabic_invitation: showArabicInvitation.value,
    decline_invitation: declineInvitation.value,
    enable_vcard: enableVCard.value,
    share_link: shareLink.value,
    selected_socials:socialsData.value
  });
})
</script>

<template>
    <b-row>
        <b-col sm="12">
            <iq-card>
                <template v-slot:headerTitle>
                    <h4 class="card-title">General - Customize Event</h4>
                </template>

                <template v-slot:body>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form @submit.prevent="onSubmit" class="w-100">
                            <b-row class="mt-3">
                                <b-col>
                                    <b-checkbox v-model="showArabicInputs" class="custom-checkbox-color"
                                                name="check-button" inline>
                                        Show Arabic Inputs
                                    </b-checkbox>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="visibilityInput">Event visibility</label>
                                        <Field as="select" name="visibility" id="visibilityInput"
                                               :class="`form-control mb-0`" :validateOnInput="true">
                                            <option value="0">Hide event from the public.</option>
                                            <option value="1">Make event visible to the public.</option>
                                            <option value="2">Access event via link only.</option>
                                            <option value="3">Visible in organiser.</option>
                                        </Field>
                                        <ErrorMessage name="visibility" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="featureInput">Accept/Reject Feature</label>
                                        <Field as="select" name="accept_reject" id="featureInput"
                                               :class="`form-control mb-0`" :validateOnInput="true">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </Field>
                                        <ErrorMessage name="accept_reject" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="waitingInput">Waiting List Option Feature</label>
                                        <Field as="select" name="waiting_list" id="waitingInput"
                                               :class="`form-control mb-0`" :validateOnInput="true">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </Field>
                                        <ErrorMessage name="waiting_list" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="termsInput">Send Terms and Conditions?</label>
                                        <Field as="select" name="send_tc" id="termsInput"
                                               :class="`form-control mb-0`" :validateOnInput="true">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </Field>
                                        <ErrorMessage name="send_tc" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <hr>

                            <b-row>
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="title">Access Level Title</label>
                                        <Field type="text" name="title" id="title"
                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                        <ErrorMessage name="title" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6" v-if="showArabicInputs">
                                    <div class="form-group">
                                        <label for="title_arabic">Access Level Title (Arabic)</label>
                                        <Field type="text" name="title_arabic" id="title_arabic"
                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                        <ErrorMessage name="title_arabic" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="quantityInput">{{ $t('input.quantity') }}</label>
                                        <Field type="number" name="quantity_available" id="quantityInput"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.quantity')" :validateOnInput="true"/>
                                        <ErrorMessage name="quantity_available" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <hr>

                            <b-row>
                                <b-col sm="12">
                                    <div class="form-group">
                                        <label for="descriptionInput">{{ $t('input.description') }}</label>
                                        <quill-editor toolbar="full" theme="snow" v-model:content="description"
                                                      content-type="html"></quill-editor>
                                        <ErrorMessage name="description" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col v-if="showArabicInputs" sm="12">
                                    <div class="form-group">
                                        <label for="descriptionArabicInput">{{ $t('input.descriptionArabic') }}</label>
                                        <quill-editor toolbar="full" theme="snow" v-model:content="description_arabic"
                                                      content-type="html"></quill-editor>
                                        <ErrorMessage name="description_arabic" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <hr>

                            <b-row>
                              <b-col sm="12">
                                <div class="form-group">
                                  <label for="successMessageInput">{{ $t('input.success_message') }}</label> <span v-if="shareLink"><small>&nbsp;Use <strong>%link_buttons%</strong> to insert Social Media Links</small></span>
                                  <quill-editor toolbar="full" theme="snow" v-model:content="success_message"
                                                content-type="html"></quill-editor>
                                  <ErrorMessage name="success_message" class="text-danger"/>
                                </div>
                              </b-col>

                              <b-col v-if="showArabicInputs" sm="12">
                                <div class="form-group">
                                  <label for="successMessageArabicInput">{{
                                      $t('input.success_messageArabic')
                                    }}</label> <span v-if="shareLink"><small>&nbsp;Use <strong>%link_buttons%</strong> to insert Social Media Links</small></span>
                                  <quill-editor toolbar="full" theme="snow"
                                                v-model:content="success_message_arabic"
                                                content-type="html"></quill-editor>
                                  <ErrorMessage name="success_message_arabic" class="text-danger"/>
                                </div>
                              </b-col>

                              <b-col sm="12">
                                <b-checkbox v-model="shareLink" class="custom-checkbox-color"
                                            name="check-button-share-link" inline>
                                  Share Link
                                </b-checkbox>
                              </b-col>
                            </b-row>

                            <b-row v-if="shareLink">
                              <b-col><p>Choose Socials you'd like to integrate.</p></b-col>
                              <b-col sm="12">
                                <b-row>
                                  <b-col sm="3" class="mb-3" v-for="social in socialsData" :key="social.value">
                                    <div class="d-flex align-items-center">
                                      <b-checkbox v-model="social.enabled" class="custom-checkbox-color"
                                                  name="check-button-selected-socials" inline>
                                        <Facebook v-if="social.value === 'facebook'" :color="social.color"/>
                                        <LinkedIn v-if="social.value === 'linkedin'" :color="social.color"/>
                                        <Twitter v-if="social.value === 'twitter'" :color="social.color"/>
                                        <Whatsapp v-if="social.value === 'whatsapp'" :color="social.color"/>
                                      </b-checkbox>
                                      <div class="ml-3 w-100">
                                        <color-picker
                                            class="form-control ml-3"
                                            v-model:pureColor="social.color"
                                            v-model:gradientColor="gradientColor"
                                            format="hex6"
                                            picker-type="chrome"
                                        />
                                      </div>
                                    </div>
                                  </b-col>
                                </b-row>
                              </b-col>
                              <b-col sm="6">
                                <div class="form-group">
                                  <label for="shareMessage">
                                    Share Message
                                  </label>
                                  <input class="form-control" type="text" v-model="social_share_message" />
                                  <ErrorMessage name="social_share_message" class="text-danger"/>
                                </div>
                              </b-col>
                              <b-col v-if="showArabicInputs" sm="6">
                                <div class="form-group">
                                  <label for="shareMessageArabic">
                                    Share Message (Arabic)
                                  </label>
                                  <input class="form-control" type="text" v-model="social_share_message_arabic" />
                                  <ErrorMessage name="social_share_message_arabic" class="text-danger"/>
                                </div>
                              </b-col>

                              <b-col sm="6">
                                <div class="form-group">
                                  <label for="link_address">Share Link</label>
                                  <input id="link_address" class="form-control" type="text" v-model="link_address" />
                                  <ErrorMessage name="link_address" class="text-danger"/>
                                </div>
                              </b-col>
                            </b-row>

                            <hr>

                            <b-row>
                                <b-col sm="12">
                                    <div class="form-group">
                                        <label for="approval_message_title">Approval Message Title</label>
                                        <Field type="text" name="approval_message_title" id="approval_message_title"
                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                        <ErrorMessage name="approval_message_title" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="12">
                                    <div class="form-group">
                                        <label for="approvalMessage">{{ $t('input.approval_message') }}</label> <span><small>&nbsp;Use <strong>%qrcode%</strong> to insert QrCode and <strong>%ref%</strong> for attendee reference:</small></span>
                                        <quill-editor toolbar="full" theme="snow" v-model:content="approval_message"
                                                      content-type="html"></quill-editor>
                                        <ErrorMessage name="approval_message" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <hr>

                            <b-row>
                                <b-col sm="12">
                                    <div class="form-group">
                                        <label for="email_message_title">Email Message Title (Sent after
                                            Registration)</label>
                                        <Field type="text" name="email_message_title" id="email_message_title"
                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                        <ErrorMessage name="email_message_title" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="12">
                                    <div class="form-group">
                                        <label for="email_message">{{ $t('input.email_message') }}</label>
                                        <quill-editor toolbar="full" theme="snow" v-model:content="email_message"
                                                      content-type="html"></quill-editor>
                                        <ErrorMessage name="email_message" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col v-if="showArabicInputs" sm="12">
                                    <div class="form-group">
                                        <label for="email_message_arabic">{{ $t('input.email_message_arabic') }}</label>
                                        <quill-editor toolbar="full" theme="snow" v-model:content="email_message_arabic"
                                                      content-type="html"></quill-editor>
                                        <ErrorMessage name="email_message_arabic" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="12">
                                    <div class="form-group">
                                        <label for="invitation_title">Invitation Link Title</label>
                                        <Field type="text" name="invitation_title" id="invitation_title"
                                               :class="`form-control mb-0`" :validateOnInput="true"/>
                                        <ErrorMessage name="invitation_title" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col>
                                    <b-checkbox v-model="showArabicInvitation" class="custom-checkbox-color"
                                                name="check-button" inline>
                                        Arabic Invitation?
                                    </b-checkbox>
                                </b-col>

                                <b-col sm="12" id="mail-invite">
                                    <div class="form-group">
                                        <label for="invitation_message">Invitation Link Message</label>
                                        <span><small>&nbsp;Copy these placeholders: <br>
                                            <strong>%invitation_link%</strong>
                                            <template v-if="accessLevel.registration"><br><strong>%registration_number%</strong> denotes the registration number.<br></template>
                                            <strong>%first_name%</strong> for First Name<br>
                                            <strong>%last_name%</strong> for Last Name<br>
                                            <strong>%full_name%</strong> for Full Name<br>
                                            <strong>%decline_link%</strong> for Decline Invitation Link
                                        </small></span>
                                        <quill-editor toolbar="full" theme="snow" v-model:content="invitation_message"
                                                      content-type="html"></quill-editor>
                                        <ErrorMessage name="invitation_message" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="12" id="sms-invite">
                                    <div class="form-group">
                                        <label for="invitation_message">Invitation Link Message (SMS)</label>
                                        <textarea v-model="invitation_message_sms" class="form-control" :class="{'arabic-input': showArabicInvitation}" rows="5"/>
                                        <ErrorMessage name="invitation_message_sms" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                          <b-row class="mt-3">
                            <b-col>
                              <b-checkbox v-model="enableVCard" class="custom-checkbox-color"
                                          name="check-button-vcard" inline>
                                Enable VCard
                              </b-checkbox>
                            </b-col>
                          </b-row>

                            <hr>

                            <b-row>
                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="start_on">Start On</label>
                                        <Field type="datetime-local" name="start_on" id="start_on"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.start_on')" :validateOnInput="true"/>
                                        <ErrorMessage name="start_on" class="text-danger"/>
                                    </div>
                                </b-col>

                                <b-col sm="6">
                                    <div class="form-group">
                                        <label for="end_on">End On</label>
                                        <Field type="datetime-local" name="end_on" id="end_on"
                                               :class="`form-control mb-0`"
                                               :placeholder="$t('input.end_on')" :validateOnInput="true"/>
                                        <ErrorMessage name="end_on" class="text-danger"/>
                                    </div>
                                </b-col>
                            </b-row>

                            <b-row class="mt-3">
                                <b-col>
                                    <b-button :disabled="isSubmitting" type="submit" variant="primary">
                                        {{ $t('button.update') }}
                                    </b-button>
                                </b-col>
                            </b-row>
                        </form>
                    </div>
                </template>
            </iq-card>
        </b-col>
    </b-row>
</template>
