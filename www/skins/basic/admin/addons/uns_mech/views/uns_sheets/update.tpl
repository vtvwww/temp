{capture name="mainbox"}
    {assign var="i" value=$sheet}
    {if is__array($i)}
        {assign var="id" value=$i.sheet_id}
        {capture name="name"}
            {$i.no} - {$i.date_open|date_format:"%d/%m/%Y"}
        {/capture}
        {assign var="name" value=$smarty.capture.name|trim}
    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
            {*{capture name="tabsbox"}*}
                {*<div id="content_general">*}
                    {* Добавить описание листа *}
                    {include file="addons/uns_mech/views/uns_sheets/components/sheet.tpl"}
                {*</div>*}
            {*{/capture}*}

            {*{include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}*}

            <div class="buttons-container cm-toggle-button buttons-bg">
                {if $mode == "update"}
                    {include file="common_templates/tools.tpl" tool_href="`$controller`.add" prefix="top" link_text="Добавить СЛ"  hide_tools=true}
                    {include file="common_templates/tools.tpl" tool_href="`$controller`.add&sheet_no=`$sheet.no+1`" prefix="top" link_text="Добавить СЛ ++"  hide_tools=false}
                    {include file="common_templates/tools.tpl" tool_href="`$controller`.delete&sheet_id=`$id`" prefix="top" link_text="Удалить этот СЛ"  hide_tools=false}
                {/if}
                {if $mode == "add"}
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]" hide_second_button=true}
                {else}
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]"}
                {/if}
            </div>
        </form>
        <br/>
        {* Добавить движения документа *}
        {if $id>0}
            {include file="addons/uns_mech/views/uns_sheets/components/motions.tpl"}
            {include file="addons/uns_mech/views/uns_sheets/components/ostatki.tpl"}
        {/if}
    </div>
    {*{capture name="tools"}*}
        {*{include file="common_templates/tools.tpl" tool_href="`$controller`.add" prefix="top" link_text="Добавить СЛ"  hide_tools=true}*}
        {*{if $mode == "update"}*}
            {*{include file="common_templates/tools.tpl" tool_href="`$controller`.add&sheet_no=`$sheet.no+1`" prefix="top" link_text="Добавить СЛ ++"  hide_tools=false}*}
            {*{include file="common_templates/tools.tpl" tool_href="`$controller`.delete&sheet_id=`$id`" prefix="top" link_text="Удалить этот СЛ"  hide_tools=false}*}
        {*{/if}*}
    {*{/capture}*}
{/capture}

{if $id > 0}
    {assign var="title" value="Редактировать СЛ №`$name`"}
{else}
    {assign var="title" value="Добавить новый СЛ"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}
{*{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox tools=$smarty.capture.tools bottom_tools=$smarty.capture.tools}*}

