{extends 'projectManager/blank.tpl'}

{block name="content"}
    {if $task == null}
        <h1>New task</h1>
    {else}
        <h1>Edit task</h1>
    {/if}
    <form action="" method="post">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" class="form-control" value="{$task->title}" required>
            {if $errors['title'] != 1}
                <small class="text-danger">
                    {$errors['title']}
                </small>
            {/if}
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea class="form-control" rows="5" name="description" id="description">{$task->description}</textarea>
            {if $errors['description'] != 1}
                <small class="text-danger">
                    {$errors['description']}
                </small>
            {/if}
        </div>
        <div class="form-group">
            <label for="assignTo">Assign to:</label>
            <select class="form-control" id="assignTo" name="assignTo">
                <option value="0">No one</option>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Status:</label>
            <select class="form-control" id="status" name="status">
                {foreach $status as $statu}
                    <option value="{$statu->id}"
                            {if $statu->id == $task->status}selected{/if}
                        >{$statu->text}</option>
                {/foreach}
            </select>
            {if $errors['status'] != 1}
                <small class="text-danger">
                    {$errors['status']}
                </small>
            {/if}
        </div>
        <div class="form-group">
            <label for="priority">Priority:</label>
            <select class="form-control" id="priority" name="priority">
                {foreach $priorities as $priority}
                    <option value="{$priority->id}"
                            {if $priority->id == $task->priority}selected{/if}
                    >{$priority->text}</option>
                {/foreach}
            </select>
            {if $errors['priority'] != 1}
                <small class="text-danger">
                    {$errors['priority']}
                </small>
            {/if}
        </div>
        <button type="submit" class="btn btn-primary mb-2">Submit</button>
    </form>
{/block}
