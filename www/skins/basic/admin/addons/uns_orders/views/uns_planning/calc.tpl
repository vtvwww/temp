{script src="js/tabs.js"}
{capture name="mainbox"}
    {include file="addons/uns_orders/views/uns_planning/components/search_form.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}
    {if $error != "Y"}
        <h2>Расчет плана производства на {$months[$search.month]} {$search.year} г.</h2>
        {if is__array($pump_series)}
            {foreach from=$pump_series item="ps" key="id" name="ps"}
            <table cellpadding="0" cellspacing="0" border="0" width="100%" class="simple" style="margin: 10px; 0">
                <thead>
                    <tr>
                        <th colspan="2" style="text-align: left; font-size: 15px;">{$smarty.foreach.ps.iteration}. Насос {$ps.ps_name}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="460px" align="center" rowspan="2" style="border-right: 2px solid #808080; ">
                            <b>График продаж {$ps.ps_name} за последние 2 года</b>
                            <br/>
                            <img src="skins/basic/admin/images/uns_charts/ps_{$id}.png" alt=""/>
                        </td>
                        <td align="center" valign="top" style="height: 1px;">
                            {include file="addons/uns_orders/views/uns_planning/components/statistica.tpl" ps_id=$id}
                        </td>
                    </tr>
                    <tr>
                        <td align="center" valign="top">
                            {include file="addons/uns_orders/views/uns_planning/components/planning.tpl"}
                        </td>
                    </tr>
                </tbody>
            </table>
            {/foreach}
        {/if}
    {/if}
{/capture}
{include file="common_templates/mainbox.tpl" title="Расчет плана производства" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
