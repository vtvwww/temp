{script src="js/tabs.js"}
{literal}
    <style>
        td.zero{
            color: #d3d3d3;
        }
    </style>
{/literal}
{capture name="mainbox"}
    {include file="addons/uns_plans/views/uns_plan_of_mech_dep/components/search_form_manage.tpl" dispatch="`$controller`.$mode" search_content=$smarty.capture.search_content but_text="ВЫПОЛНИТЬ АНАЛИЗ"}
    {*<h2>Выполнение Плана производства Механическим цехом на {$months[$search.month]} {$search.year} г.</h2>*}
    {if is__array($pump_series)}
        <table cellpadding="0" cellspacing="0" border="0" class="table" style="margin: 10px; 0">
            <thead>
                <tr style="background-color: #D4D0C8;">
                    <th style="text-transform: none;" rowspan="2" class="center">Наименование</th>
                    <th style="text-transform: none;" colspan="2" class="center b1_l b1_b">Плановая потребность</th>
                    <th style="text-transform: none;" rowspan="2" class="center b_l">Склад<br>готовой<br>продукции<br>01/{$search.month|string_format:"%02d"}/{$search.year|substr:"-2":"2"}</th>
                    <th style="text-transform: uppercase;" colspan="2" class="center b_l b1_b">План производства</th>
                    <th style="text-transform: none; width: 50px;" rowspan="2" class="center b_l">Задел<br>ожид.<br>сборку</th>
                    <th style="text-transform: none; width: 50px;" rowspan="2" class="center b2_l">Выпол-<br>нено</th>
                    <th style="text-transform: uppercase;" colspan="2" class="center b_l b1_b">Осталось</th>
                </tr>
                <tr style="background-color: #D4D0C8;">
                    <th style="text-transform: lowercase; width: 70px;" class="center b1_l">{$months[$search.month]}.{$search.year}</th>
                    <th style="text-transform: lowercase; width: 70px;" class="center b1_l">след. мес.</th>
                    <th style="text-transform: lowercase; width: 70px;" class="center b_l">{$months[$search.month]}.{$search.year}</th>
                    <th style="text-transform: lowercase; width: 70px;" class="center b1_l">след. мес.</th>
                    <th style="text-transform: lowercase; width: 70px;" class="center b_l">{$months[$search.month]}.{$search.year}</th>
                    <th style="text-transform: lowercase; width: 70px;" class="center b1_l">след. мес.</th>
                </tr>
            </thead>
            {foreach from=$pump_series item="pt" name="pt"}
            <tbody>
            <tr>
                <td colspan="10" style="background-color: #E6E2DA;"><b>{$pt.pt_name}</b></td>
            </tr>
                {foreach from=$pt.pump_series item="ps" key="ps_id" name="ps"}
                <tr>
                    {*Наименование*}
                    <td>&nbsp;&nbsp;{$ps.ps_name}</td>

                    {*Потребность*}
                    {assign var="q" value=$requirement.curr_month.$ps_id|default:0}
                    <td class="b1_l center {if !$q}zero{/if}">{$q}</td>
                    {assign var="q" value=$requirement.next_month.$ps_id|default:0}
                    <td class="b1_l center {if !$q}zero{/if}">{$q}</td>

                    {*Склад Готовой Продукции*}
                    {assign var="q" value=$sgp.$ps_id|default:0}
                    <td class="b_l  center {if !$q}zero{/if}">{$q}</td>

                    {*План производства*}
                    {assign var="q" value=$initial_production_plan.curr_month.$ps_id|default:0}
                    <td class="b_l  center bold {if !$q}zero{/if}">{$q}</td>
                    {assign var="q" value=$initial_production_plan.next_month.$ps_id|default:0}
                    <td class="b1_l center bold {if !$q}zero{/if}">{$q}</td>

                    {*Задел*}
                    <td class="b_l"></td>

                    {*Выполнено*}
                    <td class="b2_l"></td>

                    {*Осталось*}
                    <td class="b_l"></td>
                    <td class="b1_l"></td>
                </tr>
                {/foreach}
            </tbody>
            {/foreach}

            {*******************************************************************}
            {*ИТОГО*}
            <tbody>
                <tr>
                    <td class="bold" style="text-align: right; font-size: 14px;">ИТОГО:</td>

                    {*Потребность*}
                    {assign var="q" value=$requirement.curr_month.total|default:0}
                    <td style="font-size: 14px;" class="b1_l center bold {if !$q}zero{/if}">{$q}</td>
                    {assign var="q" value=$requirement.next_month.total|default:0}
                    <td style="font-size: 14px;" class="b1_l center bold {if !$q}zero{/if}">{$q}</td>

                    {*Склад Готовой Продукции*}
                    {assign var="q" value=$sgp.total|default:0}
                    <td style="font-size: 14px;" class="b_l  center bold {if !$q}zero{/if}">{$q}</td>

                    {*План производства*}
                    {assign var="q" value=$initial_production_plan.curr_month.total|default:0}
                    <td style="font-size: 14px;" class="b_l  center bold {if !$q}zero{/if}">{$q}</td>
                    {assign var="q" value=$initial_production_plan.next_month.total|default:0}
                    <td style="font-size: 14px;" class="b1_l center bold {if !$q}zero{/if}">{$q}</td>

                    {*Задел*}
                    <td style="font-size: 14px;" class="b_l"></td>

                    {*Выполнено*}
                    <td style="font-size: 14px;" class="b2_l"></td>

                    {*Осталось*}
                    <td style="font-size: 14px;" class="b_l"></td>
                    <td style="font-size: 14px;" class="b1_l"></td>
                </tr>
            </tbody>
        </table>
    {else}
        <h3>Укажите месяц и год для отображения плана производства</h3>
    {/if}
{/capture}
{include file="common_templates/mainbox.tpl" title="План производства для Механического цеха" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
