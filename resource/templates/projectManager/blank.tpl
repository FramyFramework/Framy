{extends file="layouts/app.tpl" }

{block name="content"}
    <div class="container-fluid" style="margin-top:30px">
        <div class="row">
            <div class="col-md-2 order-0" id="sticky-sidebar">
                <div class="btn-group-vertical btn-block" role="group" aria-label="Basic example">
                    <a class="btn btn-outline-info btn-block" href="/manager" role="button">Manager</a>
                    {if isset($project)}
                        <a class="btn btn-outline-info btn-block" href="/manager/project/{$project->id}" role="button">Project overview</a>
                    {/if}
                    <a class="btn btn-outline-info btn-block" href="/manager/wiki" role="button">Wiki</a>
                </div>
            </div>
            <div class="col" id="main">
                {block name="projectManagerContent"}{/block}
            </div>
        </div>
    </div>
{/block}
