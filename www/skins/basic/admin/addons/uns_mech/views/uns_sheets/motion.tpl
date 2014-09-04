{assign var="m_mode" value=$action}
<form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
    <div class="data-block top">
        <input type="hidden" name="sheet_id" value="{$sheet.sheet_id}" />
        {if $m_mode == 'add'}
            <input type="hidden" name="action"                          value="{$m_mode}" />
            <input type="hidden" name="document_type"                   value="{$document_type}" />
            <input type="hidden" name="document_id"                     value="0" />
            <input type="hidden" name="motion[document][type]"          value="{$document_type}" />

            {assign var="document_type"         value=$document_type}
            {if     $document_type == 10}   {assign var="document_type_name"    value="PVP"}
            {elseif $document_type == 2}    {assign var="document_type_name"    value="MCP"}
            {elseif $document_type == 3}    {assign var="document_type_name"    value="VCP"}
            {elseif $document_type == 12}   {assign var="document_type_name"    value="VCP_COMPLETE"}
            {elseif $document_type == 11}   {assign var="document_type_name"    value="BRAK"}
            {/if}
        {else}
            <input type="hidden" name="document_id"                     value="{$motion.document_id}" />
            <input type="hidden" name="motion[document][document_id]"   value="{$motion.document_id}" />
            {assign var="document_id"           value=$motion.document_id}
            {assign var="document_type"         value=$motion.document_type_info.dt_id}
            {assign var="document_type_name"    value=$motion.document_type_info.type}
        {/if}

        <input type="hidden" name="motion[document][sheet_id]"      value="{$sheet.sheet_id}" />
        <input type="hidden" name="motion[document][package_id]"    value="{$sheet.sheet_id}" />
        <input type="hidden" name="motion[document][package_type]"  value="SL" />

        {include file="addons/uns/views/components/get_form_field.tpl"
            f_id="motion_comment"
            f_type="textarea"
            f_required=false f_integer=false
            f_full_name="motion[document][comment]"
            f_value=$motion.comment
            f_row=1
            f_description="Комментарий"
        }

        <div class="form-field">
            {if $m_mode == 'add'}
                {assign var="motion_date" value=$smarty.now}
            {else}
                {assign var="motion_date" value=$motion.date}
            {/if}
            <label for="motion_date" class="cm-required cm-integer-more-0">Дата и время:</label>
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_id="_motion_date_`$motion.document_id`"
                f_type="date"
                f_required=true
                f_name="motion[document][date]"
                f_value=$motion_date
                button_today=true
                button_yesterday=true
                f_simple=true
            }
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_id="_motion_time_`$motion.document_id`"
                f_type="time"
                f_required=true
                f_name="motion[document][time]"
                f_value=$motion_date
                f_simple=true
            }
        </div>

        <div class="form-field">
            <label for="motion_status" class="cm-required">Статус:</label>
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_type="radio_button"
                f_id="motion_status_`$document_type_name`_`$document_id`"
                f_name="motion[document][status]"
                f_required=true f_integer=false
                f_simple=true
                f_value=$motion.status

                f1_value="A"
                f1_default=true
                f1_title=$lang.active

                f2_value="D"
                f2_title=$lang.disabled
            }
        </div>
    </div>

    {* УПРАВЛЕНИЕ КОЛИЧЕСТВОМ *}
    {include file="common_templates/subheader.tpl" title="РАСХОД"}
    <div class="subheader_block">
        <input type="hidden" name="motion[document][document_type_name]"      value="{$document_type_name}" />
        {assign var="di_index" value="0"}

        {**********************************************************************}
        {* РАСХОД                                                             *}
        {**********************************************************************}
        {if $document_type_name == "PVP"}
            <div class="form-field">
                <label for="object_from" class="cm-required cm-integer-more-0">Откуда:</label>
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="radio_button"
                    f_id="object_from_`$document_type_name`_`$document_id`"
                    f_name="motion[document][object_from]"
                    f_required=true f_integer=false
                    f_simple=true
                    f_value=$motion.object_from

                    f1_value=8
                    f1_default=true
                    f1_title=$objects_plain[8].path
                }
            </div>
            <div class="form-field">
                <label for="material_quantity" class="cm-required">Кол-во:</label>
                <table class="simple">
                    <thead>
                        <tr>
                            <th>Наменование</th>
                            <th>Кол-во</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {if strlen($sheet.material_no)}[{$sheet.material_no}] {/if}{$sheet.material_name}
                                {if is__more_0($motion.movement_items.O[$motion.object_from][$sheet.material_id].di_id)}
                                    <input type="hidden" name="motion[document_items][{$di_index}][di_id]"          value="{$motion.movement_items.O[$motion.object_from][$sheet.material_id].di_id}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][weight]"         value="{$motion.movement_items.O[$motion.object_from][$sheet.material_id].weight}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][item_id]"        value="{$motion.movement_items.O[$motion.object_from][$sheet.material_id].item_id}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][item_type]"      value="{$motion.movement_items.O[$motion.object_from][$sheet.material_id].item_type}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][motion_type]"    value="{$motion.movement_items.O[$motion.object_from][$sheet.material_id].motion_type}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][typesize]"       value="M"/>
                                {else}
                                    <input type="hidden" name="motion[document_items][{$di_index}][di_id]"          value="0"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][weight]"         value="0"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][item_id]"        value="{$sheet.material_id}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][item_type]"      value="M"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][motion_type]"    value="O"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][typesize]"       value="M"/>
                                {/if}
                            </td>
                            <td>
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                            f_type="select_range"
                                            f_name="motion[document_items][`$di_index`][quantity]"
                                            f_id="material_quantity"
                                            f_from=0
                                            f_to=200
                                            f_add_attr="0"
                                            f_value=$motion.movement_items.O[$motion.object_from][$sheet.material_id].quantity
                                            f_simple=true
                                        }
                            </td>
                        </tr>
                    </tbody>
                </table>
                {assign var="di_index" value=$di_index+1}
            </div>
        {elseif ($document_type_name == "BRAK") or ($document_type_name == "VCP") or ($document_type_name == "VCP_COMPLETE") or ($document_type_name == "MCP")}
            {assign var="f1__default" value=true}
            {assign var="f2__default" value=false}
            {if $sheet.target_object == 14}
                {assign var="f1__default" value=false}
                {assign var="f2__default" value=true}
            {/if}
            <div class="form-field">
                <label for="object_from" class="cm-required cm-integer-more-0">Откуда:</label>
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="radio_button"
                    f_id="object_from_`$document_type_name`_`$document_id`"
                    f_name="motion[document][object_from]"
                    f_required=true f_integer=false
                    f_simple=true
                    f_value=$motion.object_from

                    f1_default=$f1__default
                    f1_value=10
                    f1_title=$objects_plain[10].path

                    f2_default=$f2__default
                    f2_value=14
                    f2_title=$objects_plain[14].path
                }
            </div>

            <div class="form-field">
                <label for="detail_quantity" class="cm-required">Кол-во:</label>
                {assign var="object_to" value=$sheet.object_to}
                <table class="simple">
                    <thead>
                        <tr>
                            <th>Наменование</th>
                            <th>Кол-во</th>
                            {if $document_type_name == "BRAK" or $document_type_name == "VCP" or $document_type_name == "VCP_COMPLETE" or $document_type_name == "MCP"}
                            <th>{include file="common_templates/tooltip.tpl" tooltip="<u><b>Статус обработки:</b></u><br><br><b>Обр.</b> - деталь пока еще обрабатывается;<br><b>Зав.</b> - деталь уже обработана;" tooltip_mark="Статус"}</th>
                            {/if}
                        </tr>
                    </thead>
                    <tbody>
                    {foreach from=$sheet.details item="d" key="k" name="d"}
                        <tr>
                            <td> {*Наименование*}
                                {if strlen($d.detail_no)}[{$d.detail_no}] {/if}{$d.detail_name}
                                {if is__more_0($motion.movement_items.O[$motion.object_from][$k].di_id)}
                                    <input type="hidden" name="motion[document_items][{$di_index}][di_id]"          value="{$motion.movement_items.O[$motion.object_from][$k].di_id}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][weight]"         value="{$motion.movement_items.O[$motion.object_from][$k].weight}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][item_id]"        value="{$motion.movement_items.O[$motion.object_from][$k].item_id}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][item_type]"      value="{$motion.movement_items.O[$motion.object_from][$k].item_type}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][motion_type]"    value="{$motion.movement_items.O[$motion.object_from][$k].motion_type}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][typesize]"       value="M"/>
                                {else}
                                    <input type="hidden" name="motion[document_items][{$di_index}][di_id]"          value="0"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][weight]"         value="{$sheet.details[$k].weight}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][item_id]"        value="{$sheet.details[$k].detail_id}"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][item_type]"      value="D"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][motion_type]"    value="O"/>
                                    <input type="hidden" name="motion[document_items][{$di_index}][typesize]"       value="M"/>
                                {/if}
                            </td>
                            <td> {*Кол-во*}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_type="select_range"
                                    f_name="motion[document_items][`$di_index`][quantity]"
                                    f_id="detail_quantity"
                                    f_from=0
                                    f_to=200
                                    f_add_attr=$smarty.foreach.d.index
                                    f_value=$motion.movement_items.O[$motion.object_from][$k].quantity
                                    f_simple=true
                                }
                            </td>
                            {if $document_type_name == "BRAK" or $document_type_name == "VCP" or $document_type_name == "VCP_COMPLETE" or $document_type_name == "MCP"}
                            <td>{*Статус обработки*}
                                <select name="motion[document_items][{$di_index}][processing]" add_attr="{$smarty.foreach.d.index}">
                                    <option {if $motion.movement_items.O[$motion.object_from][$k].processing == "P"}selected="selected"{/if} value="P">Обр.</option>
                                    <option {if $motion.movement_items.O[$motion.object_from][$k].processing == "C"}selected="selected"{/if} value="C">Зав.</option>
                                </select>
                            </td>
                            {/if}
                        </tr>
                        {assign var="di_index" value=$di_index+1}
                    {/foreach}
                    </tbody>
                </table>
            </div>
        {/if}
    </div>


    {**********************************************************************}
    {* ПРИХОД                                                             *}
    {**********************************************************************}
    {include file="common_templates/subheader.tpl" title="ПРИХОД"}
    <div class="subheader_block">
        {if ($document_type_name == "PVP") or ($document_type_name == "BRAK") or ($document_type_name == "VCP_COMPLETE") or ($document_type_name == "VCP") or ($document_type_name == "MCP")}
            <div class="form-field">
                <label for="object_to" class="cm-required cm-integer-more-0">Куда:</label>
                {if ($document_type_name == "PVP") or ($document_type_name == "VCP") or ($document_type_name == "VCP_COMPLETE")}
                    {assign var="f1__default" value=true}
                    {assign var="f2__default" value=false}
                    {if ($smarty.request.o_id == 14 and $document_type_name == "PVP") or ($sheet.target_object == 10 and $document_type_name == "VCP") or ($sheet.target_object == 14 and $document_type_name == "VCP_COMPLETE")}
                        {assign var="f1__default" value=false}
                        {assign var="f2__default" value=true}
                    {/if}

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="radio_button"
                        f_id="object_to_`$document_type_name`_`$document_id`"
                        f_name="motion[document][object_to]"
                        f_required=true f_integer=false
                        f_simple=true
                        f_value=$motion.object_to

                        f1_default=$f1__default
                        f1_value=10
                        f1_title=$objects_plain[10].path

                        f2_default=$f2__default
                        f2_value=14
                        f2_title=$objects_plain[14].path
                    }
                {elseif $document_type_name == "BRAK"}
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="radio_button"
                        f_id="object_to_`$document_type_name`_`$document_id`"
                        f_name="motion[document][object_to]"
                        f_required=true f_integer=false
                        f_simple=true
                        f_value=$motion.object_to

                        f1_default=false
                        f1_value=21
                        f1_title=$objects_plain[21].path

                        f2_default=true
                        f2_value=22
                        f2_title=$objects_plain[22].path
                    }
                {elseif $document_type_name == "MCP"}
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="radio_button"
                        f_id="object_to_`$document_type_name`_`$document_id`"
                        f_name="motion[document][object_to]"
                        f_required=true f_integer=false
                        f_simple=true
                        f_value=$motion.object_to

                        f1_default=true
                        f1_value=17
                        f1_title=$objects_plain[17].path

                        f2_default=false
                        f2_value=18
                        f2_title=$objects_plain[18].path

                        f3_default=false
                        f3_value=19
                        f3_title=$objects_plain[19].path
                    }
                {/if}
            </div>

            <div class="form-field">
                <label for="detail_quantity" class="cm-required">Кол-во:</label>
                {assign var="object_to" value=$sheet.object_to}
                <table class="simple">
                    <thead>
                        <tr>
                            <th>Наменование</th>
                            <th>Кол-во</th>
                            {if $document_type_name == "PVP" or $document_type_name == "VCP" or $document_type_name == "VCP_COMPLETE"}
                            <th>{include file="common_templates/tooltip.tpl" tooltip="<u><b>Статус обработки:</b></u><br><br><b>Обр.</b> - деталь пока еще обрабатывается;<br><b>Зав.</b> - деталь уже обработана;" tooltip_mark="Статус"}</th>
                            {/if}
                        </tr>
                    </thead>
                    <tbody>
                    {foreach from=$sheet.details item="d" key="k" name="d"}
                        <tr>
                            <td> {*Наименование*}
                                {if strlen($d.detail_no)}[{$d.detail_no}] {/if}{$d.detail_name}
                                {if is__more_0($motion.movement_items.I[$motion.object_to][$k].di_id)}
                                   <input type="hidden" name="motion[document_items][{$di_index}][di_id]"          value="{$motion.movement_items.I[$motion.object_to][$k].di_id}"/>
                                   <input type="hidden" name="motion[document_items][{$di_index}][weight]"         value="{$motion.movement_items.I[$motion.object_to][$k].weight}"/>
                                   <input type="hidden" name="motion[document_items][{$di_index}][item_id]"        value="{$motion.movement_items.I[$motion.object_to][$k].item_id}"/>
                                   <input type="hidden" name="motion[document_items][{$di_index}][item_type]"      value="{$motion.movement_items.I[$motion.object_to][$k].item_type}"/>
                                   <input type="hidden" name="motion[document_items][{$di_index}][motion_type]"    value="{$motion.movement_items.I[$motion.object_to][$k].motion_type}"/>
                                   <input type="hidden" name="motion[document_items][{$di_index}][typesize]"       value="M"/>
                                {else}
                                   <input type="hidden" name="motion[document_items][{$di_index}][di_id]"          value="0"/>
                                   <input type="hidden" name="motion[document_items][{$di_index}][weight]"         value="{$sheet.details[$k].weight}"/>
                                   <input type="hidden" name="motion[document_items][{$di_index}][item_id]"        value="{$sheet.details[$k].detail_id}"/>
                                   <input type="hidden" name="motion[document_items][{$di_index}][item_type]"      value="D"/>
                                   <input type="hidden" name="motion[document_items][{$di_index}][motion_type]"    value="I"/>
                                   <input type="hidden" name="motion[document_items][{$di_index}][typesize]"       value="M"/>
                                {/if}
                            </td>
                            <td> {*Кол-во*}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_range"
                                f_name="motion[document_items][`$di_index`][quantity]"
                                f_id="detail_quantity"
                                f_from=0
                                f_to=200
                                f_add_attr=$smarty.foreach.d.index
                                f_value=$motion.movement_items.I[$motion.object_to][$k].quantity
                                f_simple=true
                                }
                            </td>
                            {if $document_type_name == "PVP" or $document_type_name == "VCP" or $document_type_name == "VCP_COMPLETE"}
                            <td>{*Статус обработки*}
                                <select name="motion[document_items][{$di_index}][processing]" add_attr="{$smarty.foreach.d.index}">
                                    <option {if $motion.movement_items.I[$motion.object_to][$k].processing == "P"}selected="selected"{/if} value="P">Обр.</option>
                                    <option {if $motion.movement_items.I[$motion.object_to][$k].processing == "C"}selected="selected"{/if} value="C">Зав.</option>
                                </select>
                            </td>
                            {/if}
                        </tr>
                        {assign var="di_index" value=$di_index+1}
                    {/foreach}
                    </tbody>
                </table>
            </div>
        {/if}
    </div>
    {* ВОЗМОЖНОСТЬ БЫСТРОГО ИЗМЕНЕНИЯ СТАТУСА СЛ*}
    {if $document_type_name == "MCP" or $document_type_name == "VCP_COMPLETE"}
        {assign var="smarty_now" value=$smarty.now}
        <div style="float: left; margin-right: 15px;">
            {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="checkbox"
            f_name="sheet[change_status]"
            f_id="change_status_`$smarty_now`"
            f_label="Изменить статус Сопровод. листа"
            f_simple=true
            f_onchange="if ($('#change_status_`$smarty_now`').attr('checked')) $('#div_change_status_`$smarty.now`').removeClass('hidden'); if (!$('#change_status_`$smarty_now`').attr('checked')) $('#div_change_status_`$smarty.now`').addClass('hidden');"
            }
        </div>
        <div id="div_change_status_{$smarty_now}" class="hidden">
            {include file="addons/uns/views/components/get_form_field.tpl"
            f_type="radio_button"
            f_id="sheet_status_`$smarty_now`"
            f_name="sheet[status]"
            f_required=true
            f_simple=true

            f1_value="OP"
            f1_title="Открыт"

            f2_value="PARTIALLYCL"
            f2_title="Частично закрыт"

            f3_default=true
            f3_value="CL"
            f3_title="Закрыт"
        }
        </div>
    {/if}

    <br>
    <div class="buttons-container cm-toggle-button buttons-bg">
        {if $m_mode == "add"}
            {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.motion.update]" hide_second_button=true mode=$m_mode}
        {else}
            {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.motion.update]" hide_second_button=true mode=$m_mode}
        {/if}
    </div>
</form>




