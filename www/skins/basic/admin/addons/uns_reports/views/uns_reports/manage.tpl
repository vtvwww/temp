{capture name="section"}
    <form action="{""|fn_url}" name="search_form" method="get">
        {* Добавить ФИЛЬТРАЦИЮ по ВРЕМЕНИ *}
        {include file="addons/uns/views/components/search/s_time.tpl"}

        {**************************************************************************}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="Отчет по Литейному цеху" but_name="dispatch[`$controller`.get_report.foundry]" but_role="submit"}
                </td>
            </tr>
            {*<tr>*}
                {*<td>&nbsp;</td>*}
            {*</tr>*}
            {*<tr>*}
                {*<td>*}
                    {*{include file="buttons/button.tpl" but_text="Отчет по Складу литья" but_name="dispatch[`$controller`.get_report.sl]" but_role="submit"}*}
                {*</td>*}
            {*</tr>*}
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="Учет движения отливок на Складе литья" but_name="dispatch[`$controller`.get_report.accounting]" but_role="submit"}
                </td>
            </tr>
            {*<tr>*}
                {*<td colspan="2"><hr style="border: 1px solid #000000;"></td>*}
            {*</tr>*}
            {*<tr>*}
                {*<td>*}
                    {*{include file="buttons/button.tpl" but_text="Бланки планирования для ЛЦ" but_name="dispatch[`$controller`.get_blank.planning_LC]" but_role="submit"}*}
                {*</td>*}
                {*<td>*}
                    {*<div class="mode_report_pumps {if $mode_report == "I"}hidden{/if}">*}
                        {*<label for="pump_id">Выбор насоса:</label>*}
                            {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                                {*f_id="pump_id"*}
                                {*f_type="select_by_group"*}
                                {*f_name="pump_id"*}
                                {*f_required=true f_integer_more_0=true*}
                                {*f_options="pumps"*}
                                {*f_option_id="p_id"*}
                                {*f_option_value="p_name"*}
                                {*f_optgroups=$pumps*}
                                {*f_optgroup_label="ps_name"*}
                                {*f_description=$lang.uns_accounting_main_unit*}
                                {*f_blank=true*}
                                {*f_simple=true*}
                            {*}*}
                    {*</div>*}
                {*</td>*}
            {*</tr>*}
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="Баланс по мех. цеху" but_name="dispatch[`$controller`.get_report.mc]" but_role="submit"}
                </td>
                <td>
                    <label>Отчет в виде пустого бланка: <input type="checkbox" name="as_blank" value="Y"/></label>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="Комплектация серии насосов" but_name="dispatch[`$controller`.get_report.test]" but_role="submit"}
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="Общий отчет" but_name="dispatch[`$controller`.get_report.general_report]" but_role="submit"}
                </td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
{*<code>*}
    {*1. Получить отчет по литейному цеху - <a href="{"uns_reports.get_report.foundry"|fn_url}">>></a>*}
{*</code>*}