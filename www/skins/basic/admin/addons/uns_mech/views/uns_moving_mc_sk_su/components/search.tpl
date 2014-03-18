{strip}
{capture name="section"}
    <form action="{""|fn_url}" name="search_form" method="get">
        {* Дата проведение документа *}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td class="nowrap search-field">
                    <label>Дата проведения документа:</label>
                    <div class="break">
                        {include file="common_templates/period_selector.tpl" period=$search.period prefix=""}
                    </div>
                </td>
            </tr>
        </table>

        {*<table cellpadding="10" cellspacing="0" border="0" class="search-header">*}
            {*<tr>*}
                {*<td class="nowrap search-field">*}
                    {*<label>Тип документа:</label>*}
                    {*<div class="break">*}
                        {*{include file="addons/uns/views/components/get_form_field.tpl"*}
                            {*f_type="document_type"*}
                            {*f_name="type"*}
                            {*f_options=$document_types*}
                            {*f_with_id=true*}
                            {*f_target=$search.type*}
                            {*f_blank=true*}
                            {*f_simple=true*}
                        {*}*}
                    {*</div>*}
                {*</td>*}
            {*</tr>*}
        {*</table>*}

        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td rowspan="2" class="nowrap search-field" style="padding-right: 10px">
                    <label>Выбор объекта:</label>
                    <div class="break">
                        <select name="o_id" id="">
                            <option value="0">---</option>
                            <option {if $search.o_id == 10}selected="selected"{/if} value="10">Мех.цех №1</option>
                            <option {if $search.o_id == 14}selected="selected"{/if} value="14">Мех.цех №2</option>
                            <option {if $search.o_id == 17}selected="selected"{/if} value="17">Склад комплектующих</option>
                            <option {if $search.o_id == 18}selected="selected"{/if} value="18">Сборочный участок</option>
                            <option {if $search.o_id == 19}selected="selected"{/if} value="19">Склад готовой продукции</option>
                        </select>
                    </div>
                </td>
                <td align="right" style="padding: 0;" class="nowrap search-field">
                    <input type="hidden"                        name="include_sheets" value="N"/>
                    <input type="checkbox" id="include_sheets"  name="include_sheets" value="Y" {if $search.include_sheets == "Y"}checked="checked"{/if}/>
                </td>
                <td align="left" style="padding: 0;" class="nowrap search-field">
                    <label for="include_sheets">Отобразить документы по СОПРОВОДИТЕЛЬНЫМ ЛИСТАМ</label>
                </td>
            </tr>
            <tr>
                <td align="right" style="padding: 0;" class="nowrap search-field">
                    <input type="hidden"                        name="include_kits" value="N"/>
                    <input type="checkbox" id="include_kits"  name="include_kits" value="Y" {if $search.include_kits == "Y"}checked="checked"{/if}/>
                </td>
                <td align="left" style="padding: 0;" class="nowrap search-field">
                    <label for="include_kits">Отобразить документы по ПАРТИЯМ ДЕТАЛЕЙ</label>
                </td>
            </tr>
        </table>

        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td rowspan="2" class="nowrap search-field" style="padding-right: 10px">
                    <label>Тип документа:</label>
                    <div class="break">
                        <select name="type" id="">
                            <option value="0">---</option>
                            <option {if $search.type == 7}selected="selected"{/if} value="7">{$document_types[7].name}</option> {*RO*}
                        </select>
                    </div>
                </td>
            </tr>
        </table>

        {**************************************************************************}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="П о и с к" but_name="dispatch[`$dispatch`]" but_role="big" but_input_css="width:888px;font-weight:bold;"}
                </td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
{/strip}