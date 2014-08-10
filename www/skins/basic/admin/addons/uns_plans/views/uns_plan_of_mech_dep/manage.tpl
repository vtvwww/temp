{strip}
    {script src="js/tabs.js"}
    {literal}
        <style>
            .zero, a.zero{
                color: #d3d3d3;
            }

            td.bar_available,
            td.bar_available:hover{
                background-color:#ddd !important;
                border: 1px solid #999 !important;
                height:5px;
                padding: 0 !important;
                /*border-top-right-radius: 2px;*/
                /*border-bottom-right-radius: 2px;*/
            }

            td.bar_zadel,
            td.bar_zadel:hover{
                background-color: #999 !important;
                border-bottom: 3px solid #fff !important;
                border-right: 2px solid #999 !important;
                border-left: 2px solid #999 !important;
                border-top: 3px solid #fff !important;
                padding: 0 !important;
                height:3px;
            }

            td.bar_space,
            td.bar_space:hover,
            td.bar_none,
            td.bar_none:hover{
                background-color:#fff !important;
                padding: 0 !important;
                height:9px;
            }
        </style>
    {/literal}
    {capture name="mainbox"}
        {if $search.analisys_of_production_plan == "Y"}
            {assign var="analysis_of_plan" value=true}
        {/if}

        {if $search.analisis_of_sales == "Y"}
            {assign var="analisis_of_sales" value=true}
        {/if}

        {include file="addons/uns_plans/views/uns_plan_of_mech_dep/components/search_form_manage.tpl" dispatch="`$controller`.$mode" search_content=$smarty.capture.search_content but_text="ВЫПОЛНИТЬ РАСЧЕТ"}
        {if is__array($pump_series)}
            {* ПЛАН ПРОИЗВОДСТВА ЛИТЕЙНОГО ЦЕХА *}
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="action-add">
               <a target="_blank" href="{"uns_plan_of_mech_dep.planning.LC"|fn_url}"><b>План производства Лит. цеха</b></a>
            </span>

{*            *}{* АНАЛИЗ РАЗРЕШЕННЫХ НАСОСОВ *}{*
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="action-add">
               <a target="_blank" href="{"uns_plan_of_mech_dep.analysis_of_pumps.allowance"|fn_url}">Анализ <b>РАЗРЕШЕННЫХ</b> насосов</a>
            </span>

            *}{* АНАЛИЗ ЗАПРЕЩЕННЫХ НАСОСОВ *}{*
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span class="action-add">
               <a target="_blank" href="{"uns_plan_of_mech_dep.analysis_of_pumps.prohibition"|fn_url}">Анализ <b>ЗАПРЕЩЕННЫХ</b> насосов</a>
            </span>*}

            <table cellpadding="0" cellspacing="0" border="0" class="table" style="margin: 10px; 0">
                <thead>
                    <tr style="background-color: #D4D0C8;">
                        <th style="text-transform: none;"               rowspan="3"             class="center">Наименование</th>
                        <th style="text-transform: none;"               rowspan="3"             class="center">&nbsp;</th>
                        <th style="text-transform: none;"               rowspan="3"             class="center b_l">СГП<br>на<br>00:00<br>01/{$search.month|string_format:"%02d"}<hr class="roman_dates">{$search.year}</th>
                        <th style="text-transform: none;"               rowspan="2" colspan="{if $analysis_of_plan}3{else}2{/if}" class="center b_l b1_b">План<br>продаж</th>
                        {if $search.type_of_production_plan == "actual"}
                            <th style="text-transform: none;"          rowspan="2" colspan="3" class="center b3_l b1_b">План<br>производства<br>ФАКТИЧЕСКИЙ</th>
                        {elseif $search.type_of_production_plan == "parties"}
                            <th style="text-transform: none;"          rowspan="2" colspan="3" class="center b3_l b1_b">План<br>производства<br>ПО ПАРТИЯМ</th>
                        {/if}

                        <th style="text-transform: uppercase;background-color:#B8C1FF; "                      colspan="5" class="center b3_l">Выполнение плана на 23:59 {$search.current_day|fn_parse_date|date_format:"%d/%m/%y"}</th>
                        <th style="text-transform: none;"               rowspan="3"             class="center b3_l">СГП<br>на<br>23:59<br>{$search.current_day|fn_parse_date|date_format:"%d/%m"}<hr class="roman_dates">{$search.year}</th>

                        {*Кратность партии*}
                        {if $analysis_of_plan}
                        <th style="text-transform: none;"               rowspan="3"             class="center b3_l">{include file="common_templates/tooltip.tpl" tooltip="<b>Кратность партии насоса.</b><br>ОТ - ДО, ШАГ" tooltip_mark="<b>КП</b>"}</th>
                        {/if}
                    </tr>
                    <tr style="background-color: #D4D0C8;">
                        <th style="text-transform: none; width: 50px; background-color: #FFEF8C;"  rowspan="3"             class="center b3_l b_t">Задел<br>ожид.<br>сборку</th>
                        <th style="text-transform: none; width: 50px; background-color: #C0FF9A;"  rowspan="3"             class="center b2_l b_t">Выпол-<br>нено</th>
                        <th style="text-transform: uppercase; background-color:#B8C1FF;"                      colspan="3" class="center b_l b1_b">Осталось</th>
                    </tr>
                    <tr style="background-color: #D4D0C8;">
                        <th style="width: 40px;" class="center b_l">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="width: 40px;" class="center b1_l">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        {if $analysis_of_plan}
                        <th style="width: 40px;" class="center b1_l">{$tpl_next2_month_roman.month}<hr class="roman_dates">{$tpl_next2_month_roman.year}</th>
                        {/if}

                        <th style="width: 40px;" class="center b3_l">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="width: 40px;" class="center b1_l">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        <th style="width: 40px;" class="center b1_l">{$tpl_next2_month_roman.month}<hr class="roman_dates">{$tpl_next2_month_roman.year}</th>

                        <th style="width: 40px;background-color: #B8C1FF;" class="center b_l">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="width: 40px;background-color: #B8C1FF;" class="center b1_l">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        <th style="width: 40px;background-color: #B8C1FF;" class="center b1_l">{$tpl_next2_month_roman.month}<hr class="roman_dates">{$tpl_next2_month_roman.year}</th>
                    </tr>
                </thead>
                {foreach from=$pump_series item="pt" name="pt"}
                <tbody>
                <tr>
                    <td colspan="20" style="background-color: #E6E2DA; font-size: 12px;" class="bold">{$pt.pt_name}</td>
                </tr>
                    {foreach from=$pt.pump_series item="ps" key="ps_id" name="ps"}
                    {*АНАЛИЗ ПЛАНА ПРОИЗВОДСТВА*}
                    {if $analisis_of_sales}
                        {assign var="sale_progressbar" value=" style='background-image: url(images/uns/bar-50px.png); background-position: `$sales_tpl.$ps_id.bar`px center;' "}
                        {assign var="sale_value" value=$sales.$ps_id|default:0}
                        {assign var="sale_tpl" value="<span style='font-size:9px; font-weight:normal;'>`$sale_value`/</span> "}
                    {/if}

                    {if $analysis_of_plan}
                        {assign var="analisys_rowspan" value=" rowspan='2' "}
                        {assign var="b_offset"    value="<td class='bar_space'        style='width:`$analisys.$ps_id.offset`%;'></td>"}
                        {if $analisys.$ps_id.total>0}
                        {assign var="b_available" value="<td class='bar_available'    style='width:`$analisys.$ps_id.total`%;'></td>"}
                        {else}
                        {assign var="b_available"     value=""}
                        {/if}

                        {if $analisys.$ps_id.zadel>0}
                        {assign var="b_zadel"     value="<td class='bar_zadel'        style='width:`$analisys.$ps_id.zadel`%;'></td>"}
                        {else}
                        {assign var="b_zadel"     value=""}
                        {/if}

                        {assign var="b_none"      value="<td class='bar_none'         style='width:`$analisys.$ps_id.none`%;'></td>"}


                        {assign var="analisys_progress" value="<table style='margin:1px 0 1px 0;' cellpadding='0' cellspacing='0' border='0' width='100%'><thead><tr>`$b_offset``$b_available``$b_zadel``$b_none`</tr></thead></table>"}
                        {assign var="analisys_add_rows" value="<tr><td valign='bottom' style='border-top:none;border-bottom:none;padding: 0;' class='b_l' colspan='3'>`$analisys_progress`</td></tr>"}
                    {/if}

                    <tr>
                        {*Наименование*}
                        <td {$analisys_rowspan} class="b2_t ">
                            <a  rev="content_item_name_{$ps_id}" id="opener_item_name_{$ps_id}" href="{"uns_plan_of_mech_dep.analysis_of_pumps.pump?ps_id=`$ps_id`"|fn_url}" class="block cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black" {if $is_mark===false}{else}onclick="mark_item($(this));"{/if}>{$ps.ps_name}</a>
                            <div id="content_item_name_{$ps_id}" class="hidden" title="Анализ насоса <u>{$ps.ps_name}</u> на 23:59 {$search.current_day}"></div>
                        </td>

                        <td {$analisys_rowspan} align="right" class="b2_t ">
                            {if $prohibition.$ps_id == "Y"}<img src="skins/basic/admin/addons/uns_plans/images/prohibition.png" alt="X"/>{else}&nbsp;{/if}
                        </td>

                        {*Склад Готовой Продукции*}
                        {assign var="q" value=$sgp.$ps_id|default:0}
                        <td {$analisys_rowspan} class="b_l b2_t center {if !$q}zero{/if}">{if $q>=0}{$q}{else}<span class="info_warning_block">{$q}</span>{/if}</td>

                        {*План продаж*}
                        {assign var="q" value=$requirement.curr_month.$ps_id|default:0}
                        <td {$sale_progressbar} class="b_l b2_t center {if $analysis_of_plan || $analisis_of_sales} bold {/if}{if !$q and !$sale_value}zero{/if}">{$sale_tpl}{$q}</td>
                        {assign var="q" value=$requirement.next_month.$ps_id|default:0}
                        <td class="b1_l b2_t center {if $analysis_of_plan || $analisis_of_sales} bold {/if} {if !$q}zero{/if}">{$q}</td>
                        {if $analysis_of_plan}
                        {assign var="q" value=$requirement.next2_month.$ps_id|default:0}
                        <td class="b1_l b2_t center bold {if !$q}zero{/if}">{$q}</td>
                        {/if}

                        {if $search.type_of_production_plan == "actual"}
                            {*План производства*}
                            {assign var="q" value=$initial_production_plan.curr_month.$ps_id|default:0}
                            <td {$analisys_rowspan} class="b3_l b2_t center {if !$q}zero{/if}">{$q}</td>
                            {assign var="q" value=$initial_production_plan.next_month.$ps_id|default:0}
                            <td {$analisys_rowspan} class="b1_l b2_t center {if !$q}zero{/if}">{$q}</td>
                            {assign var="q" value=$initial_production_plan.next2_month.$ps_id|default:0}
                            <td {$analisys_rowspan} class="b1_l b2_t center {if !$q}zero{/if}">{$q}</td>

                        {elseif $search.type_of_production_plan == "parties"}
                            {*Плановая сдача партий насосов на СГП*}
                            {assign var="q" value=$initial_production_plan_parties.curr_month.$ps_id|default:0}
                            <td {$analisys_rowspan} class="b3_l b2_t center {if !$q}zero{/if}">{$q}</td>
                            {assign var="q" value=$initial_production_plan_parties.next_month.$ps_id|default:0}
                            <td {$analisys_rowspan} class="b1_l b2_t center {if !$q}zero{/if}">{$q}</td>
                            {assign var="q" value=$initial_production_plan_parties.next2_month.$ps_id|default:0}
                            <td {$analisys_rowspan} class="b1_l b2_t center {if !$q}zero{/if}">{$q}</td>
                        {/if}

                        {*ЗАДЕЛ*}
                        {assign var="q" value=$zadel_current_day.$ps_id|default:0}
                        <td {$analisys_rowspan} style="background-color: #FFEF8C;" class="b3_l b2_t center {if !$q}zero{else}bold{/if}">{$q}</td>

                        {*ВЫПОЛНЕНО*}
                        {assign var="q" value=$done_current_day.$ps_id|default:0}
                        <td {$analisys_rowspan} style="background-color: #C0FF9A;" class="b2_l b2_t center {if !$q}zero{else}bold{/if}">{$q}</td>

                        {*ОСТАЛОСЬ*}
                        {if $search.type_of_production_plan == "actual"}
                            {assign var="q" value=$remaining_production_plan_current_day.curr_month.$ps_id|default:0}
                            <td {$analisys_rowspan} style="background-color: #B8C1FF;" class="b_l b2_t center {if !$q}zero{else}bold{/if}">{$q}</td>
                            {assign var="q" value=$remaining_production_plan_current_day.next_month.$ps_id|default:0}
                            <td {$analisys_rowspan} style="background-color: #B8C1FF;" class="b1_l b2_t center {if !$q}zero{else}bold{/if}">{$q}</td>
                            {assign var="q" value=$remaining_production_plan_current_day.next2_month.$ps_id|default:0}
                            <td {$analisys_rowspan} style="background-color: #B8C1FF;" class="b1_l b2_t center {if !$q}zero{else}bold{/if}">{$q}</td>

                        {elseif $search.type_of_production_plan == "parties"}
                            {assign var="q" value=$remaining_production_plan_parties_current_day.curr_month.$ps_id|default:0}
                            <td {$analisys_rowspan} style="background-color: #B8C1FF;" class="b_l b2_t center {if !$q}zero{else}bold{/if}">{$q}</td>
                            {assign var="q" value=$remaining_production_plan_parties_current_day.next_month.$ps_id|default:0}
                            <td {$analisys_rowspan} style="background-color: #B8C1FF;" class="b1_l b2_t center {if !$q}zero{else}bold{/if}">{$q}</td>
                            {assign var="q" value=$remaining_production_plan_parties_current_day.next2_month.$ps_id|default:0}
                            <td {$analisys_rowspan} style="background-color: #B8C1FF;" class="b1_l b2_t center {if !$q}zero{else}bold{/if}">{$q}</td>
                        {/if}

                        {*Склад Готовой Продукции*}
                        {assign var="q" value=$sgp_current_day.$ps_id|default:0}
                        <td {$analisys_rowspan} class="b3_l b2_t center {if !$q}zero{else}bold{/if}">{if $q>=0}{$q}{else}<span class="info_warning_block">{$q}</span>{/if}</td>

                        {*КРАТНОСТЬ ПАРТИИ НАСОСОВ*}
                        {if $analysis_of_plan}
                            <td {$analisys_rowspan} class="b3_l b2_t center">
                                {$ps.party_size_min}-{$ps.party_size_max},{$ps.party_size_step}
                            </td>
                        {/if}
                    </tr>
                    {$analisys_add_rows}
                    {/foreach}
                </tbody>
                {/foreach}

                {*******************************************************************}
                {*ИТОГО*}
                {*******************************************************************}
                <tbody>
                    {*КОЛИЧЕСТВО*}
                    <tr>
                        <td class="b3_t bold" colspan="1" rowspan="2" style="text-align: right; font-size: 14px;">ИТОГО:</td>
                        <td class="b3_t b1_l bold center" style="font-size: 14px;">шт</td>

                        {*СКЛАД ГОТОВОЙ ПРОДУКЦИИ*}
                        {assign var="q" value=$sgp.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*ПОТРЕБНОСТЬ*}
                        {assign var="q" value=$requirement.curr_month.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b_l center bold {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$requirement.next_month.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b1_l center bold {if !$q}zero{/if}">{$q}</td>
                        {if $analysis_of_plan}
                        {assign var="q" value=$requirement.next2_month.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b1_l center bold {if !$q}zero{/if}">{$q}</td>
                        {/if}

                        {*ПЛАН ПРОИЗВОДСТВА*}
                        {if $search.type_of_production_plan == "actual"}
                            {assign var="q" value=$initial_production_plan.curr_month.total|default:0}
                            <td style="font-size: 14px;" class="b3_t b3_l  center bold {if !$q}zero{/if}">{$q}</td>
                            {assign var="q" value=$initial_production_plan.next_month.total|default:0}
                            <td style="font-size: 14px;" class="b3_t b1_l center bold {if !$q}zero{/if}">{$q}</td>
                            {assign var="q" value=$initial_production_plan.next2_month.total|default:0}
                            <td style="font-size: 14px;" class="b3_t b1_l center bold {if !$q}zero{/if}">{$q}</td>

                        {elseif $search.type_of_production_plan == "parties"}
                            {assign var="q" value=$initial_production_plan_parties.curr_month.total|default:0}
                            <td style="font-size: 14px;" class="b3_t b3_l  center bold {if !$q}zero{/if}">{$q}</td>
                            {assign var="q" value=$initial_production_plan_parties.next_month.total|default:0}
                            <td style="font-size: 14px;" class="b3_t b1_l center bold {if !$q}zero{/if}">{$q}</td>
                            {assign var="q" value=$initial_production_plan_parties.next2_month.total|default:0}
                            <td style="font-size: 14px;" class="b3_t b1_l center bold {if !$q}zero{/if}">{$q}</td>

                        {/if}

                        {*ЗАДЕЛ*}
                        {assign var="q" value=$zadel_current_day.total|default:0}
                        <td style="font-size: 14px; background-color: #FFEF8C;" class="b3_t b3_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*ВЫПОЛНЕНО*}
                        {assign var="q" value=$done_current_day.total|default:0}
                        <td style="font-size: 14px; background-color: #C0FF9A;" class="b3_t b2_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*ОСТАЛОСЬ*}
                        {if $search.type_of_production_plan == "actual"}
                            {assign var="q" value=$remaining_production_plan_current_day.curr_month.total|default:0}
                            <td style="font-size: 14px; background-color: #B8C1FF;" class="b3_t b_l center bold {if !$q}zero{/if}">{$q}</td>
                            {assign var="q" value=$remaining_production_plan_current_day.next_month.total|default:0}
                            <td style="font-size: 14px; background-color: #B8C1FF;" class="b3_t b1_l center bold {if !$q}zero{/if}">{$q}</td>
                            {assign var="q" value=$remaining_production_plan_current_day.next2_month.total|default:0}
                            <td style="font-size: 14px; background-color: #B8C1FF;" class="b3_t b1_l center bold {if !$q}zero{/if}">{$q}</td>

                        {elseif $search.type_of_production_plan == "parties"}
                            {assign var="q" value=$remaining_production_plan_parties_current_day.curr_month.total|default:0}
                            <td style="font-size: 14px; background-color: #B8C1FF;" class="b3_t b_l center bold {if !$q}zero{/if}">{$q}</td>
                            {assign var="q" value=$remaining_production_plan_parties_current_day.next_month.total|default:0}
                            <td style="font-size: 14px; background-color: #B8C1FF;" class="b3_t b1_l center bold {if !$q}zero{/if}">{$q}</td>
                            {assign var="q" value=$remaining_production_plan_parties_current_day.next2_month.total|default:0}
                            <td style="font-size: 14px; background-color: #B8C1FF;" class="b3_t b1_l center bold {if !$q}zero{/if}">{$q}</td>

                        {/if}

                        {*СКЛАД ГОТОВОЙ ПРОДУКЦИИ*}
                        {assign var="q" value=$sgp_current_day.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b3_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*КРАТНОСТЬ ПАРТИИ*}
                        {if $analysis_of_plan}
                            <td class="b3_t b3_l center b3_b" rowspan="3">&nbsp;</td>
                        {/if}
                    </tr>

                    {*ВЕС*}
                    <tr>
                        <td class="b_t b1_l bold center" style="font-size: 14px;">т</td>

                        {*СКЛАД ГОТОВОЙ ПРОДУКЦИИ*}
                        {assign var="q" value=$weights.sgp|array_sum}
                        <td style="font-size: 12px;" class="b_t b_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>

                        {*ПОТРЕБНОСТЬ*}
                        {assign var="q" value=$weights.requirement.curr_month|array_sum}
                        <td style="font-size: 12px;" class="b_t b_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                        {assign var="q" value=$weights.requirement.next_month|array_sum}
                        <td style="font-size: 12px;" class="b_t b1_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                        {if $analysis_of_plan}
                        {assign var="q" value=$weights.requirement.next2_month|array_sum}
                        <td style="font-size: 12px;" class="b_t b1_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                        {/if}

                        {*ПЛАН ПРОИЗВОДСТВА*}
                        {if $search.type_of_production_plan == "actual"}
                            {assign var="q" value=$weights.initial_production_plan.curr_month|array_sum}
                            <td style="font-size: 12px;" class="b_t b3_l  center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                            {assign var="q" value=$weights.initial_production_plan.next_month|array_sum}
                            <td style="font-size: 12px;" class="b_t b1_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                            {assign var="q" value=$weights.initial_production_plan.next2_month|array_sum}
                            <td style="font-size: 12px;" class="b_t b1_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>

                        {elseif $search.type_of_production_plan == "parties"}
                            {assign var="q" value=$weights.initial_production_plan_parties.curr_month|array_sum}
                            <td style="font-size: 12px;" class="b_t b3_l  center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                            {assign var="q" value=$weights.initial_production_plan_parties.next_month|array_sum}
                            <td style="font-size: 12px;" class="b_t b1_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                            {assign var="q" value=$weights.initial_production_plan_parties.next2_month|array_sum}
                            <td style="font-size: 12px;" class="b_t b1_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>

                        {/if}

                        {*ЗАДЕЛ*}
                        {assign var="q" value=$weights.zadel|array_sum}
                        <td style="font-size: 12px; background-color: #FFEF8C;" class="b_t b3_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>

                        {*ВЫПОЛНЕНО*}
                        {assign var="q" value=$weights.done|array_sum}
                        <td style="font-size: 12px; background-color: #C0FF9A;" class="b_t b2_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>

                        {*ОСТАЛОСЬ*}
                        {if $search.type_of_production_plan == "actual"}
                            {assign var="q" value=$weights.remaining_production_plan_current_day.curr_month|array_sum}
                            <td style="font-size: 12px; background-color: #B8C1FF;" class="b_t b_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                            {assign var="q" value=$weights.remaining_production_plan_current_day.next_month|array_sum}
                            <td style="font-size: 12px; background-color: #B8C1FF;" class="b_t b1_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                            {assign var="q" value=$weights.remaining_production_plan_current_day.next2_month|array_sum}
                            <td style="font-size: 12px; background-color: #B8C1FF;" class="b_t b1_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>

                        {elseif $search.type_of_production_plan == "parties"}
                            {assign var="q" value=$weights.remaining_production_plan_parties_current_day.curr_month|array_sum}
                            <td style="font-size: 12px; background-color: #B8C1FF;" class="b_t b_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                            {assign var="q" value=$weights.remaining_production_plan_parties_current_day.next_month|array_sum}
                            <td style="font-size: 12px; background-color: #B8C1FF;" class="b_t b1_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                            {assign var="q" value=$weights.remaining_production_plan_parties_current_day.next2_month|array_sum}
                            <td style="font-size: 12px; background-color: #B8C1FF;" class="b_t b1_l center  {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                        {/if}

                        {*СКЛАД ГОТОВОЙ ПРОДУКЦИИ*}
                        {assign var="q" value=$weights.sgp_current_day|array_sum}
                        <td style="font-size: 12px;" class="b_t b3_l center {if !$q}zero{/if}">{$q/1000|fn_fvalue:1}</td>
                    </tr>

                    {* Средний вес*}
                    <tr>
                        <td class="b_t b3_b bold" colspan="1" rowspan="2" style="text-align: right; font-size: 14px;">Средний вес:</td>
                        <td class="b_t b1_l b3_b bold center" style="font-size: 14px;">кг</td>

                        {*СКЛАД ГОТОВОЙ ПРОДУКЦИИ*}
                        {assign var="q" value=$weights.sgp|array_sum}
                        <td style="font-size: 12px;" class="b_t b3_b b_l center  {if !$q}zero{/if}">{$q/$sgp.total|fn_fvalue:0}</td>

                        {*ПОТРЕБНОСТЬ*}
                        {assign var="q" value=$weights.requirement.curr_month|array_sum}
                        <td style="font-size: 12px;" class="b_t b3_b b_l center  {if !$q}zero{/if}">{$q/$requirement.curr_month.total|fn_fvalue:0}</td>
                        {assign var="q" value=$weights.requirement.next_month|array_sum}
                        <td style="font-size: 12px;" class="b_t b3_b b1_l center  {if !$q}zero{/if}">{$q/$requirement.next_month.total|fn_fvalue:0}</td>
                        {if $analysis_of_plan}
                        {assign var="q" value=$weights.requirement.next2_month|array_sum}
                        <td style="font-size: 12px;" class="b_t b3_b b1_l center  {if !$q}zero{/if}">{$q/$requirement.next2_month.total|fn_fvalue:0}</td>
                        {/if}

                        {*ПЛАН ПРОИЗВОДСТВА*}
                        {if $search.type_of_production_plan == "actual"}
                            {assign var="q" value=$weights.initial_production_plan.curr_month|array_sum}
                            <td style="font-size: 12px;" class="b_t b3_b b3_l  center  {if !$q}zero{/if}">{$q/$initial_production_plan.curr_month.total|fn_fvalue:0}</td>
                            {assign var="q" value=$weights.initial_production_plan.next_month|array_sum}
                            <td style="font-size: 12px;" class="b_t b3_b b1_l center  {if !$q}zero{/if}">{$q/$initial_production_plan.next_month.total|fn_fvalue:0}</td>
                            {assign var="q" value=$weights.initial_production_plan.next2_month|array_sum}
                            <td style="font-size: 12px;" class="b_t b3_b b1_l center  {if !$q}zero{/if}">{$q/$initial_production_plan.next2_month.total|fn_fvalue:0}</td>

                        {elseif $search.type_of_production_plan == "parties"}
                            {assign var="q" value=$weights.initial_production_plan_parties.curr_month|array_sum}
                            <td style="font-size: 12px;" class="b_t b3_b b3_l  center  {if !$q}zero{/if}">{$q/$initial_production_plan_parties.curr_month.total|fn_fvalue:0}</td>
                            {assign var="q" value=$weights.initial_production_plan_parties.next_month|array_sum}
                            <td style="font-size: 12px;" class="b_t b3_b b1_l center   {if !$q}zero{/if}">{$q/$initial_production_plan_parties.next_month.total|fn_fvalue:0}</td>
                            {assign var="q" value=$weights.initial_production_plan_parties.next2_month|array_sum}
                            <td style="font-size: 12px;" class="b_t b3_b b1_l center  {if !$q}zero{/if}">{$q/$initial_production_plan_parties.next2_month.total|fn_fvalue:0}</td>

                        {/if}

                        {*ЗАДЕЛ*}
                        {assign var="q" value=$weights.zadel|array_sum}
                        <td style="font-size: 12px; background-color: #FFEF8C;" class="b_t b3_b b3_l center  {if !$q}zero{/if}">{$q/$zadel_current_day.total|fn_fvalue:0}</td>

                        {*ВЫПОЛНЕНО*}
                        {assign var="q" value=$weights.done|array_sum}
                        <td style="font-size: 12px; background-color: #C0FF9A;" class="b_t b3_b b2_l center  {if !$q}zero{/if}">{$q/$done_current_day.total|fn_fvalue:0}</td>

                        {*ОСТАЛОСЬ*}
                        {if $search.type_of_production_plan == "actual"}
                            {assign var="q" value=$weights.remaining_production_plan_current_day.curr_month|array_sum}
                            <td style="font-size: 12px; background-color: #B8C1FF;" class="b_t b3_b b_l center  {if !$q}zero{/if}">{$q/$remaining_production_plan_current_day.curr_month.total|fn_fvalue:0}</td>
                            {assign var="q" value=$weights.remaining_production_plan_current_day.next_month|array_sum}
                            <td style="font-size: 12px; background-color: #B8C1FF;" class="b_t b3_b b1_l center  {if !$q}zero{/if}">{$q/$remaining_production_plan_current_day.next_month.total|fn_fvalue:0}</td>
                            {assign var="q" value=$weights.remaining_production_plan_current_day.next2_month|array_sum}
                            <td style="font-size: 12px; background-color: #B8C1FF;" class="b_t b3_b b1_l center  {if !$q}zero{/if}">{$q/$remaining_production_plan_current_day.next2_month.total|fn_fvalue:0}</td>

                        {elseif $search.type_of_production_plan == "parties"}
                            {assign var="q" value=$weights.remaining_production_plan_parties_current_day.curr_month|array_sum}
                            <td style="font-size: 12px; background-color: #B8C1FF;" class="b_t b3_b b_l center  {if !$q}zero{/if}">{$q/$remaining_production_plan_parties_current_day.curr_month.total|fn_fvalue:0}</td>
                            {assign var="q" value=$weights.remaining_production_plan_parties_current_day.next_month|array_sum}
                            <td style="font-size: 12px; background-color: #B8C1FF;" class="b_t b3_b b1_l center  {if !$q}zero{/if}">{$q/$remaining_production_plan_parties_current_day.next_month.total|fn_fvalue:0}</td>
                            {assign var="q" value=$weights.remaining_production_plan_parties_current_day.next2_month|array_sum}
                            <td style="font-size: 12px; background-color: #B8C1FF;" class="b_t b3_b b1_l center  {if !$q}zero{/if}">{$q/$remaining_production_plan_parties_current_day.next2_month.total|fn_fvalue:0}</td>
                        {/if}

                        {*СКЛАД ГОТОВОЙ ПРОДУКЦИИ*}
                        {assign var="q" value=$weights.sgp_current_day|array_sum}
                        <td style="font-size: 12px;" class="b_t b3_b b3_l center {if !$q}zero{/if}">{$q/$sgp_current_day.total|fn_fvalue:0}</td>
                    </tr>
                </tbody>
                <tbody>
                    <tr style="background-color: #D4D0C8;">
                        <th rowspan="3" style="text-transform: none;" class="center">Наименование</th>
                        <th rowspan="3" style="text-transform: none;" class="center">&nbsp;</th>
                        <th rowspan="3" style="text-transform: none;" class="center b_l">СГП<br>на<br>00:00<br>01/{$search.month|string_format:"%02d"}<hr class="roman_dates">{$search.year}</th>
                        <th style="" class="b_l center">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="" class="b1_l center">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        {if $analysis_of_plan}
                        <th style="" class="b1_l center">{$tpl_next2_month_roman.month}<hr class="roman_dates">{$tpl_next2_month_roman.year}</th>
                        {/if}

                        <th style="" class="b3_l center">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="" class="b1_l center">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        <th style="" class="b1_l center">{$tpl_next2_month_roman.month}<hr class="roman_dates">{$tpl_next2_month_roman.year}</th>

                        <th style="text-transform: none; background-color: #FFEF8C;" rowspan="2" class="b3_l b_b center">Задел<br>ожид.<br>сборку</th>
                        <th style="text-transform: none; background-color: #C0FF9A;" rowspan="2" class="b2_l b_b center">Выпол-<br>нено</th>
                        <th style="background-color: #B8C1FF;" class="b_l center">{$tpl_curr_month_roman.month}<hr class="roman_dates">{$tpl_curr_month_roman.year}</th>
                        <th style="background-color: #B8C1FF;" class="b1_l center">{$tpl_next_month_roman.month}<hr class="roman_dates">{$tpl_next_month_roman.year}</th>
                        <th style="background-color: #B8C1FF;" class="b1_l center">{$tpl_next2_month_roman.month}<hr class="roman_dates">{$tpl_next2_month_roman.year}</th>
                        <th rowspan="3" style="text-transform: none;" class="b3_l center">СГП<br>на<br>23:59<br>{$search.current_day|fn_parse_date|date_format:"%d/%m"}<hr class="roman_dates">{$search.year}</th>
                        {*Кратность партии*}
                        {if $analysis_of_plan}
                        <th style="text-transform: none;"               rowspan="3"             class="center b3_l">{include file="common_templates/tooltip.tpl" tooltip="<b>Кратность партии насоса.</b><br>ОТ - ДО, ШАГ" tooltip_mark="<b>КП</b>"}</th>
                        {/if}
                    </tr>
                    <tr style="background-color: #D4D0C8;">
                        <th rowspan="2" colspan="{if $analysis_of_plan}3{else}2{/if}" style="text-transform: none;" class="center b_l b1_b b1_t">План<br>продаж</th>

                        {if $search.type_of_production_plan == "actual"}
                            <th rowspan="2" colspan="3" style="text-transform: none;" class="center b3_l b1_t">План<br>производства<br>ФАКТИЧЕСКИЙ</th>
                        {elseif $search.type_of_production_plan == "parties"}
                            <th rowspan="2" colspan="3" style="text-transform: none;" class="center b3_l b1_t">План<br>производства<br>ПО ПАРТИЯМ</th>
                        {/if}

                        <th colspan="3" style="background-color:#B8C1FF; text-transform: uppercase;" class="center b_l b1_t">Осталось</th>
                    </tr>
                    <tr style="background-color: #D4D0C8;">
                        <th style="background-color:#B8C1FF; text-transform: uppercase;" colspan="5" class="center b3_l">Выполнение плана на 23:59 {$search.current_day|fn_parse_date|date_format:"%d/%m/%y"}</th>
                    </tr>
                </tbody>
            </table>

            <ol style="color: red;">
                <li style="font-weight: bold;">
                    <span style="font-weight: normal;">СГП - остатки по Складу готовой продукции только в Александрии (без учета склада в Днепропетровске).</span>
                </li>
                <li style="font-weight: bold;">
                    <span style="font-weight: normal;">В плане производства исключены данные по комплектам деталей - корпус насоса в сборе или ротор насоса в сборе.</span>
                </li>
                <li style="font-weight: bold;">
                    <span style="font-weight: normal;"><img src="skins/basic/admin/addons/uns_plans/images/prohibition.png" alt="X"/> - к производству запрещены <b>{$prohibition|count}</b> видов насосов, так как по ним уже есть {$search.months_supply}-х месячный запас для продаж.
                    <br>Ограничение накладывается если: (СГП на тек. день + ЗАДЕЛ) &ge; ПЛАНА ПРОДАЖ на {$search.months_supply} мес. вперед.
                    <br>Ограничение автоматически снимается, как только со Склада готовой продукции будет продано значительное количество насосов.</span>
                </li>
            </ol>



        {else}
            <h3>Укажите месяц и год для отображения плана производства</h3>
        {/if}
    {/capture}

    {assign var="curr_time" value=$smarty.now|date_format:"%H:%M"}
    {include file="common_templates/mainbox.tpl" title="План производства насосов на `$months_full[$search.month]` `$search.year` г. (на 23:59 `$search.current_day`)" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
{/strip}