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
    end_on: yup.string().nullable(),
    checkin_limit: yup.number().nullable()
})

export const accessLevelRequestFormSchema = yup.object({
    message_before: yup.string().required('This field is required.'),
    message_before_arabic: yup.string().nullable(),
    message_after: yup.string().required('This field is required.'),
    message_after_arabic: yup.string().nullable()
})

export const accessLevelSocialsSchema = yup.object({
    email: yup.string().nullable(),
    instagram: yup.string().nullable(),
    phone_number: yup.string().nullable(),
})

export const zoneSchema = yup.object({
    zones: array()
        .of(
            yup.object().shape({
                zone: yup.string().required('This field is required'),
            })
        )
})

export const areaSchema = yup.object({
    areas: array()
        .of(
            yup.object().shape({
                area: yup.string().required('This field is required'),
            })
        )
})

export const createBadgeSchema = yup.object({
    title: yup.string().required(),
    description: yup.string().required(),
    width: yup.number().required(),
    height: yup.number().required(),
})

export const accreditationFormSchema = lang => yup.object({
    surveys: array().of(
        yup.object().shape({
            required: yup.number().required(),
            type: yup.string().required(),
            is_private: yup.number().required(),
            answer: yup.string()
                .when(['required', 'type', 'is_private'], {
                    is: (required, type, is_private) => {
                        return required === 1 && type !== '10' && !is_private;
                    },
                    then: () => yup.string().required(lang === 'english' ? 'This field is required1' : 'هذه الخانة مطلوبه')
                })
                .when(['type', 'input_type', 'input_length', 'required'], ([type, input_type, input_length, required], schema) => {
                    if (['1', '2', '12'].includes(type) && input_type && input_length) {
                        const inputLengthArray = input_length.split('-');

                        let schemaBuilder;
                        if (!required) {
                            schemaBuilder = schema.nullable();
                        } else {
                            schemaBuilder = schema.required(lang === 'english' ? 'This field is required1' : 'هذه الخانة مطلوبه');
                        }
                        if (inputLengthArray.length === 2) {
                            schemaBuilder = schemaBuilder
                                .min(inputLengthArray[0], `Must be at least ${inputLengthArray[0]} characters`).max(inputLengthArray[1], `Must be at most ${inputLengthArray[1]} characters`);
                        } else {
                            schemaBuilder = schemaBuilder
                                .min(input_length, `Must be exactly ${input_length} digits`)
                                .max(input_length, `Must be exactly ${input_length} digits`)
                        }

                        if (input_type === 'number') {
                            schemaBuilder = schemaBuilder.matches(/^[0-9]+$/, lang === 'english' ? 'Must only be numbers' : 'يجب أن تكون أرقامًا فقط');
                        }

                        if (input_type === 'text') {
                            schemaBuilder = schemaBuilder.matches(/^[a-zA-Z ]*$/, lang === 'english' ? 'Must only be texts' : 'يجب أن تكون النصوص فقط');
                        }


                        return schemaBuilder;

                    }
                })
                .when('type', {
                    is: '5',
                    then: () => yup.string().matches(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/, {message: lang === 'english' ? 'Please supply a valid email address.' : 'يرجى تقديم عنوان بريد إلكتروني صالح'}).required(lang === 'english' ? 'This field is required2' : 'هذه الخانة مطلوبه')
                })
                .when('type', {
                    is: '8',
                    then: () => yup.array().required(lang === 'english' ? 'This field is required3' : 'هذه الخانة مطلوبه')
                })
                .when('type', {
                    is: '7',
                    then: () => yup.array().required(lang === 'english' ? 'This field is required4' : 'هذه الخانة مطلوبه')
                })
                .nullable()
        })
    )
})

export function checkIfFilesAreTooBig(files) {
    console.log(files.size);
    let valid = true
    if (files) {
        files.map(file => {
            const size = file.size / 1024 / 1024
            if (size > 10) {
                valid = false
            }
        })
    }
    return valid
}

export const createUserSchema = yup.object({
    first_name: yup.string().required(),
    last_name: yup.string().required(),
    email: yup.string().email().required(),
    role_id: yup.string().required(),
    events: yup.array().nullable(),
    all_events: yup.boolean().nullable()
})

export const createManagerSchema = yup.object({
    first_name: yup.string().required(),
    last_name: yup.string().required(),
    email: yup.string().email().required(),
})

export const emailPreferenceSchema = yup.object({
    bg_color: yup.string().required(),
    font_color: yup.string().required(),
    qr_color: yup.string().required(),
    logo: yup.string().required()
})

