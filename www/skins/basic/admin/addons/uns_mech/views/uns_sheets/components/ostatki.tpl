{if $sheet.material_type == "O"}
    {assign var="search" value=$search_SL}
    {include file="common_templates/subheader.tpl" title="Остатки как литья (на Складе Литья)"}
    <div class="subheader_block">
        <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
            <tr>
                <th style="text-align: center;" width="250px">
                    <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                    <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                    &nbsp;Наименование
                </th>

                {if $search.mode_report == "P"}
                    <th style="text-align: center;" width="80px">{include file="common_templates/tooltip.tpl" tooltip='Общее требуемое кол-во литейных заготовок на изготовление одной единицы насоса'  tooltip_mark="Кол."}</th>
                {else}
                    <th>&nbsp;</th>
                {/if}
                <th class="b1_l" style="text-align: center;" width="25px">{include file="common_templates/tooltip.tpl" tooltip='Вес 1шт, кг'  tooltip_mark="<b>Вес</b>"}</th>
                <th class="b_l" style="text-align: center;" width="30px">
                    {include file="common_templates/tooltip.tpl" tooltip='Начальный остаток'  tooltip_mark="<b>НО</b>"}
                </th>
                <th class="b_l" style="text-align: center;" width="30px">{include file="common_templates/tooltip.tpl" tooltip='Приход'  tooltip_mark="<b>П</b>"}</th>
                <th class="b1_l" style="text-align: center;" width="30px">{include file="common_templates/tooltip.tpl" tooltip='Расход'  tooltip_mark="<b>Р</b>"}</th>
                <th class="b_l" style="background-color: #d3d3d3; text-align: center;" width="30px">
                    {include file="common_templates/tooltip.tpl" tooltip='Конечный остаток'  tooltip_mark="<b>КО</b>"}
                </th>
                {if $search.accessory_pumps == "Y"}
                    <th class="b_l">Применяемость в насосах</th>
                {/if}
            </tr>
            </thead>
            {foreach from=$balance_SL item=i key=k}
                {include file="addons/uns_foundry/views/foundry_get_balance/components/view.tpl" item=$i key=$k mode_report=$search.mode_report pump_materials=$search.pump_materials is_mark=false}
            {foreachelse}
                <tr class="no-items">
                    <td colspan="5"><p>{$lang.no_data}</p></td>
                </tr>
            {/foreach}
        </table>
    </div>
{/if}


{assign var="search" value=$search_D}
{include file="common_templates/subheader.tpl" title="Остатки как детали (на мех. цехах и складе КМП)"}
<div class="subheader_block">
    {if $search.mode_report == "P"}
    {else}
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
                    <th rowspan="3" class="b_l center" style="text-transform: none;">Принадлежность к насосам</th>
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
    </div>
{/if}