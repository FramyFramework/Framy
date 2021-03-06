<form class="form-horizontal" role="form" method="POST" action="">

    <div class="form-group">
        <label class="col-md-4 control-label" for="email">Username</label>

        <div class="col-md-6">
            <input type="text" id="name" class="form-control" name="name" value="{$old['name']}">
            {if !is_null($errors) && $errors->keyExists("name")}
                <span class="help-block">
                    <strong>{$errors->key('name')}</strong>
                </span>
            {/if}
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 control-label" for="email">E-Mail Address</label>

        <div class="col-md-6">
            <input type="email" id="email" class="form-control" name="email" value="{$old['email']}">
            {if !is_null($errors) && $errors->keyExists("email")}
                <span class="help-block">
                    <strong>{$errors->key('email')}</strong>
                </span>
            {/if}
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 control-label" for="password">Password</label>

        <div class="col-md-6">
            <input type="password" id="password" class="form-control" name="password">
            {if !is_null($errors) && $errors->keyExists("password")}
                <span class="help-block">
                    <strong>{$errors->key('password')}</strong>
                </span>
            {/if}
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6 col-md-offset-4">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="remember"> Remember Me
                </label>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-btn fa-sign-in"></i>Login
            </button>

            <a class="btn btn-link" href="">Forgot Your Password?</a>
        </div>
    </div>
</form>
