{script src="js/tabs.js"}
{capture name="mainbox"}
    {capture name="search_content"}
        {include file="addons/uns/views/components/search/s_time.tpl"}
        {include file="addons/uns/views/components/search/s_materials.tpl" material_classes_as_input=true}
        {*{include file="addons/uns/views/components/search/s_mode_report.tpl"}*}
        {*{include file="addons/uns/views/components/search/s_view_all_position.tpl"}*}
        {*{include file="addons/uns/views/components/search/s_accessory_pumps.tpl"}*}
    {/capture}
    {include file="addons/uns/views/components/search/search.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}

    {*<p style="font-size: 12px;">*}
        {*Период: <b>{$search.period|fn_get_period_name:$search.time_from:$search.time_to}</b>*}
        {*<br>*}
        {*Объект: <b>{$objects_plain[$search.o_id].path}</b>*}
    {*</p>*}

    <table cellpadding="0" cellspacing="0" border="0" class="table">
        <thead>
            <tr>
                <th style="text-align: center;" width="250px">
                    <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                    <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                    &nbsp;Наименование
                </th>

                {if $search.mode_report == "P"}
                    <th style="text-align: center;" width="80px">{include file="common_templates/tooltip.tpl" tooltip='Общее требуемое кол-во литейных заготовок на изготовление одной единицы насоса'  tooltip_mark="Кол."}</th>
                {else}
                    <th>&nbsp;</th>
                {/if}
                <th class="b1_l" style="text-align: center;" width="25px">{include file="common_templates/tooltip.tpl" tooltip='Вес 1шт, кг'  tooltip_mark="<b>Вес</b>"}</th>
                <th class="b_l" style="text-align: center;" width="30px">
                    {include file="common_templates/tooltip.tpl" tooltip='Начальный остаток'  tooltip_mark="<b>НО</b>"}
                </th>
                <th class="b_l" style="text-align: center;" width="30px">{include file="common_templates/tooltip.tpl" tooltip='Приход'  tooltip_mark="<b>П</b>"}</th>
                <th class="b1_l" style="text-align: center;" width="30px">{include file="common_templates/tooltip.tpl" tooltip='Расход'  tooltip_mark="<b>Р</b>"}</th>
                <th class="b_l" style="background-color: #d3d3d3; text-align: center;" width="30px">
                    {include file="common_templates/tooltip.tpl" tooltip='Конечный остаток'  tooltip_mark="<b>КО</b>"}
                </th>
                {if $search.accessory_pumps == "Y"}
                    <th class="b_l">Применяемость в насосах</th>
                {/if}
            </tr>
        </thead>
        {if $balance|is__array}
            {*Список отливок*}
            {foreach from=$balance item=i key=k}
                {include file="addons/uns_foundry/views/foundry_get_balance/components/view.tpl" item=$i key=$k mode_report=$search.mode_report pump_materials=$search.pump_materials}
            {/foreach}

            {*Итого Кол-во*}
            <tr>
                <td style="background-color: #d3d3d3;" class="hand right  bold b2_t" colspan="2" rowspan="2">ИТОГО</td>
                <td style="background-color: #d3d3d3;" class="hand center  bold b2_t b1_l" colspan="1">шт</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b2_t b_l" >{if $amounts.n}{$amounts.n}{else}&nbsp;{/if}</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b2_t b_l" >{if $amounts.p}{$amounts.p}{else}&nbsp;{/if}</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b2_t b1_l">{if $amounts.r}{$amounts.r}{else}&nbsp;{/if}</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b2_t b_l" >{if $weights.k}{$amounts.k}{else}&nbsp;{/if}</td>
                <td style="background-color: #d3d3d3;" class="hand left bold   b2_t b_l"></td>
            </tr>
            {*Итого ВЕС*}
            <tr>
                <td style="background-color: #d3d3d3;" class="hand center  bold b1_t b1_l" colspan="1">т</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b1_t b_l"  title="{$weights.n|number_format:1:".":" "} кг">{if $weights.n}{$weights.n/1000|number_format:1:".":" "}{else}&nbsp;{/if}</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b1_t b_l"  title="{$weights.p|number_format:1:".":" "} кг">{if $weights.p}{$weights.p/1000|number_format:1:".":" "}{else}&nbsp;{/if}</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b1_t b1_l" title="{$weights.r|number_format:1:".":" "} кг">{if $weights.r}{$weights.r/1000|number_format:1:".":" "}{else}&nbsp;{/if}</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b1_t b_l"  title="{$weights.k|number_format:1:".":" "} кг">{if $weights.k}{$weights.k/1000|number_format:1:".":" "}{else}&nbsp;{/if}</td>
                <td style="background-color: #d3d3d3;" class="hand left bold   b1_t b_l"><span class="info_warning">в кг при наведении курсора</span></td>
            </tr>
            {*Средний вес отливки*}
            <tr>
                <td style="background-color: #d3d3d3;" class="hand right  bold b1_t" colspan="2">Средний вес отливки</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b1_t b1_l" colspan="1">кг</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b1_t b_l" >{if $weights.n>0 and $amounts.n>0}{$weights.n/$amounts.n|number_format:1}{else}&nbsp;{/if}</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b1_t b_l" >{if $weights.p>0 and $amounts.p>0}{$weights.p/$amounts.p|number_format:1}{else}&nbsp;{/if}</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b1_t b1_l">{if $weights.r>0 and $amounts.r>0}{$weights.r/$amounts.r|number_format:1}{else}&nbsp;{/if}</td>
                <td style="background-color: #d3d3d3;" class="hand center bold b1_t b_l" >{if $weights.k>0 and $amounts.k>0}{$weights.k/$amounts.k|number_format:1}{else}&nbsp;{/if}</td>
                <td style="background-color: #d3d3d3;" class="hand left bold   b1_t b_l"></td>
            </tr>
        {else}
            <tr class="no-items">
                <td style="background-color: #F7F7F7;" colspan="10">Выберите категорию отливок</td>
            </tr>
        {/if}
    </table>
{/capture}
{assign var="time_from" value=$search.time_from|fn_parse_date|date_format:"%d/%m/%Y"}
{assign var="time_to" value=$search.time_to|fn_parse_date|date_format:"%d/%m/%Y"}
{include file="common_templates/mainbox.tpl" title="Баланс по Складу Литья (`$time_from` - `$time_to`)" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
