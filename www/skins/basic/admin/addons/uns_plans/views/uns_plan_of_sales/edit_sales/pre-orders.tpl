<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr style="background-color: #EDEDED;">
            <th style="text-transform: none;" class="center">№</th>
            <th style="text-transform: none;" class="center b1_l">&nbsp;</th>{*Статус*}
            <th style="text-transform: none;" class="center b1_l">Клиент</th>
            <th style="text-transform: none;" class="center b1_l">Дата отгрузки<br>(осталось)</th>
            <th style="text-transform: none;" class="center b1_l">Наименование</th>
            <th style="text-transform: none;" class="center b1_l">Кол-во</th>
        </tr>
    </thead>
    <tbody>
        {assign var="total_q" value=0}
        {foreach from=$pre_orders item='o' name="o"}
            {foreach from=$o.items item='i'}
                <tr class="{if $o.status == "Open"}OP{elseif $o.status == "Close"}CL{/if}">
                    <td class="center">{$smarty.foreach.o.iteration}</td>
                    <td class="center b1_l">
                        {if      $o.status == "Hide"}
                            <img class="hand" border="0" title="Скрыт - предварительный заказ" src="skins/basic/admin/addons/uns_acc/images/circle_yellow.png">
                        {elseif  $o.status == "Open"}
                            <img class="hand" border="0" title="Открыт - заказ готов к выполнению" src="skins/basic/admin/addons/uns_acc/images/circle_green.png">
                        {elseif  $o.status == "Close" or "Shipped"}
                            <img class="hand" border="0" title="Выполнен - заказ отгружен" src="skins/basic/admin/addons/uns_acc/images/done.png">
                        {/if}
                    </td>
                    <td class="center b1_l"><b>{$o.customer_short_name}</b>{if strlen($o.comment)}<br>{$o.comment}{/if}</td>
                    <td class="center b1_l">{$o.date_finished|fn_parse_date|date_format:"%a %d/%m/%Y"} <span class="info_warning">({$o.remaining_time} дн.)</span></td>
                    <td class="center b1_l">{$i.p_name}</td>
                    <td class="center b1_l">{$i.quantity|fn_fvalue}</td>
                    {assign var="total_q" value=$total_q+$i.quantity}
                </tr>
                {/foreach}
        {/foreach}
    </tbody>
    <tbody>
        <tr style="background-color: #EDEDED;">
            <td style="background-color: #EDEDED;" colspan="5" align="right" class="bold">ИТОГО:</td>
            <td style="background-color: #EDEDED;" class="center b1_l bold">{$total_q|fn_fvalue}</td>
        </tr>
    </tbody>
</table>