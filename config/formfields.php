<?php

return [
    'field_types' => [
        1 => ['name' => 'Single-line text box', 'key' => 'textbox', 'has_options' => false],
        2 => ['name' => 'Multi-line text box (textarea)', 'key' => 'textarea', 'has_options' => false],
        3 => ['name' => 'DateTime', 'key' => 'datetime', 'has_options' => false],
        4 => ['name' => 'File', 'key' => 'file', 'has_options' => false],
        5 => ['name' => 'Email', 'key' => 'email', 'has_options' => false, 'required' => true],
        6 => ['name' => 'Dropdown (single selection)', 'key' => 'single_select', 'has_options' => true],
        7 => ['name' => 'Dropdown (multiple selection)', 'key' => 'multi_select', 'has_options' => true],
        8 => ['name' => 'Checkbox', 'key' => 'checkbox', 'has_options' => true],
        9 => ['name' => 'Radio input', 'key' => 'radio', 'has_options' => true],
        10 => ['name' => 'Header', 'key' => 'header', 'has_options' => false],
        11 => ['name' => 'Country', 'key' => 'country', 'has_options' => false],
        12 => ['name' => 'Mobile Number', 'key' => 'mobile', 'has_options' => false],
        13 => ['name' => 'Terms & Conditions', 'key' => 'tandc', 'has_options' => false],
    ]
];
