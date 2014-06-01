{script src="js/tabs.js"}
{capture name="mainbox"}
    {capture name="search_content"}
        {include file="addons/uns/views/components/search/s_time.tpl"}
        {include file="addons/uns/views/components/search/s_details.tpl"}
        {include file="addons/uns/views/components/search/s_accessory_pumps.tpl"}
        {*Отобразить по всем категориям*}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td class="nowrap search-field">
                    <label for="all_details">Отобразить все детали:</label>
                    {*<div class="break">*}
                        {include file="addons/uns/views/components/get_form_field.tpl"
                            f_type="checkbox"
                            f_id="all_details"
                            f_name="all_details"
                            f_value=$search.all_details
                            f_style="margin-top:5px;"
                            f_simple=true
                        }
                    {*</div>*}
                </td>
            </tr>
        </table>
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
                    <th rowspan="3" style="border-left: 1px solid #808080;">{include file="common_templates/tooltip.tpl" tooltip="Номер клейма" tooltip_mark="<b>№</b>"}</th>
                    <th rowspan="3" style="border-right: 1px solid #808080;">&nbsp;</th>
                    <th colspan="4" style=" text-align: center;" width="110px">Мех. цех</th>
                    <th rowspan="3" style="border-left: 1px solid #808080; text-align: center;" width="0">Скл<br>КМП</th>
                    {*<th rowspan="3" style="border-left: 1px solid #808080; text-align: center; font-size: 35px; font-weight: normal;" width="0">&Sigma;</th>*}
                    {*<th rowspan="3" style="border-left: 1px solid #808080; text-align: center;" width="0">Сб.<br>Уч.</th>*}
                    {if $search.accessory_pumps == "Y"}
                        <th rowspan="3" style="border-left: 1px solid #808080; text-align: center;">Принадлежность к насосам</th>
                    {/if}
                </tr>
                <tr>
                    <th colspan="2" style="text-align: center; border-top: 1px solid #808080; border-right: 1px solid #808080;">№1</th>
                    <th colspan="2" style="text-align: center; border-top: 1px solid #808080;">№2</th>
                </tr>
                <tr>
                    <th style="text-align: center; border-top: 1px solid #808080; border-right: 1px solid #808080;">{include file="common_templates/tooltip.tpl" tooltip="Деталь еще в ОБРАБОТКЕ" tooltip_mark="<b>О</b>"}</th>
                    <th style="text-align: center; border-top: 1px solid #808080; border-right: 1px solid #808080;">{include file="common_templates/tooltip.tpl" tooltip="Деталь уже обработана, т.е. ЗАВЕРШЕНА" tooltip_mark="<b>З</b>"}</th>
                    <th style="text-align: center; border-top: 1px solid #808080; border-right: 1px solid #808080;">{include file="common_templates/tooltip.tpl" tooltip="Деталь еще в ОБРАБОТКЕ" tooltip_mark="<b>O</b>"}</th>
                    <th style="text-align: center; border-top: 1px solid #808080;">{include file="common_templates/tooltip.tpl" tooltip="Деталь уже обработана, т.е. ЗАВЕРШЕНА" tooltip_mark="<b>З</b>"}</th>
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
                        <th>Принадлежность к насосам</th>
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
                        <th>Принадлежность к насосам</th>
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
{assign var="last_date" value=$search.period|fn_get_period_name:$search.time_from:$search.time_to}
{*{assign var="last_date" value=$info_of_the_last_movement.date|fn_parse_date|date_format:"%d/%m/%Y"}*}
{*{assign var="last_document_id" value=$info_of_the_last_movement.document_id}*}
{include file="common_templates/mainbox.tpl" title="Баланс Мех.цеха, Скл. Комплектующих<br>`$last_date`" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
