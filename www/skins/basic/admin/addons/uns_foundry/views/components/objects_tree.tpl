{if $parent_id}
<div class="hidden" id="cat_{$parent_id}">
{/if}

{if !is_array($categories_tree)}
    {assign var="categories_tree" value=$objects}
{/if}

{foreach from=$categories_tree item=category}
    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="table table-fixed table-tree">
    {if $header && !$parent_id}
        {assign var="header" value=""}
        <tr>
            <th class="center" width="3%">
                <input type="checkbox" name="check_all" value="Y" title="{$lang.check_uncheck_all}" class="checkbox cm-check-items" /></th>
            <th width="95%">
                <div class="float-left">
                    <img src="{$images_dir}/plus_minus.gif" width="13" height="12" border="0" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" id="on_cat" class="hand cm-combinations{if $expand_all} hidden{/if}" />
                    <img src="{$images_dir}/minus_plus.gif" width="13" height="12" border="0" alt="{$lang.expand_collapse_list}" title="{$lang.expand_collapse_list}" id="off_cat" class="hand cm-combinations{if !$expand_all} hidden{/if}" />
                </div>
                &nbsp;{$lang.name}
            </th>
            <th width="3%">Поз.</th>
            <th width="30%">&nbsp;</th>
        </tr>
    {/if}

    <tr {if $category.level > 0}class="multiple-table-row"{/if}>
        {math equation="x*35" x=$category.level|default:"0" assign="shift"}
        {assign var="id" value=$category.o_id}
        {assign var="value" value="o_id"}
        {assign var="name" value=$category.o_name}
        <td class="center" width="3%">
            <input type="checkbox" name="o_ids[]" value="{$id}" class="checkbox cm-item" />
        </td>
        <td width="100%">
            {strip}
            <span class="strong" style="padding-left: {$shift}px;">
                {if $category.has_children || $category.subcategories}
                    <img src="{$images_dir}/plus.gif" width="14" height="9" border="0" alt="{$lang.expand_sublist_of_items}" title="{$lang.expand_sublist_of_items}" id="on_cat_{$id}" class="hand cm-combination {if $expand_all}hidden{/if}" />
                    <img src="{$images_dir}/minus.gif" width="14" height="9" border="0" alt="{$lang.collapse_sublist_of_items}" title="{$lang.collapse_sublist_of_items}" id="off_cat_{$id}" class="hand cm-combination{if !$expand_all || !$show_all} hidden{/if}" />&nbsp;
                {/if}
                <span {if !$category.subcategories} style="padding-left: 4px;" class="normal"{/if} >{$name}
            </span>
            {/strip}
        </td>
        <td width="3%">
            {$category.o_position}
        </td>
        <td width="30%" class="nowrap right">
            {capture name="tools_items"}
                <li><a class="" href="{"`$controller`.add?`$value`=`$id`&add_child=Y"|fn_url}">Добавить вложенный объект</a></li>
                <li><a class="" href="{"`$controller`.update?`$value`=`$id`&copy=Y"|fn_url}">Копировать</a></li>
                <li><a class="cm-confirm" href="{"`$controller`.delete?`$value`=`$id`"|fn_url}">{$lang.delete}</a></li>
            {/capture}
            {include    file="common_templates/table_tools_list.tpl"
                        id="`$controller``$id`"
                        text="`$lang.uns_detail_category`: `$name`</b>"
                        act="edit"
                        href="`$controller`.update?`$value`=`$id`"
                        prefix=$id
                        tools_list=$smarty.capture.tools_items}
        </td>
    </tr>

    </table>
{if $category.has_children || $category.subcategories}
	<div{if !$expand_all} class="hidden"{/if} id="cat_{$category.o_id}">
	{if $category.subcategories}
        {include file="addons/uns_acc/views/components/objects_tree.tpl" categories_tree=$category.subcategories parent_id=false}
	{/if}
	<!--cat_{$category.o_id}--></div>
{/if}
{/foreach}
{if $parent_id}<!--cat_{$parent_id}--></div>{/if}
