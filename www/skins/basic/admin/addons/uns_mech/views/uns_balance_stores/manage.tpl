{script src="js/tabs.js"}
{capture name="mainbox"}
    {include file="addons/uns_mech/views/uns_balance_stores/components/search_form.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}

    <table cellpadding="0" cellspacing="0" border="0" class="table" style="background-color: #F7F7F7;background-position: center 45px;">
        <thead>
            <tr>
                <th class="center b1_b" width="250px">
                    <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                    <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                    &nbsp;Наименование</th>
                <th class="center b1_l b1_b" width="30px">{include file="common_templates/tooltip.tpl" tooltip="Единицы измерения" tooltip_mark="<b>ЕИ</b>"}</th>
                <th class="center b1_l b1_b" width="30px">{include file="common_templates/tooltip.tpl" tooltip="Начальный остаток" tooltip_mark="<b>НО</b>"}</th>
                <th class="center b1_l b1_b" width="30px">{include file="common_templates/tooltip.tpl" tooltip="Приход" tooltip_mark="<b>П</b>"}</th>
                <th class="center b1_l b1_b" width="30px">{include file="common_templates/tooltip.tpl" tooltip="Расход" tooltip_mark="<b>Р</b>"}</th>
                <th class="center b1_l b1_b" width="40px">{include file="common_templates/tooltip.tpl" tooltip="Конечный остаток" tooltip_mark="<b>КО</b>"}</th>
                <th class="center b2_l b1_b" >Комментарий</th>
            </tr>
        </thead>
        {if is__array($balances)}
            {include file="addons/uns_mech/views/uns_balance_stores/components/view_balance.tpl" balances=$balances}
        {else}
            <tbody>
                <tr class="no-items">
                    <td colspan="10" style="background-color: #F7F7F7;"><p>{$lang.no_data}</p></td>
                </tr>
            </tbody>
        {/if}
    </table>
{/capture}
{assign var="time_from" value=$search.time_from|fn_parse_date|date_format:"%d/%m/%Y"}
{assign var="time_to" value=$search.time_to|fn_parse_date|date_format:"%d/%m/%Y"}
{assign var="object_name" value="Склад Метизов и Подшипников"}
{include file="common_templates/mainbox.tpl" title="Баланс `$object_name` (`$time_from` - `$time_to`)" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
