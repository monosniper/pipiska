<?php

return [
    'pages' => [
        'auth' => 'Авторизация',
        'pagination' => 'Пагинация',
        'passwords' => 'Пароли',
        'test_validation' => 'Тест Авторизация',
    ],

    'groups' => [
        'kirano_translate_main' => 'Основное',
    ],

    // resources/lang - if Laravel <= 9
    'lang_dir' => 'lang',

    'default_lang' => 'ru',
    'default_page' => 'auth',

    'can_revert' => true,

    'auto_translate' => [
        'from' => 'ru',
        'exclude_languages' => [
            'ru',
        ],
        'exclude_pages' => [],
    ],

    'theme' => [
        'text' => 'black',
        'group_text' => 'white',
        'accent' => '#ABCDFF',
        'field_background' => 'white',
        'field_text' => 'black',
        'background' => '#EEF9FF',
    ],
];
