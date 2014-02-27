{script src="js/tabs.js"}
{capture name="mainbox"}
    {capture name="search_content"}
        {include file="addons/uns/views/components/search/s_time.tpl"}
        {*<span style="color: #FF0000;display: block;font-size: 11px;font-weight: bold;margin-left: 337px;margin-top: -10px;padding: 0;">Важной является конечная дата!</span>*}
        {*{include file="addons/uns/views/components/search/s_details.tpl"}*}
        {*{include file="addons/uns_mech/views/uns_balance_mc_sk_su/components/s_objects.tpl" }*}
        {*{include file="addons/uns/views/components/search/s_mode_report.tpl"}*}
        {*{include file="addons/uns/views/components/search/s_view_all_position.tpl"}*}
        {*{include file="addons/uns/views/components/search/s_accessory_pumps.tpl"}*}
    {/capture}
    {include file="addons/uns/views/components/search/search.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}

    {* БАЛАНС ПО НАСОСНОЙ ПРОДУКЦИИ *}
    {include file="addons/uns_mech/views/uns_balance_sgp/components/view_orders.tpl"}

    {* БАЛАНС ПО НАСОСНОЙ ПРОДУКЦИИ *}
    {include file="addons/uns_mech/views/uns_balance_sgp/components/view_pumps.tpl" balances=$balances}

    {* БАЛАНС ПО ДЕТАЛЯМ НА СГП *}
    {include file="common_templates/subheader.tpl" title="Детали"}
    <div class="subheader_block">
        <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
                <tr>
                    <th style="text-align: center; " width="300px">
                        <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                        <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                        &nbsp;Наименование
                    </th>
                    <th style="border-left: 1px solid #808080;">КЛМ</th>
                    <th style="border-left: 1px solid #808080;">СГП</th>
                    <th style="border-left: 1px solid #808080;">Принадлежность к насосам</th>
                </tr>
            </thead>
            {if is__array($balances.D)}
                {include file="addons/uns_mech/views/uns_balance_sgp/components/view_details.tpl" balances=$balances.D}
            {else}
                <tbody>
                    <tr class="no-items">
                        <td colspan="7"><p>{$lang.no_data}</p></td>
                    </tr>
                </tbody>
            {/if}
        </table>
    </div>
{/capture}
{assign var="last_date" value=$info_of_the_last_movement.date|fn_parse_date|date_format:"%d/%m/%Y"}
{assign var="last_document_id" value=$info_of_the_last_movement.document_id}
{include file="common_templates/mainbox.tpl" title="Баланс СКЛАДА ГОТОВОЙ ПРОДУКЦИИ `$last_date` [`$last_document_id`]" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
