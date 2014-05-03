{script src="js/tabs.js"}
{capture name="mainbox"}
    {include file="addons/uns_plans/views/uns_plan_of_sales/components/search_form.tpl" dispatch="`$controller`.$mode" search_content=$smarty.capture.search_content}
    {if $error != "Y"}
        <h2>Анализ продаж по {$months[$search.month]} {$search.year} г.</h2>
        {assign var="pump_count" value=0 }
        {if is__array($pump_series)}
            {foreach from=$pump_series item="ps" key="id" name="ps"}
            <table cellpadding="0" cellspacing="0" border="0" width="100%" class="simple" style="margin: 10px; 0">
                <thead>
                    <tr>
                        <th colspan="2" style="text-align: left; font-size: 17px;">{$smarty.foreach.ps.iteration}. Насос {$ps.ps_name}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="460px" valign="top" align="center" rowspan="2" style="border-right: 2px solid #808080; ">
                            <b>Графики продаж {$ps.ps_name} за последние 2 года</b>
                            <br/>
                            <img src="skins/basic/admin/images/uns_charts/{$graphs_key}_ps_{$id}.png" alt=""/>
                        </td>
                        <td align="left" valign="top" style="height: 1px; padding: 5px 20px;">
                            {include file="addons/uns_plans/views/uns_plan_of_sales/components/statistica.tpl" ps_id=$id}
                        </td>
                    </tr>
                    <tr>
                        <td align="left" valign="top" style="height: 1px; padding: 5px 20px;">
                            {include file="addons/uns_plans/views/uns_plan_of_sales/components/planning.tpl" ps_id=$id}
                        </td>
                    </tr>
                </tbody>
            </table>

            {*Разрыв страницы при печати*}
            {assign var="pump_count" value=$pump_count+1 }
            {if $pump_count >= 3}
                {assign var="pump_count" value=0 }
                <span style="page-break-before:always;"></span>
            {/if}

            {/foreach}
        {/if}

        {*{include file="addons/uns_plans/views/uns_plan_of_sales/components/summary_table.tpl"}*}
        {include file="addons/uns_plans/views/uns_plan_of_sales/components/summary_table_for_save_in_plans.tpl"}

    {/if}
{/capture}
{include file="common_templates/mainbox.tpl" title="Расчет плана продаж" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
