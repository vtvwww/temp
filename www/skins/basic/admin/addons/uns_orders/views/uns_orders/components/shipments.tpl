{include file="common_templates/subheader.tpl" title="Отгрузки по заказу"}
<div class="subheader_block">

&nbsp;
<span class="action-add">
    {include    file="common_templates/table_tools_list.tpl"
                popup=true
                id="add_MCP"
                text="Отгрузка по Заказу `$o.order_id`"
                act="edit"
                link_text="Добавить Отгрузку (Расходный ордер)"
                href="`$controller`.shipment.add?order_id=`$o.order_id`"
                link_class="cm-dialog-auto-size"
                tools_list=$smarty.capture.tools_items}
</span>

<br>

<div style="margin: 10px;">
    <a name="shipments"></a>
    {if $documents|is__array}
        <table class="table" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th class=" center">№</th>
                <th class="b1_l center">Дата</th>
                <th class="b1_l center">?</th>
                <th class="b1_l center">&nbsp;</th>
                <th class="b1_l center">Перечень</th>
                <th class="b1_l">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$documents item="d" name="doc"}
            <tr>
                {assign var="id" value=$d.document_id}
                {assign var="value" value="document_id"}
                <td align="center"><b>{$smarty.foreach.doc.iteration}</b></td>

                {* Дата *}
                <td class="b1_l"><a name="document_{$id}"></a>
                    {include    file="common_templates/table_tools_list.tpl"
                                popup=true
                                id="`$id`"
                                text="Отгрузка (Расходный ордер) "
                                act="edit"
                                link_text=$d.date|date_format:"%d/%m/%Y"
                                href="`$controller`.shipment.update?order_id=`$o.order_id`&`$value`=`$id`&document_type=`$d.type`#document_`$id`"
                                prefix=$id
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
                <td class="b1_l">
                    {if $d.status == "A"}
                        <img border="0" title="Вкл" src="images/uns/circle_green.png">
                    {elseif $d.status == "D"}
                        <img border="0" title="Выкл" src="images/uns/circle_gray.png">
                    {/if}
                </td>

                {* Перечень *}
                <td class="b1_l">
                    {foreach from=$d.items item="i" name="i"}
                        {if $i.item_type == "D"}
                            {$i.item_info.detail_name}{if $i.item_info.detail_no} [{$i.item_info.detail_no}]{/if}&nbsp;&nbsp;&nbsp;{if $i.change_type == "NEG"}-{/if}{$i.quantity|fn_fvalue} шт.
                        {else}
                             {$i.item_info.p_name}{if $i.item_type == "PF"} на раме{elseif $i.item_type == "PA"} агрегат{/if}&nbsp;&nbsp;&nbsp;{$i.quantity|fn_fvalue} шт.</b>
                        {/if}
                        {if !$smarty.foreach.last}<br>{/if}
                    {/foreach}
                </td>
                <td class="b1_l">{include file="uns/buttons/delete.tpl" href="`$controller`.motion_delete?kit_id=`$kit.kit_id`&`$value`=`$id`"}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {/if}
</div>
</div>
