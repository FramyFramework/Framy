<h1>Add user to Project</h1>
<form method="post" action="/manager/project/{$project->id}/addUser">
    <div class="form-group">
        <label for="userId">User id:</label>
        <input type="number" name="userId" id="userId" class="form-control">
        {if $errors !== true}
            <small class="text-danger">
                {$errors}
            </small>
        {/if}
    </div>
    <button type="submit" class="btn btn-primary mb-2">Add</button>
</form>
<hr>
<h1>User in Project</h1>
<table class="table">
    <thead>
        <tr>
            <th>Username</th>
            <th>Tasks assigned</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        {foreach $members as $member}
        <tr>
            <td>{$member['username']}</td>
            <td>{$member['tasksAssigned']}</td>
            <td>remove</td>
        </tr>
        {/foreach}
    </tbody>
</table>
