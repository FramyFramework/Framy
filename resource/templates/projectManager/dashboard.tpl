{extends file="projectManager/blank.tpl" }

{block name="projectManagerContent"}
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        My work
                    </div>
                    <div class="card-body">
                        soon to be added
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Projects</div>
                    <div class="card-body">
                        <table class="table table-hover">
                            <tbody>
                                {foreach $projects as $project}
                                    <tr>
                                        <td>
                                            <a href="/manager/project/{$project->id}">{$project->name}</a>
                                            <br>{$project->description}
                                        </td>
                                        <td class="text-right align-middle">
                                            <a class="btn btn-bitbucket"
                                               href="/manager/project/{$project->id}/tasks"
                                               title="Tasks"
                                               role="button">
                                                <span class="fa fa-check-circle"></span>
                                            </a>
                                            <a class="btn btn-bitbucket"
                                               href="/manager/project/{$project->id}/settings"
                                               title="Tasks"
                                               role="button">
                                                <span class="fa fa-cog"></span>
                                            </a>
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
                <a href="/manager/project/new">Create project</a>
            </div>
        </div>
    </div>
{/block}
