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
        "icon": "ri-dashboard-line"
    },
    {
        "title": "Access Levels",
        "name": "sidebar.access_levels",
        "is_active": false,
        "link": `/event/${eventId}/access-levels`,
        "class_name": "",
        "is_icon_class": true,
        "component": ["Events/Event/AccessLevels/Index", "Events/Event/AccessLevels/Create"],
        "icon": "ri-sound-module-line",
        "disabled_for": ["Operations"]
    },
    {
        "title": "Surveys",
        "name": "sidebar.surveys",
        "is_active": false,
        "link": `/event/${eventId}/event-surveys`,
        "class_name": "",
        "is_icon_class": true,
        "component": ["Events/Event/Surveys/EventSurveys", "Events/Event/Surveys/Index", "Events/Event/Surveys/Create"],
        "icon": "ri-questionnaire-line",
        "disabled_for": ["Operations", "Editors"]
    },
    {
        "title": "Badges",
        "name": "sidebar.badges",
        "is_active": false,
        "link": `/event/${eventId}/badges`,
        "class_name": "",
        "is_icon_class": true,
        "component": ["Events/Event/Badges/Index", "Events/Event/Badges/Create"],
        "icon": "ri-pencil-ruler-line",
        "disabled_for": ["Operations"]
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
        "icon": "ri-group-2-line"
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
        "icon": "ri-command-line",
        "disabled_for": ["Operations", "Editors"]
    },
    {
        "title": "Areas",
        "name": "sidebar.areas",
        "is_heading": false,
        "is_active": false,
        "link": `/event/${eventId}/areas`,
        "class_name": "",
        "is_icon_class": true,
        "component": ["Events/Event/Areas/Index", "Events/Event/Areas/Create"],
        "icon": "ri-command-line",
        "disabled_for": ["Operations"]
    }
])
