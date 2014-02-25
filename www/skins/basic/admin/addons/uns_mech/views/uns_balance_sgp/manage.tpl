{script src="js/tabs.js"}
{capture name="mainbox"}
    {capture name="search_content"}
        {include file="addons/uns/views/components/search/s_time.tpl"}
        {*<span style="color: #FF0000;display: block;font-size: 11px;font-weight: bold;margin-left: 337px;margin-top: -10px;padding: 0;">Важной является конечная дата!</span>*}
        {*{include file="addons/uns/views/components/search/s_details.tpl"}*}
        {*{include file="addons/uns_mech/views/uns_balance_mc_sk_su/components/s_objects.tpl" }*}
        {*{include file="addons/uns/views/components/search/s_mode_report.tpl"}*}
        {*{include file="addons/uns/views/components/search/s_view_all_position.tpl"}*}
        {*{include file="addons/uns/views/components/search/s_accessory_pumps.tpl"}*}
    {/capture}
    {include file="addons/uns/views/components/search/search.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}

{if $search.total_balance_of_details == "Y"}
    {if $search.mode_report == "P"}
    {else}
        {* БАЛАНС ПО НАСОСНОЙ ПРОДУКЦИИ *}
        <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
                <tr>
                    <th rowspan="3" style="text-align: center; " width="300px">
                        <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                        <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                        &nbsp;Наименование
                    </th>
                    <th style="border-left: 1px solid #808080;">Насос</th>
                    <th style="border-left: 1px solid #808080;">На раме</th>
                    <th style="border-left: 1px solid #808080;">Агрегат</th>
                </tr>
            </thead>
            {if is__array($balances)}
                {include file="addons/uns_mech/views/uns_balance_sgp/components/view_pumps.tpl" balances=$balances}
            {else}
                <tbody>
                    <tr class="no-items">
                        <td colspan="7"><p>{$lang.no_data}</p></td>
                    </tr>
                </tbody>
            {/if}
        </table>

        <br>
        <br>
        {* БАЛАНС ПО ДЕТАЛЯМ НА СГП *}
        <table cellpadding="0" cellspacing="0" border="0" class="table">
            <thead>
                <tr>
                    <th style="text-align: center; " width="300px">
                        <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                        <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                        &nbsp;Наименование
                    </th>
                    <th style="border-left: 1px solid #808080;">КЛМ</th>
                    <th style="border-left: 1px solid #808080;">СГП</th>
                    <th style="border-left: 1px solid #808080;">Принадлежность к насосам</th>
                </tr>
            </thead>
            {if is__array($balances.D)}
                {include file="addons/uns_mech/views/uns_balance_sgp/components/view_details.tpl" balances=$balances.D}
            {else}
                <tbody>
                    <tr class="no-items">
                        <td colspan="7"><p>{$lang.no_data}</p></td>
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
{assign var="last_date" value=$info_of_the_last_movement.date|fn_parse_date|date_format:"%d/%m/%Y"}
{assign var="last_document_id" value=$info_of_the_last_movement.document_id}
{include file="common_templates/mainbox.tpl" title="Баланс СКЛАДА ГОТОВОЙ ПРОДУКЦИИ `$last_date` [`$last_document_id`]" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
