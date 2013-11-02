{if $uns_unit.u_id}
	{assign var="id" value=$uns_unit.u_id}
{else}
	{assign var="id" value=0}
{/if}

<div id="content_group{$id}">
    <form action="{""|fn_url}" method="post" name="update_uns_units_form_{$id}" class="cm-form-highlight">
        <input type="hidden" name="unit_id" value="{$id}" />
        {capture name="tabsbox"}
            <div id="content_general_{$id}">
                <div class="form-field">
                    <label for="uс_name">Категория:</label>
                    <select id="uс_name" name="unit_data[uc_id]">
                        {foreach from=$uns_unit_categories item=uns_unit_category}
                            <option value="{$uns_unit_category.uc_id}"{if $uns_unit_category.uc_id == $uns_unit.uc_id} selected="selected"{/if}>{$uns_unit_category.uc_name}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="form-field">
                    <label class="cm-required" for="u_name">Наименование:</label>
                    <input type="text" id="u_name" name="unit_data[u_name]" size="35" value="{$uns_unit.u_name}" class="input-text-large main-input" />
                </div>

                <div class="form-field">
                    <label for="u_type">{$lang.type}:</label>
                    <select id="u_type" name="unit_data[u_type]">
                        <option value="A"{if $uns_unit.u_type == "A"} selected="selected"{/if}>Дополнительная</option>
                        <option value="M"{if $uns_unit.u_type == "M"} selected="selected"{/if}>Основная</option>
                    </select>
                </div>

                <div class="form-field">
                    <label class="cm-required cm-value-decimal" for="u_coefficient">Коэффициент:</label>
                    <input type="text" id="u_coefficient" name="unit_data[u_coefficient]" size="35" value="{if $uns_unit.u_coefficient}{$uns_unit.u_coefficient}{else}1.0000{/if}" class="input-text main-input" />
                </div>

                <div class="form-field">
                    <label class="cm-required cm-integer" for="u_position">Позиция:</label>
                    <input type="text" id="u_position" name="unit_data[u_position]" size="35" value="{if $uns_unit.u_position}{$uns_unit.u_position}{else}0{/if}" class="input-text main-input" />
                </div>

                <div class="form-field">
                    <label class="" for="u_comment">Комментарий:</label>
                    <textarea name="unit_data[u_comment]" id="u_comment" rows="5">{$uns_unit.u_comment}</textarea>
                </div>

                {include file="common_templates/select_status.tpl" input_name="unit_data[u_status]" id="unit_data_`$id`" obj=$uns_unit}

            </div>
        {/capture}

        {include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox}

        <div class="buttons-container">
            {include file="buttons/save_cancel.tpl" but_name="dispatch[uns_units.update]" cancel_action="close"}
        </div>
    </form>
</div>