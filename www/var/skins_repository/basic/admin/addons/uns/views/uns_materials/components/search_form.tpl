{capture name="section"}
    {literal}
        <script type="text/javascript">
            $(function () {
                $('select[name="mclass_id"], select[name="mcat_id"]').live('change', function (e) {
                    $('form[name="{/literal}{$controller}{literal}_search_form"]').submit();
                });
            });
        </script>
    {/literal}
    <form action="{""|fn_url}" name="{$controller}_search_form" method="get">
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
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
                <td class="buttons-container">
                    {include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[`$dispatch`]" but_role="submit"}
                </td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}