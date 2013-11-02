{script src="js/tabs.js"}
{capture name="mainbox"}
    <form action="{""|fn_url}" method="post" name="category_tree_form" class="{if ""|fn_check_form_permissions}cm-hide-inputs{/if}">
        <div class="items-container multi-level">
            {if $objects}
                {include file="addons/uns_acc/views/components/objects_tree.tpl" header="1" parent_id=false}
            {else}
                <p class="no-items">{$lang.no_items}</p>
            {/if}
        </div>

    {if $objects}
        <div class="buttons-container buttons-bg">
            <div class="float-left">
            {include file="buttons/button.tpl" but_name="dispatch[`$controller`.delete]" but_text=$lang.delete_selected but_role="button_main" but_meta="cm-process-items cm-confirm"}
            </div>
        </div>
    {/if}

    {capture name="tools"}
        {include file="common_templates/tools.tpl" tool_href="`$controller`.add" prefix="top" link_text=$lang.uns_new_object hide_tools=true}
    {/capture}
    </form>
{/capture}

{include file="common_templates/mainbox.tpl" title=$lang.uns_objects content=$smarty.capture.mainbox tools=$smarty.capture.tools}
