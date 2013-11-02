{capture name="section"}
    {literal}
        <script type="text/javascript">
            $(function () {
                $('select[name="dcat_id"]').live('change', function (e) {
                    $('form[name="{/literal}{$controller}{literal}_search_form"]').submit();
                });
            });
        </script>
    {/literal}

    <form action="{""|fn_url}" name="{$controller}_search_form" method="get">
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
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
                <td class="buttons-container">
                    {include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[`$dispatch`]" but_role="submit"}
                </td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}