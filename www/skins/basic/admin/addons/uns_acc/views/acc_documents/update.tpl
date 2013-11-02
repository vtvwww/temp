{capture name="mainbox"}
    {assign var="i" value=$document}
    {if is__array($i)}
        {assign var="id" value=$i.document_id}
        {assign var="name" value="№`$id` - `$document_types[$i.type].name`"}
    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
            <input type="hidden" value="" name="selected_section">
            {capture name="tabsbox"}
                <div id="content_general">
                    {* Добавить описание документа *}
                    {include file="addons/uns_acc/views/acc_documents/components/document.tpl" d=$i}

                    {if $id>0}
                    {* Добавить позиции документа *}
                    {include file="addons/uns_acc/views/acc_documents/components/document_items.tpl" d=$i}
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
{/capture}

{if $id > 0}
    {assign var="title" value="Редактирование: `$name`"}
{else}
    {assign var="title" value="Добавить новый документ"}
{/if}

{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}