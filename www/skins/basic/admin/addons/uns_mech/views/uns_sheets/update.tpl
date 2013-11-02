{capture name="mainbox"}
    {assign var="i" value=$sheet}
    {if is__array($i)}
        {assign var="id" value=$i.sheet_id}
        {capture name="name"}
            {$i.no} - {$i.date_open|date_format:"%d/%m/%Y"}
        {/capture}
        {assign var="name" value=$smarty.capture.name}
    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
            <input type="hidden" value="" name="selected_section">
            {capture name="tabsbox"}
                <div id="content_general">
                    {* Добавить описание листа *}
                    {include file="addons/uns_mech/views/uns_sheets/components/sheet.tpl"}

                    {if $id>0}{* Добавить движения документа *}
                        {include file="addons/uns_mech/views/uns_sheets/components/motions.tpl"}
                    {/if}
                </div>
            {/capture}

            {include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

            <div class="buttons-container cm-toggle-button buttons-bg">
                {if $mode == "add"}
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]" hide_second_button=true}
                {else}
                    {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]"}
                {/if}
            </div>
        </form>
    </div>
    {capture name="tools"}
        {include file="common_templates/tools.tpl" tool_href="`$controller`.add" prefix="top" link_text="Добавить новый Сопроводительный лист"  hide_tools=true}
    {/capture}
{/capture}

{if $id > 0}
    {assign var="title" value="Редактирование: `$name`"}
{else}
    {assign var="title" value="Добавить новый"}
{/if}
{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox tools=$smarty.capture.tools}

