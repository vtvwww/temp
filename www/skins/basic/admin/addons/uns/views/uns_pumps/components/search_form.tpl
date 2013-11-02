{capture name="section"}
    {literal}
        <script type="text/javascript">
            $(function () {
                $('select[name="pt_id"], select[name="ps_id"]').live('change', function (e) {
                    $('form[name="{/literal}{$controller}{literal}_search_form"]').submit();
                });
            });
        </script>
    {/literal}
    <form action="{""|fn_url}" name="{$controller}_search_form" method="get">
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td class="nowrap search-field">
                    <label>{$lang.uns_pump_types}:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="select"
                            f_required=true f_integer=false
                            f_name="pt_id"
                            f_options=$pump_types
                            f_option_id="pt_id"
                            f_option_value="pt_name"
                            f_option_target_id=$search.pt_id
                            f_simple=true
                            f_blank=true
                        }
                    </div>
                </td>
                <td class="nowrap search-field">
                    <label>{$lang.uns_pump_series}:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="select_by_group"
                            f_name="ps_id"
                            f_required=true f_integer=false
                            f_options="pump_series"
                            f_option_id="ps_id"
                            f_option_value="ps_name"
                            f_option_target_id=$search.ps_id
                            f_optgroups=$pump_series
                            f_optgroup_label="pt_name"
                            f_simple=true
                            f_blank=true
                        }
                    </div>
                </td>
                <td class="nowrap search-field">
                    <label>{$lang.uns_pumps}:</label>
                    <div class="break">
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="input"
                            f_required=true f_integer=false
                            f_name="p_name"
                            f_value=$search.p_name
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