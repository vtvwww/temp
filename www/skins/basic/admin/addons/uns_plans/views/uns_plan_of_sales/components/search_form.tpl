{capture name="search_content"}
    {literal}
        <style>
            .search-field label{
                margin: 0; padding: 0; height: 35px;
            }
        </style>
    {/literal}
    <table cellpadding="10" cellspacing="0" border="0" class="search-header">
        <tr>
            <td class="nowrap search-field">
                <label>Расчетный месяц</label>
                <div class="break">
                    <select name="month">
                        <option value="0">---</option>
                        <option value="1"  {if $search.month == 1}selected="selected"{/if}>Январь</option>
                        <option value="2"  {if $search.month == 2}selected="selected"{/if}>Февраль</option>
                        <option value="3"  {if $search.month == 3}selected="selected"{/if}>Март</option>
                        <option value="4"  {if $search.month == 4}selected="selected"{/if}>Апрель</option>
                        <option value="5"  {if $search.month == 5}selected="selected"{/if}>Май</option>
                        <option value="6"  {if $search.month == 6}selected="selected"{/if}>Июнь</option>
                        <option value="7"  {if $search.month == 7}selected="selected"{/if}>Июль</option>
                        <option value="8"  {if $search.month == 8}selected="selected"{/if}>Август</option>
                        <option value="9"  {if $search.month == 9}selected="selected"{/if}>Сентябрь</option>
                        <option value="10" {if $search.month == 10}selected="selected"{/if}>Октябрь</option>
                        <option value="11" {if $search.month == 11}selected="selected"{/if}>Ноябрь</option>
                        <option value="12" {if $search.month == 12}selected="selected"{/if}>Декабрь</option>
                    </select>
                    <select name="year">
                        <option value="0">---</option>
                        <option value="2014" {if $search.year == 2014}selected="selected"{/if}>2014</option>
                        <option value="2015" {if $search.year == 2015}selected="selected"{/if}>2015</option>
                        <option value="2016" {if $search.year == 2016}selected="selected"{/if}>2016</option>
                        <option value="2017" {if $search.year == 2017}selected="selected"{/if}>2017</option>
                        <option value="2018" {if $search.year == 2018}selected="selected"{/if}>2018</option>
                        <option value="2019" {if $search.year == 2019}selected="selected"{/if}>2019</option>
                        <option value="2020" {if $search.year == 2020}selected="selected"{/if}>2020</option>
                        <option value="2021" {if $search.year == 2021}selected="selected"{/if}>2021</option>
                        <option value="2022" {if $search.year == 2022}selected="selected"{/if}>2022</option>
                        <option value="2023" {if $search.year == 2023}selected="selected"{/if}>2023</option>
                        <option value="2024" {if $search.year == 2024}selected="selected"{/if}>2024</option>
                        <option value="2025" {if $search.year == 2025}selected="selected"{/if}>2025</option>
                        <option value="2026" {if $search.year == 2026}selected="selected"{/if}>2026</option>
                        <option value="2027" {if $search.year == 2027}selected="selected"{/if}>2027</option>
                        <option value="2028" {if $search.year == 2028}selected="selected"{/if}>2028</option>
                        <option value="2029" {if $search.year == 2029}selected="selected"{/if}>2029</option>
                        <option value="2030" {if $search.year == 2030}selected="selected"{/if}>2030</option>
                    </select>
                </div>
            </td>
            {*<td class="nowrap search-field" style="border-left: 1px solid #808080;">*}
                {*<label>Дополнительный запас продукции<br>на конец расчетного месяца</label>*}
                {*<div class="break">*}
                    {*<select name="week_supply">*}
                        {*<option value="0">---</option>*}
                        {*<option value="1"  {if $search.week_supply == 1}selected="selected"{/if}>+ 1-о недельные продажи</option>*}
                        {*<option value="2"  {if $search.week_supply == 2}selected="selected"{/if}>+ 2-х недельные продажи</option>*}
                        {*<option value="3"  {if $search.week_supply == 3}selected="selected"{/if}>+ 3-х недельные продажи</option>*}
                        {*<option value="4"  {if $search.week_supply == 4}selected="selected"{/if}>+ 4-х недельные продажи</option>*}
                        {*<option value="5"  {if $search.week_supply == 5}selected="selected"{/if}>+ 5-ти недельные продажи</option>*}
                        {*<option value="6"  {if $search.week_supply == 6}selected="selected"{/if}>+ 6-ти недельные продажи</option>*}
                    {*</select>*}
                {*</div>*}
            {*</td>*}
            {*<td class="nowrap search-field">*}
                {*<label>Кол-во лет<br>для статистического<br>анализа продаж:</label>*}
                {*<div class="break">*}
                    {*<select name="years_for_analysis">*}
                        {*<option value="0">---</option>*}
                        {*<option value="2"  {if $search.years_for_analysis == 2}selected="selected"{/if}>за последних 2 года</option>*}
                        {*<option value="3" disabled  {if $search.years_for_analysis == 3}selected="selected"{/if}>за последних 3 года</option>*}
                        {*<option value="4" disabled {if $search.years_for_analysis == 4}selected="selected"{/if}>за последних 4 года</option>*}
                        {*<option value="5" disabled {if $search.years_for_analysis == 5}selected="selected"{/if}>за последних 5 лет</option>*}
                    {*</select>*}
                {*</div>*}
            {*</td>*}
            <td class="nowrap search-field" style="border-left: 1px solid #808080;">
                <label>Если <b>ПЛАН ПРОДАЖ &le; УЖЕ ИМЕЮЩИМСЯ ЗАКАЗАМ</b>,<br>тогда <b>ПЛАН ПРОДАЖ = ИМЕЮЩИЕСЯ ЗАКАЗЫ + X%</b></label>
                <div class="break">
                    <select name="koef_plan_prodazh">
                        <option value="0">0</option>
                        <option value="10" {if $search.koef_plan_prodazh == 10}selected="selected"{/if}>+10% от имеющихся заказов</option>
                        <option value="20" {if $search.koef_plan_prodazh == 20}selected="selected"{/if}>+20% от имеющихся заказов</option>
                        <option value="30" {if $search.koef_plan_prodazh == 30}selected="selected"{/if}>+30% от имеющихся заказов</option>
                        <option value="40" {if $search.koef_plan_prodazh == 40}selected="selected"{/if}>+40% от имеющихся заказов</option>
                        <option value="50" {if $search.koef_plan_prodazh == 50}selected="selected"{/if}>+50% от имеющихся заказов</option>
                        <option value="60" {if $search.koef_plan_prodazh == 60}selected="selected"{/if}>+60% от имеющихся заказов</option>
                        <option value="70" {if $search.koef_plan_prodazh == 70}selected="selected"{/if}>+70% от имеющихся заказов</option>
                        <option value="80" {if $search.koef_plan_prodazh == 80}selected="selected"{/if}>+80% от имеющихся заказов</option>
                        <option value="90" {if $search.koef_plan_prodazh == 90}selected="selected"{/if}>+90% от имеющихся заказов</option>
                    </select>
                </div>
            </td>
        </tr>
    </table>
{/capture}
{include file="addons/uns/views/components/search/search.tpl" but_text="ВЫПОЛНИТЬ РАСЧЕТ" dispatch="`$controller`.`$mode`" search_content=$smarty.capture.search_content}
