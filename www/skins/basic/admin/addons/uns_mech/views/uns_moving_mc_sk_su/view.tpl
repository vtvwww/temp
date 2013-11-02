{capture name="mainbox"}
    {assign var="i" value=$document}
    {if is__array($i)}
        {assign var="id" value=$i.document_id}
        {assign var="name" value="№`$id` - `$document_types[$i.type].name`"}
        {assign var="doc_name" value="<b>№`$id`</b> - `$document_types[$i.type].name`" }

    {else}
        {assign var="id" value=0}
    {/if}

    <div id="content_group">
        {* Добавить описание документа *}
        {include file="addons/uns_acc/views/acc_documents/components/document__view.tpl" d=$i}

        {if $id>0}
        {* Добавить позиции документа *}
        {include file="addons/uns_acc/views/acc_documents/components/document_items__view.tpl" d=$i}
        {/if}
    </div>
{/capture}

{include file="common_templates/mainbox.tpl" title=$doc_name content=$smarty.capture.mainbox}