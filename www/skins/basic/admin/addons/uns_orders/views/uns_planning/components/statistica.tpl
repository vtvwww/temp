<b>Статистика продаж {$ps.ps_name} за последние 2 года</b>
{capture name="month_period"}
    {if $search.month == 1}
    {elseif $search.month == 2}
        За Янв
    {elseif $search.month == 3}
        За Янв-Фев
    {elseif $search.month == 4}
        За Янв-Мар
    {elseif $search.month == 5}
        За Янв-Апр
    {elseif $search.month == 6}
        За Янв-Май
    {elseif $search.month == 7}
        За Янв-Июн
    {elseif $search.month == 8}
        За Янв-Июл
    {elseif $search.month == 9}
        За Янв-Авг
    {elseif $search.month == 10}
        За Янв-Сен
    {elseif $search.month == 11}
        За Янв-Окт
    {elseif $search.month == 12}
        За Янв-Ноя
    {/if}
{/capture}

<table border="0" class="simple" cellspacing="0" cellpadding="0" style="margin: 10px 0 3px 0;">
    <thead>
        <tr>
            <th rowspan="2" width="50px" align="center">ГОД</th>
            <th colspan="2" width="100px">За год</th>
            <th colspan="2" width="100px">{$smarty.capture.month_period}</th>
        </tr>
        <tr>
            <th>Ср.зн.</th>
            <th>Итого</th>
            <th>Ср.зн.</th>
            <th>Итого</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$analysis.$ps_id item="d" key="year" name="d"}
            <tr>
                <td align="center">
                    <span style="font-size: 14px; font-weight: bold;">{$year}</span>
                </td>
                <td align="center">
                    {if $smarty.foreach.d.last}
                        <span style="font-size: 12px; font-style: italic;">{$d.for_year.avr}*</span>
                    {else}
                        <span style="font-size: 14px; font-weight: bold;">{$d.for_year.avr}</span>
                    {/if}
                </td>
                <td align="center">
                    {if $smarty.foreach.d.last}
                        <span style="font-size: 12px; font-style: italic;">{$d.for_year.total}*</span>
                    {else}
                        <span style="font-size: 14px; font-weight: bold;">{$d.for_year.total}</span>
                    {/if}
                </td>

                <td align="center"><span style="font-size: 14px; font-weight: bold;">{$d.for_months_ref_year.avr}</span></td>
                <td align="center"><span style="font-size: 14px; font-weight: bold;">{$d.for_months_ref_year.total}</span></td>
            </tr>
        {/foreach}
    </tbody>
</table>
<span style="font-style: italic; font-size: 11px;"><b>*</b> - прогнозируемые значения за год</span>