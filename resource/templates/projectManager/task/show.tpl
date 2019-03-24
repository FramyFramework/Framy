{extends 'projectManager/blank.tpl'}

{block name="content"}
    <section>
        <div class="row">
            <div class="col-6">
                <h1>#{$task->id}</h1>
            </div>
            <div class="col-6">
                <div class="float-right">
                    <a href="{$task->id}/edit/" class="btn btn-primary" role="button" >Edit</a>
                </div>
            </div>
        </div>
        <div class="card card-body">
            <h2>{$task->title}<br>
                <small class="text-muted">
                    Added by <a href="/user/{{$task->createdBy}}">{$task->createdByUser}</a> on {$task->created_at}
                </small>
            </h2>
            <div class="row">
                <div class="col-md-6">
                    <table>
                        <tbody>
                            <tr>
                                <td><b>Status:</b></td>
                                <td>{$task->status}</td>
                            </tr>
                            <tr>
                                <td><b>Priority:</b></td>
                                <td>{$task->priority}</td>
                            </tr>
                            <tr>
                                <td><b>Assigned to:</b></td>
                                <td>{$task->assignedTo}</td>
                            </tr>
                            <tr>
                                <td><b>Last changed:</b></td>
                                <td>{$task->updated_at}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card card-body">
            <h2>Description</h2>
            <pre class="font-weight-normal">{$task->description}</pre>
        </div>
    </section>
{/block}
