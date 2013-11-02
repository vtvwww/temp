{assign var="m_mode" value=$action}
<form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
    <div class="data-block top">
        <input type="hidden" name="kit_id" value="{$kit.kit_id}" />
        {if $m_mode == 'add'}
            <input type="hidden" name="action"                          value="{$m_mode}" />
            <input type="hidden" name="document_type"                   value="{$document_type}" />
            <input type="hidden" name="document_id"                     value="0" />
            <input type="hidden" name="motion[document][type]"          value="{$document_type}" />
            <input type="hidden" name="motion[document][package_id]"    value="{$kit.kit_id}" />
            <input type="hidden" name="motion[document][package_type]"  value="PN" />
        {else}
            <input type="hidden" name="document_id"                     value="{$motion.document_id}" />
            <input type="hidden" name="motion[document][document_id]"   value="{$motion.document_id}" />
            <input type="hidden" name="motion[document][package_id]"    value="{$motion.package_id}" />
            <input type="hidden" name="motion[document][package_type]"  value="{$motion.package_type}" />
        {/if}

        {if $m_mode == 'add'}
            {assign var="document_id"           value=0}
            {assign var="document_type"         value=$document_type}
            {if     $document_type == 10}   {assign var="document_type_name"    value="PVP"}
            {elseif $document_type == 2}    {assign var="document_type_name"    value="MCP"}
            {elseif $document_type == 3}    {assign var="document_type_name"    value="VCP"}
            {elseif $document_type == 12}   {assign var="document_type_name"    value="VCP_COMPLETE"}
            {elseif $document_type == 11}   {assign var="document_type_name"    value="BRAK"}
            {elseif $document_type == 8}    {assign var="document_type_name"    value="AIO"}
            {elseif $document_type == 13}   {assign var="document_type_name"    value="VN"}
            {elseif $document_type == 14}   {assign var="document_type_name"    value="VD"}
            {/if}
        {else}
            {assign var="document_id"           value=$motion.document_id}
            {assign var="document_type"         value=$motion.document_type_info.dt_id}
            {assign var="document_type_name"    value=$motion.document_type_info.type}
        {/if}

        {include file="addons/uns/views/components/get_form_field.tpl"
            f_id="motion_comment"
            f_type="textarea"
            f_required=false f_integer=false
            f_full_name="motion[document][comment]"
            f_value=$motion.comment
            f_row=1
            f_description="Комментарий"
        }

        {* ДАТА *}
        <div class="form-field">
            {if $m_mode == 'add'}
                {assign var="motion_date" value=$smarty.const.TIME}
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

        {* СТАТУС *}
        <div class="form-field">
            <label for="motion_status" class="cm-required">Статус:</label>
            {include file="addons/uns/views/components/get_form_field.tpl"
                f_id="motion_status"
                f_type="status"
                f_required=true f_integer=false
                f_name="motion[document][status]"
                f_value=$motion.status
                f_simple=true
            }
        </div>

        {* ОТКУДА *}
        {if $document_type_name != "AIO"}
        <div class="form-field">
            <label for="object_from" class="cm-required cm-integer-more-0">Откуда:</label>
            <select name="motion[document][object_from]" id="object_from">
                {if ($document_type_name == "MCP")}
                    <option {if $motion.object_from == 10}selected="selected"{/if} value="10">ЦЕХ Мех. обр. - №1</option>
                    <option {if $motion.object_from == 14}selected="selected"{/if} value="14">ЦЕХ Мех. обр. - №2</option>
                    <option {if $motion.object_from == 17}selected="selected"{/if} value="17">Склад комплектующих</option>
                {elseif ($document_type_name == "BRAK" or $document_type_name == "VD" or $document_type_name == "VN")}
                    <option {if $motion.object_from == 18}selected="selected"{/if} value="18">Сборочный участок</option>
                {/if}
            </select>
        </div>
        {/if}

        {* КУДА *}
        <div class="form-field">
            <label for="object_from" class="cm-required cm-integer-more-0">Куда:</label>
            <select name="motion[document][object_to]" id="object_to">
                {if ($document_type_name == "MCP" or $document_type_name == "AIO")}
                    <option {if $motion.object_to == 18}selected="selected"{/if} value="18">Сборочный участок</option>
                {elseif ($document_type_name == "BRAK")}
                    <option {if $motion.object_to == 21}selected="selected"{/if} value="21">Брак на отжиг</option>
                    <option {if $motion.object_to == 22}selected="selected"{/if} value="22">Брак на переплавку</option>
                {elseif ($document_type_name == "VD" or $document_type_name == "VN")}
                    <option {if $motion.object_to == 19}selected="selected"{/if} value="19">Склад Готовой Продукции</option>
                {/if}
            </select>
        </div>

        {* ДЕТАЛЬ *}
        <div class="form-field">
            <label for="detail_id" class="cm-required cm-integer-more-0">Деталь:</label>
            <table class="simple">
                <thead>
                    <tr>
                        <th>
                            <input checked type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items-{$document_type_name}_{$document_id}" />
                        </th>
                        <th>№</th>
                        <th>Наименование</th>
                        <th>Кол-во</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$kit.details item="d" name="d"}
                        {assign var="index"     value=$smarty.foreach.d.iteration}
                        {assign var="e_n"       value="motion[document_items][`$index`]"}

                        {assign var="d_a"       value=""} {* Уже добавленная деталь в документ *}

                        {foreach from=$motion.items item="i"}
                            {if $i.item_id == $d.detail_id and $i.item_type == "D"}
                                {assign var="d_a"   value=$i}
                            {/if}
                        {/foreach}
                        <tr>
                            <td>
                                <input type="hidden"    name="{$e_n}[state]" value="N"/>
                                <input type="checkbox"  name="{$e_n}[state]" value="Y" id="detail_id_{$index}" {if $d_a|is__array}checked{/if} class="checkbox cm-item-{$document_type_name}_{$document_id}" />

                                <input type="hidden"    name="{$e_n}[item_id]"        value="{$d.detail_id}"/>
                                <input type="hidden"    name="{$e_n}[item_type]"      value="D"/>
                                <input type="hidden"    name="{$e_n}[processing]"     value="C"/>

                                {if $document_type_name == "VN"}
                                    <input type="hidden"    name="{$e_n}[motion_type]"  value="{$smarty.const.UNS_MOTION_TYPE__O}"/>
                                {/if}

                                {if $d_a|is__array}
                                    <input type="hidden"    name="{$e_n}[di_id]"        value="{$d_a.di_id}"/>
                                    <input type="hidden"    name="{$e_n}[typesize]"     value="{$d_a.typesize}"/>
                                    <input type="hidden"    name="{$e_n}[u_id]"         value="{$d_a.u_id}"/>
                                    <input type="hidden"    name="{$e_n}[weight]"       value="{$d_a.weight}"/>
                                {/if}
                            </td>
                            <td align="center"><b>{$index}</b></td>
                            <td><label style="padding: 0; margin: 0; color: #000000; float: none;" for="detail_id_{$index}">{$d.detail_name}{if $d.detail_no} [{$d.detail_no}]{/if}</label></td>
                            <td>
                                {assign var="q_min" value=0}
                                {assign var="q_max" value=200}
                                {assign var="q" value=0}
                                {if $document_type_name == "AIO"}
                                    {assign var="q_min" value=-200}
                                {/if}

                                {if $d_a|is__array}
                                    {assign var="q" value=$d_a.quantity}
                                {/if}
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_type="select_range"
                                    f_name="`$e_n`[quantity]"
                                    f_id="quantity_`$add_index`"
                                    f_from=$q_min
                                    f_to=$q_max
                                    f_value=$q
                                    f_simple=true
                                }
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>

        {if $document_type_name == "VN"}
        {* НАСОС *}
        <div class="form-field">
            <label for="pump_id" class="cm-required cm-integer-more-0">Насос:</label>
            <table class="simple">
                <thead>
                    <tr>
                        <th>
                            <input checked type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items-{$document_type_name}_{$document_id}_P" />
                        </th>
                        <th>Наименование</th>
                        <th>Исполнение</th>
                        <th>Кол-во</th>
                    </tr>
                </thead>
                <tbody>
                {foreach from=$motion.items item="i" name="i"}
                    {if $i.item_type == "P" or $i.item_type == "PF"}
                        {assign var="index"     value=$smarty.foreach.i.iteration}
                        {assign var="e_n"       value="motion[document_items][p_`$index`]"}
                        <tr>
                            <td>
                                <input type="hidden"    name="{$e_n}[state]" value="N"/>
                                <input type="checkbox"  name="{$e_n}[state]" value="Y" checked class="checkbox cm-item-{$document_type_name}_{$document_id}_P" />

                                <input type="hidden"    name="{$e_n}[di_id]"        value="{$d_a.di_id}"/>
                                <input type="hidden"    name="{$e_n}[typesize]"     value="{$d_a.typesize}"/>
                                <input type="hidden"    name="{$e_n}[u_id]"         value="{$d_a.u_id}"/>
                                <input type="hidden"    name="{$e_n}[weight]"       value="{$d_a.weight}"/>
                                <input type="hidden"    name="{$e_n}[motion_type]"  value="{$smarty.const.UNS_MOTION_TYPE__I}"/>
                            </td>
                            <td align="center">
                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_id="kit_pump"
                                    f_type="select_by_group"
                                    f_name="`$e_n`[item_id]"
                                    f_required=true f_integer_more_0=true
                                    f_options="pumps"
                                    f_option_id="p_id"
                                    f_option_value="p_name"
                                    f_option_target_id=$i.item_id|default:"0"
                                    f_optgroups=$pumps
                                    f_optgroup_label="ps_name"
                                    f_blank=true
                                    f_simple=true
                                }
                            </td>
                            <td>
                                <select name="{$e_n}[item_type]">
                                    <option {if $i.item_type == "P"}selected="selected"{/if}    value="P">Насос</option>
                                    <option {if $i.item_type == "PF"}selected="selected"{/if}   value="PF">Насос на раме</option>
                                </select>
                            </td>
                            <td>
                                {assign var="q_min" value=0}
                                {assign var="q_max" value=200}
                                {assign var="q"     value=$i.quantity}

                                {include file="addons/uns/views/components/get_form_field.tpl"
                                    f_type="select_range"
                                    f_name="`$e_n`[quantity]"
                                    f_id="quantity_`$add_index`"
                                    f_from=$q_min
                                    f_to=$q_max
                                    f_value=$q
                                    f_simple=true
                                }
                            </td>
                        </tr>
                    {/if}
                {/foreach}

                    {* empty *}
                    {assign var="e_n"       value="motion[document_items][p_0]"}
                    <tr>
                        <td>
                            <input type="hidden"    name="{$e_n}[state]" value="N"/>
                            <input type="checkbox"  name="{$e_n}[state]" value="Y" class="checkbox cm-item-{$document_type_name}_{$document_id}_P" />
                        </td>
                        <td align="center">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_id="kit_pump"
                                f_type="select_by_group"
                                f_name="`$e_n`[item_id]"
                                f_required=true f_integer_more_0=true
                                f_options="pumps"
                                f_option_id="p_id"
                                f_option_value="p_name"
                                f_option_target_id=$kit.p_id|default:"0"
                                f_optgroups=$pumps
                                f_optgroup_label="ps_name"
                                f_blank=true
                                f_simple=true
                            }
                        </td>
                        <td>
                            <select name="{$e_n}[item_type]">
                                <option value="P">Насос</option>
                                <option value="PF">Насос на раме</option>
                            </select>
                        </td>
                        <td>
                            {assign var="q_min" value=0}
                            {assign var="q_max" value=200}
                            {assign var="q"     value=0}

                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_range"
                                f_name="`$e_n`[quantity]"
                                f_id="quantity_`$add_index`"
                                f_from=$q_min
                                f_to=$q_max
                                f_value=$q
                                f_simple=true
                            }
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {/if}
    </div>
    <br>
    <div class="buttons-container cm-toggle-button buttons-bg">
        {if $m_mode == "add"}
            {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.motion.update]" hide_second_button=true mode=$m_mode}
        {else}
            {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.motion.update]" hide_second_button=true mode=$m_mode}
        {/if}
    </div>
</form>
{*<pre>{$kit|print_r}</pre>*}
{*<pre>{$motion|print_r}</pre>*}
{*<pre>{$pump|print_r}</pre>*}
