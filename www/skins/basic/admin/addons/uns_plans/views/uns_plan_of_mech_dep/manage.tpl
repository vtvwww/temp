{strip}
    {script src="js/tabs.js"}
    {literal}
        <style>
            .zero, a.zero{
                color: #d3d3d3;
            }
        </style>
    {/literal}
    {capture name="mainbox"}
        {include file="addons/uns_plans/views/uns_plan_of_mech_dep/components/search_form_manage.tpl" dispatch="`$controller`.$mode" search_content=$smarty.capture.search_content but_text="ВЫПОЛНИТЬ РАСЧЕТ"}
        {if is__array($pump_series)}
            {* ПЛАН ПРОИЗВОДСТВА ЛИТЕЙНОГО ЦЕХА *}
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="action-add">
               <a target="_blank" href="{"uns_plan_of_mech_dep.planning.LC"|fn_url}"><b>План производства Лит. цеха</b></a>
            </span>

            {* АНАЛИЗ РАЗРЕШЕННЫХ НАСОСОВ *}
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="action-add">
               <a target="_blank" href="{"uns_plan_of_mech_dep.analysis_of_pumps.allowance"|fn_url}">Анализ <b>РАЗРЕШЕННЫХ</b> насосов</a>
            </span>

            {* АНАЛИЗ ЗАПРЕЩЕННЫХ НАСОСОВ *}
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="action-add">
               <a target="_blank" href="{"uns_plan_of_mech_dep.analysis_of_pumps.prohibition"|fn_url}">Анализ <b>ЗАПРЕЩЕННЫХ</b> насосов</a>
            </span>

            {* АНАЛИЗ ВСЕХ НАСОСОВ *}
            {*&nbsp;&nbsp;&nbsp;&nbsp;*}
            {*<span class="action-add">*}
               {*<a target="_blank" href="{"uns_plan_of_mech_dep.analysis_of_pumps.all"|fn_url}">Анализ <b>ВСЕХ</b> насосов</a>*}
            {*</span>*}

            <table cellpadding="0" cellspacing="0" border="0" class="table" style="margin: 10px; 0">
                <thead>
                    <tr style="background-color: #D4D0C8;">
                        <th style="text-transform: none;"               rowspan="3"             class="center">Наименование</th>
                        <th style="text-transform: none;"               rowspan="3"             class="center">&nbsp;</th>
                        <th style="text-transform: none;"               rowspan="3"             class="center b_l">СГП<br>на<br>01/{$search.month|string_format:"%02d"}<hr class="roman_dates">{$search.year}</th>
                        <th style="text-transform: none;"               rowspan="2" colspan="2" class="center b_l b1_b">План<br>продаж</th>
                        <th style="text-transform: none;"          rowspan="2" colspan="3" class="center b3_l b1_b">План<br>производства<br>насосов</th>
                        {*Плановая сдача партий насосов на СГП*}
                        {if $search.plan_parties == "Y"}
                        <th style="text-transform: none;"          rowspan="2" colspan="3" class="center b3_l b1_b">Плановая сдача<br>партий насосов<br>на СГП</th>
                        {/if}
                        <th style="text-transform: uppercase;background-color:#B8C1FF; "                      colspan="5" class="center b3_l b1_b">Выполнение плана на {$search.current_day|fn_parse_date|date_format:"%d/%m/%y"}</th>
                        <th style="text-transform: none;"               rowspan="3"             class="center b3_l">СГП<br>на<br>{$search.current_day|fn_parse_date|date_format:"%d/%m"}<hr class="roman_dates">{$search.year}</th>

                        {*Кратность партии*}
                        {if $search.plan_parties == "Y"}
                        <th style="text-transform: none;"               rowspan="3"             class="center b3_l">{include file="common_templates/tooltip.tpl" tooltip="<b>Кратность партии насоса.</b><br>ОТ - ДО, ШАГ" tooltip_mark="<b>КП</b>"}</th>
                        {/if}
                    </tr>
                    <tr style="background-color: #D4D0C8;">
                        <th style="text-transform: none; width: 50px; background-color: #FFEF8C;"  rowspan="3"             class="center b3_l">Задел<br>ожид.<br>сборку</th>
                        <th style="text-transform: none; width: 50px; background-color: #C0FF9A;"  rowspan="3"             class="center b2_l">Выпол-<br>нено</th>
                        <th style="text-transform: uppercase; background-color:#B8C1FF;"                      colspan="3" class="center b3_l b1_b">Осталось</th>
                    </tr>
                    <tr style="background-color: #D4D0C8;">
                        <th style="width: 40px;" class="center b_l">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="width: 40px;" class="center b1_l">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        <th style="width: 40px;" class="center b3_l">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="width: 40px;" class="center b1_l">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        <th style="width: 40px;" class="center b1_l">{$tpl_next2_month_roman.month}<hr class="roman_dates">{$tpl_next2_month_roman.year}</th>

                        {*Плановая сдача партий насосов на СГП*}
                        {if $search.plan_parties == "Y"}
                        <th style="width: 40px;" class="center b3_l">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="width: 40px;" class="center b1_l">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        <th style="width: 40px;" class="center b1_l">{$tpl_next2_month_roman.month}<hr class="roman_dates">{$tpl_next2_month_roman.year}</th>
                        {/if}

                        <th style="width: 40px;background-color: #B8C1FF;" class="center b3_l">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="width: 40px;background-color: #B8C1FF;" class="center b1_l">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        <th style="width: 40px;background-color: #B8C1FF;" class="center b1_l">{$tpl_next2_month_roman.month}<hr class="roman_dates">{$tpl_next2_month_roman.year}</th>
                    </tr>
                </thead>
                {foreach from=$pump_series item="pt" name="pt"}
                <tbody>
                <tr>
                    <td colspan="20" style="background-color: #E6E2DA; font-size: 12px; font-style: italic; font-weight: normal;">{$pt.pt_name}</td>
                </tr>
                    {foreach from=$pt.pump_series item="ps" key="ps_id" name="ps"}
                    <tr>
                        {*Наименование*}
                        <td>
                            <a  rev="content_item_name_{$ps_id}" id="opener_item_name_{$ps_id}" href="{"uns_plan_of_mech_dep.analysis_of_pumps.pump?ps_id=`$ps_id`"|fn_url}" class="block cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black bold" {if $is_mark===false}{else}onclick="mark_item($(this));"{/if}>{$ps.ps_name}</a>
                            <div id="content_item_name_{$ps_id}" class="hidden" title="Анализ насоса <u>{$ps.ps_name}</u>"></div>
                        </td>

                        <td>
                            {if $prohibition.$ps_id == "Y"}<img src="skins/basic/admin/addons/uns_plans/images/prohibition.png" alt="X"/>{/if}
                        </td>

                        {*Склад Готовой Продукции*}
                        {assign var="q" value=$sgp.$ps_id|default:0}
                        <td class="b_l  center {if !$q}zero{/if}">{$q}</td>

                        {*План продаж*}
                        {assign var="q" value=$requirement.curr_month.$ps_id|default:0}
                        <td class="b_l center {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$requirement.next_month.$ps_id|default:0}
                        <td class="b1_l center {if !$q}zero{/if}">{$q}</td>

                        {*План производства*}
                        {assign var="q" value=$initial_production_plan.curr_month.$ps_id|default:0}
                        <td class="b3_l  center {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$initial_production_plan.next_month.$ps_id|default:0}
                        <td class="b1_l center {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$initial_production_plan.next2_month.$ps_id|default:0}
                        <td class="b1_l center {if !$q}zero{/if}">{$q}</td>

                        {*Плановая сдача партий насосов на СГП*}
                        {if $search.plan_parties == "Y"}
                        {assign var="q" value=$initial_production_plan_parties.curr_month.$ps_id|default:0}
                        <td class="b3_l  center {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$initial_production_plan_parties.next_month.$ps_id|default:0}
                        <td class="b1_l center {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$initial_production_plan_parties.next2_month.$ps_id|default:0}
                        <td class="b1_l center {if !$q}zero{/if}">{$q}</td>
                        {/if}

                        {*Задел*}
                        {assign var="q" value=$zadel.$ps_id|default:0}
                        <td style="background-color: #FFEF8C;" class="b3_l center {if !$q}zero{else}bold{/if}">{$q}</td>

                        {*Выполнено*}
                        {assign var="q" value=$done.$ps_id|default:0}
                        <td style="background-color: #C0FF9A;" class="b2_l center {if !$q}zero{else}bold{/if}">{$q}</td>

                        {*Осталось*}
                        {assign var="q" value=$remaining_production_plan.curr_month.$ps_id|default:0}
                        <td style="background-color: #B8C1FF;" class="b3_l center {if !$q}zero{else}bold{/if}">{$q}</td>
                        {assign var="q" value=$remaining_production_plan.next_month.$ps_id|default:0}
                        <td style="background-color: #B8C1FF;" class="b1_l center {if !$q}zero{else}bold{/if}">{$q}</td>
                        {assign var="q" value=$remaining_production_plan.next2_month.$ps_id|default:0}
                        <td style="background-color: #B8C1FF;" class="b1_l center {if !$q}zero{else}bold{/if}">{$q}</td>

                        {*Склад Готовой Продукции*}
                        {assign var="q" value=$sgp_current_day.$ps_id|default:0}
                        <td class="b3_l  center {if !$q}zero{else}bold{/if}">{$q}</td>

                        {*КРАТНОСТЬ ПАРТИИ НАСОСОВ*}
                        {if $search.plan_parties == "Y"}
                            <td class="b3_l center">
                                {*{$initial_production_plan_parties.curr_month.$ps_id|default:0}/{$initial_production_plan_parties.next_month.$ps_id|default:0}/{$initial_production_plan_parties.next2_month.$ps_id|default:0}*}
                                {$ps.party_size_min}-{$ps.party_size_max},{$ps.party_size_step}
                            </td>
                        {/if}
                    </tr>
                    {/foreach}
                </tbody>
                {/foreach}

                {*******************************************************************}
                {*ИТОГО*}
                <tbody>
                    <tr>
                        <td class="b3_t b3_b bold" colspan="2" style="text-align: right; font-size: 14px;">ИТОГО:</td>

                        {*Склад Готовой Продукции*}
                        {assign var="q" value=$sgp.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b3_b b_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*Потребность*}
                        {assign var="q" value=$requirement.curr_month.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b3_b b_l center bold {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$requirement.next_month.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b3_b b1_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*План производства*}
                        {assign var="q" value=$initial_production_plan.curr_month.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b3_b b3_l  center bold {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$initial_production_plan.next_month.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b3_b b1_l center bold {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$initial_production_plan.next2_month.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b3_b b1_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*Плановая сдача партий насосов на СГП*}
                        {if $search.plan_parties == "Y"}
                        {assign var="q" value=$initial_production_plan_parties.curr_month.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b3_b b3_l  center bold {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$initial_production_plan_parties.next_month.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b3_b b1_l center bold {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$initial_production_plan_parties.next2_month.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b3_b b1_l center bold {if !$q}zero{/if}">{$q}</td>
                        {/if}

                        {*Задел*}
                        {assign var="q" value=$zadel.total|default:0}
                        <td style="font-size: 14px; background-color: #FFEF8C;" class="b3_t b3_b b3_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*Выполнено*}
                        {assign var="q" value=$done.total|default:0}
                        <td style="font-size: 14px; background-color: #C0FF9A;" class="b3_t b3_b b2_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*Осталось*}
                        {assign var="q" value=$remaining_production_plan.curr_month.total|default:0}
                        <td style="font-size: 14px; background-color: #B8C1FF;" class="b3_t b3_b b3_l center bold {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$remaining_production_plan.next_month.total|default:0}
                        <td style="font-size: 14px; background-color: #B8C1FF;" class="b3_t b3_b b1_l center bold {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$remaining_production_plan.next2_month.total|default:0}
                        <td style="font-size: 14px; background-color: #B8C1FF;" class="b3_t b3_b b1_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*Склад Готовой Продукции*}
                        {assign var="q" value=$sgp_current_day.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b3_b b3_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*Кратность партии*}
                        {if $search.plan_parties == "Y"}
                            <td class="b3_t b3_b b3_l center">&nbsp;</td>
                        {/if}

                    </tr>
                </tbody>
                <tbody>
                    <tr style="background-color: #D4D0C8;">
                        <th rowspan="3" style="text-transform: none;" class="center">Наименование</th>
                        <th rowspan="3" style="text-transform: none;" class="center">&nbsp;</th>
                        <th rowspan="3" style="text-transform: none;" class="center b_l">СГП<br>на<br>01/{$search.month|string_format:"%02d"}<hr class="roman_dates">{$search.year}</th>
                        <th style="" class="b_l center">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="" class="b1_l center">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        <th style="" class="b3_l center">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="" class="b1_l center">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        <th style="" class="b1_l center">{$tpl_next2_month_roman.month}<hr class="roman_dates">{$tpl_next2_month_roman.year}</th>

                        {*Плановая сдача партий насосов на СГП*}
                        {if $search.plan_parties == "Y"}
                        <th style="" class="b3_l center">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="" class="b1_l center">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        <th style="" class="b1_l center">{$tpl_next2_month_roman.month}<hr class="roman_dates">{$tpl_next2_month_roman.year}</th>
                        {/if}

                        <th style="text-transform: none; background-color: #FFEF8C;" rowspan="2" class="b3_l center">Задел<br>ожид.<br>сборку</th>
                        <th style="text-transform: none; background-color: #C0FF9A;" rowspan="2" class="b2_l center">Выпол-<br>нено</th>
                        <th style="background-color: #B8C1FF;" class="b3_l center">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="background-color: #B8C1FF;" class="b1_l center">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        <th style="background-color: #B8C1FF;" class="b1_l center">{$tpl_next2_month_roman.month}<hr class="roman_dates">{$tpl_next2_month_roman.year}</th>
                        <th rowspan="3" style="text-transform: none;" class="b3_l center">СГП<br>на<br>{$search.current_day|fn_parse_date|date_format:"%d/%m"}<hr class="roman_dates">{$search.year}</th>
                        {*Кратность партии*}
                        {if $search.plan_parties == "Y"}
                        <th style="text-transform: none;"               rowspan="3"             class="center b3_l">{include file="common_templates/tooltip.tpl" tooltip="<b>Кратность партии насоса.</b><br>ОТ - ДО, ШАГ" tooltip_mark="<b>КП</b>"}</th>
                        {/if}
                    </tr>
                    <tr style="background-color: #D4D0C8;">
                        <th rowspan="2" colspan="2" style="text-transform: none;" class="center b_l b1_b b1_t">План<br>продаж</th>
                        <th rowspan="2" colspan="3" style="text-transform: none;" class="center b3_l b1_t">План<br>производства<br>насосов</th>
                        {*Плановая сдача партий насосов на СГП*}
                        {if $search.plan_parties == "Y"}
                        <th rowspan="2" colspan="3" style="text-transform: none;" class="center b3_l b1_t">Плановая сдача<br>партий насосов<br>на СГП</th>
                        {/if}

                        <th colspan="3" style="background-color:#B8C1FF; text-transform: uppercase;" class="center b3_l b1_t">Осталось</th>
                    </tr>
                    <tr style="background-color: #D4D0C8;">
                        <th style="background-color:#B8C1FF; text-transform: uppercase;" colspan="5" class="center b3_l b1_t">Выполнение плана на {$search.current_day|fn_parse_date|date_format:"%d/%m/%y"}</th>
                    </tr>
                </tbody>
            </table>
            &nbsp;&nbsp;&nbsp;<span style="color: red;"><img src="skins/basic/admin/addons/uns_plans/images/prohibition.png" alt="X"/> - к производству запрещены <b>{$prohibition|count}</b> видов насосов, так как по ним уже есть {$search.months_supply}-х месячный запас для продаж.
            <br>&nbsp;&nbsp;&nbsp;Ограничение накладывается если: (СГП на тек. день + ЗАДЕЛ) &ge; ПЛАНА ПРОДАЖ на {$search.months_supply} мес. вперед.
            <br>&nbsp;&nbsp;&nbsp;Ограничение автоматически снимается, как только со Склада готовой продукции будет продано значительное количество насосов.</span>
        {else}
            <h3>Укажите месяц и год для отображения плана производства</h3>
        {/if}
    {/capture}
    {include file="common_templates/mainbox.tpl" title="План производства насосов на `$months_full[$search.month]` `$search.year` г. (`$search.current_day`)" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
{/strip}