{******************************************************************************}
{* ТИП ДОКУМЕНТА *}
{******************************************************************************}
{if $mode == "add"}
    {assign var="plan_id"   value=0}
    {assign var="details"   value=""}
    {assign var="disabled"  value=false}

{elseif $mode == "update"}
    {assign var="plan_id"   value=$plan.plan_id}
    {assign var="items"     value=$plan.items}
    {assign var="disabled"  value=true}
{/if}

{assign var="e_n" value="data[plan]"}

{include file="addons/uns/views/components/get_form_field.tpl"
    f_type="hidden"
    f_name="plan_id"
    f_value=$plan_id}

{include file="addons/uns/views/components/get_form_field.tpl"
    f_type="hidden"
    f_name="`$e_n`[type]"
    f_value="sales"}

{*МЕСЯЦ*}
<div class="form-field">
    <label class="cm-required" for="plan_month">Месяц:</label>
    <select name="{$e_n}[month]" id="plan_month">
        <option value="">---</option>
        <option value="1"  {if $p.month == 1} selected="selected"{/if}>Январь</option>
        <option value="2"  {if $p.month == 2} selected="selected"{/if}>Февраль</option>
        <option value="3"  {if $p.month == 3} selected="selected"{/if}>Март</option>
        <option value="4"  {if $p.month == 4} selected="selected"{/if}>Апрель</option>
        <option value="5"  {if $p.month == 5} selected="selected"{/if}>Май</option>
        <option value="6"  {if $p.month == 6} selected="selected"{/if}>Июнь</option>
        <option value="7"  {if $p.month == 7} selected="selected"{/if}>Июль</option>
        <option value="8"  {if $p.month == 8} selected="selected"{/if}>Август</option>
        <option value="9"  {if $p.month == 9} selected="selected"{/if}>Сентябрь</option>
        <option value="10" {if $p.month == 10}selected="selected"{/if}>Октябрь</option>
        <option value="11" {if $p.month == 11}selected="selected"{/if}>Ноябрь</option>
        <option value="12" {if $p.month == 12}selected="selected"{/if}>Декабрь</option>
    </select>
</div>

{*ГОД*}
<div class="form-field">
    <label class="cm-required" for="plan_year">Год:</label>
    <select name="{$e_n}[year]" id="plan_year">
        <option value="">---</option>
        <option value="2014" {if $p.year == 2014}selected="selected"{/if}>2014</option>
        <option value="2015" {if $p.year == 2015}selected="selected"{/if}>2015</option>
        <option value="2016" {if $p.year == 2016}selected="selected"{/if}>2016</option>
        <option value="2017" {if $p.year == 2017}selected="selected"{/if}>2017</option>
        <option value="2018" {if $p.year == 2018}selected="selected"{/if}>2018</option>
        <option value="2019" {if $p.year == 2019}selected="selected"{/if}>2019</option>
        <option value="2020" {if $p.year == 2020}selected="selected"{/if}>2020</option>
        <option value="2021" {if $p.year == 2021}selected="selected"{/if}>2021</option>
        <option value="2022" {if $p.year == 2022}selected="selected"{/if}>2022</option>
        <option value="2023" {if $p.year == 2023}selected="selected"{/if}>2023</option>
        <option value="2024" {if $p.year == 2024}selected="selected"{/if}>2024</option>
        <option value="2025" {if $p.year == 2025}selected="selected"{/if}>2025</option>
        <option value="2026" {if $p.year == 2026}selected="selected"{/if}>2026</option>
        <option value="2027" {if $p.year == 2027}selected="selected"{/if}>2027</option>
        <option value="2028" {if $p.year == 2028}selected="selected"{/if}>2028</option>
        <option value="2029" {if $p.year == 2029}selected="selected"{/if}>2029</option>
        <option value="2030" {if $p.year == 2030}selected="selected"{/if}>2030</option>
    </select>
</div>

{*КОММЕНТАРИЙ*}
{include file="addons/uns/views/components/get_form_field.tpl"
    f_id="comment"
    f_type="textarea"
    f_row=1
    f_required=false f_integer=false
    f_full_name="`$e_n`[comment]"
    f_value=$p.comment
    f_description="Комментарий"
}
