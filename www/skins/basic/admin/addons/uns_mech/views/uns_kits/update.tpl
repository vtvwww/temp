{capture name="mainbox"}
    {assign var="i" value=$kit}
    {if is__array($i)}
        {assign var="id" value=$i.kit_id}
        {capture name="name"}
            {$id}
        {/capture}
        {assign var="name" value=$smarty.capture.name}
    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
            <hr>
            {* Добавить описание листа *}
            {include file="addons/uns_mech/views/uns_kits/components/kit.tpl"}

            <div class="buttons-container cm-toggle-button buttons-bg">
                {if $mode == "add"}
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]" hide_second_button=true}
                {else}
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]"}
                {/if}
            </div>
        </form>
        <br>
        {if $id>0}
            {include file="addons/uns_mech/views/uns_kits/components/completeness.tpl"}
            {include file="addons/uns_mech/views/uns_kits/components/motions.tpl"}
        {/if}
    </div>
{/capture}
{if $id > 0}
    {assign var="title" value="Редактирование: `$name`"}
{else}
    {assign var="title" value="Добавить новую партию"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}

{*<hr>*}
{*<pre>{$documents|print_r}</pre>*}
{*<pre>{$pumps|print_r}</pre>*}