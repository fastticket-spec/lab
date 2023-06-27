import * as yup from "yup";

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
    password:  yup.string().required().min(7),
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

export const createTicketSchema = yup.object({
    contact_to_purchase: yup.boolean(),
    title: yup.string().required('This field is required'),
    title_arabic: yup.string().nullable(),
    price: yup.string().nullable(),
    quantity_available: yup.number(),
    description: yup.string().nullable(),
    description_arabic: yup.string().nullable(),
    notes: yup.string().nullable(),
    notes_arabic: yup.string().nullable(),
    checkin_limit: yup.number().required(),
    checkout_limit: yup.number().required(),
    minimum_ticket_per_order: yup.string().required(),
    maximum_ticket_per_order: yup.string().required(),
    for_ticket_seller: yup.boolean().required(),
    tax_included: yup.boolean().required(),
    hide_ticket: yup.boolean().required(),
    request_visa: yup.boolean().required(),
    start_sale: yup.string(),
    end_sale: yup.string(),
    all_day: yup.boolean().required(),
    time_slots_minutes: yup.number(),
    contact_email: yup.string().when(['contact_to_purchase'], {
        is: (contact_to_purchase) => {
            return !!contact_to_purchase;
        },
        then: () => yup.string().email().required('This field is required')
    }).nullable()
});

export const createServiceSchema = yup.object({
    service: yup.string().required(),
    service_arabic: yup.string().nullable(),
    price: yup.number().required(),
    quantity_available: yup.number(),
    description: yup.string().nullable(),
    description_arabic: yup.string().nullable(),
    min_person_per_service: yup.string().required(),
    max_person_per_service: yup.string().required(),
    hide_service: yup.boolean().required(),
    start_sale: yup.string(),
    end_sale: yup.string(),
});

export const createSurveySchema = yup.object({
    title: yup.string().required(),
    title_arabic: yup.string().nullable(),
    type: yup.string().required(),
    has_parent: yup.boolean().required(),
    required: yup.boolean().required(),
    post_order: yup.boolean().required(),
    parent_survey: yup.string().when(['has_parent'], {
        is: has_parent => {
            return has_parent;
        },
        then: () => yup.string().required()
    }),
    parent:  yup.string().when(['has_parent'], {
        is: has_parent => {
            return has_parent;
        },
        then: () => yup.string().required()
    })
})

export const createCouponSchema = yup.object({
    code: yup.string().required(),
    discount_type: yup.string().nullable(),
    value: yup.number().required(),
    usage_limit: yup.number().nullable(),
    for_weekends: yup.boolean().required(),
})

export const createUserSchema = yup.object({
    first_name: yup.string().required(),
    last_name: yup.string().required(),
    email: yup.string().email().required(),
    role_id: yup.string().required(),
    events: yup.array().nullable(),
    all_events: yup.boolean().nullable()
})

export const editAttendeeSchema = yup.object({
    first_name: yup.string(),
    last_name: yup.string(),
    email: yup.string().email(),
    ticket_id: yup.string().required(),
    attend_date: yup.date(),
    note: yup.string().nullable(),
})

