{strip}

<hr>
<div style="margin-bottom: 2px;padding: 6px 5px 6px 189px;">

<table cellpadding="0" cellspacing="0" class="table">
    <tbody>
        <tr class="first-sibling">
            <th class="cm-non-cb">Категория материалов</th>
            <th class="cm-non-cb">Материал</th>
            <th class="cm-non-cb">Кол-во</th>
            {*<th class="cm-non-cb">Припуск</th>*}
            <th class="cm-non-cb">Ед. изм.</th>
            <th class="cm-non-cb">&nbsp;</th>
        </tr>
    </tbody>
    {if is__array($am__existing_materials)}
        {foreach from=$am__existing_materials item="m" name="f_m"}
        {assign var="num" value=$smarty.foreach.f_m.iteration}
        {assign var="id" value=$m.di_id}

        {if $smarty.request.copy == "Y"}
            {assign var="di_id" value=0}
        {else}
            {assign var="di_id" value=$id}
        {/if}

        {if is__more_0($id)}
            <tbody class="hover cm-row-item" id="am_{$id}_{$num}">
                <tr>
                    <td class="cm-non-cb">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="hidden"
                            f_name="data[accounting][materials][`$num`][di_id]"
                            f_value=$di_id
                        }

                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_id=$id
                            f_type="mcategories_plain"
                            f_required=true f_integer=false
                            f_name="data[accounting][materials][`$num`][mcat_id]"
                            f_options=$am__mcategories_plain
                            f_option_id="mcat_id"
                            f_option_value="mcat_name"
                            f_option_target_id=$m.mcat_id
                            f_simple=true
                        }
                    </td>
                    <td class="cm-non-cb">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_id=$id
                            f_type="select"
                            f_required=true f_integer=false
                            f_name="data[accounting][materials][`$num`][material_id]"
                            f_options=$m.materials
                            f_option_id="material_id"
                            f_option_value="format_name"
                            f_option_target_id=$m.material_id
                            f_simple=true
                        }
                    </td>
                    <td class="cm-non-cb">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_id=$id
                            f_type="input"
                            f_required=true f_integer=false
                            f_name="data[accounting][materials][`$num`][quantity]"
                            f_value=$m.quantity
                            f_simple=true
                        }

                        {assign var="add_quantity_state" value="D"}
                        {assign var="add_quantity_class" value=" hidden "}
                        {if $m.add_quantity_state == "A"}
                            {assign var="add_quantity_state" value="A"}
                            {assign var="add_quantity_class" value=""}
                        {/if}

                        <span class="add_quantity {$add_quantity_class}">&nbsp;x&nbsp;</span>
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="hidden"
                            f_class="add_quantity_state"
                            f_name="data[accounting][materials][`$num`][add_quantity_state]"
                            f_value=$add_quantity_state
                        }

                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_id=$id
                            f_type="input"
                            f_add_class="add_quantity `$add_quantity_class`"
                            f_required=true f_integer=false
                            f_name="data[accounting][materials][`$num`][add_quantity]"
                            f_value=$m.add_quantity
                            f_simple=true
                        }
                    </td>
                    {*<td class="cm-non-cb">*}
                        {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                            {*f_id=$id*}
                            {*f_type="input"*}
                            {*f_required=true f_integer=false*}
                            {*f_name="data[accounting][materials][`$num`][allowance]"*}
                            {*f_value=$m.allowance*}
                            {*f_simple=true*}
                        {*}*}
                    {*</td>*}
                    <td class="cm-non-cb">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_id=$id
                            f_type="select"
                            f_required=true f_integer=false
                            f_name="data[accounting][materials][`$num`][u_id]"
                            f_options=$m.units
                            f_option_id="u_id"
                            f_option_value="u_name"
                            f_option_target_id=$m.u_id
                            f_simple=true
                        }
                    </td>
                    <td class="right cm-non-cb">
                        {include file="buttons/multiple_buttons.tpl" item_id="am_`$id`_`$num`" tag_level="3" only_delete="Y"}
                    </td>
                </tr>
            </tbody>
        {/if}
        {/foreach}
    {else}
        {math equation="x + 1" assign="num" x=$num|default:0}
        <tbody class="hover cm-row-item " id="box_add_am_{$id}">
            <tr>
                <td class="cm-non-cb">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="hidden"
                        f_name="data[accounting][materials][`$num`][di_id]"
                        f_value=0
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="mcategories_plain"
                        f_required=true f_integer=false
                        f_name="data[accounting][materials][`$num`][mcat_id]"
                        f_options=$am__mcategories_plain
                        f_option_id="mcat_id"
                        f_option_value="mcat_name"
                        f_option_target_id=0
                        f_simple=true
                        f_blank_name="---"
                        f_blank=true
                    }
                </td>
                <td class="cm-non-cb">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="select"
                        f_required=true f_integer=false
                        f_name="data[accounting][materials][`$num`][material_id]"
                        f_options=""
                        f_simple=true
                    }
                </td>
                <td class="cm-non-cb">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="data[accounting][materials][`$num`][quantity]"
                        f_value=""
                        f_simple=true
                    }

                    <span class="add_quantity hidden">&nbsp;x&nbsp;</span>
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="hidden"
                        f_class="add_quantity_state"
                        f_name="data[accounting][materials][`$num`][add_quantity_state]"
                        f_value="D"
                    }

                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="input"
                        f_add_class="add_quantity hidden"
                        f_name="data[accounting][materials][`$num`][add_quantity]"
                        f_value=""
                        f_simple=true
                    }

                </td>
{*                <td class="cm-non-cb">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="input"
                        f_required=true f_integer=false
                        f_name="data[accounting][materials][`$num`][allowance]"
                        f_value=""
                        f_simple=true
                    }
                </td>*}
                <td class="cm-non-cb">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_id=$id
                        f_type="select"
                        f_required=true f_integer=false
                        f_name="data[accounting][materials][`$num`][u_id]"
                        f_options=$m.units
                        f_option_id="u_id"
                        f_option_value="unit_name"
                        f_option_target_id=$m.material_id
                        f_simple=true
                    }
                </td>
                <td class="right cm-non-cb">
                    {include file="buttons/multiple_buttons.tpl" item_id="add_am_`$id`" tag_level="2"}
                </td>
            </tr>
        </tbody>
    {/if}
</table>
</div>
{/strip}
