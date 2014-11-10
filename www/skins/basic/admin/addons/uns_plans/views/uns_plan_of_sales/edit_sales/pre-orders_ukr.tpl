<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr style="background-color: #EDEDED;">
            <th style="text-transform: none;" class="center">№</th>
            <th style="text-transform: none;" colspan="2" class="center b1_l">Клиент</th>
            {*<th style="text-transform: none;" class="center b1_l">Дата отгрузки<br>(осталось)</th>*}
            <th style="text-transform: none;" class="center b1_l">Наименование</th>
            <th style="text-transform: none;" class="center b1_l">Кол-во</th>
        </tr>
    </thead>
    <tbody>
        {assign var="iteration" value=0}
        {assign var="total_q" value=0}
        {foreach from=$pre_orders.items item="i" name="i"}
            {if $i.shipped != "full"}
            {assign var="iteration" value=$iteration+1}
            <tr class="{if $o.status == "Open"}OP{elseif $o.status == "Close"}CL{/if}">
                <td class="center">{$iteration}</td>
                {*<td class="center b1_l">*}
                    {*{if      $o.status == "Hide"}*}
                        {*<img class="hand" border="0" title="Скрыт - предварительный заказ" src="skins/basic/admin/addons/uns_acc/images/circle_yellow.png">*}
                    {*{elseif  $o.status == "Open"}*}
                        {*<img class="hand" border="0" title="Открыт - заказ готов к выполнению" src="skins/basic/admin/addons/uns_acc/images/circle_green.png">*}
                    {*{elseif  $o.status == "Close" or "Shipped"}*}
                        {*<img class="hand" border="0" title="Выполнен - заказ отгружен" src="skins/basic/admin/addons/uns_acc/images/done.png">*}
                    {*{/if}*}
                {*</td>*}
                {assign var="curr_order" value=$i.order_id}
                {assign var="curr_customer" value=$pre_orders.order_list.$curr_order.customer_id}
                <td class="center b1_l"><b>{$customers[$curr_customer].name_short}</b></td>
                <td class="center">{include file="common_templates/tooltip.tpl" tooltip="`$customers[$curr_customer].name`"}</td>
                {*<td class="center b1_l">{$o.date_finished|fn_parse_date|date_format:"%a %d/%m/%Y"} <span class="info_warning">({$o.remaining_time} дн.)</span></td>*}
                <td class="left b1_l">{$i.p_name}{if $i.item_type == "PF"} на раме{elseif $i.item_type == "PA"} агрегат{/if}</td>
                <td class="center b1_l">{$i.quantity-$i.info_RO.total_q}</td>
                {assign var="total_q" value=$total_q+$i.quantity-$i.info_RO.total_q}
            </tr>
            {/if}
        {/foreach}
    </tbody>
    <tfoot>
        <tr style="background-color: #EDEDED;">
            <td style="background-color: #EDEDED;" colspan="4" align="right" class="bold">ИТОГО:</td>
            <td style="background-color: #EDEDED;" class="center b1_l bold">{$total_q|fn_fvalue}</td>
        </tr>
    </tfoot>
</table>