<div id="content_group">
    <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
        <div id="content_general">
            <input type="hidden" name="kit_id" value="{$smarty.request.kit_id}"/>
            {if $smarty.request.mode == "add"}
                {assign var="add_index" value="0"}
                {assign var="e_n" value="kit_details[add_`$add_index`]"}
                {assign var="disabled" value=false}
                <input type="hidden" name="{$e_n}[pd_id]" value="0"/>
            {else}
                {assign var="add_index" value=$detail.pd_id}
                {assign var="e_n" value="kit_details[`$detail.pd_id`]"}
                {assign var="disabled" value=true}
                <input type="hidden" name="{$e_n}[pd_id]" value="{$detail.pd_id}"/>
            {/if}
            <div class="form-field">
                <label class="cm-required cm-integer-more-0" for="dcat_id_{$add_index}">Категория деталей:</label>
                {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="dcategories_plain"
                        f_id="dcat_id_`$add_index`"
                        f_name="`$e_n`[dcat_id]"
                        f_options=$dcategories_plain
                        f_option_id="dcat_id"
                        f_option_value="dcat_name"
                        f_option_target_id=$detail.dcat_id
                        f_with_q_ty=false
                        f_blank=true
                        f_disabled=$disabled
                        f_blank_name="---"
                        f_simple=true
                    }
            </div>
            <div class="form-field">
                <label class="cm-required cm-integer-more-0" for="detail_id_{$add_index}">Деталь:</label>
                <select name="{$e_n}[detail_id]" id="detail_id_{$add_index}" {if $disabled}disabled="disabled"{/if}>
                    <option value="{$detail.detail_id}">{$detail.detail_name}{if $detail.detail_no} [{$detail.detail_no}]{/if}</option>
                </select>
            </div>
            <div class="form-field">
                <label class="cm-required cm-integer-more-0" for="quantity_{$add_index}">Количество:</label>
                {include file="addons/uns/views/components/get_form_field.tpl"
                    f_type="select_range"
                    f_name="`$e_n`[quantity]"
                    f_id="quantity_`$add_index`"
                    f_from=0
                    f_to=200
                    f_value=$detail.quantity|default:1
                    f_simple=true
                    f_plus_minus=true
                }
            </div>
        </div>

        <div class="buttons-container cm-toggle-button buttons-bg">
            {if $mode == "detail_update"}
                {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.`$mode`]" hide_second_button=true}
            {else}
                {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.`$mode`]" hide_second_button=true}
            {/if}
        </div>
    </form>
</div>
{*<hr>*}
{*<pre>{$detail|print_r}</pre>*}
