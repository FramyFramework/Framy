{extends 'projectManager/blank.tpl'}

{block name="projectManagerContent"}
    <form method="post" action="/manager/project/new">
        <div class="form-group">
            <label for="name">Project name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" rows="5" minlength="3" maxlength="255" id="description"></textarea>
        </div>
        <button type="submit" class="btn btn-primary mb-2">Submit</button>
    </form>
{/block}
