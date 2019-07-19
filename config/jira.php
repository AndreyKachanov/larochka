<?php

return [
    'host' => env('JIRA_HOST'),
    'user' => env('JIRA_USER'),
    'password' => env('JIRA_PASS'),
    'project_name' => env('JIRA_PROJECT_NAME', 'HELP'),
    'fetched_count' => env('JIRA_FETCHED_COUNT', 100),
    'issue_types' => [
        [
            'name' => 'Відмовлено в реєстрації',
            'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
            'borderColor' => 'rgba(255,99,132,1)',
            'borderWidth' => 1
        ],
        [
            'name' => 'Зареєстровано та не розподілено',
            'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
            'borderColor' => 'rgba(54, 162, 235, 1)',
            'borderWidth' => 1
        ],
        [
            'name' => 'Помилка обробки',
            'backgroundColor' => 'rgba(255, 206, 86, 0.2)',
            'borderColor' => 'rgba(255, 206, 86, 1)',
            'borderWidth' => 1
        ],
        [
            'name' => 'Прийнято та очікує на реєстрацію',
            'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
            'borderColor' => 'rgba(75, 192, 192, 1)',
            'borderWidth' => 1
        ],
        [
            'name' => 'Решта (не статистика)',
            'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
            'borderColor' => 'rgba(153, 102, 255, 1)',
            'borderWidth' => 1
        ],
        [
            'name' => 'Інші проекти',
            'backgroundColor' => 'rgba(255, 159, 64, 0.2)',
            'borderColor' => 'rgba(255, 159, 64, 1)',
            'borderWidth' => 1
        ]
    ],
];
