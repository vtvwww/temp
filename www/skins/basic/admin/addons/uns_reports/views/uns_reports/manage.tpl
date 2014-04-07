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
            <tr>
                <td><hr/></td>
            </tr>
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="Отчет планирования" but_name="dispatch[`$controller`.get_report.planning_report]" but_role="submit"}
                    на:
                    <select name="planning[month]">
                        <option value="1" {if date("n") == 1}selected="selected"{/if}>Январь</option>
                        <option value="2" {if date("n") == 2}selected="selected"{/if}>Февраль</option>
                        <option value="3" {if date("n") == 3}selected="selected"{/if}>Март</option>
                        <option value="4" {if date("n") == 4}selected="selected"{/if}>Апрель</option>
                        <option value="5" {if date("n") == 5}selected="selected"{/if}>Май</option>
                        <option value="6" {if date("n") == 6}selected="selected"{/if}>Июнь</option>
                        <option value="7" {if date("n") == 7}selected="selected"{/if}>Июль</option>
                        <option value="8" {if date("n") == 8}selected="selected"{/if}>Август</option>
                        <option value="9" {if date("n") == 9}selected="selected"{/if}>Сентябрь</option>
                        <option value="10" {if date("n") == 10}selected="selected"{/if}>Октябрь</option>
                        <option value="11" {if date("n") == 11}selected="selected"{/if}>Ноябрь</option>
                        <option value="12" {if date("n") == 12}selected="selected"{/if}>Декабрь</option>
                    </select>
                    <select name="planning[year]">
                        <option value="2013" {if date("Y") == 2013}selected="selected"{/if}>2013</option>
                        <option value="2014" {if date("Y") == 2014}selected="selected"{/if}>2014</option>
                        <option value="2015" {if date("Y") == 2015}selected="selected"{/if}>2015</option>
                        <option value="2016" {if date("Y") == 2016}selected="selected"{/if}>2016</option>
                        <option value="2017" {if date("Y") == 2017}selected="selected"{/if}>2017</option>
                        <option value="2018" {if date("Y") == 2018}selected="selected"{/if}>2018</option>
                        <option value="2019" {if date("Y") == 2019}selected="selected"{/if}>2019</option>
                        <option value="2020" {if date("Y") == 2020}selected="selected"{/if}>2020</option>
                        <option value="2021" {if date("Y") == 2021}selected="selected"{/if}>2021</option>
                        <option value="2022" {if date("Y") == 2022}selected="selected"{/if}>2022</option>
                        <option value="2023" {if date("Y") == 2023}selected="selected"{/if}>2023</option>
                        <option value="2024" {if date("Y") == 2024}selected="selected"{/if}>2024</option>
                        <option value="2025" {if date("Y") == 2025}selected="selected"{/if}>2025</option>
                        <option value="2026" {if date("Y") == 2026}selected="selected"{/if}>2026</option>
                        <option value="2027" {if date("Y") == 2027}selected="selected"{/if}>2027</option>
                        <option value="2028" {if date("Y") == 2028}selected="selected"{/if}>2028</option>
                        <option value="2029" {if date("Y") == 2029}selected="selected"{/if}>2029</option>
                        <option value="2030" {if date("Y") == 2030}selected="selected"{/if}>2030</option>
                    </select>
                </td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
{*<code>*}
    {*1. Получить отчет по литейному цеху - <a href="{"uns_reports.get_report.foundry"|fn_url}">>></a>*}
{*</code>*}