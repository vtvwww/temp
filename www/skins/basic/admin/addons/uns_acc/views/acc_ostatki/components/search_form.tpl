{capture name="section"}
    <form action="{""|fn_url}" name="acc_ostatki_search_form" method="get">
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td class="nowrap search-field">
                    <label>Начало периода:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="date"
                            f_name="date_from"
                            f_value=$search.date_from
                            f_simple=true
                        }
                    </div>
                </td>
                <td class="nowrap search-field">
                    <label>Конец периода:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="date"
                            f_name="date_to"
                            f_value=$search.date_to
                            f_simple=true
                        }
                    </div>
                </td>
                <td class="nowrap search-field">
                    <label>Объект:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="objects_plain"
                            f_name="o_id"
                            f_options=$objects_plain
                            f_option_id="o_id"
                            f_option_value="o_name"
                            f_option_target_id=$search.o_id
                            f_simple=true
                        }
                    </div>
                </td>
            </tr>
        </table>
        <hr>
        {* МАТЕРИАЛЫ *}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header materials" id="search_form_materials">
            <tr>
                <td class="nowrap search-field">
                    <label for="item_type_M">{$lang.uns_materials}:</label>
                    <div class="break">
                        <input type="radio" name="item_type" id="item_type_M" value="M" {if $search.item_type=="M" or ($search.item_type!="M" and $search.item_type!="D")} checked="checked" {/if}>
                    </div>
                </td>
                <td class="nowrap search-field">
                    <label>{$lang.uns_material_classes}:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="select"
                            f_required=true f_integer=false
                            f_name="mclass_id"
                            f_options=$mclasses
                            f_option_id="mclass_id"
                            f_option_value="mclass_name"
                            f_option_target_id=$search.mclass_id
                            f_simple=true
                            f_blank=true
                        }
                    </div>
                </td>
                <td class="nowrap search-field">
                    <label>{$lang.uns_material_categories}:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="mcategories_plain"
                            f_required=true f_integer=false
                            f_name="mcat_id"
                            f_options=$mcategories_plain
                            f_option_id="mcat_id"
                            f_option_value="mcat_name"
                            f_option_target_id=$search.mcat_id
                            f_simple=true
                            f_blank=true
                            f_with_q_ty=$mcategories_plain_with_q_ty
                            f_blank_name="---"
                        }
                    </div>
                </td>
                <td class="nowrap search-field">
                    <label>{$lang.uns_material}:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="input"
                            f_required=true f_integer=false
                            f_name="material_name"
                            f_value=$search.material_name
                            f_simple=true
                        }
                    </div>
                </td>
                <td class="nowrap search-field">
                    <label>{$smarty.const.L_material_no}:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="input"
                            f_required=true f_integer=false
                            f_name="material_no"
                            f_value=$search.material_no
                            f_simple=true
                        }
                    </div>
                </td>
            </tr>
        </table>
        <hr>
        <table cellpadding="10" cellspacing="0" border="0" class="search-header" id="search_form_details">
            <tr>
                <td class="nowrap search-field">
                    <label for="item_type_D">{$lang.uns_details}:</label>
                    <div class="break">
                        <input type="radio" name="item_type" id="item_type_D" value="D" {if $search.item_type=="D"} checked="checked" {/if}>
                    </div>
                </td>
                <td class="nowrap search-field">
                    <label>{$lang.uns_detail_categories}:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="dcategories_plain"
                            f_name="dcat_id"
                            f_options=$dcategories_plain
                            f_option_id="dcat_id"
                            f_option_value="dcat_name"
                            f_option_target_id=$search.dcat_id
                            f_simple=true
                            f_blank=true
                            f_with_q_ty=$dcategories_plain_with_q_ty
                            f_blank_name="---"
                        }
                    </div>
                </td>
                <td class="nowrap search-field">
                    <label>{$lang.uns_detail}:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="input"
                            f_style="width:120px;"
                            f_name="detail_name"
                            f_value=$search.detail_name
                            f_simple=true
                        }
                    </div>
                </td>
                <td class="nowrap search-field">
                    <label>{$smarty.const.L_detail_no}:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="input"
                            f_name="detail_no"
                            f_style="width:120px;"
                            f_value=$search.detail_no
                            f_simple=true
                        }
                    </div>
                </td>
            </tr>
        </table>
        <hr>
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td class="{*buttons-container*}">
                    {include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[`$dispatch`]" but_role="submit"}
                </td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}