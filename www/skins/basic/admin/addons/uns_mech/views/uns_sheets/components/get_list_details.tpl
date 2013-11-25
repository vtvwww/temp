{if $details|is__array}
    {foreach from=$details item="d" name="d"}
        &nbsp;
        <span class="action-add ">
            <a class="add_detail" detail_id="{$d.detail_id}"><span class="{if $d.checked == "N"}item_verification_required{/if}">{$d.format_name}</span></a>
        </span>
        <span style="font-size: 11px; font-weight: bold;">
            {if $d.accessory_view == "S"}
                {$d.accessory_pump_series}
            {elseif $d.accessory_view == "P"}
                {$d.accessory_pumps} <span class="info_warning">({$d.accessory_view})</span>
            {elseif $d.accessory_view == "M"}
                {$d.accessory_manual} <span class="info_warning">({$d.accessory_view})</span>
            {/if}
        </span>
        <div class="data_detail__{$d.detail_id}" style="display: none;">
            {assign var="label" value="add_`$d.detail_id`"}
            <table>
                <tbody>
                    <tr>
                        <td>
                            <input type="hidden" name="detail_id" value="{$label}"/>
                        </td>
                        <td>
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="dcategories_plain"
                                f_name="details[`$label`][dcat_id]"
                                f_options=$dcategories_plain
                                f_option_id="dcat_id"
                                f_option_value="dcat_name"
                                f_option_target_id=$d.dcat_id
                                f_with_q_ty=false
                                f_blank=true
                                f_blank_name="---"
                                f_simple=true
                            }
                        </td>
                        <td>
                            <select name="details[{$label}][detail_id]">
                                <option value="{$d.detail_id}">{$d.format_name}</option>
                            </select>
                        </td>
                        <td>
                            <span class="delete_detail">x</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        {if !$smarty.foreach.d.last}
            <br>
            <br>
        {/if}
    {/foreach}
{/if}
