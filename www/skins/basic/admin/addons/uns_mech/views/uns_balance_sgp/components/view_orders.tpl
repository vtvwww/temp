{strip}
    {if $orders|is__array}
        {include file="common_templates/subheader.tpl" title="Заказы"}
        <div class="subheader_block">

        <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
                <tr>
                    <th>№</th>
                    <th style="text-align: center; border-left: 1px solid #808080;">Дата отгрузки</th>
                    <th style="text-align: center; border-left: 1px solid #808080;">Регион</th>
                    <th style="text-align: center; border-left: 1px solid #808080;">Комментарий</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$orders item="o"}
                <tr>
                    <td style="text-align: center;">{$o.order_id}</td>
                    <td style="border-left: 1px solid #808080;">{$o.date_finished|date_format:"%a %d/%m/%y"} (осталось {$o.remaining_time} дней)</td>
                    <td style="border-left: 1px solid #808080;"><b>{$regions[$o.region_id].name_short}</b> - {$regions[$o.region_id].name}</td>
                    <td style="border-left: 1px solid #808080;">{$o.comment}</td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        </div>
    {/if}
{/strip}