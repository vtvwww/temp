{strip}
{*{assign var="s_time"        value=true}*}
{*{assign var="s_materials"   value=true}*}
{*{assign var="s_details"     value=false}*}
{*{assign var="s_objects"     value=true}*}
{*{assign var="s_mode_report" value="P"}*}
{* P - отчет в разрезе насоса,  I - отчет в разрезе элементов*}

{capture name="section"}
    <form action="{""|fn_url}" name="search_form" method="get">
        {* Добавить ФИЛЬТРАЦИЮ по ВРЕМЕНИ *}
        {if $s_time}
            {include file="addons/uns/views/components/search/s_time.tpl"}
        {/if}

        {* Добавить ФИЛЬТРАЦИЮ по МАТЕРИАЛАМ *}
        {if $s_materials}
            {include file="addons/uns/views/components/search/s_materials.tpl"}
        {/if}

        {* Добавить ФИЛЬТРАЦИЮ по ДЕТАЛЯМ *}
        {if $s_details}
            {*{include file="addons/uns/views/components/search/add_materials.tpl"}*}
        {/if}

        {* Добавить ФИЛЬТРАЦИЮ по НАСОСАМ *}
        {if $s_pumps}
            {*{include file="addons/uns/views/components/search/add_materials.tpl"}*}
        {/if}

        {* Добавить ФИЛЬТРАЦИЮ по ОБЪЕКТАМ *}
        {if $s_objects}
            {include file="addons/uns/views/components/search/s_objects.tpl" lock_change=true}
        {/if}

        {* Способ представления отчета *}
        {if $s_mode_report}
            {include file="addons/uns/views/components/search/s_mode_report.tpl"}
        {/if}

        {* Отображать все позиции отчета *}
        {if $s_view_all_position}
            {include file="addons/uns/views/components/search/s_view_all_position.tpl"}
        {/if}

        {**************************************************************************}
        <table cellpadding="10" cellspacing="0" border="0" class="search-header">
            <tr>
                <td>
                    {include file="buttons/button.tpl" but_text="П О И С К" but_name="dispatch[`$dispatch`]" but_role="big" but_input_css="width:918px;font-weight:bold;"}
                    {*{include file="buttons/button.tpl" but_text=$lang.search but_name="dispatch[`$dispatch`]" but_role="submit"}*}
                </td>
            </tr>
        </table>
    </form>
{/capture}
{include file="common_templates/section.tpl" section_content=$smarty.capture.section}
{/strip}