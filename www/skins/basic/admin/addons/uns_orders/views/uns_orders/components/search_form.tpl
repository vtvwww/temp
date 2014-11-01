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
            <td class="nowrap search-field b1_l">
                <label for="country_id">Регион/Область:</label>
                <div class="break">
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="select"
                        f_id="region_id"
                        f_name="region_id"
                        f_options=$regions
                        f_option_id="id"
                        f_option_value="name"
                        f_option_target_id=$search.region_id
                        f_simple=true
                        f_blank=true
                    }
                </div>
            </td>
            {*<td class="nowrap search-field b1_l">*}
                {*<label>Клиент:</label>*}
                {*<div class="break">*}
                    {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                        {*f_type="input"*}
                        {*f_required=true f_integer=false*}
                        {*f_name="material_name"*}
                        {*f_value=$search.material_name*}
                        {*f_simple=true*}
                    {*}*}
                {*</div>*}
            {*</td>*}
            <td class="nowrap search-field b1_l">
                <label for="status">Статус:</label>
                <div class="break">
                    <select class="order_status" name="status" style="height: 24px;">
                        <option value="">---</option>
                        <option value="Open"    class="open"    {if $search.status == "Open"}selected="selected"{/if}>Открыт</option>
                        <option value="Paid"    class="paid"    {if $search.status == "Paid"}selected="selected"{/if}>Оплачен</option>
                        <option value="Shipped" class="shipped" {if $search.status == "Shipped"}selected="selected"{/if}>Отгружен</option>
                    </select>
                </div>
            </td>
        </tr>
    </table>
{/capture}
{include file="addons/uns/views/components/search/search.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}
