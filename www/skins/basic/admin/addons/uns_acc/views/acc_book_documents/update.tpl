{if $smarty.request.lock == "Y"}
    {assign var="lock" value=true}
{/if}
{capture name="mainbox"}
    {assign var="i" value=$document}
    {if is__array($i)}
        {if $smarty.request.copy != "Y"}
            {assign var="id" value=$i.document_id}

            {if $i.document_type == $smarty.const.UNS_DOCUMENT__PRIH_ORD}
                {assign var="name" value="`$smarty.const.UNS_DOCUMENT__PRIH_ORD_NAME` №`$id`"}
            {elseif $i.document_type == $smarty.const.UNS_DOCUMENT__SDAT_N}
                {assign var="name" value="`$smarty.const.UNS_DOCUMENT__SDAT_N_NAME` №`$id`"}
            {elseif $i.document_type == $smarty.const.UNS_DOCUMENT__NOPM}
                {assign var="name" value="`$smarty.const.UNS_DOCUMENT__NOPM_NAME` №`$id`"}
            {else}
                {assign var="name" value=$i.document_type}
            {/if}



            {assign var="type" value=$i.document_type}
        {else}
            {assign var="id" value=0}
            {assign var="copy" value=true}
        {/if}
    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        <form action="{""|fn_url}" method="post" name="update_{$controller}_form_{$id}" class="cm-form-highlight">
            <input type="hidden" value="" name="selected_section">
            {capture name="tabsbox"}
                <div id="content_general">
                    {* Добавить описание документа *}
                    {include file="addons/uns_acc/views/components/types_of_documents.tpl" d=$i}
              </div>
            {/capture}

            {include file="common_templates/tabsbox.tpl" content=$smarty.capture.tabsbox active_tab=$smarty.request.selected_section track=true}

            {if !$lock}
            <div class="buttons-container cm-toggle-button buttons-bg">
                {include file="buttons/save_cancel.tpl" but_name="dispatch[`$controller`.update]"}
            </div>
            {/if}
        </form>
    </div>
{/capture}

{if $id > 0}
    {assign var="title" value="Редактирование: `$name`"}
{else}
    {assign var="title" value="Добавить"}
{/if}

{include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox}