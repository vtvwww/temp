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
                    <th rowspan="3" style="border-left: 1px solid #808080;">{include file="common_templates/tooltip.tpl" tooltip="Номер клейма" tooltip_mark="<b>№</b>"}</th>
                    <th rowspan="3" style="border-right: 1px solid #808080;">&nbsp;</th>
                    <th colspan="4" style=" text-align: center;" width="110px">Мех. цех</th>
                    <th rowspan="3" style="border-left: 1px solid #808080; text-align: center;" width="0">Скл<br>КМП</th>
                    <th rowspan="3" style="border-left: 1px solid #808080; text-align: center;">Принадлежность к насосам</th>
                </tr>
                <tr>
                    <th colspan="2" style="text-align: center; border-top: 1px solid #808080; border-right: 1px solid #808080;">№1</th>
                    <th colspan="2" style="text-align: center; border-top: 1px solid #808080;">№2</th>
                </tr>
                <tr>
                    <th style="text-align: center; border-top: 1px solid #808080; border-right: 1px solid #808080;">{include file="common_templates/tooltip.tpl" tooltip="Деталь еще в ОБРАБОТКЕ" tooltip_mark="<b>О</b>"}</th>
                    <th style="text-align: center; border-top: 1px solid #808080; border-right: 1px solid #808080;">{include file="common_templates/tooltip.tpl" tooltip="Деталь уже обработана, т.е. ЗАВЕРШЕНА" tooltip_mark="<b>З</b>"}</th>
                    <th style="text-align: center; border-top: 1px solid #808080; border-right: 1px solid #808080;">{include file="common_templates/tooltip.tpl" tooltip="Деталь еще в ОБРАБОТКЕ" tooltip_mark="<b>O</b>"}</th>
                    <th style="text-align: center; border-top: 1px solid #808080;">{include file="common_templates/tooltip.tpl" tooltip="Деталь уже обработана, т.е. ЗАВЕРШЕНА" tooltip_mark="<b>З</b>"}</th>
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