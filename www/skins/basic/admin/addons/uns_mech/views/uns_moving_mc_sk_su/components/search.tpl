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
                <td class="nowrap search-field b1_l">
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
                <td class="nowrap search-field b1_l">
                    <label>Выбор документа:</label>
                    <div class="break">
                        <input type="hidden"                        name="include_sheets" value="N"/>
                        <input type="checkbox" id="include_sheets"  name="include_sheets" value="Y" {if $search.include_sheets == "Y"}checked="checked"{/if}/>
                        <label style="font-size: 11px; color: #555; float: none; padding: 0;" for="include_sheets">по Сопровод. листам</label>
                    </div>
                    <div class="break">
                        <input type="hidden"                        name="include_kits" value="N"/>
                        <input type="checkbox" id="include_kits"  name="include_kits" value="Y" {if $search.include_kits == "Y"}checked="checked"{/if}/>
                        <label style="font-size: 11px; color: #555; float: none; padding: 0;" for="include_kits">по Партиям насосов</label>
                    </div>
                </td>
            </tr>
        </table>

        {if $auth.usergroup_ids[0] == 6 or $auth.usergroup_ids[0] == 8 or $auth.usergroup_ids[0] == 10}
            <hr/>
            <table cellpadding="10" cellspacing="0" border="0" class="search-header">
                <tr>
                    <td class="nowrap search-field">
                        <label>Только Расходный ордер:</label>
                        <div class="break">
                            <select name="type" id="">
                                <option value="0">---</option>
                                <option {if $search.type == 7}selected="selected"{/if} value="7">{$document_types[7].name}</option> {*RO*}
                            </select>
                        </div>
                    </td>
                    <td class="nowrap search-field b1_l">
                        <label for="country_id">Страна Клиента:</label>
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
                        <label for="country_id">Регион/Область Клиента:</label>
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
                </tr>
            </table>
        {else}
            <input type="hidden" id="exclude_type" name="exclude_type" value="7"/>
        {/if}

        {**************************************************************************}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="П О И С К" but_name="dispatch[`$dispatch`]" but_role="big" but_input_css="width:918px;font-weight:bold;"}
                </td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
{/strip}