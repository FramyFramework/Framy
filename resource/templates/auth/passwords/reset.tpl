<form class="form-horizontal" role="form" method="POST" action="/reset-password/{$token}">

    <input type="hidden" name="token" value="{$token}">

    <div class="form-group">
        <label class="col-md-4 control-label" for="password">Password</label>

        <div class="col-md-6">
            <input type="password" class="form-control" id="password" name="password">

            {if !is_null($errors) && $errors->keyExists("password")}
                <span class="help-block">
                    <strong>{$errors->key('password')}</strong>
                </span>
            {/if}
        </div>
    </div>

    <div class="form-group">
        <label class="col-md-4 control-label" for="password_confirmation">Confirm Password</label>
        <div class="col-md-6">
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">

            {if !is_null($errors) && $errors->keyExists("password_confirmation")}
                <span class="help-block">
                    <strong>{$errors->key('password_confirmation')}</strong>
                </span>
            {/if}

        </div>
    </div>

    <div class="form-group">
        <div class="col-md-6 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-btn fa-refresh"></i>Reset Password
            </button>
        </div>
    </div>
</form>
