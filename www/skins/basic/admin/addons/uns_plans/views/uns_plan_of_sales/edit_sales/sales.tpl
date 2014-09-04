<table class="table" cellspacing="0" cellpadding="0" border="0">
    <thead>
        <tr style="background-color: #EDEDED;">
            <th style="text-transform: none;" class="center">№</th>
            <th style="text-transform: none;" class="center b1_l">Клиент</th>
            <th style="text-transform: none;" class="center b1_l">Дата</th>
            <th style="text-transform: none;" class="center b1_l">Наименование</th>
            <th style="text-transform: none;" class="center b1_l">Кол-во</th>
        </tr>
    </thead>
    <tbody>
        {assign var="total_q" value=0}
        {foreach from=$actual_sales item='s' name="s"}
            {foreach from=$s.items item='i'}
                <tr>
                    <td class="center">{$smarty.foreach.s.iteration}</td>
                    <td class="center b1_l">{$s.customer_short_name}</td>
                    <td class="center b1_l">{$s.date|fn_parse_date|date_format:"%a %d/%m/%Y"}</td>
                    <td class="b1_l">{$pumps[$i.item_id].p_name}{if $i.item_type == "PF"} на раме{elseif $i.item_type == "PA"} агрегат{/if}</td>
                    <td class="center b1_l">{$i.quantity|fn_fvalue}</td>
                    {assign var="total_q" value=$total_q+$i.quantity}
                </tr>
                {/foreach}
        {/foreach}
    </tbody>
    <tbody>
        <tr style="background-color: #EDEDED;">
            <td style="background-color: #EDEDED;" colspan="4" align="right" class="bold">ИТОГО:</td>
            <td style="background-color: #EDEDED;" class="center b1_l bold">{$total_q|fn_fvalue}</td>
        </tr>
    </tbody>
</table>