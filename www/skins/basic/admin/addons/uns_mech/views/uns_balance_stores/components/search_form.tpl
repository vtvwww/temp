{strip}
{capture name="section"}
    <form action="{""|fn_url}" name="search_form" method="get">
        {* Дата проведение документа *}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td class="nowrap search-field" colspan="2">
                    <label>Период:</label>
                    <div class="break">
                        {include file="common_templates/period_selector.tpl" period=$search.period prefix=""}
                    </div>
                </td>
                <td rowspan="2" class="nowrap search-field b1_l">
                    <label>Выбор Склада:</label>
                    <div class="break">
                        <select name="o_id" id="">
                            <option value="0">---</option>
                            <option selected="selected" value="6">Метизов и Подшипников</option>
                        </select>
                    </div>
                </td>
                <td class="nowrap search-field b1_l">
                    <input type="hidden" name="mclass_id" value="1">
                    <input type="hidden" name="item_type" value="M">
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
                            f_with_q_ty=false
                            f_blank_name="---"
                        }
                    </div>
                </td>
            </tr>
        </table>

        {**************************************************************************}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="П О И С К" but_name="dispatch[`$dispatch`]" but_role="big" but_input_css="width:918px;font-weight:bold;"}
                </td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
{/strip}