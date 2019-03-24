<form method="post" action="/manager/project/{$project->id}/edit">
    <div class="form-group">
        <label for="name">Project name:</label>
        <input type="text" name="name" id="name" class="form-control" value="{$project->name}">
    </div>
    <div class="form-group">
        <label for="description">Description:</label>
        <textarea class="form-control" name="description" rows="5" minlength="3" maxlength="255" id="description">{$project->description}</textarea>
    </div>
    <input type="hidden" name="category" value="general">
    <button type="submit" class="btn btn-primary mb-2">Save</button>
</form>
