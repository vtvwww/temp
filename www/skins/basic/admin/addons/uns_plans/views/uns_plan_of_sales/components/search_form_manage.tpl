{capture name="search_content"}
    <table cellpadding="10" cellspacing="0" border="0" class="search-header">
        <tr>
            <td class="nowrap search-field">
                <label>Месяц/Год:</label>
                <div class="break">
                    <select name="month">
                        <option value="0">---</option>
                        <option value="1"  {if $search.month == 1   or (!$search.month and date('m') == 1 )}selected="selected"{/if}>Январь</option>
                        <option value="2"  {if $search.month == 2   or (!$search.month and date('m') == 2 )}selected="selected"{/if}>Февраль</option>
                        <option value="3"  {if $search.month == 3   or (!$search.month and date('m') == 3 )}selected="selected"{/if}>Март</option>
                        <option value="4"  {if $search.month == 4   or (!$search.month and date('m') == 4 )}selected="selected"{/if}>Апрель</option>
                        <option value="5"  {if $search.month == 5   or (!$search.month and date('m') == 5 )}selected="selected"{/if}>Май</option>
                        <option value="6"  {if $search.month == 6   or (!$search.month and date('m') == 6 )}selected="selected"{/if}>Июнь</option>
                        <option value="7"  {if $search.month == 7   or (!$search.month and date('m') == 7 )}selected="selected"{/if}>Июль</option>
                        <option value="8"  {if $search.month == 8   or (!$search.month and date('m') == 8 )}selected="selected"{/if}>Август</option>
                        <option value="9"  {if $search.month == 9   or (!$search.month and date('m') == 9 )}selected="selected"{/if}>Сентябрь</option>
                        <option value="10" {if $search.month == 10  or (!$search.month and date('m') == 10)}selected="selected"{/if}>Октябрь</option>
                        <option value="11" {if $search.month == 11  or (!$search.month and date('m') == 11)}selected="selected"{/if}>Ноябрь</option>
                        <option value="12" {if $search.month == 12  or (!$search.month and date('m') == 12)}selected="selected"{/if}>Декабрь</option>
                    </select>
                    <select name="year">
                        <option value="0">---</option>
                        <option value="2014" {if $search.year == 2014 or (!$search.year and date('Y') == 2014) }selected="selected"{/if}>2014</option>
                        <option value="2015" {if $search.year == 2015 or (!$search.year and date('Y') == 2015) }selected="selected"{/if}>2015</option>
                        <option value="2016" {if $search.year == 2016 or (!$search.year and date('Y') == 2016) }selected="selected"{/if}>2016</option>
                        <option value="2017" {if $search.year == 2017 or (!$search.year and date('Y') == 2017) }selected="selected"{/if}>2017</option>
                        <option value="2018" {if $search.year == 2018 or (!$search.year and date('Y') == 2018) }selected="selected"{/if}>2018</option>
                        <option value="2019" {if $search.year == 2019 or (!$search.year and date('Y') == 2019) }selected="selected"{/if}>2019</option>
                        <option value="2020" {if $search.year == 2020 or (!$search.year and date('Y') == 2020) }selected="selected"{/if}>2020</option>
                        <option value="2021" {if $search.year == 2021 or (!$search.year and date('Y') == 2021) }selected="selected"{/if}>2021</option>
                        <option value="2022" {if $search.year == 2022 or (!$search.year and date('Y') == 2022) }selected="selected"{/if}>2022</option>
                        <option value="2023" {if $search.year == 2023 or (!$search.year and date('Y') == 2023) }selected="selected"{/if}>2023</option>
                        <option value="2024" {if $search.year == 2024 or (!$search.year and date('Y') == 2024) }selected="selected"{/if}>2024</option>
                        <option value="2025" {if $search.year == 2025 or (!$search.year and date('Y') == 2025) }selected="selected"{/if}>2025</option>
                        <option value="2026" {if $search.year == 2026 or (!$search.year and date('Y') == 2026) }selected="selected"{/if}>2026</option>
                        <option value="2027" {if $search.year == 2027 or (!$search.year and date('Y') == 2027) }selected="selected"{/if}>2027</option>
                        <option value="2028" {if $search.year == 2028 or (!$search.year and date('Y') == 2028) }selected="selected"{/if}>2028</option>
                        <option value="2029" {if $search.year == 2029 or (!$search.year and date('Y') == 2029) }selected="selected"{/if}>2029</option>
                        <option value="2030" {if $search.year == 2030 or (!$search.year and date('Y') == 2030) }selected="selected"{/if}>2030</option>
                    </select>
                </div>
            </td>
        </tr>
    </table>
{/capture}
{include file="addons/uns/views/components/search/search.tpl" dispatch="`$controller`.`$mode`" search_content=$smarty.capture.search_content}
