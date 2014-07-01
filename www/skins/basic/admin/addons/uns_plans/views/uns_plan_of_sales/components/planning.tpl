{strip}
<span style="font-size: 14px; font-weight: bold;">{if $zone == "EXP"}ЭКСПОРТ: {elseif $zone == "UKR"}УКРАИНА: {/if}Анализ Плана продаж {*{$ps.ps_name} *}на {$months[$search.month]} {$search.year} г.</span>

{*<p>ps_id={$ps_id}</p>*}

{*$pump_series, $sales, $analysis, $plan, $ps_order*}

{assign var="step" value=1}

<ol>
    {*1. Отобразить таблицу имеющихся заказов *}
    {if $zone == "EXP" and $plan.$zone.$ps_id.orders|is__array}
    <li>Имеющиеся заказы на {$ps.ps_name} на {$months[$search.month]|mb_convert_case:1:"utf-8"} {$search.year} г.:
        <table border="0" class="simple" cellspacing="0" cellpadding="0" style="margin: 3px 0;">
            <thead>
                <tr>
                    <th>№</th>
                    <th>Клиент/Регион</th>
                    <th>Дата&nbsp;отгрузки</th>
                    <th>Кол.</th>
                </tr>
            </thead>
            <tbody>
                {assign var="total_q" value=0}
                {foreach from=$plan.$zone.$ps_id.orders key="order_id" item="order_q" name="o"}
                    {assign var="date_finished" value=$orders[$order_id].date_finished}
                    {assign var="customer_id"   value=$orders[$order_id].customer_id}
                    <tr>
                        <td>{$smarty.foreach.o.iteration}</td>
                        <td>{$customers[$customer_id].name}</td>
                        <td align="center">{$date_finished|fn_parse_date|date_format:"%a %d/%m/%Y"}</td>
                        <td align="right">{$order_q|fn_fvalue}</td>
                    </tr>
                {/foreach}
                <tr>
                    <td colspan="3" align="right"><b>Итого, шт:</b></td>
                    <td align="right"><b>{$plan.$zone.$ps_id.total_orders}</b></td>
                </tr>
            </tbody>
        </table>
    </li>
    {/if}

    {*2. Прогнозируемый план продаж *}
    <li>
        {if $plan.$zone.$ps_id.plan_prodazh_recalc == "Y1"}
            Прогнозируемый План продаж равен {$plan.$zone.$ps_id.plan_prodazh_statistical|fn_fvalue:0} шт., но т.к.
            <br/>прогноз. план продаж &le; имеющимся заказам ({$plan.$zone.$ps_id.plan_prodazh_statistical|fn_fvalue:0} &le; {$plan.$zone.$ps_id.total_orders}),
            <br/>тогда <b>План продаж = Заказы + {$search.koef_plan_prodazh}% = {$plan.$zone.$ps_id.plan_prodazh_calc} шт.</b>
            <br/>
        {elseif $plan.$zone.$ps_id.plan_prodazh_recalc == "Y2"}

        {else}
            Прогнозируемый <b>План продаж: {$plan.$zone.$ps_id.plan_prodazh_statistical|fn_fvalue:0} шт.</b>
        {/if}
    </li>

    {*3. Фактическая потребность в насосе *}
    {*<li>*}
        {*Факт. потребность = План Продаж + {$search.week_supply}-х нед. продажи<br/>*}
        {*<b>Факт. потребность = </b><b>{$plan.$zone.$ps_id.plan_prodazh_calc} + {$search.week_supply} нед. * ({$plan.$zone.$ps_id.plan_prodazh_calc}/4) = {$plan.$zone.$ps_id.potrebnost} шт.</b>*}
    {*</li>*}

</ol>
<table cellpadding="0" cellspacing="0" border="0" class="simple" style="margin: 10px; 0">
    <thead>
        <tr>
            {if $zone == "UKR"}
            <th>
                {*Украина<br>*}
                на {$months[$search.month]} {$search.year} г.
            </th>
            <th>
                {*Украина<br>*}
                на след. мес.
            </th>
            {/if}

            {if $zone == "EXP"}
            <th>
                {*Экспорт<br>*}
                на {$months[$search.month]} {$search.year} г.
            </th>
            <th>
                {*Экспорт<br>*}
                на след. мес.
            </th>
            {/if}
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>&nbsp;<br/>&nbsp;<br/></td>
            <td>&nbsp;<br/>&nbsp;<br/></td>
        </tr>
    </tbody>
</table>

{*2. Прогнозируемый план продаж *}

    {*<pre>{$customers|print_r}</pre>*}
    {*<pre>{$orders|print_r}</pre>*}
    {*<pre>{$plan.$ps_id|print_r}</pre>*}
{/strip}