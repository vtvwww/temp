{capture name="mainbox"}
    {literal}
        <style>
            table.table.LC td.w{
                font-size: 11px;
                color: #808080;
            }

            table.table.LC td.g{ /* gray */
                background-color: #f1f1f1;
            }

            table.table.LC td.dg{ /* dark gray */
                background-color: #D3D3D3;
            }

            table.table.LC td.r{ /* remaining */
                background-color: #B8C1FF;
            }

            table.table.LC td.prh{ /* prohibition */
                background: url('skins/basic/admin/addons/uns_plans/images/prohibition.png') no-repeat center center;
            }

            table.table.LC td.p_r{ /* priority materials RED*/
                background-color: #ff8888;
            }
            table.table.LC td.p_y{ /* priority materials YELLOW*/
                background-color: #FFE388;
            }
        </style>
    {/literal}
    <table style="margin: 10px" width="100%">
        <tr>
            <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_r.png"/> - потребность в ближайшие 2 недели</td>
            <td class="b1_l"></td>
            <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_y.png"/> - потребность на 2-й ÷ 4-й неделях</td>
            <td class="b1_l"></td>
            <td class="center"><img src="skins/basic/admin/addons/uns_plans/images/p_g.png"/> - потребность с 4-й недели</td>
        </tr>
        <tr>
            <td class="center bold">{$priority_materials_q.R|fn_fvalue:0} шт.; {$priority_materials_w.R/1000|fn_fvalue:1} т</td>
            <td class="b1_l"></td>
            <td class="center bold">{$priority_materials_q.Y|fn_fvalue:0} шт.; {$priority_materials_w.Y/1000|fn_fvalue:1} т</td>
            <td class="b1_l"></td>
            <td></td>
        </tr>
    </table>
    <table cellpadding="0" cellspacing="0" border="0" class="table LC">
        <thead>
            <tr style="background-color: #D4D0C8;">
                <th rowspan="2" style="text-align: center;" width="250px">
                    <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                    <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                    &nbsp;Наименование
                </th>

                <th rowspan="2" class="b1_l center" style="text-transform: none;" width="25px">{include file="common_templates/tooltip.tpl" tooltip='Вес 1 шт в кг'  tooltip_mark="<b>Вес<br>кг</b>"}</th>

                <th colspan="3" class="b3_l center" style="text-transform: none;" width="60px">
                    Потребность<br>в отливках<br>на 00:00<br>01/{$data.month|string_format:"%02d"}/{$data.year}
                </th>
                <th colspan="4" class="b3_l center" style="text-transform: none;" width="60px">
                    Движение отливок<br> по Складу литья<br>за {$months_full[$data.month]} {$data.year} г.
                </th>
                <th colspan="3" class="b3_l center" style="text-transform: none; background-color: #B8C1FF;" width="60px">
                    ОСТАЛОСЬ<br>на 23:59<br>{$data.current_day}
                </th>
                {*Запрет*}
                <th rowspan="2" class="b3_l" style="text-align: center;" width="10px">&nbsp;</th>

                <th rowspan="2" class="" style="text-transform: none;">Принадлежность к насосам</th>
            </tr>
            <tr style="background-color: #D4D0C8;">
                {*ПЛАН*}
                <th style="width: 42px; padding: 0; font-weight: bold; font-size: 11px;" class="center b3_l b1_t">{$data.tpl_curr_month_roman.month}<hr class="roman_dates">{$data.tpl_curr_month_roman.year}</th>
                <th style="width: 42px; padding: 0; font-weight: bold; font-size: 11px;" class="center b1_l b1_t">{$data.tpl_next_month_roman.month}<hr class="roman_dates">{$data.tpl_next_month_roman.year}</th>
                <th style="width: 42px; padding: 0; font-weight: bold; font-size: 11px;" class="center b1_l b1_t">{$data.tpl_next2_month_roman.month}<hr class="roman_dates">{$data.tpl_next2_month_roman.year}</th>
                {*<th style="width: 42px;" class="center b1_l b1_t">{$data.tpl_next3_month_roman.month}<hr class="roman_dates">{$data.tpl_next3_month_roman.year}</th>*}

                {*СКЛАД ЛИТЬЯ*}
                <th style="width: 42px; padding: 0; font-weight: bold; font-size: 11px; text-transform: none; background-color: #f1f1f1;" class="center b3_l b1_t">{include file="common_templates/tooltip.tpl" tooltip="Остаток склада литья на начало месяца" tooltip_mark="<b>Нач.<br>ост.</b>"}</th>
                <th style="width: 42px; padding: 0; font-weight: bold; font-size: 11px; text-transform: none; background-color: #f1f1f1;" class="center b1_l b1_t">{include file="common_templates/tooltip.tpl" tooltip="Приход на склад литья" tooltip_mark="<b>П*</b>"}</th>
                <th style="width: 42px; padding: 0; font-weight: bold; font-size: 11px; text-transform: none; background-color: #f1f1f1;" class="center b1_l b1_t">{include file="common_templates/tooltip.tpl" tooltip="Расход со склада литья" tooltip_mark="<b>Р</b>"}</th>
                <th style="width: 42px; padding: 0; font-weight: bold; font-size: 11px; text-transform: none;" class="center b2_l b1_t">{include file="common_templates/tooltip.tpl" tooltip="Остаток склада литья на `$data.current_day`" tooltip_mark="<b>Тек.<br>ост.</b>"}</th>

                {*ОСТАЛОСЬ*}
                <th style="width: 42px; padding: 0; font-weight: bold; font-size: 11px; background-color: #B8C1FF;" class="center b3_l b1_t">{$data.tpl_curr_month_roman.month}<hr class="roman_dates">{$data.tpl_curr_month_roman.year}</th>
                <th style="width: 42px; padding: 0; font-weight: bold; font-size: 11px; background-color: #B8C1FF;" class="center b1_l b1_t">{$data.tpl_next_month_roman.month}<hr class="roman_dates">{$data.tpl_next_month_roman.year}</th>
                <th style="width: 42px; padding: 0; font-weight: bold; font-size: 11px; background-color: #B8C1FF;" class="center b1_l b1_t">{$data.tpl_next2_month_roman.month}<hr class="roman_dates">{$data.tpl_next2_month_roman.year}</th>
                {*<th style="width: 42px; background-color: #B8C1FF;" class="center b1_l b1_t">{$data.tpl_next3_month_roman.month}<hr class="roman_dates">{$data.tpl_next3_month_roman.year}</th>*}
            </tr>
        </thead>
        {foreach from=$balance_of_casts item=i key=k}
            {if $i.group_view_in_plans == "Y"}
                {include file="addons/uns_plans/views/uns_plan_of_mech_dep/components/view_planning_lc.tpl" item=$i key=$k}
            {/if}
        {foreachelse}
            <tr class="no-items">
                <td colspan="5"><p>{$lang.no_data}</p></td>
            </tr>
        {/foreach}
        {*******************************************************************}
        {* ВЕС *}
        {*******************************************************************}
        <tbody>
            <tr class="">
                <td class="b3_t b3_b" rowspan="2" colspan="2" align="right"><span style="font-size: 15px; font-weight: bold;">ИТОГО, т:</span></td>
                {*ПОТРЕБНОСТЬ*}
                {assign var="q" value=$requirement_of_casts.curr_month.total_weight/1000|fn_fvalue:1|default:0}
                <td class="center b3_t b3_l"><span style="font-size: 15px;">{$q}</span></td>

                {assign var="q" value=$requirement_of_casts.next_month.total_weight/1000|fn_fvalue:1|default:0}
                <td class="center b3_t b1_l"><span style="font-size: 15px;">{$q}</span></td>

                {assign var="q" value=$requirement_of_casts.next2_month.total_weight/1000|fn_fvalue:1|default:0}
                <td class="center b3_t b1_l"><span style="font-size: 15px;">{$q}</span></td>

                {*ДВИЖЕНИЕ*}
                {assign var="q" value=$movement_of_casts.nach/1000|fn_fvalue:1|default:0}
                <td style="background-color: #f1f1f1;" rowspan="2" class="center b3_t b3_b b3_l {if !$q}zero{/if}"><span style="font-size: 15px;">{$q}</span></td>

                {assign var="q" value=$movement_of_casts.in/1000|fn_fvalue:1|default:0}
                <td style="background-color: #f1f1f1;" rowspan="2" class="center b3_t b3_b b1_l {if !$q}zero{/if}"><span style="font-size: 15px;">{$q}</span></td>

                {assign var="q" value=$movement_of_casts.out/1000|fn_fvalue:1|default:0}
                <td style="background-color: #f1f1f1;" rowspan="2" class="center b3_t b3_b b1_l {if !$q}zero{/if}"><span style="font-size: 15px;">{$q}</span></td>

                {assign var="q" value=$movement_of_casts.konech/1000|fn_fvalue:1|default:0}
                <td style="background-color: #D3D3D3;" rowspan="2" class="center b3_t b3_b b2_l {if !$q}zero{/if} bold"><span style="font-size: 15px;">{$q}</span></td>

                {*ОСТАЛОСЬ*}
                {assign var="q" value=$remaining_of_casts.curr_month.total_weight/1000|fn_fvalue:1|default:0}
                <td class="center b3_t b3_l" style="background-color: #B8C1FF;"><span style="font-size: 15px; font-weight: bold;">{$q}</span></td>

                {assign var="q" value=$remaining_of_casts.next_month.total_weight/1000|fn_fvalue:1|default:0}
                <td class="center b3_t b1_l" style="background-color: #B8C1FF;"><span style="font-size: 15px; font-weight: bold;">{$q}</span></td>

                {assign var="q" value=$remaining_of_casts.next2_month.total_weight/1000|fn_fvalue:1|default:0}
                <td class="center b3_t b1_l" style="background-color: #B8C1FF;"><span style="font-size: 15px; font-weight: bold;">{$q}</span></td>

                <td class="b3_t b3_l b3_b" rowspan="2" colspan="2">&nbsp;</td>
            </tr>
            <tr class="">
                {assign var="q" value=$requirement_of_casts.curr_month.total_weight+$requirement_of_casts.next_month.total_weight+$requirement_of_casts.next2_month.total_weight}
                <td class="center b1_t b3_l b3_b" colspan="3"><span style="font-size: 15px;">{$q/1000|fn_fvalue:1|default:0}</span></td>

                {assign var="q" value=$remaining_of_casts.curr_month.total_weight+$remaining_of_casts.next_month.total_weight+$remaining_of_casts.next2_month.total_weight}
                <td class=" center b1_t b3_l b3_b" style="background-color: #B8C1FF;" colspan="3"><span style="font-size: 15px; font-weight: bold;">{$q/1000|fn_fvalue:1|default:0}</span></td>
            </tr>
        </tbody>

{*        *}{*******************************************************************}{*
        *}{*ШАПКА ТАБЛИЦЫ*}{*
        *}{*******************************************************************}{*
        <tbody>
        <tr style="background-color: #D4D0C8;">
            <th rowspan="2" style="text-align: center;" width="250px">
                <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                &nbsp;Наименование
            </th>

            <th rowspan="2" class="b1_l center" style="text-transform: none;" width="25px">{include file="common_templates/tooltip.tpl" tooltip='Вес 1 шт в кг'  tooltip_mark="<b>Вес<br>кг</b>"}</th>

            *}{*ПОТРЕБНОСТЬ*}{*
            <th style=" font-weight: bold; font-size: 11px; padding: 0;" class="center b3_l ">{$data.tpl_curr_month_roman.month}<hr class="roman_dates">{$data.tpl_curr_month_roman.year}</th>
            <th style=" font-weight: bold; font-size: 11px; padding: 0;" class="center b1_l ">{$data.tpl_next_month_roman.month}<hr class="roman_dates">{$data.tpl_next_month_roman.year}</th>
            <th style=" font-weight: bold; font-size: 11px; padding: 0;" class="center b1_l ">{$data.tpl_next2_month_roman.month}<hr class="roman_dates">{$data.tpl_next2_month_roman.year}</th>
            *}{*<th style="" class="center b1_l ">{$data.tpl_next3_month_roman.month}<hr class="roman_dates">{$data.tpl_next3_month_roman.year}</th>*}{*

            *}{*СКЛАД ЛИТЬЯ*}{*
            <th style=" text-transform: none; font-weight: bold; padding: 0; font-size: 11px; background-color: #f1f1f1;" class="center b3_l ">{include file="common_templates/tooltip.tpl" tooltip="Остаток склада литья на начало месяца" tooltip_mark="<b>Нач.<br>ост.</b>"}</th>
            <th style=" text-transform: none; font-weight: bold; padding: 0; font-size: 11px; background-color: #f1f1f1;" class="center b1_l ">{include file="common_templates/tooltip.tpl" tooltip="Приход на склад литья" tooltip_mark="<b>П*</b>"}</th>
            <th style=" text-transform: none; font-weight: bold; padding: 0; font-size: 11px; background-color: #f1f1f1;" class="center b1_l ">{include file="common_templates/tooltip.tpl" tooltip="Расход со склада литья" tooltip_mark="<b>Р</b>"}</th>
            <th style=" text-transform: none; font-weight: bold; padding: 0; font-size: 11px;" class="center b2_l ">{include file="common_templates/tooltip.tpl" tooltip="Остаток склада литья на `$data.current_day`" tooltip_mark="<b>Тек.<br>ост.</b>"}</th>

            *}{*ОСТАЛОСЬ*}{*
            <th style=" background-color: #B8C1FF; padding: 0; font-weight: bold; font-size: 11px;" class="center b3_l ">{$data.tpl_curr_month_roman.month}<hr class="roman_dates">{$data.tpl_curr_month_roman.year}</th>
            <th style=" background-color: #B8C1FF; padding: 0; font-weight: bold; font-size: 11px;" class="center b1_l ">{$data.tpl_next_month_roman.month}<hr class="roman_dates">{$data.tpl_next_month_roman.year}</th>
            <th style=" background-color: #B8C1FF; padding: 0; font-weight: bold; font-size: 11px;" class="center b1_l ">{$data.tpl_next2_month_roman.month}<hr class="roman_dates">{$data.tpl_next2_month_roman.year}</th>
            *}{*<th style=" background-color: #B8C1FF;" class="center b1_l ">{$data.tpl_next3_month_roman.month}<hr class="roman_dates">{$data.tpl_next3_month_roman.year}</th>*}{*

            *}{*Запрет*}{*
            <th rowspan="2" class="b3_l" style="text-align: center;" width="10px">&nbsp;</th>
            <th rowspan="2" class="" style="text-transform: none;">Принадлежность к насосам</th>
        </tr>
        <tr style="background-color: #D4D0C8;">
            <th colspan="3" class="b3_l b1_t center" style="text-transform: none;" width="60px">
                Потребность<br>в отливках<br>на 00:00<br>01/{$data.month|string_format:"%02d"}/{$data.year}
            </th>

            <th colspan="4" class="b3_l b1_t center" style="text-transform: none;" width="60px">
                Движение отливок<br> по Складу литья<br>за {$months_full[$data.month]} {$data.year} г.
            </th>

            <th colspan="3" class="b3_l b1_t center" style="text-transform: none; background-color: #B8C1FF;" width="60px">
                ОСТАЛОСЬ<br>на 23:59<br>{$data.current_day}
            </th>
        </tr>
        </tbody>*}
    </table>
    {*{if is__array($priority_materials.R)}*}
        {*<br/>&nbsp;&nbsp;&nbsp;<span style="color: red;"><img src="skins/basic/admin/addons/uns_plans/images/warning_material_r.png"/> - ВЫСОКИЙ приоритет отливки. Насосов по этой отливке хватит только на 3 недели. (общее кол-во: {$priority_materials_q.R|fn_fvalue:0} шт.; общий вес: {$priority_materials_w.R/1000|fn_fvalue:1} т)</span>*}
    {*{/if}*}
    {*{if is__array($priority_materials.Y)}*}
        {*<br/>&nbsp;&nbsp;&nbsp;<span style="color: red;"><img src="skins/basic/admin/addons/uns_plans/images/warning_material_y.png"/> - СРЕДНИЙ приоритет отливки. Насосов по этой отливке хватит от 3-х до 5-ти недель.(общее кол-во: {$priority_materials_q.Y|fn_fvalue:0} шт.; общий вес: {$priority_materials_w.Y/1000|fn_fvalue:1} т)</span>*}
    {*{/if}*}
    {if count($priority_materials.R)>0 or count($priority_materials.Y)>0}
        <br/>&nbsp;&nbsp;&nbsp;<span style="color: #000000;">Для снятия приоритетности необходимо выполнить по плану только текущий и следующий месяцы.</span>
        <hr/>
    {/if}
    &nbsp;&nbsp;&nbsp;<span style="color: red;"><img src="skins/basic/admin/addons/uns_plans/images/prohibition.png" alt="X"/> - к производству запрещены <b>{$prohibition_of_casts|count}</b> вида(-ов) заготовок, так как по ним уже есть {$data.months_supply}-х месячный запас заготовок на складе литья и соответсвующие им детали в мех. цехах и на складе комплектующих.
    <br/>
    &nbsp;&nbsp;&nbsp;<span style="color: red; font-weight: bold;">П*</span> - (приход) это все поступления на склад литья с первого числа месяца по последнее число. Это значение может отличаться от выпуска литейного цеха текущего месяца на: последний выпуск литейного цеха предыдущего месяца <b>+</b> приход отливок после отжига <b>+</b> переучеты по складу литья <b>+</b> отливки на продажу или на собственные нужды.
    <br/>
    &nbsp;&nbsp;&nbsp;<span style="color: red; font-weight: bold;">10*</span> - число со звездочкой - это потребность в заготовках для партий насосов + потребность в заготовках для коммерческого отдела при продаже деталей. <b>Заготовки со зведочкой выполняются в первую очередь!</b>
{/capture}
{include file="common_templates/mainbox.tpl" title="План производства ЛИТ. ЦЕХА на `$months_full[$data.month]` `$data.year` г. (на 23:59 `$data.current_day`)" content=$smarty.capture.mainbox tools=$smarty.capture.tools}