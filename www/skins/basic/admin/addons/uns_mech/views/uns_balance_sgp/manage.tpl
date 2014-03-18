{script src="js/tabs.js"}
{capture name="mainbox"}
    {capture name="search_content"}
        {include file="addons/uns/views/components/search/s_time.tpl"}
    {/capture}
    {include file="addons/uns/views/components/search/search.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}

    {* БАЛАНС ПО НАСОСНОЙ ПРОДУКЦИИ *}
    {include file="addons/uns_mech/views/uns_balance_sgp/components/view_orders.tpl"}

    {* БАЛАНС ПО НАСОСНОЙ ПРОДУКЦИИ *}
    {include file="addons/uns_mech/views/uns_balance_sgp/components/view_pumps.tpl" balances=$balances}

    {* БАЛАНС ПО ДЕТАЛЯМ НА СГП *}
    {include file="addons/uns_mech/views/uns_balance_sgp/components/view_details.tpl" balances=$balances.D}
{/capture}
{assign var="last_date" value=$info_of_the_last_movement.date|fn_parse_date|date_format:"%d/%m/%Y"}
{assign var="last_document_id" value=$info_of_the_last_movement.document_id}
{include file="common_templates/mainbox.tpl" title="Баланс СКЛАДА ГОТОВОЙ ПРОДУКЦИИ `$last_date`" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
