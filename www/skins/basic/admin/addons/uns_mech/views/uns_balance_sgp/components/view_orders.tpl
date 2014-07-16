{strip}
    {if $orders|is__array}
        {include file="common_templates/subheader.tpl" title="Заказы"}
        <div class="subheader_block">

        <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
                <tr>
                    <th>&nbsp;</th>
                    <th class="b1_l center">Дата отгрузки</th>
                    <th class="b1_l center">Регион/Клиент</th>
                    <th class="b1_l center">Кол-во</th>
                    <th class="b1_l center" style="text-transform: none;">Вес, кг</th>
                    <th class="b1_l center">Комментарий</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$orders item="o" name="o"}
                <tr>
                    <td class="center">№{$o.order_id}</td>
                    <td class="b1_l center">{$o.date_finished|date_format:"%a %d/%m/%y"}<br><nobr>(осталось {$o.remaining_time} дн.)</nobr></td>
                    <td class="b1_l"><b>{$customers[$o.customer_id].name_short}</b> - {$customers[$o.customer_id].name}</td>
                    <td class="b1_l center">{$o.total_quantity}</td>
                    <td class="b1_l"><nobr>{$o.total_weight|number_format:1:".":" "}</nobr></td>
                    <td class="b1_l">{$o.comment}&nbsp;</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        </div>
    {/if}
{/strip}