{capture name="section"}
    <form action="{""|fn_url}" name="uns_sheet_search_form" id="uns_sheet_search_form" method="get">
        <hr>
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td class="nowrap search-field">
                    <label>Период:</label>
                    <div class="break">
                        {include file="common_templates/period_selector.tpl" period=$search.period}
                    </div>
                </td>
                <td class="nowrap search-field b1_l">
                    <label for="status">Статус:</label>
                    <div class="break">
                        <select name="status" id="status">
                          <option value="0">---</option>
                          <option {if $search.status == "OP"}selected="selected"{/if} value="OP">Открыт</option>
                          <option {if $search.status == "CL"}selected="selected"{/if} value="CL">Закрыт</option>
                        </select>
                    </div>
                </td>
                <td class="nowrap search-field b1_l">
                    <label for="material_type">Тип:</label>
                    <div class="break">
                        <select name="material_type" id="material_type">
                            <option value="0">---</option>
                            <option {if $search.material_type == "O"}selected="selected"{/if} value="O">Отливка</option>
                            <option {if $search.material_type == "M"}selected="selected"{/if} value="M">Металлопрокат</option>
                        </select>
                    </div>
                </td>
                <td class="nowrap search-field b1_l">
                    <label for="target_object">Цех:</label>
                    <div class="break">
                        <select name="target_object" id="target_object">
                            <option value="0">---</option>
                            <option {if $search.target_object == "10"}selected="selected"{/if} value="10">Мех. цех 1</option>
                            <option {if $search.target_object == "14"}selected="selected"{/if} value="14">Мех. цех 2</option>
                            <option {if $search.target_object == "17"}selected="selected"{/if} value="17">Скл. КМП</option>
                        </select>
                    </div>
                </td>
            </tr>
        </table>
        <hr>
        {* МАТЕРИАЛЫ *}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header materials" id="search_form_materials">
            <tr>
                <td class="nowrap search-field">
                    <label>{$lang.uns_material_categories}:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="mcategories_plain"
                            f_required=true f_integer=false
                            f_id="mcat_id"
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
                <td class="nowrap search-field b1_l">
                    <label>Клеймо отливки:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="input"
                            f_required=false f_integer=false
                            f_name="material_no"
                            f_value=$search.material_no
                            f_simple=true
                        }
                    </div>
                </td>
                <td class="nowrap search-field b1_l">
                    <label>Насос:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="input"
                            f_required=false f_integer=false
                            f_name="pump_name"
                            f_value=$search.pump_name
                            f_class="input-text-long"
                            f_simple=true
                        }
                    </div>
                </td>
                {*<td class="nowrap search-field">*}
                    {*<label>&nbsp;</label>*}
                    {*<div class="break">*}
                        {*<input type="reset" value="Сбросить"/>*}
                    {*</div>*}
                {*</td>*}
                <td class="nowrap search-field b1_l">
                    <label for="detail_id">Деталь:</label>
                    <div class="break">
                        <select name="detail_id" id="detail_id">
                            {include file="addons/uns/views/components/get_form_field.tpl"
                                f_type="select_by_group"
                                f_options="details"
                                f_option_id="detail_id"
                                f_option_value="detail_name"
                                f_optgroups=$category_details
                                f_optgroup_label="dcat_name"
                                f_option_target_id=$search.detail_id
                                f_simple_2=true
                                f_blank=true
                            }
                        </select>
                    </div>
                </td>
            </tr>
        </table>
        <hr>
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