{script src="js/tabs.js"}
{capture name="mainbox"}
    {include file="addons/uns/views/components/search/form.tpl"
        dispatch="`$controller`.manage"
        s_time=true
        s_materials=true
        s_objects=true
        s_mode_report=true
        s_view_all_position=true
    }

    <p style="font-size: 12px;">
        Период: <b>{$search.period|fn_get_period_name:$search.time_from:$search.time_to}</b>
        <br>
        Объект: <b>{$objects_plain[$search.o_id].path}</b>
    </p>

    <table cellpadding="0" cellspacing="0" border="0" class="table">
        <thead>
            <tr>
                <th style="text-align: center;" width="300px">
                    <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="/skins/basic/admin/images/plus_minus.gif">
                    <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="/skins/basic/admin/images/minus_plus.gif">
                    &nbsp;Наименование</th>
                <th style="text-align: center;" width="100px">Принадлежность</th>
                <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="80px">
                    Нач.&nbsp;ост.<br>
                    <span style="font-size: 9px; padding: 0;">({if $search.time_from == 0}Сначала{else}{$search.time_from|fn_parse_date|date_format:"%d/%m/%Y"}{/if})</span></th>
                <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="80px">Приход</th>
                <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="80px">Расход</th>
                <th style="border-left: 1px solid #EBEBEB; text-align: center;" width="80px">
                    Кон.&nbsp;ост.<br>
                    <span style="font-size: 9px; padding: 0;">({$search.time_to|fn_parse_date|date_format:"%d/%m/%Y"})</span></th>
            </tr>
        </thead>
        {foreach from=$balance item=i key=k}
            {include file="addons/uns_foundry/views/foundry_get_balance/components/view.tpl" item=$i key=$k}
        {foreachelse}
            <tr class="no-items">
                <td colspan="5"><p>{$lang.no_data}</p></td>
            </tr>
        {/foreach}
    </table>
{/capture}
{include file="common_templates/mainbox.tpl" title="Баланс по Складу Литья" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
