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
        {include file="addons/uns_plans/views/uns_plan_of_mech_dep/components/search_form_manage.tpl" dispatch="`$controller`.$mode" search_content=$smarty.capture.search_content but_text="ВЫПОЛНИТЬ АНАЛИЗ"}
        {if is__array($pump_series)}
            <table cellpadding="0" cellspacing="0" border="0" class="table" style="margin: 10px; 0">
                <thead>
                    <tr style="background-color: #D4D0C8;">
                        <th style="text-transform: none;"               rowspan="3"             class="center">Наименование</th>
                        <th style="text-transform: none;"               rowspan="3"             class="center b_l">Склад<br>готовой<br>продукции<br>на<br>01/{$search.month|string_format:"%02d"}/{$search.year|substr:"-2":"2"}</th>
                        <th style="text-transform: none;"               rowspan="2" colspan="2" class="center b_l b1_b">План<br>продаж</th>
                        <th style="text-transform: uppercase;"          rowspan="2" colspan="2" class="center b3_l b1_b">План<br>производства<br>Мех. цеха</th>
                        <th style="text-transform: uppercase;"                      colspan="4" class="center b3_l b1_b">Выполнение плана на {$search.current_day|fn_parse_date|date_format:"%d/%m/%y"}</th>
                        <th style="text-transform: none;"               rowspan="3"             class="center b3_l">Склад<br>готовой<br>продукции<br>на<br>{$search.current_day|fn_parse_date|date_format:"%d/%m/%y"}</th>
                    </tr>
                    <tr style="background-color: #D4D0C8;">
                        <th style="text-transform: none; width: 50px; background-color: #FFEF8C;"  rowspan="3"             class="center b3_l">Задел<br>ожид.<br>сборку</th>
                        <th style="text-transform: none; width: 50px; background-color: #C0FF9A;"  rowspan="3"             class="center b2_l">Выпол-<br>нено</th>
                        <th style="text-transform: uppercase;"                      colspan="2" class="center b3_l b1_b">Осталось</th>
                    </tr>
                    <tr style="background-color: #D4D0C8;">
                        <th style="text-transform: lowercase; width: 50px;" class="center b_l">{$tpl_curr_month}</th>
                        <th style="text-transform: lowercase; width: 50px;" class="center b1_l">{$tpl_next_month}</th>
                        <th style="text-transform: lowercase; width: 50px;" class="center b3_l">{$tpl_curr_month}</th>
                        <th style="text-transform: lowercase; width: 50px;" class="center b1_l">{$tpl_next_month}</th>
                        <th style="text-transform: lowercase; width: 50px;background-color: #B8C1FF;" class="center b3_l">{$tpl_curr_month}</th>
                        <th style="text-transform: lowercase; width: 50px;background-color: #B8C1FF;" class="center b1_l">{$tpl_next_month}</th>
                    </tr>
                </thead>
                {foreach from=$pump_series item="pt" name="pt"}
                <tbody>
                <tr>
                    <td colspan="11" style="background-color: #E6E2DA; font-size: 14px; font-style: italic;"><b>{$pt.pt_name}</b></td>
                </tr>
                    {foreach from=$pt.pump_series item="ps" key="ps_id" name="ps"}
                    <tr>
                        {*Наименование*}
                        <td>
                            <a  rev="content_item_name_{$analysis_links.$ps_id.id}" id="opener_item_name_{$analysis_links.$ps_id.id}" href="{$analysis_links.$ps_id.href|fn_url}" class="block cm-dialog-opener cm-dialog-auto-size text-button-edit cm-ajax-update black bold" {if $is_mark===false}{else}onclick="mark_item($(this));"{/if}>{$ps.ps_name}</a>
                            <div id="content_item_name_{$analysis_links.$ps_id.id}" class="hidden" title="Анализ насоса <u>{$analysis_links.$ps_id.name}</u>"></div>
                        </td>

                        {*Склад Готовой Продукции*}
                        {assign var="q" value=$sgp.$ps_id|default:0}
                        <td class="b_l  center {if !$q}zero{/if}">{$q}</td>

                        {*Потребность*}
                        {assign var="q" value=$requirement.curr_month.$ps_id|default:0}
                        <td class="b_l center {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$requirement.next_month.$ps_id|default:0}
                        <td class="b1_l center {if !$q}zero{/if}">{$q}</td>

                        {*План производства*}
                        {assign var="q" value=$initial_production_plan.curr_month.$ps_id|default:0}
                        <td class="b3_l  center bold {if !$q}zero{/if}">{$q}</td>
                        {assign var="q" value=$initial_production_plan.next_month.$ps_id|default:0}
                        <td class="b1_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*Задел*}
                        {assign var="q" value=$zadel.$ps_id|default:0}
                        <td style="background-color: #FFEF8C;" class="b3_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*Выполнено*}
                        {assign var="q" value=$done.$ps_id|default:0}
                        <td style="background-color: #C0FF9A;" class="b2_l center bold {if !$q}zero{/if}">{$q}</td>

                        {*Осталось*}
                        {assign var="q" value=$remaining_production_plan.curr_month.$ps_id|default:0}
                        <td style="background-color: #B8C1FF;" class="b3_l center bold {if !$q}zero{/if}">{$q}
                        </td>
                        {assign var="q" value=$remaining_production_plan.next_month.$ps_id|default:0}
                        <td style="background-color: #B8C1FF;" class="b1_l center bold {if !$q}zero{/if}">{$q}
                        </td>

                        {*Склад Готовой Продукции*}
                        {assign var="q" value=$sgp_current_day.$ps_id|default:0}
                        <td class="b3_l  center {if !$q}zero{/if}">{$q}</td>
                    </tr>
                    {/foreach}
                </tbody>
                {/foreach}

                {*******************************************************************}
                {*ИТОГО*}
                <tbody>
                    <tr>
                        <td class="b3_t b3_b bold" style="text-align: right; font-size: 14px;">ИТОГО:</td>

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

                        {*Склад Готовой Продукции*}
                        {assign var="q" value=$sgp_current_day.total|default:0}
                        <td style="font-size: 14px;" class="b3_t b3_b b3_l center bold {if !$q}zero{/if}">{$q}</td>
                    </tr>
                </tbody>
                <tbody>
                    <tr style="background-color: #D4D0C8;">
                        <th rowspan="3" style="text-transform: none;" class="center">Наименование</th>
                        <th rowspan="3" style="text-transform: none;" class="center b_l">Склад<br>готовой<br>продукции<br>на<br>01/{$search.month|string_format:"%02d"}/{$search.year|substr:"-2":"2"}</th>
                        <th style="text-transform: lowercase;" class="b_l center">{$tpl_curr_month}</th>
                        <th style="text-transform: lowercase;" class="b1_l center">{$tpl_next_month}</th>
                        <th style="text-transform: lowercase;" class="b3_l center">{$tpl_curr_month}</th>
                        <th style="text-transform: lowercase;" class="b1_l center">{$tpl_next_month}</th>
                        <th style="text-transform: none; background-color: #FFEF8C;" rowspan="2" class="b3_l center">Задел<br>ожид.<br>сборку</th>
                        <th style="text-transform: none; background-color: #C0FF9A;" rowspan="2" class="b2_l center">Выпол-<br>нено</th>
                        <th style="text-transform: lowercase;background-color: #B8C1FF;" class="b3_l center">{$tpl_curr_month}</th>
                        <th style="text-transform: lowercase;background-color: #B8C1FF;" class="b1_l center">{$tpl_next_month}</th>
                        <th rowspan="3" style="text-transform: none;" class="b3_l center">Склад<br>готовой<br>продукции<br>на<br>{$search.current_day|fn_parse_date|date_format:"%d/%m/%y"}</th>
                    </tr>
                    <tr style="background-color: #D4D0C8;">
                        <th rowspan="2" colspan="2" style="text-transform: none;" class="center b_l b1_b b1_t">План<br>продаж</th>
                        <th rowspan="2" colspan="2" style="text-transform: uppercase;" class="center b3_l b1_t">План<br>производства<br>Мех. цеха</th>
                        <th colspan="2" style="text-transform: uppercase;" class="center b3_l b1_t">Осталось</th>
                    </tr>
                    <tr style="background-color: #D4D0C8;">
                        <th style="text-transform: uppercase;" colspan="4" class="center b3_l b1_t">Выполнение плана на {$search.current_day|fn_parse_date|date_format:"%d/%m/%y"}</th>
                    </tr>
                </tbody>
            </table>
        {else}
            <h3>Укажите месяц и год для отображения плана производства</h3>
        {/if}
    {/capture}
    {include file="common_templates/mainbox.tpl" title="План производства Механического цеха" content=$smarty.capture.mainbox tools=$smarty.capture.tools}


{/strip}