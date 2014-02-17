{include file="common_templates/subheader.tpl" title="Движения по Сопроводительному листу"}
<div class="subheader_block">

    <a name="motions"></a>
    {literal}
        <style type="text/css">
            table.uns_table{
                border-collapse: collapse;
            }

            table.uns_table tr th{
                border: 1px solid #000000;
                padding: 2px 5px;
            }

            table.uns_table tr td{
                border: 1px solid #000000;
                padding: 2px 5px;
            }


        </style>
    {/literal}
    &nbsp;
    <span class="action-add">
            {include    file="common_templates/table_tools_list.tpl"
                        popup=true
                        id="add_PVP"
                        text="Передать в производство [10]"
                        act="edit"
                        link_text="В производство"
                        href="`$controller`.motion.add?document_type=10&sheet_id=`$sheet.sheet_id`"
                        link_class="cm-dialog-auto-size"
                        tools_list=$smarty.capture.tools_items}
    </span>
    &nbsp;&nbsp;
    <span class="action-add">
            {include    file="common_templates/table_tools_list.tpl"
                    popup=true
                    id="add_BRAK"
                    text="В брак [11]"
                    act="edit"
                    link_text="В брак"
                    href="`$controller`.motion.add?document_type=11&sheet_id=`$sheet.sheet_id`"
                    link_class="cm-dialog-auto-size"
                    tools_list=$smarty.capture.tools_items}</li>
    </span>
    &nbsp;&nbsp;
    <span class="action-add">
            {include    file="common_templates/table_tools_list.tpl"
                    popup=true
                    id="add_VCP"
                    text="Добавить перемещение МЦ1 <=> МЦ2 [3]"
                    act="edit"
                    link_text="МЦ1 <=> МЦ2"
                    href="`$controller`.motion.add?document_type=3&sheet_id=`$sheet.sheet_id`"
                    link_class="cm-dialog-auto-size"
                    tools_list=$smarty.capture.tools_items}</li>
    </span>
    &nbsp;&nbsp;
    <span class="action-add">
            {include    file="common_templates/table_tools_list.tpl"
                    popup=true
                    id="add_VCP_COMPLETE"
                    text="Завершение обработки [12]"
                    act="edit"
                    link_text="Завершено"
                    href="`$controller`.motion.add?document_type=12&sheet_id=`$sheet.sheet_id`"
                    link_class="cm-dialog-auto-size"
                    tools_list=$smarty.capture.tools_items}</li>
    </span>
    &nbsp;&nbsp;
    <span class="action-add">
            {include    file="common_templates/table_tools_list.tpl"
                    popup=true
                    id="add_MCP"
                    text="Сдать на Склад Комплектующих [2]"
                    act="edit"
                    link_text="Сдать на Скл. КМП"
                    href="`$controller`.motion.add?document_type=2&sheet_id=`$sheet.sheet_id`"
                    link_class="cm-dialog-auto-size"
                    tools_list=$smarty.capture.tools_items}</li>
    </span>

    <br>
    <br>

    <table class="uns_table sheet_motions">
        <thead>
            <tr>
                <th rowspan="3" style="text-align: center; text-transform: uppercase;">&nbsp;</th>
                <th rowspan="3" style="text-align: center; text-transform: uppercase;">Тип</th>
                <th rowspan="3" style="text-align: center; text-transform: uppercase;">&nbsp;</th>
                <th rowspan="3" style="text-align: center; text-transform: uppercase;">Дата</th>
                <th colspan="4" style="text-align: center; text-transform: uppercase;">Цех механической обработки</th>
                <th rowspan="3" style="text-align: center; text-transform: uppercase;">Итого</th>
                <th colspan="4" style="text-align: center; text-transform: uppercase;">Брак</th>
                <th rowspan="3" style="text-align: center; text-transform: uppercase;">&nbsp;</th>
            </tr>
            <tr>
                <th colspan="2">Цех №1</th>
                <th colspan="2">Цех №2</th>
                <th colspan="2">Отжиг</th>
                <th colspan="2">Переплавка</th>
            </tr>
            <tr>
                <th><-></th>
                <th>Ост.</th>
                <th><-></th>
                <th>Ост.</th>
                <th><-></th>
                <th>Ост.</th>
                <th><-></th>
                <th>Ост.</th>
            </tr>
        </thead>
        {if is__array($motions)}
            {foreach from=$motions key="m_id" item="m" name="m"}
                <tbody>
                {assign var="name_item" value="motions[`$m_id`]"}
                    <tr>
                        <td align="center">
                            {if $m.status == "A"}
                                <img border="0" title="Вкл" src="skins/basic/admin/addons/uns_acc/images/circle_green.png">
                            {elseif $m.status == "D"}
                                <img border="0" title="Выкл" src="skins/basic/admin/addons/uns_acc/images/circle_gray.png">
                            {/if}
                        </td>
                        <td{* align="center"*}>
                            {if $m.document_type_info.type == "PVP"}
                                {assign var="doc_name" value="В производство"}
                            {elseif $m.document_type_info.type == "MCP"}
                                {assign var="doc_name" value="Сдать на Скл. КМП"}
                            {elseif $m.document_type_info.type == "BRAK"}
                                {assign var="doc_name" value="В брак"}
                            {elseif $m.document_type_info.type == "VCP"}
                                {if $m.object_from == 10 and $m.object_to == 14}
                                    {assign var="doc_name" value="МЦ1 => МЦ2"}
                                {elseif $m.object_from == 14 and $m.object_to == 10}
                                    {assign var="doc_name" value="МЦ1 <= МЦ2"}
                                {else}
                                    {assign var="doc_name" value="МЦ1 < ???? > МЦ2 - ОШИБКА!"}
                                {/if}
                            {elseif $m.document_type_info.type == "VCP_COMPLETE"}
                                {assign var="doc_name" value="Завершено"}
                            {/if}

                            {if $m.status == "D"}
                                {assign var="doc_name" value="<span class='bold status_D zero' title='`$m.comment`'>`$doc_name` [`$m_id`]</span>"}
                            {else}
                                {assign var="doc_name" value="<span class='bold' title='`$m.comment`'>`$doc_name` [`$m_id`]</span>"}
                            {/if}
                            {include    file="common_templates/table_tools_list.tpl"
                                        popup=true
                                        id="`$m_id`"
                                        text="`$m.document_type_info.name`"
                                        act="edit"
                                        link_text=$doc_name
                                        href="`$controller`.motion.update?document_id=`$m_id`&sheet_id=`$sheet.sheet_id`"
                                        link_class="cm-dialog-auto-size black"
                                        tools_list=$smarty.capture.tools_items}
                        </td>
                        <td align="center">
                            {if strlen($m.comment)}
                                {include file="common_templates/tooltip.tpl" tooltip=$m.comment}
                            {/if}
                        </td>
                        <td align="center"> {* ДАТА *}
                            <span class="{if $m.status == "D"}status_D zero{/if} monospace">{$m.date|date_format:"%d/%m/%y"}&nbsp;{$m.date|date_format:"%H:%M"}</span>
                        </td>
                        <td align="center"> {*10 мех.цех 1*}
                            <span class="{if $m.status == "D"}status_D zero{/if} monospace {if $m.calc_movement_items.motion[10].total|array_sum == 0}zero{/if}">{$m.calc_movement_items.motion[10].total_str|html_entity_decode}</span>
                        </td>
                        <td align="center"> {*10 мех.цех 1*}
                            <span class="{if $m.status == "D"}status_D zero{/if} monospace bold {if $m.calc_movement_items.balance[10].total|array_sum == 0}zero{/if}">{if $m.status == "A"}{$m.calc_movement_items.balance[10].total_str}{/if}</span>
                        </td>
                        <td align="center"> {*14 мех.цех 2*}
                            <span class="{if $m.status == "D"}status_D zero{/if} monospace {if $m.calc_movement_items.motion[14].total|array_sum == 0}zero{/if}">{$m.calc_movement_items.motion[14].total_str|html_entity_decode}</span>
                        </td>
                        <td align="center"> {*14 мех.цех 2*}
                            <span class="{if $m.status == "D"}status_D zero{/if} monospace bold {if $m.calc_movement_items.balance[14].total|array_sum == 0}zero{/if}">{if $m.status == "A"}{$m.calc_movement_items.balance[14].total_str}{/if}</span>
                        </td>
                        <td align="center" style="background-color: #d3d3d3;">
                            {*ИТОГО*}
                            <span class="{if $m.status == "D"}brak{/if} monospace bold {if $m.calc_movement_items.balance.total|array_sum == 0}zero{/if}">{if $m.status == "A"}{$m.calc_movement_items.balance.total_str}{/if}</span>
                        </td>
                        <td align="center">
                            {*21 Брак отжиг*}
                            <span class="{if $m.status == "D"}status_D zero{/if} monospace {if $m.calc_movement_items.motion[21].total|array_sum == 0}zero{/if}">{$m.calc_movement_items.motion[21].total_str|html_entity_decode}</span>
                        </td>
                        <td align="center">
                            {*21 Брак отжиг*}
                            <span class="{if $m.status == "D"}status_D zero{/if} monospace bold {if $m.calc_movement_items.balance[21].total|array_sum == 0}zero{/if}">{if $m.status == "A"}{$m.calc_movement_items.balance[21].total_str}{/if}</span>
                        </td>
                        <td align="center">
                            {*22 Брак переплавка*}
                            <span class="{if $m.status == "D"}status_D zero{/if} monospace {if $m.calc_movement_items.motion[22].total|array_sum == 0}zero{/if}">{$m.calc_movement_items.motion[22].total_str|html_entity_decode}</span>
                        </td>
                        <td align="center">
                            {*22 Брак переплавка*}
                            <span class="{if $m.status == "D"}status_D zero{/if} monospace bold {if $m.calc_movement_items.balance[22].total|array_sum == 0}zero{/if}">{if $m.status == "A"}{$m.calc_movement_items.balance[22].total_str}{/if}</span>
                        </td>
                        <td>
                            <a class="cm-confirm block" href="{"`$controller`.motion.delete?document_id=`$m_id`&sheet_id=`$sheet.sheet_id`#motions"|fn_url}">
                                <img border="0" src="skins/basic/admin/addons/uns_acc/images/delete.png">
                            </a>
                        </td>
                    </tr>
                </tbody>
            {/foreach}
        {/if}
    </table>
</div>
