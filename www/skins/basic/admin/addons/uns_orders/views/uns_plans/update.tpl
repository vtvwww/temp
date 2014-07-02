{capture name="mainbox"}
    {assign var="p" value=$plan}
    {if is__array($p)}
        {assign var="id" value=$p.plan_id}
        {capture name="name"}{$months[$p.month]} {$p.year} г.{/capture}
        {assign var="name" value=$smarty.capture.name}
    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
            <hr>
            {include file="addons/uns_orders/views/uns_plans/components/plan.tpl"}
            <hr>
            {include file="addons/uns_orders/views/uns_plans/components/items.tpl"}

            <div class="buttons-container cm-toggle-button buttons-bg">
                {if $mode == "add"}
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]" hide_second_button=true}
                {else}
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]"}
                {/if}
            </div>
        </form>
        <br>
    </div>
{/capture}
{if $id > 0}
    {assign var="title" value="Редактирование: План производства на `$name`"}
{else}
    {assign var="title" value="Добавить новый план"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}

