{extends 'projectManager/blank.tpl'}

{block name="projectManagerContent"}
    <h1>Settings</h1>

    <div class="row">
        <div class="col-3">
            <div class="list-group">
                <a href="/manager/project/{$project->id}/settings?p=general" class="list-group-item list-group-item-action">General</a>
                <a href="/manager/project/{$project->id}/settings?p=members" class="list-group-item list-group-item-action">Members</a>
            </div>
        </div>
        {if !is_null($goto)}
            {$page = $goto}
        {else}
            {$page = $smarty.get.p}
        {/if}
        <div class="col-9">
            {if $page == "general" or is_null($page)}
                {include "projectManager/project/settings/general.tpl"}
            {elseif $page == "members"}
                {include "projectManager/project/settings/members.tpl"}
            {/if}
        </div>
    </div>
{/block}
