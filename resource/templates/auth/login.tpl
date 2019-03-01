<form action="/login" method="post">
    <label for="username">Username</label>
    <input type="text" id="username" name="_username" value="{$last_username}" required="required" autocomplete="username" />

    <label for="password">Password</label>
    <input type="password" id="password" name="_password" required="required" autocomplete="current-password" />

    <input type="checkbox" id="remember_me" name="_remember_me" value="on" />
    <label for="remember_me">Remember me</label>

    <input type="submit" id="_submit" name="_submit" value="submit" />
</form>
