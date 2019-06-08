<?php

return [
    'host' => env('JIRA_HOST'),
    'user' => env('JIRA_USER'),
    'password' => env('JIRA_PASS'),
    'project_name' => env('JIRA_PROJECT_NAME', 'HELP'),
    'fetched_count' => env('JIRA_FETCHED_COUNT', 100),
];
