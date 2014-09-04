{strip}
    {if $orders_tpl|is__array}
        {include file="common_templates/subheader.tpl" title="Заказы"}
        <div class="subheader_block">

        <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
                <tr style="background-color: #EDEDED">
                    <th rowspan="2">№</th>
                    <th rowspan="2" class="b1_l center" style="text-transform: none;">Дата отгрузки</th>
                    <th rowspan="2" class="b1_l center" style="text-transform: none;">{include file="common_templates/tooltip.tpl" tooltip="Аббревиатура" params="black" tooltip_mark="<b>Абб.</b>"}</th>
                    <th rowspan="2" class="b1_l center" width="300" style="text-transform: none;">Клиент</th>
                    <th colspan="2" class="b1_l center" style="text-transform: none;">Насосов</th>
                    <th colspan="2" class="b1_l center" style="text-transform: none;">Деталей</th>
                    <th rowspan="2" class="b1_l center" style="text-transform: none;">Комментарий</th>
                </tr>
                <tr style="background-color: #EDEDED">
                    <th class="b1_l b1_t center" width="30" style="text-transform: none;">кол</th>
                    <th class="b1_l b1_t center" width="60" style="text-transform: none;">вес</th>
                    <th class="b1_l b1_t center" width="30" style="text-transform: none;">кол</th>
                    <th class="b1_l b1_t center" width="60" style="text-transform: none;">вес</th>
                </tr>
            </thead>
            {if $orders_tpl|is__array}
                {assign var="t_p_q" value=0} {*Насос*}
                {assign var="t_p_w" value=0} {*Насос*}
                {assign var="t_d_q" value=0} {*Деталь*}
                {assign var="t_d_w" value=0} {*Деталь*}
                {foreach from=$orders_tpl item="o" name="o"}
                <tbody>
                    <tr>
                        {assign var="t_p_q" value=$t_p_q+$o.quantity.pumps} {*Насос*}
                        {assign var="t_p_w" value=$t_p_w+$o.weight.pumps} {*Насос*}
                        {assign var="t_d_q" value=$t_d_q+$o.quantity.details} {*Деталь*}
                        {assign var="t_d_w" value=$t_d_w+$o.weight.details} {*Деталь*}

                        <td class="center">№{$o.order_id}</td>
                        <td class="b1_l center">{$o.date_finished|date_format:"%a %d/%m/%y"}<br><nobr>(осталось {$o.remaining_time} дн.)</nobr></td>
                        <td class="b1_l center"><b>{$customers_tpl[$o.customer_id].name_short}</b></td>
                        <td class="b1_l">{$customers_tpl[$o.customer_id].name}</td>

                        <td class="b1_l center bold">{if $o.quantity.pumps}{$o.quantity.pumps}{else}&nbsp;{/if}</td>
                        <td class="b1_l right">{if $o.weight.pumps}<nobr>{$o.weight.pumps|number_format:1:".":" "}</nobr>{else}&nbsp;{/if}</td>

                        <td class="b1_l center bold">{if $o.quantity.details}{$o.quantity.details}{else}&nbsp;{/if}</td>
                        <td class="b1_l right">{if $o.weight.details}<nobr>{$o.weight.details|number_format:1:".":" "}</nobr>{else}&nbsp;{/if}</td>

                        <td class="b1_l">{$o.comment}&nbsp;</td>
                    </tr>
                </tbody>
                {/foreach}

                {*ИТОГО*}
                <tbody>
                    <tr>
                        <th style="background-color: #D3D3D3;" colspan="4" class="b1_t right">ИТОГО:</th>
                        <th style="background-color: #D3D3D3;" class="b1_t b1_l center bold">{if $t_p_q}{$t_p_q}{/if}</th>
                        <th style="background-color: #D3D3D3;" class="b1_t b1_l right  bold">{if $t_p_w}<nobr>{$t_p_w|number_format:1:".":" "}</nobr>{/if}</th>
                        <th style="background-color: #D3D3D3;" class="b1_t b1_l center bold">{if $t_d_q}{$t_d_q}{/if}</th>
                        <th style="background-color: #D3D3D3;" class="b1_t b1_l right  bold">{if $t_d_w}<nobr>{$t_d_w|number_format:1:".":" "}</nobr>{/if}</th>
                        <th style="background-color: #D3D3D3;" class="b1_t b1_l">&nbsp;</th>
                    </tr>
                </tbody>
            {/if}
        </table>
        </div>
    {/if}
{/strip}