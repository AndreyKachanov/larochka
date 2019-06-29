<nav class="navbar navbar-jira navbar-expand-lg navbar-dark primary-color">
    <a class="navbar-brand" href="{{ route('cabinet.jira.home') }}">
        <img src="/assets/images/helpdesk_avatar.svg" width="30" alt="helpdesk avatar" title="ICS Helpdesk">
    </a>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item     {{ (Route::currentRouteName() === 'cabinet.jira.issues') ? 'active' : '' }}">
                <a class="nav-link waves-effect waves-light" href="{{ route('cabinet.jira.issues') }}" title="Issues">Issues</a>
            </li>
            <li class="nav-item {{ (Route::currentRouteName() === 'cabinet.jira.creators') ? 'active' : '' }}">
                <a class="nav-link waves-effect waves-light" href="{{ route('cabinet.jira.creators') }}" title="Creators">Creators</a>
            </li>
            <li class="nav-item     {{ (Route::currentRouteName() === 'cabinet.jira.operators') ? 'active' : '' }}">
                <a class="nav-link waves-effect waves-light" href="{{ route('cabinet.jira.operators') }}" title="Operators">Operators</a>
            </li>
        </ul>
    </div>
</nav>