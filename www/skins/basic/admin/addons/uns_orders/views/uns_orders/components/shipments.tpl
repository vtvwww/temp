<hr>
<a name="shipments"></a>
<h3 style="border-bottom: 3px double gray;margin-bottom: 6px;padding-bottom: 2px;">Отгрузки по заказу</h3>
<div style="margin: 10px;">
    {if $documents|is__array}
        <table class="table shipments" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th class=" center">№</th>
                <th class="b1_l center">Дата</th>
                <th class="b1_l center">?</th>
                {*<th class="b1_l center">&nbsp;</th>*}
                <th class="b1_l center">Отгружено, шт</th>
                <th class="b1_l">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$documents item="d" name="doc"}
            <tr>
                {assign var="id" value=$d.document_id}
                {assign var="value" value="document_id"}
                <td id="{$id}" class="center mark_item_clear"><b>{$smarty.foreach.doc.total-$smarty.foreach.doc.iteration+1}</b></td>

                {* Дата *}
                <td class="b1_l"><a name="doc_{$id}"></a>
                    {assign var="date"  value=$d.date|date_format:"%d/%m/%Y"}
                    {include    file="common_templates/table_tools_list.tpl"
                                popup=true
                                id="shipment_`$id`"
                                text="Отгрузка за `$date` (Расходный ордер №`$id`)"
                                act="edit"
                                link_text="`$date` (№`$id`)"
                                href="`$controller`.shipment.update?order_id=`$o.order_id`&`$value`=`$id`&#document_`$id`"
                                prefix=$id
                                edit_onclick="ms(`$id`);"
                                link_class="cm-dialog-auto-size black"
                                tools_list=$smarty.capture.tools_items}
                </td>

                {* Комментарий *}
                <td align="center" class="b1_l">
                    {if strlen($d.comment)}
                        {include file="common_templates/tooltip.tpl" tooltip=$d.comment}
                    {/if}
                </td>

                {* Статус *}
                {*<td class="b1_l">*}
                    {*{if $d.status == "A"}*}
                        {*<img border="0" title="Вкл" src="images/uns/circle_green.png">*}
                    {*{elseif $d.status == "D"}*}
                        {*<img border="0" title="Выкл" src="images/uns/circle_gray.png">*}
                    {*{/if}*}
                {*</td>*}

                {* Перечень *}
                <td class="b1_l">
                    {foreach from=$d.items item="i" name="i"}
                        {if $i.item_type == "D"}
                            {$i.item_info.detail_name}{if $i.item_info.detail_no} [{$i.item_info.detail_no}]{/if}&nbsp;&nbsp;&nbsp;{if $i.change_type == "NEG"}-{/if}{$i.quantity|fn_fvalue} шт.
                        {else}
                             {$i.item_info.p_name}{if $i.item_type == "PF"} на раме{elseif $i.item_type == "PA"} агрегат{/if}&nbsp;&nbsp;&nbsp;{$i.quantity|fn_fvalue} шт.</b>
                        {/if}
                        {if !$smarty.foreach.last}<br>{/if}
                    {foreachelse}
                        <span class="info_warning">Нет данных!</span>
                    {/foreach}
                </td>
                <td class="b1_l" align="center" valign="middle">
                    {assign var="date" value=$d.date|date_format:"%d/%m/%Y"}
                    {include file="uns/buttons/delete.tpl" confirm_message="Удалить отгрузку по текущему счету за `$date`" href="`$controller`.shipment.delete?order_id=`$o.order_id`&`$value`=`$id`"}
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {/if}
</div>