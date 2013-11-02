{strip}
    <table cellpadding="10" cellspacing="0" border="0" class="search-header">
        <tr>
            <td class="nowrap search-field">
                <label for="view_all_position">Отображать позиции, которые удовлетворяют условиям отбора,<br>но по ним не было движений, за выбранный период:</label>
                {*<div class="break">*}
                    {include file="addons/uns/views/components/get_form_field.tpl"
                        f_type="checkbox"
                        f_id="view_all_position"
                        f_name="view_all_position"
                        f_value=$search.view_all_position
                        f_style="margin-top:15px;"
                        f_simple=true
                    }
                {*</div>*}
            </td>
        </tr>
    </table>
{/strip}