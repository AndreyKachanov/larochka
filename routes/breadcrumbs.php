<?php
use DaveJamesMiller\Breadcrumbs\BreadcrumbsGenerator as Crumbs;

Breadcrumbs::register('home', function (Crumbs $crumbs) {
    $crumbs->push('Home', route('home'));
});

Breadcrumbs::register('login', function (Crumbs $crumbs) {
    $crumbs->parent('home');
    $crumbs->push('Login', route('login'));
});

Breadcrumbs::register('login.phone', function (Crumbs $crumbs) {
    $crumbs->parent('home');
    $crumbs->push('Login', route('login.phone'));
});


Breadcrumbs::register('register', function (Crumbs $crumbs) {
    $crumbs->parent('home');
    $crumbs->push('Registration', route('register'));
});

Breadcrumbs::register('password.request', function (Crumbs $crumbs) {
    $crumbs->parent('login');
    $crumbs->push('Reset request', route('password.request'));
});

Breadcrumbs::register('password.reset', function (Crumbs $crumbs) {
    $crumbs->parent('password.request');
    $crumbs->push('Change');
});

// Admin

Breadcrumbs::register('admin.home', function (Crumbs $crumbs) {
    $crumbs->parent('home');
    $crumbs->push('Admin', route('admin.home'));
});

// Cabinet
Breadcrumbs::register('cabinet.home', function (Crumbs $crumbs) {
    $crumbs->parent('home');
    $crumbs->push('Cabinet', route('cabinet.home'));
});

Breadcrumbs::register('cabinet.profile.home', function (Crumbs $crumbs) {
    $crumbs->parent('cabinet.home');
    $crumbs->push('Profile', route('cabinet.profile.home'));
});

Breadcrumbs::register('cabinet.profile.edit', function (Crumbs $crumbs) {
    $crumbs->parent('cabinet.profile.home');
    $crumbs->push('Edit', route('cabinet.profile.edit'));
});

Breadcrumbs::register('cabinet.profile.phone', function (Crumbs $crumbs) {
    $crumbs->parent('cabinet.profile.home');
    $crumbs->push('Phone', route('cabinet.profile.phone'));
});

// Users
Breadcrumbs::register('admin.users.index', function (Crumbs $crumbs) {
    $crumbs->parent('admin.home');
    $crumbs->push('Users', route('admin.users.index'));
});

Breadcrumbs::register('admin.users.create', function (Crumbs $crumbs) {
    $crumbs->parent('admin.users.index');
    $crumbs->push('Create', route('admin.users.create'));
});

Breadcrumbs::register('admin.users.show', function (Crumbs $crumbs, User $user) {
    $crumbs->parent('admin.users.index');
    $crumbs->push($user->name, route('admin.users.show', $user));
});

Breadcrumbs::register('admin.users.edit', function (Crumbs $crumbs, User $user) {
    $crumbs->parent('admin.users.show', $user);
    $crumbs->push('Edit', route('admin.users.edit', $user));
});

// Cabinet messages
Breadcrumbs::register('cabinet.messages.index', function (Crumbs $crumbs) {
    $crumbs->parent('cabinet.home');
    $crumbs->push('Outlook', route('cabinet.messages.index'));
});

//VK Parser

Breadcrumbs::register('cabinet.currencies.index', function (Crumbs $crumbs) {
    $crumbs->parent('cabinet.home');
    $crumbs->push('Vk parser', route('cabinet.currencies.index'));
});


// Jira
Breadcrumbs::register('cabinet.jira.home', function (Crumbs $crumbs) {
    $crumbs->parent('cabinet.home');
    $crumbs->push('Jira', route('cabinet.jira.home'));
});

Breadcrumbs::register('cabinet.jira.users', function (Crumbs $crumbs) {
    $crumbs->parent('cabinet.jira.home');
    $crumbs->push('Creators', route('cabinet.jira.users'));
});

Breadcrumbs::register('cabinet.jira.operators', function (Crumbs $crumbs) {
    $crumbs->parent('cabinet.jira.home');
    $crumbs->push('Operators', route('cabinet.jira.operators'));
});

Breadcrumbs::register('cabinet.jira.issues', function (Crumbs $crumbs) {
    $crumbs->parent('cabinet.jira.home');
    $crumbs->push('Issues', route('cabinet.jira.issues'));
});
