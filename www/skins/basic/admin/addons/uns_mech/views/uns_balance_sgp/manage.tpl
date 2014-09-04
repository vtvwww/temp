{script src="js/tabs.js"}
{capture name="mainbox"}
    {capture name="search_content"}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td class="nowrap search-field">
                    <label>Период:</label>
                    <div class="break">
                        {include file="common_templates/period_selector.tpl" period=$search.period}
                    </div>
                </td>
                <td class="b1_l nowrap search-field">
                    <label for="view_all_pumps">Показать все насосы:</label>
                    <div class="break">
                        <input type="hidden" value="N" name="view_all_pumps"/>
                        <input id="view_all_pumps" type="checkbox" value="Y" name="view_all_pumps" {if $search.view_all_pumps == "Y"}checked="checked"{/if} />
                    </div>
                </td>
                <td class="b1_l nowrap search-field">
                    <label for="group_orders">Объединить заказы:</label>
                    <div class="break">
                        <select id="group_orders" name="group_orders">
                            <option>---</option>
                            <option {if $search.group_orders == "UKR" or !isset($search.group_orders)}selected="selected"{/if} value="UKR">по Украине</option>
                            {*<option {if $search.group_orders == "UKR_EXP"}  selected="selected"{/if} value="UKR_EXP">и по Украине и на Экспорт</option>*}
                        </select>
                    </div>
                </td>
            </tr>
        </table>
    {/capture}
    {include file="addons/uns/views/components/search/search.tpl" dispatch="`$controller`.`$mode`" search_content=$smarty.capture.search_content}

    {if $mode == 'dnepr'}
        {assign var="target_town" value="(Днепропетровск)"}
    {else}
        {assign var="target_town" value="(Александрия)"}
    {/if}

    {* БАЛАНС ПО НАСОСНОЙ ПРОДУКЦИИ *}
    {include file="addons/uns_mech/views/uns_balance_sgp/components/view_orders.tpl"}

    {* БАЛАНС ПО НАСОСНОЙ ПРОДУКЦИИ *}
    {include file="addons/uns_mech/views/uns_balance_sgp/components/view_pumps.tpl" balances=$balances}

    {* БАЛАНС ПО ДЕТАЛЯМ НА СГП *}
    {include file="addons/uns_mech/views/uns_balance_sgp/components/view_details.tpl" balances=$balances_D}
{/capture}
{assign var="last_date" value=$info_of_the_last_movement.date|fn_parse_date|date_format:"%d/%m/%Y"}
{assign var="last_document_id" value=$info_of_the_last_movement.document_id}
{include file="common_templates/mainbox.tpl" title="Баланс СКЛАДА ГОТОВОЙ ПРОДУКЦИИ `$target_town` `$last_date`" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
