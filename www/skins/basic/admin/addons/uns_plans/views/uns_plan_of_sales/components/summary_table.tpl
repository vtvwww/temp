{if is__array($pump_series)}
<span style="page-break-before:always;"></span>
<span style="font-size: 14px; font-weight: bold;">Сводная таблица <u>Плана продаж</u> и <u>Фактической потребности в продукции</u> на {$months[$search.month]} {$search.year} г.</span>
    <table cellpadding="0" cellspacing="0" border="0" class="simple" style="margin: 10px; 0">
        <thead>
            <tr>
                <th>№</th>
                <th>Наименование</th>
                <th>
                    План продаж<br>
                    на {$months[$search.month]} {$search.year} г.
                </th>
                <th>
                    Факт. потреб.<br>
                    на {$months[$search.month]} {$search.year} г.
                </th>
            </tr>
        </thead>
        <tbody>
            {assign var="total_plan_prodazh_calc" value=0}
            {assign var="total_potrebnost" value=0}
            {foreach from=$pump_series item="ps" key="id" name="ps"}
                {assign var="total_plan_prodazh_calc" value=$total_plan_prodazh_calc+$plan[$ps.ps_id].plan_prodazh_calc}
                {assign var="total_potrebnost" value=$total_potrebnost+$plan[$ps.ps_id].potrebnost}
                <tr>
                    <td align="center">{$smarty.foreach.ps.iteration}</td>
                    <td>{$ps.ps_name}</td>
                    <td align="center">
                        <span class="bold {if $plan[$ps.ps_id].plan_prodazh_calc<0}info_warning_block{elseif $plan[$ps.ps_id].plan_prodazh_calc==0}zero{/if}">
                            {$plan[$ps.ps_id].plan_prodazh_calc}
                        </span>
                    </td>
                    <td align="center">
                        <span class="bold {if $plan[$ps.ps_id].potrebnost<0}info_warning_block{elseif $plan[$ps.ps_id].potrebnost==0}zero{/if}">
                            {$plan[$ps.ps_id].potrebnost}
                        </span>
                    </td>
                </tr>
            {/foreach}
            <tr>
                <td align="right" colspan="2"><b>Итого:</b></td>
                <td align="right">
                    <span class="bold {if $total_plan_prodazh_calc<0}info_warning_block{elseif $total_plan_prodazh_calc==0}zero{/if}">
                        {$total_plan_prodazh_calc}
                    </span>
                </td>
                <td align="right">
                    <span class="bold {if $total_potrebnost<0}info_warning_block{elseif $total_potrebnost==0}zero{/if}">
                        {$total_potrebnost}
                    </span>
            </tr>

        </tbody>
    </table>
{/if}
