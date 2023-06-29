import * as yup from "yup";
import {array} from "yup";

export const signInSchema = yup.object({
    email: yup.string().required().email(),
    password: yup.string().required().min(7)
})

export const passwordResetSchema = yup.object({
    email: yup.string().required().email(),
})

export const passwordTokenSchema = yup.object({
    token: yup.string().required()
})

export const changePasswordSchema = yup.object({
    password: yup.string().required().min(7),
    confirm_password: yup.string()
        .oneOf([yup.ref('password'), null], 'Passwords must match')
})

export const createOrganiserSchema = yup.object({
    name: yup.string().required(),
    email: yup.string().required().email(),
})

export const createEventSchema = yup.object().shape({
    title: yup.string().required(),
    title_arabic: yup.string().nullable(),
    description: yup.string().required(),
    description_arabic: yup.string().nullable()
})

export const createAccessLevelSchema = yup.object().shape({
    title: yup.string().required(),
    quantity_available: yup.string().nullable()
})

export const createSurveySchema = yup.object({
    surveys: array()
        .of(
            yup.object().shape({
                title: yup.string().required('This field is required'),
                title_arabic: yup.string().nullable(),
                type: yup.string().required('This field is required'),

                contact_email: yup.string().when(['contact_to_purchase'], {
                    is: (contact_to_purchase) => {
                        return !!contact_to_purchase;
                    },
                    then: () => yup.string().email().required('This field is required')
                }).nullable(),

                options: yup.array().when(['type'], {
                    is: type => {
                        return ['6', '7', '8', '9'].includes(type)
                    },
                    then: () => array().of(
                        yup.object().shape({
                            name: yup.string().required('This fields is required'),
                            name_arabic: yup.string().nullable()
                        })
                    )
                })
            })
        )
})

export const accessLevelGeneralSchema = yup.object({
    visibility: yup.string().required('This field is required.'),
    accept_reject: yup.string().required('This field is required.'),
    waiting_list: yup.string().required('This field is required.'),
    send_tc: yup.string().required('This field is required.'),
    title: yup.string().required('This field is required.'),
    title_arabic: yup.string().nullable(),
    quantity_available: yup.string().nullable(),
    description: yup.string().required('This field is required.'),
    description_arabic: yup.string().nullable(),
    success_message: yup.string().required('This field is required.'),
    success_message_arabic: yup.string().nullable(),
    approval_message_title: yup.string().required('This field is required.'),
    approval_message: yup.string().required('This field is required.'),
    email_message_title: yup.string().required('This field is required.'),
    email_message: yup.string().required('This field is required.'),
    email_message_arabic: yup.string().nullable(),
    start_on: yup.string().nullable(),
    end_on: yup.string().nullable()
})

export const accessLevelRequestFormSchema = yup.object({
    message_before: yup.string().required('This field is required.'),
    message_before_arabic: yup.string().nullable(),
    message_after: yup.string().required('This field is required.'),
    message_after_arabic: yup.string().nullable()
})

export const editAttendeeSchema = yup.object({
    first_name: yup.string(),
    last_name: yup.string(),
    email: yup.string().email(),
    ticket_id: yup.string().required(),
    attend_date: yup.date(),
    note: yup.string().nullable(),
})

