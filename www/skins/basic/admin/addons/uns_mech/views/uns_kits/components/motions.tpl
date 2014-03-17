{include file="common_templates/subheader.tpl" title="Движения"}
<div class="subheader_block">

&nbsp;
<span class="action-add">
    {include    file="common_templates/table_tools_list.tpl"
                popup=true
                id="add_MCP"
                text="Отложить на Сб.Уч. [2]"
                act="edit"
                link_text="Отложить на Сб.Уч. [2]"
                href="`$controller`.motion.add?document_type=2&kit_id=`$kit.kit_id`"
                link_class="cm-dialog-auto-size"
                tools_list=$smarty.capture.tools_items}
</span>
&nbsp;&nbsp;&nbsp;
<span class="action-add">
    {include    file="common_templates/table_tools_list.tpl"
                popup=true
                id="add_BRAK"
                text="Списать в брак [11]"
                act="edit"
                link_text="Списать в брак [11]"
                href="`$controller`.motion.add?document_type=11&kit_id=`$kit.kit_id`"
                link_class="cm-dialog-auto-size"
                tools_list=$smarty.capture.tools_items}
</span>
&nbsp;&nbsp;&nbsp;
<span class="action-add">
    {include    file="common_templates/table_tools_list.tpl"
                popup=true
                id="add_AIO"
                text="Акт изменения отстатка [8]"
                act="edit"
                link_text="Акт изменения отстатка [8]"
                href="`$controller`.motion.add?document_type=8&kit_id=`$kit.kit_id`"
                link_class="cm-dialog-auto-size"
                tools_list=$smarty.capture.tools_items}
</span>
{if $kit.kit_type == "D"}
    &nbsp;&nbsp;&nbsp;
    <span class="action-add">
        {include    file="common_templates/table_tools_list.tpl"
                    popup=true
                    id="add_VD"
                    text="Выпуск деталей [14]"
                    act="edit"
                    link_text="Выпуск деталей [14]"
                    href="`$controller`.motion.add?document_type=14&kit_id=`$kit.kit_id`"
                    link_class="cm-dialog-auto-size"
                    tools_list=$smarty.capture.tools_items}
    </span>
{else}
    &nbsp;&nbsp;&nbsp;
    <span class="action-add">
        {include    file="common_templates/table_tools_list.tpl"
                    popup=true
                    id="add_VN"
                    text="Выпуск насосов [13]"
                    act="edit"
                    link_text="Выпуск насосов [13]"
                    href="`$controller`.motion.add?document_type=13&kit_id=`$kit.kit_id`"
                    link_class="cm-dialog-auto-size"
                    tools_list=$smarty.capture.tools_items}
    </span>
{/if}
<br>

<div style="margin: 10px;">
    <a name="motions"></a>
    {if $documents|is__array}
        <table class="table" cellspacing="0" cellpadding="0" border="0">
        <thead>
            <tr>
                <th class="b1_r center">№</th>
                <th class="b1_r center">Тип</th>
                <th class="b1_r center">Дата</th>
                <th class="b1_r center">?</th>
                <th class="b1_r center"></th>
                <th class="b1_r center">Откуда/Куда</th>
                <th class="b1_r center">Деталь/Кол-во</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$documents item="d" name="doc"}
            <tr>
                {assign var="id" value=$d.document_id}
                {assign var="value" value="document_id"}
                <td align="center" class="b1_r"><b>{$smarty.foreach.doc.iteration}</b></td>

                {* Тип *}
                <td class="b1_r"><a name="document_{$id}"></a>
                    {include    file="common_templates/table_tools_list.tpl"
                                popup=true
                                id="`$id`"
                                text=$d.document_type_info.name
                                act="edit"
                                link_text="`$d.document_type_info.name` (`$id`)"
                                href="`$controller`.motion.update?kit_id=`$kit.kit_id`&`$value`=`$id`&document_type=`$d.type`#document_`$id`"
                                prefix=$id
                                link_class="cm-dialog-auto-size black"
                                tools_list=$smarty.capture.tools_items}

                </td>

                {* Дата *}
                <td align="center" class="b1_r">
                    {$d.date|date_format:"%d/%m/%y %H:%S"}
                </td>

                {* Комментарий *}
                <td align="center" class="b1_r">
                    {if strlen($d.comment)}
                        {include file="common_templates/tooltip.tpl" tooltip=$d.comment}
                    {/if}
                </td>

                {* Статус *}
                <td class="b1_r">
                    {if $d.status == "A"}
                        <img border="0" title="Вкл" src="images/uns/circle_green.png">
                    {elseif $d.status == "D"}
                        <img border="0" title="Выкл" src="images/uns/circle_gray.png">
                    {/if}
                </td>

                {* Откуда/Куда *}
                <td class="b1_r">
                    {$d.objects_info.object_from.path}<br>{$d.objects_info.object_to.path}
                </td>

                {* Детали *}
                <td class="b1_r">
                    {foreach from=$d.items item="i" name="i"}
                        {if $i.item_type != "P" and $i.item_type != "PF"}
                            {$i.item_info.detail_name}{if $i.item_info.detail_no} [{$i.item_info.detail_no}]{/if}&nbsp;&nbsp;&nbsp;{if $i.change_type == "NEG"}-{/if}{$i.quantity|fn_fvalue} шт.
                        {else}
                            {if $i.item_type == "P" or $i.item_type == "PF"}
                                <hr>
                                <b>{$pumps_simple[$i.item_id].p_name}{if $i.item_type == "PF"} на раме{/if} - {$i.quantity|fn_fvalue} шт.</b>
                            {/if}
                        {/if}
                        {if !$smarty.foreach.last}<br>{/if}
                    {/foreach}
                </td>

                <td>{include file="uns/buttons/delete.tpl" href="`$controller`.motion_delete?kit_id=`$kit.kit_id`&`$value`=`$id`"}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    {/if}
</div>
</div>
