{script src="js/tabs.js"}
{capture name="mainbox"}
    {capture name="search_content"}
        {include file="addons/uns/views/components/search/s_time.tpl"}
        <hr/>
        {include file="addons/uns/views/components/search/s_details.tpl"}
    {/capture}
    {include file="addons/uns/views/components/search/search.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}

{if $search.total_balance_of_details == "Y"}
    {if $search.mode_report == "P"}
    {else}
        <table cellpadding="0" cellspacing="0" border="0" class="table" style="background-color: #F7F7F7;background-position: center 45px;">
            <thead>
                <tr>
                    <th rowspan="3" style="text-align: center; " width="300px">
                        <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                        <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                        &nbsp;Наименование</th>
                    <th rowspan="3" class="center b1_l" style="text-transform: none; font-size: 11px;">Клеймо</th>
                    <th colspan="4" class="b_l center" style="width: 110px; text-transform: none;">МЕХАНИЧЕСКИЙ ЦЕХ</th>
                    <th rowspan="3" class="b_l center" style="width:30px;">{include file="common_templates/tooltip.tpl" tooltip="Склад комплектующих" tooltip_mark="<b>Скл<br>КМП</b>"}</th>
                    <th rowspan="3" class="b_l center" style="text-transform: none;">Применяемость в насосах</th>
                </tr>
                <tr>
                    <th colspan="2" class="center b_l b1_t">№1</th>
                    <th colspan="2" class="center b_l b1_t">№2</th>
                </tr>
                <tr>
                    <th class="center b1_t b_l"  style="width:30px;">{include file="common_templates/tooltip.tpl" tooltip="Деталь еще в ОБРАБОТКЕ" tooltip_mark="<b>О</b>"}</th>
                    <th class="center b1_t b1_l" style="width:30px;">{include file="common_templates/tooltip.tpl" tooltip="Деталь уже обработана, т.е. ЗАВЕРШЕНА" tooltip_mark="<b>З</b>"}</th>
                    <th class="center b1_t b_l"  style="width:30px;">{include file="common_templates/tooltip.tpl" tooltip="Деталь еще в ОБРАБОТКЕ" tooltip_mark="<b>O</b>"}</th>
                    <th class="center b1_t b1_l" style="width:30px;">{include file="common_templates/tooltip.tpl" tooltip="Деталь уже обработана, т.е. ЗАВЕРШЕНА" tooltip_mark="<b>З</b>"}</th>
                </tr>
            </thead>
            {if is__array($balances)}
                {include file="addons/uns_mech/views/uns_balance_mc_sk_su/components/view_total_balance_of_details.tpl" balances=$balances}
            {else}
                <tbody>
                    <tr class="no-items">
                        <td colspan="10" style="background-color: #F7F7F7;"><p>{$lang.no_data}</p></td>
                    </tr>
                </tbody>
            {/if}
        </table>
    {/if}

{else}
    <p style="font-size: 12px;">
        Период: <b>{$search.period|fn_get_period_name:$search.time_from:$search.time_to}</b>
        <br>
        Объект: <b>{$objects_plain[$search.o_id].path}</b>
    </p>

    {if $search.mode_report == "P"}
        <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
                <tr>
                    <th style="text-align: center;" width="300px">
                        <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                        <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                        &nbsp;Наименование</th>
                    <th style="text-align: center;" width="80px">{include file="common_templates/tooltip.tpl" tooltip='Общее требуемое кол-во литейных заготовок на изготовление одной единицы насоса'  tooltip_mark="Кол."}</th>
                    <th style="text-align: center;" width="40px">Вес<br><span style="font-size: 9px; padding: 0; text-transform: none;">в кг 1 шт.</span></th>
                    <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="80px">
                        Нач.&nbsp;ост.<br>
                        <span style="font-size: 9px; padding: 0;">({if $search.time_from == 0}Сначала{else}{$search.time_from|fn_parse_date|date_format:"%d/%m/%y"}{/if})</span></th>
                    <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="80px">Приход</th>
                    <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="80px">Расход</th>
                    <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="80px">
                        Кон.&nbsp;ост.<br>
                        <span style="font-size: 9px; padding: 0;">({$search.time_to|fn_parse_date|date_format:"%d/%m/%y"})</span></th>
                    {if $search.accessory_pumps == "Y"}
                        <th>Применяемость в насосах</th>
                    {/if}
                </tr>
            </thead>
            {foreach from=$balance item=i key=k}
                {include file="addons/uns_mech/views/uns_balance_mc_sk_su/components/view.tpl" item=$i key=$k mode_report=$search.mode_report pump_materials=$search.pump_materials}
            {foreachelse}
                <tr class="no-items">
                            <td colspan="5"><p>{$lang.no_data}</p></td>
                        </tr>
                    {/foreach}
                </table>
    {else}
                <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
                <tr>
                    <th style="text-align: center;" width="300px">
                        <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                        <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                        &nbsp;Наименование</th>
                    <th>&nbsp;</th>
                    <th style="text-align: center;" width="40px">Вес<br><span style="font-size: 9px; padding: 0; text-transform: none;">в кг 1 шт.</span></th>
                    <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="65px">
                        Нач.&nbsp;ост.<br>
                        <span style="font-size: 9px; padding: 0;">({if $search.time_from == 0}Сначала{else}{$search.time_from|fn_parse_date|date_format:"%d/%m/%y"}{/if})</span></th>
                    <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="65px">Приход</th>
                    <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="65px">Расход</th>
                    <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="65px">
                        Кон.&nbsp;ост.<br>
                        <span style="font-size: 9px; padding: 0;">({$search.time_to|fn_parse_date|date_format:"%d/%m/%y"})</span></th>
                    {if $search.accessory_pumps == "Y"}
                        <th>Применяемость в насосах</th>
                    {/if}
                </tr>
            </thead>
            {foreach from=$balance item=i key=k}
                {include file="addons/uns_mech/views/uns_balance_mc_sk_su/components/view.tpl" item=$i key=$k}
            {foreachelse}
                <tr class="no-items">
                    <td colspan="5"><p>{$lang.no_data}</p></td>
                </tr>
            {/foreach}
        </table>
    {/if}
{/if}
{/capture}
{assign var="time_from" value=$search.time_from|fn_parse_date|date_format:"%d/%m/%Y"}
{assign var="time_to" value=$search.time_to|fn_parse_date|date_format:"%d/%m/%Y"}
{include file="common_templates/mainbox.tpl" title="Баланс Мех.цеха, Склада комплектующих (`$time_from` - `$time_to`)" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
