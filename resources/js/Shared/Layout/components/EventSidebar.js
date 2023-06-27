export const eventSideBar = eventId => ([
    {
        "title": "Event",
        "name": "sidebar.event",
        "is_heading": true,
        "is_active": false,
        "class_name": "",
        "is_icon_class": true,
        "icon": ""
    },
    {
        "title": "Dashboard",
        "name": "sidebar.dashboard",
        "is_heading": false,
        "is_active": false,
        "link": `/event/${eventId}/dashboard`,
        "class_name": "",
        "is_icon_class": true,
        "component": "Events/Event/Dashboard",
        "icon": "ri-home-4-line"
    },
    {
        "title": "Passing Methods",
        "name": "sidebar.passingMethods",
        "is_heading": false,
        "is_active": false,
        "link": '#',
        "class_name": "",
        "is_icon_class": true,
        "component": [
            "Events/Event/Tickets/Index",
            "Events/Event/Tickets/Create",
            "Events/Event/Services/Index",
            "Events/Event/Services/Create",
            "Events/Event/WaitingList/Index",
            "Events/Event/Surveys/Index",
            "Events/Event/Surveys/Create"
        ],
        "icon": "ri-settings-3-line",
        "disabled_for": ["Customer Support Users"],
        "children": [
            {
                "title": "Tickets",
                "name": "sidebar.tickets",
                "is_active": false,
                "link": `/event/${eventId}/tickets`,
                "class_name": "",
                "is_icon_class": true,
                "component": ["Events/Event/Tickets/Index", "Events/Event/Tickets/Create"],
                "icon": ""
            },
            {
                "title": "Services",
                "name": "sidebar.services",
                "is_active": false,
                "link": `/event/${eventId}/services`,
                "class_name": "",
                "is_icon_class": true,
                "component": ["Events/Event/Services/Index", "Events/Event/Services/Create"],
                "icon": ""
            },
            {
                "title": "Waiting List",
                "name": "sidebar.waitingList",
                "is_active": false,
                "link": `/event/${eventId}/waiting-list`,
                "class_name": "",
                "is_icon_class": true,
                "component": "Events/Event/WaitingList/Index",
                "icon": ""
            },
            {
                "title": "Surveys",
                "name": "sidebar.surveys",
                "is_active": false,
                "link": `/event/${eventId}/surveys`,
                "class_name": "",
                "is_icon_class": true,
                "component": ["Events/Event/Surveys/Index", "Events/Event/Surveys/Create"],
                "icon": ""
            }
        ]
    },
    {
        "title": "Orders",
        "name": "sidebar.orders",
        "is_heading": false,
        "is_active": false,
        "link": "#",
        "class_name": "",
        "is_icon_class": true,
        "component": ["Events/Event/Orders/Index", "Events/Event/Orders/Details"],
        "icon": "ri-shopping-cart-line",
        "disabled_for": ["Ticket Sellers", "Report Users", "Checkin Users", "Checkout Users", "Verify Users"],
        "children": [
            {
                "title": "View",
                "name": "sidebar.view",
                "is_active": false,
                "link": `/event/${eventId}/orders`,
                "class_name": "",
                "is_icon_class": true,
                "component": "Events/Event/Orders/Index",
                "icon": "",
                "type": "organiser"
            }
        ]
    },
    {
        "title": "Attendees",
        "name": "sidebar.attendees",
        "is_heading": false,
        "is_active": false,
        "link": '#',
        "class_name": "",
        "is_icon_class": true,
        "component": ["Events/Event/Attendees/Index", "Events/Event/Attendees/Edit"],
        "icon": "ri-settings-3-line",
        "disabled_for": ["Ticket Sellers", "Report Users"],
        "children": [
            {
                "title": "View",
                "name": "sidebar.view",
                "is_active": false,
                "link": `/event/${eventId}/attendees`,
                "class_name": "",
                "is_icon_class": true,
                "component": "Events/Event/Attendees/Index",
                "icon": ""
            }
        ]
    },
    {
        "title": "Customize",
        "name": "sidebar.customize",
        "is_heading": false,
        "is_active": false,
        "link": '#',
        "class_name": "",
        "is_icon_class": true,
        "component": ["Events/Event/Customize/General", "Events/Event/Customize/ServiceFees", "Events/Event/Customize/Design", "Events/Event/Customize/TicketDesign", "Events/Event/Customize/Seats"],
        "icon": "ri-settings-3-line",
        "disabled_for": ["Editors", "Ticket Sellers", "Report Users", "Checkin Users", "Checkout Users", "Verify Users", "Customer Support Users"],
        "children": [
            {
                "title": "Edit Event",
                "name": "event.edit",
                "is_active": false,
                "link": `/events/${eventId}/edit?return=${location.href}`,
                "class_name": "",
                "is_icon_class": true,
                "component": "Events/Create",
                "icon": ""
            },
            {
                "title": "General",
                "name": "sidebar.general",
                "is_active": false,
                "link": `/event/${eventId}/customize`,
                "class_name": "",
                "is_icon_class": true,
                "component": "Events/Event/Customize/General",
                "icon": ""
            },
            {
                "title": "Service Fees & Taxes",
                "name": "sidebar.serviceFees",
                "is_active": false,
                "link": `/event/${eventId}/service-fees`,
                "class_name": "",
                "is_icon_class": true,
                "component": "Events/Event/Customize/ServiceFees",
                "icon": ""
            },
            {
                "title": "DesignPage",
                "name": "sidebar.designPage",
                "is_active": false,
                "link": `/event/${eventId}/page-design`,
                "class_name": "",
                "is_icon_class": true,
                "component": "Events/Event/Customize/Design",
                "icon": ""
            },
            {
                "title": "Ticket Design",
                "name": "sidebar.designTicket",
                "is_active": false,
                "link": `/event/${eventId}/ticket-design`,
                "class_name": "",
                "is_icon_class": true,
                "component": "Events/Event/Customize/TicketDesign",
                "icon": ""
            },
            {
                "title": "Seats.io",
                "name": "sidebar.seats",
                "is_active": false,
                "link": `/event/${eventId}/seats-design`,
                "class_name": "",
                "is_icon_class": true,
                "component": "Events/Event/Customize/Seats",
                "icon": ""
            }
        ]
    },
    {
        "title": "Coupons",
        "name": "sidebar.coupons",
        "is_heading": false,
        "is_active": false,
        "link": "#",
        "class_name": "",
        "is_icon_class": true,
        "component": ["Events/Event/Coupons/Index", "Events/Event/Coupons/Create"],
        "icon": "ri-shopping-cart-line",
        "disabled_for": ["Customer Support Users"],
        "children": [
            {
                "title": "View",
                "name": "sidebar.view",
                "is_active": false,
                "link": `/event/${eventId}/coupons`,
                "class_name": "",
                "is_icon_class": true,
                "component": "Events/Event/Coupons/Index",
                "icon": ""
            },
            {
                "title": "Create",
                "name": "sidebar.create",
                "is_active": false,
                "link": `/event/${eventId}/coupons/create`,
                "class_name": "",
                "is_icon_class": true,
                "component": "Events/Event/Coupons/Create",
                "icon": "",
                "disabled_for": ["Editors", "Ticket Sellers", "Report Users", "Checkin Users", "Checkout Users", "Verify Users", "Customer Support Users"]
            }
        ]
    },
    {
        "title": "Tools",
        "name": "sidebar.event_tools",
        "is_heading": false,
        "is_active": false,
        "link": "#",
        "class_name": "",
        "is_icon_class": true,
        "component": ["Events/Event/Tools/Affiliates"],
        "icon": "ri-settings-3-line",
        "disabled_for": ["Editors", "Ticket Sellers", "Report Users", "Checkin Users", "Checkout Users", "Verify Users", "Customer Support Users"],
        "children": [
            {
                "title": "Affiliates",
                "name": "sidebar.affiliates",
                "is_active": false,
                "link": `/event/${eventId}/affiliates`,
                "class_name": "",
                "is_icon_class": true,
                "component": "Events/Event/Tools/Affiliates",
                "icon": ""
            }
        ]
    }
])
