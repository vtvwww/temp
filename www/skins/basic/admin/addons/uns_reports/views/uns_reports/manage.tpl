{capture name="section"}
    <form action="{""|fn_url}" name="search_form" method="get">
        {* Добавить ФИЛЬТРАЦИЮ по ВРЕМЕНИ *}
        {include file="addons/uns/views/components/search/s_time.tpl"}

        {**************************************************************************}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td colspan="10"><hr style="border-color: #000000;"/></td>
            </tr>
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="Общий отчет" but_name="dispatch[`$controller`.get_report.general_report]" but_role="submit"}
                </td>
                <td align="right">
                    <label style="cursor: pointer;">Добавить значение выпуска литейного цеха<br>с начала месяца: <input type="checkbox" name="production_LC_from_the_beginning_of_the_month" checked="checked" value="Y"/></label>
                    {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                        {*f_id="production_LC_date_from"*}
                        {*f_type="date"*}
                        {*f_name="production_LC_date_from"*}
                        {*f_value=$production_LC_date_from*}
                        {*f_simple=true*}
                    {*}*}
                </td>
            </tr>
            <tr>
                <td colspan="10"><hr style="border-color: #000000;"/></td>
            </tr>
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="Баланс по мех. цеху" but_name="dispatch[`$controller`.get_report.mc]" but_role="submit"}
                </td>
                {*<td align="right">*}
                    {*<label>Отчет в виде пустого бланка: <input type="checkbox" name="as_blank" value="Y"/></label>*}
                {*</td>*}
            </tr>
            <tr>
                <td colspan="10"><hr style="border-color: #000000;"/></td>
            </tr>
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="Отчет по Литейному цеху" but_name="dispatch[`$controller`.get_report.foundry]" but_role="submit"}
                </td>
            </tr>
            <tr>
                <td colspan="10"><hr style="border-color: #000000;"/></td>
            </tr>
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="Учет движения отливок на Складе литья" but_name="dispatch[`$controller`.get_report.accounting]" but_role="submit"}
                </td>
            </tr>
            <tr>
                <td colspan="10"><hr style="border-color: #000000;"/></td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}