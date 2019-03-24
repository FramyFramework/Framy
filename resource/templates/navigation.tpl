<!-- Navigation -->
<nav class="navbar navbar-expand-sm bg-light">
    <a class="navbar-brand" href="/">MrFibunacci</a>

    <!-- Toggler/collapsibe Button -->
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
        <span class="fa fa-bars"></span>
    </button>

    <!-- Navbar links -->
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
        <ul class="navbar-nav ml-auto">
            {if !$auth->check()}
                <li class="nav-item">
                    <a class="nav-link" href="/login">Sign In</a>
                </li>
            {else}
                <li class="nav-item">
                    <a class="nav-link" href="/user/{$auth->user()->id}">{$auth->user()->username}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/manager">Manager</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/logout">Logout</a>
                </li>
            {/if}
        </ul>
    </div>
</nav>
