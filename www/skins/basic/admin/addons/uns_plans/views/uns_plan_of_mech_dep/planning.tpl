{strip}
    {if $action == "LC"}
        {include file="addons/uns_plans/views/uns_plan_of_mech_dep/planning_lc.tpl"}
    {elseif $action == "balance_of_details"}
        <table cellpadding="0" cellspacing="0" border="0" class="table" style="background-color: #F7F7F7;background-position: center 45px;">
            <thead>
            <tr>
                <th rowspan="3" style="text-align: center; " width="300px">
                    <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                    <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                    &nbsp;Наименование</th>
                <th rowspan="3" class="center b1_l" style="text-transform: none; font-size: 11px;">Клеймо</th>
                <th colspan="4" class="b_l center" style="width: 110px; text-transform: none;">МЕХАНИЧЕСКИЙ ЦЕХ</th>
                <th rowspan="3" class="b_l center" style="width:30px;">Скл<br>КМП</th>
                <th rowspan="3" class="b_l center" style="text-transform: none;">Применяемость в насосах</th>
            </tr>
            <tr>
                <th colspan="2" class="center b_l b1_t">№1</th>
                <th colspan="2" class="center b_l b1_t">№2</th>
            </tr>
            <tr>
                <th class="center b1_t b_l"  style="width:30px;">{include file="common_templates/tooltip.tpl" tooltip="Деталь еще в ОБРАБОТКЕ" tooltip_mark="<b>О</b>"}</th>
                <th class="center b1_t b1_l" style="width:30px;">{include file="common_templates/tooltip.tpl" tooltip="Деталь уже обработана, т.е. ЗАВЕРШЕНА" tooltip_mark="<b>З</b>"}</th>
                <th class="center b1_t b_l"  style="width:30px;">{include file="common_templates/tooltip.tpl" tooltip="Деталь еще в ОБРАБОТКЕ" tooltip_mark="<b>O</b>"}</th>
                <th class="center b1_t b1_l" style="width:30px;">{include file="common_templates/tooltip.tpl" tooltip="Деталь уже обработана, т.е. ЗАВЕРШЕНА" tooltip_mark="<b>З</b>"}</th>
            </tr>
            </thead>
            {if is__array($balances_D)}
                {include file="addons/uns_mech/views/uns_balance_mc_sk_su/components/view_total_balance_of_details.tpl" balances=$balances_D}
            {else}
                <tbody>
                    <tr class="no-items">
                        <td colspan="7"><p>{$lang.no_data}</p></td>
                    </tr>
                </tbody>
            {/if}
        </table>
    {/if}
{/strip}