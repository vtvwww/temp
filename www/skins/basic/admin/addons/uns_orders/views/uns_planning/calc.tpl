{script src="js/tabs.js"}
{capture name="mainbox"}
    {include file="addons/uns_orders/views/uns_planning/components/search_form.tpl" dispatch="`$controller`.manage" search_content=$smarty.capture.search_content}

    <form action="{""|fn_url}" method="post" name="{$controller}_form" class="{if ""|fn_check_form_permissions} cm-hide-inputs{/if}">
        {include file="common_templates/pagination.tpl"}
        <table cellpadding="0" cellspacing="0" border="0" width="" class="table">
            <tr>
                <th width="10px">&nbsp;</th>
                <th width="50px">Месяц</th>
                <th width="50px">Год</th>
                <th width="10px">Кол-во</th>
                <th width="300px">Коментарий</th>
                <th>&nbsp;</th>
            </tr>
            {foreach from=$plans item="i" name="p"}
                <tr>
                    {assign var="id" value=$i.plan_id}
                    {assign var="value" value="plan_id"}
                    <td>
                        {include file="addons/uns/views/components/tools.tpl" type="edit" href="`$controller`.update?`$value`=`$id`"}
                    </td>
                    <td align="right"> {* Месяц *}
                        {$months[$i.month]}
                    </td>
                    <td align="left"> {* Год *}
                        {$i.year}
                    </td>
                    <td align="right">  {*ПОЗИЦИЙ*}
                        {$i.count}
                    </td>
                    <td>  {*Комментарий*}
                        {$i.comment}&nbsp;
                    </td>
                    <td class="nowrap right">
                        {capture name="tools_items"}
                            <li><a class="cm-confirm" href="{"`$controller`.delete?`$value`=`$id`"|fn_url}">
                                    <img border="0" src="skins/basic/admin/addons/uns_acc/images/delete.png">
                                </a>
                            </li>
                        {/capture}
                        {include    file="common_templates/table_tools_list.tpl"
                                    id="`$controller``$id`"
                                    text="`$lang.uns_pumps`: `$name`</b>"
                                    act="edit"
                                    prefix=$id
                                    tools_list=$smarty.capture.tools_items}
                    </td>
                </tr>
                {foreachelse}
                <tr class="no-items">
                    <td colspan="70"><p>{$lang.no_items}</p></td>
                </tr>
            {/foreach}
        </table>
        {include file="common_templates/pagination.tpl"}
    </form>
{/capture}
{include file="common_templates/mainbox.tpl" title="Расчет плана производства" content=$smarty.capture.mainbox tools=$smarty.capture.tools}
