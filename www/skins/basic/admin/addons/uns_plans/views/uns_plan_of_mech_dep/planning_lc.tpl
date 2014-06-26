{capture name="mainbox"}
    <p>{$prohibition_of_casts|count}</p>
    <table cellpadding="0" cellspacing="0" border="0" class="table">
        <thead>
            <tr style="background-color: #D4D0C8;">
                <th rowspan="2" style="text-align: center;" width="250px">
                    <img id="on_cat" class="hand cm-combinations hidden" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/plus_minus.gif">
                    <img id="off_cat" class="hand cm-combinations" width="13" height="12" border="0" title="Расширить / сократить список элементов" alt="Расширить / сократить список элементов" src="skins/basic/admin/images/minus_plus.gif">
                    &nbsp;Наименование
                </th>

                {*Запрет*}
                <th rowspan="2" class="" style="text-align: center;" width="10px">&nbsp;</th>

                <th rowspan="2" class="b1_r b1_l" style="text-align: center;" width="25px">{include file="common_templates/tooltip.tpl" tooltip='Вес 1шт, кг'  tooltip_mark="<b>Вес</b>"}</th>

                <th colspan="4" class="b1_r b1_l center" style="text-transform: none;" width="60px">
                    План произ-<br>водства отливок
                </th>
                <th colspan="4" class="b1_r b1_l center" style="text-transform: none;" width="60px">
                    СКЛАД ЛИТЬЯ
                </th>
                <th colspan="4" class="b1_r b1_l center" style="text-transform: none;" width="60px">
                    ОСТАЛОСЬ
                </th>
                <th rowspan="2" class="b_l">Принадлежность к насосам</th>
            </tr>
            <tr style="background-color: #D4D0C8;">
                {*ПЛАН*}
                <th style="width: 30px;" class="center b_l">{$data.tpl_curr_month|replace:".":".<br>20"}</th>
                <th style="width: 30px;" class="center b1_l">{$data.tpl_next_month|replace:".":".<br>20"}</th>
                <th style="width: 30px;" class="center b1_l">{$data.tpl_next2_month|replace:".":".<br>20"}</th>
                <th style="width: 30px;" class="center b1_l">{$data.tpl_next3_month|replace:".":".<br>20"}</th>

                {*СКЛАД ЛИТЬЯ*}
                <th style="width: 30px;" class="center b_l">НО</th>
                <th style="width: 30px;" class="center b_l">П</th>
                <th style="width: 30px;" class="center b_l">Р</th>
                <th style="width: 30px;" class="center b_l">КО</th>

                {*ОСТАЛОСЬ*}
                <th style="width: 30px;" class="center b_l">{$data.tpl_curr_month|replace:".":".<br>20"}</th>
                <th style="width: 30px;" class="center b1_l">{$data.tpl_next_month|replace:".":".<br>20"}</th>
                <th style="width: 30px;" class="center b1_l">{$data.tpl_next2_month|replace:".":".<br>20"}</th>
                <th style="width: 30px;" class="center b1_l">{$data.tpl_next3_month|replace:".":".<br>20"}</th>
            </tr>
        </thead>
        {foreach from=$balance_of_casts item=i key=k}
            {include file="addons/uns_plans/views/uns_plan_of_mech_dep/components/view_planning_lc.tpl" item=$i key=$k}
        {foreachelse}
            <tr class="no-items">
                <td colspan="5"><p>{$lang.no_data}</p></td>
            </tr>
        {/foreach}
            <tr class="no-items">
                <td colspan="3" align="right"><b>Итого, т:</b></td>
                <td><b>{$requirement_of_casts.curr_month.total_weight/1000|fn_fvalue:1:0}</b></td>
                <td><b>{$requirement_of_casts.next_month.total_weight/1000|fn_fvalue:1:0}</b></td>
                <td><b>{$requirement_of_casts.next2_month.total_weight/1000|fn_fvalue:1:0}</b></td>
                <td><b>{$requirement_of_casts.next3_month.total_weight/1000|fn_fvalue:1:0}</b></td>
                <td colspan="4" align="right">&nbsp;</td>
                <td><b>{$remaining_of_casts.curr_month.total_weight/1000|fn_fvalue:1:0}</b></td>
                <td><b>{$remaining_of_casts.next_month.total_weight/1000|fn_fvalue:1:0}</b></td>
                <td><b>{$remaining_of_casts.next2_month.total_weight/1000|fn_fvalue:1:0}</b></td>
                <td><b>{$remaining_of_casts.next3_month.total_weight/1000|fn_fvalue:1:0}</b></td>
                <td>&nbsp;</td>
            </tr>
    </table>
{/capture}
{include file="common_templates/mainbox.tpl" title="План производства ЛИТЕЙНОГО ЦЕХА на `$data.current_day`" content=$smarty.capture.mainbox tools=$smarty.capture.tools}