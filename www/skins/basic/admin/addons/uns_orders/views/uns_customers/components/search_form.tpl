{capture name="search_content"}
    <table cellpadding="10" cellspacing="0" border="0" class="search-header">
        <tr>
            <td class="nowrap search-field">
                <label for="country_id">Страна:</label>
                <div class="break">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="select"
                        f_id="country_id"
                        f_name="country_id"
                        f_options=$countries
                        f_option_id="id"
                        f_option_value="name"
                        f_option_target_id=$search.country_id
                        f_simple=true
                        f_blank=true
                    }
                </div>
            </td>
        </tr>
    </table>
{/capture}
{include file="addons/uns/views/components/search/search.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}
