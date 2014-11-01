{capture name="mainbox"}
    {assign var="o" value=$order}
    {if is__array($o)}
        {assign var="id" value=$o.order_id}
        {capture name="name"}{$id}{/capture}
        {assign var="name" value=$smarty.capture.name}
    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
            <hr>
            {include file="addons/uns_orders/views/uns_orders/components/order.tpl"}
            <hr>
            {include file="addons/uns_orders/views/uns_orders/components/items.tpl"}
            <hr>
            {include file="addons/uns_orders/views/uns_orders/components/shipments.tpl"}

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
    {assign var="title" value="Редактирование: Заказ №`$name`"}
{else}
    {assign var="title" value="Добавить новый заказ"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}

