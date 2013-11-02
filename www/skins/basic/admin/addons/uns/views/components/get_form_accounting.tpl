{assign var="ai" value=$ai__accounting}
{if is__array($ai)}
    {assign var="id" value=$ai.ai_id}
{else}
    {assign var="id" value=0}
{/if}

{include file="common_templates/subheader.tpl" title=$lang.uns_accounting_main_unit}
{include file="addons/uns/views/components/get_form_accounting_main.tpl" ai_main=$ai id_main=$id}

{include file="common_templates/subheader.tpl" title="Удельный вес"}
{if $item_type != "D"}
    {include file="addons/uns/views/components/get_form_accounting_weights.tpl"            ws=$ai.weights typesize=$smarty.const.UNS_TYPESIZE__M}
{else}
    <table border="1">
        <tr>
            <th><b>Исполнение БАЗОВОЕ</b></th>
            <th><b>Исполнение А</b></th>
            <th><b>Исполнение Б</b></th>
        </tr>
        <tr>
            <td valign="top">
                {include file="addons/uns/views/components/get_form_accounting_weights.tpl" ws=$ai.weights typesize=$smarty.const.UNS_TYPESIZE__M }
            </td>
            <td valign="top">
                {include file="addons/uns/views/components/get_form_accounting_weights.tpl" ws=$ai.weights typesize=$smarty.const.UNS_TYPESIZE__A typesize_status=$detail.size_a}
            </td>
            <td valign="top">
                {include file="addons/uns/views/components/get_form_accounting_weights.tpl" ws=$ai.weights typesize=$smarty.const.UNS_TYPESIZE__B typesize_status=$detail.size_b}
            </td>
        </tr>
    </table>
{/if}

{if $item_type == "D"}
    {include file="common_templates/subheader.tpl" title="Исходные материалы"}
    {include file="addons/uns/views/components/get_form_accounting_materials.tpl" am=$ai.materials}
{/if}
