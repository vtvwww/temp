{capture name="section"}
    <form action="{""|fn_url}" name="acc_ostatki_search_form" method="get">
        <hr>
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td class="nowrap search-field">
                    {include file="common_templates/period_selector.tpl" period=$search.period}
                </td>
            </tr>
        </table>
        <hr>
        {* МАТЕРИАЛЫ *}
        {*<table cellpadding="10" cellspacing="0" border="0" class="search-header materials" id="search_form_materials">
            <tr>
                <td class="nowrap search-field">
                    <label for="item_type_M">{$lang.uns_materials}:</label>
                    <div class="break">
                        <input type="radio" name="item_type" id="item_type_M" value="M" {if $search.item_type=="M" or ($search.item_type!="M" and $search.item_type!="D")} checked="checked" {/if}>
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
        <hr>*}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="П О И С К" but_name="dispatch[`$dispatch`]" but_role="big" but_input_css="width:918px;font-weight:bold;"}
                    {*{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[`$dispatch`]" but_role="submit"}*}
                </td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}