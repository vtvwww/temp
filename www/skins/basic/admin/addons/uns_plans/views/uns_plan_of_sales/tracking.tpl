{script src="js/tabs.js"}
{literal}
    <style>
        div.progress-bar{
            border: 1px solid #2B29FF;
            height: 16px;
            overflow: hidden;
        }

        div.progress-bar .done{
            background-color: #5C5BFF;
            float: left;
            display: inline;
            height: 16px;
        }

        div.progress-bar .overflow{
            float: right;
            background-color: #228b22;
            display: inline;
            height: 16px;
        }
    </style>
{/literal}
{capture name="mainbox"}
    {capture name="add_cond"}
        <td class="nowrap search-field b1_l">
            <label for="select_sgp">Выбор СКЛАДА ГОТОВОЙ ПРОДУКЦИИ:</label>
            <div class="break">
                <select name="select_sgp" id="select_sgp">
                    <option value="19_25"   {if $search.select_sgp == "19_25"}selected="selected"{/if}>Александрия + Днепропетровск</option>
                    <option value="19"      {if $search.select_sgp == "19"   }selected="selected"{/if}>Александрия</option>
                    <option value="25"      {if $search.select_sgp == "25"   }selected="selected"{/if}>Днепропетровск</option>
                </select>
            </div>
        </td>
    {/capture}
    {include file="addons/uns_plans/views/uns_plan_of_sales/components/search_form_manage.tpl" dispatch="`$controller`.$mode" search_content=$smarty.capture.search_content but_text="ВЫПОЛНИТЬ АНАЛИЗ" add_cond=$smarty.capture.add_cond}
    <h2>Выполнение Плана продаж на {$months[$search.month]} {$search.year} г.</h2>
    {if is__array($pump_series)}
        <table cellpadding="0" cellspacing="0" border="0" class="table" style="margin: 10px; 0">
            <thead>
                <tr style="background-color: #D4D0C8;">
                    <th class="center">Наименование</th>
                    <th class="b1_l center" align="center">План продаж <br/>на {$months[$search.month]} {$search.year}</th>
                    <th class="b1_l center" align="center">Факт. продажи <br/>на {$months[$search.month]} {$search.year}</th>
                    <th class="b1_l " width="50px">&nbsp;</th>
                    <th class="center" width="200px">Выполнение</th>
                </tr>
            </thead>
            <tfoot>
                <tr style="background-color: #D4D0C8;">
                    <th class="center">Наименование</th>
                    <th class="b1_l center" align="center">План продаж <br/>на {$months[$search.month]} {$search.year}</th>
                    <th class="b1_l center" align="center">Факт. продажи <br/>на {$months[$search.month]} {$search.year}</th>
                    <th class="b1_l center" colspan="2" width="200px">Выполнение</th>
                </tr>
            </tfoot>

            {assign var="total_plan" value=0}
            {assign var="total_sales" value=0}
            {foreach from=$pump_series item="pt" name="pt"}
            <tbody>
            <tr>
                <td colspan="10" style="background-color: #E6E2DA;"><b>{$pt.pt_name}</b></td>
            </tr>
                {foreach from=$pt.pump_series item="ps" key="ps_id" name="ps"}
                {assign var="t_plan" value=$plan.group_by_item.S.$ps_id.ukr_curr+$plan.group_by_item.S.$ps_id.exp_curr}
                {assign var="t_sale" value=$sales.$ps_id|default:0}

                {assign var="total_plan" value=$total_plan+$t_plan}
                {assign var="total_sales" value=$total_sales+$t_sale}
                <tr>
                    <td>&nbsp;&nbsp;{$ps.ps_name}</td>
                    <td class="b1_l center bold"><span class="{if !$t_plan}zero{/if}">{$t_plan}</span></td>
                    <td class="b1_l center bold"><span class="{if !$t_sale}zero{/if}">{$t_sale}</span></td>
                    <td class="b1_l right" {if $percs.$ps_id.ovf == "Y"}style="font-weight: bold; color: #ff0000;" {/if}>{$percs.$ps_id.done}%</td>
                    <td class="b1_l" title="{$percs.$ps_id.title}">
                        <div class="progress-bar">
                            <div class="done"     style="width: {$percs.$ps_id.d}%"></div>
                            <div class="overflow" style="width: {$percs.$ps_id.o}%"></div>
                        </div>
                    </td>
                </tr>
                {/foreach}
            </tbody>
            {/foreach}
            <tr>
                <td style="text-align: right;"><b>Итого:</b></td>
                <td class="b1_l center"><span style="font-weight: bold; font-size: 17px;">{$total_plan}</span></td>
                <td class="b1_l center"><span style="font-weight: bold; font-size: 17px;">{$total_sales}</span></td>
                {assign var="total_perc" value=0}
                {if $total_plan>0 and  $total_sales>=0}
                    {math assign="total_perc" equation="100*total_sales/total_plan" total_sales=$total_sales total_plan=$total_plan format="%.0d"}
                {/if}
                <td class="b1_l right"><span style="font-weight: bold; font-size: 17px;">{$total_perc}%</span></td>
                <td class="b1_l" title="{$percs.total.title}">
                    <div class="progress-bar" style="border: 1px solid red">
                        <div class="done"     style="background-color: rgba(255, 23, 0, 0.62); width: {$total_perc}%"></div>
                        <div class="overflow"></div>
                    </div>
                </td>
            </tr>
        </table>
    {/if}
{/capture}
{include file="common_templates/mainbox.tpl" title="Отслеживание плана продаж" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
