{strip}
    <table cellpadding="10" cellspacing="0" border="0" class="search-header">
        <tr>
            {*<td class="nowrap search-field">*}
                {*<input type="hidden" name="item_type" value="D">*}
                {*<label>{$lang.uns_object}:</label>*}
                {*<div class="break">*}
                    {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                        {*f_id="object_from"*}
                        {*f_type="objects_plain"*}
                        {*f_name="o_id"*}
                        {*f_option_target_id=$search.o_id*}
                        {*f_options=$objects_plain*}
                        {*f_option_id="o_id"*}
                        {*f_option_value="o_name"*}
                        {*f_options_enabled=$enabled_objects*}
                        {*f_simple=true*}
                    {*}*}
                {*</div>*}
            {*</td>*}
            <td class="nowrap search-field">
                <label for="total_balance_of_details">Отобразить остатки по Мех. цехам и Складу комплектующих:</label>
                <div class="break">
                    {*<pre>{$search|print_r}</pre>*}
                    <input type="hidden" name="total_balance_of_details" value="Y"/>
                    <input id="total_balance_of_details"
                           type="checkbox"
                           name="total_balance_of_details"
                           {*{if $search.total_balance_of_details == "Y"}*}checked="checked"{*{/if}*}
                           value="Y"
                           class="checkbox"
                           />
                </div>
            </td>
        </tr>
    </table>
{/strip}