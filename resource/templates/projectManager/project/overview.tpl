{extends 'projectManager/blank.tpl'}

{block name="projectManagerContent"}
    <div class="row">
        <div class="col-12">
            <h1>{$project->name}</h1>
            <h2>Tasks:</h2>
            <a href="/manager/project/{$project->id}/task/new">crete new</a>
            <table class="table table-striped" id="myTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Title</th>
                        <th>Created by</th>
                        <th>Assigned to</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {$lastStatus = null}
                    {foreach $tasks as $key => $task}
                        {if $lastStatus != $task->status}
                            {$lastStatus = $task->status}
                            <tr>
                                <td></td>
                                <td><b>{$task->status}</b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        {/if}

                        <tr>
                            <td>#{$task->id}</td>
                            <td>{$task->status}</td>
                            <td>
                                {include 'projectManager/project/priority.tpl' priority=$task->priority }
                            </td>
                            <td><a href="/manager/project/{$project->id}/task/{$task->id}">{$task->title}</a></td>
                            <td>{$task->createdBy}</td>
                            <td>{$task->assignedTo}</td>
                            <td class="text-right align-middle">
                                <div class="dropdown">
                                    <button class="btn btn-sm">
                                        <a href="/manager/project/{$project->id}/task/{$task->id}/edit" title="edit">
                                            <i class="fa fa-pen"></i>
                                        </a>
                                    </button>
                                    <button class="btn btn-sm">
                                        <a href="/manager/project/{$project->id}/task/{$task->id}/" title="delete">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
{/block}
