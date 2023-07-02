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
            "Events/Event/AccessLevels/Index",
            "Events/Event/AccessLevels/Create",
            "Events/Event/Surveys/Index",
            "Events/Event/Surveys/Create"
        ],
        "icon": "ri-settings-3-line",
        "children": [
            {
                "title": "Access Levels",
                "name": "sidebar.access_levels",
                "is_active": false,
                "link": `/event/${eventId}/access-levels`,
                "class_name": "",
                "is_icon_class": true,
                "component": ["Events/Event/AccessLevels/Index", "Events/Event/AccessLevels/Create"],
                "icon": ""
            },
            {
                "title": "Surveys",
                "name": "sidebar.surveys",
                "is_active": false,
                "link": `/event/${eventId}/event-surveys`,
                "class_name": "",
                "is_icon_class": true,
                "component": ["Events/Event/Surveys/EventSurveys", "Events/Event/Surveys/Index", "Events/Event/Surveys/Create"],
                "icon": ""
            },
            {
                "title": "Badges",
                "name": "sidebar.badges",
                "is_active": false,
                "link": `/event/${eventId}/badges`,
                "class_name": "",
                "is_icon_class": true,
                "component": ["Events/Event/Badges/Index", "Events/Event/Badges/Create"],
                "icon": ""
            }
        ]
    },
    {
        "title": "Attendees",
        "name": "sidebar.attendees",
        "is_heading": false,
        "is_active": false,
        "link": `/event/${eventId}/attendees`,
        "class_name": "",
        "is_icon_class": true,
        "component": ["Events/Event/Attendees/Index", "Events/Event/Attendees/Edit"],
        "icon": "ri-settings-3-line"
    },
    {
        "title": "Zones",
        "name": "sidebar.zones",
        "is_heading": false,
        "is_active": false,
        "link": `/event/${eventId}/zones`,
        "class_name": "",
        "is_icon_class": true,
        "component": ["Events/Event/Zones/Index", "Events/Event/Zones/Create"],
        "icon": "ri-settings-3-line"
    }
])
