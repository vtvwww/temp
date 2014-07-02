{if $action == "pump"}
{foreach from=$ps_sets key="ps_id" item="pump_series"}
    {include file="addons/uns_plans/views/uns_plan_of_mech_dep/analysis_of_pump.tpl" ps_set_id=$ps_id pump_series=$pump_series}
{/foreach}
{else}
    {if $action == "allowance" or $action == "prohibition" or $action == "all"}
        {capture name="mainbox"}
            {foreach from=$ps_sets key="ps_id" item="pump_series"}
                {include file="addons/uns_plans/views/uns_plan_of_mech_dep/analysis_of_pump.tpl" ps_set_id=$ps_id pump_series=$pump_series}
            {/foreach}
        {/capture}
    {/if}
    {if $action == "allowance"}
        {assign var="title" value="Анализ РАЗРЕШЕННЫХ насосов на `$months_full[$data.month]` `$data.year` г. (`$data.current_day`)"}
    {elseif $action == "prohibition"}
        {assign var="title" value="Анализ ЗАПРЕЩЕННЫХ насосов на `$months_full[$data.month]` `$data.year` г. (`$data.current_day`)"}
    {else}
        {assign var="title" value="Анализ ВСЕХ насосов на `$months_full[$data.month]` `$data.year` г. (`$data.current_day`)"}
    {/if}

    {include file="common_templates/mainbox.tpl" title=$title content=$smarty.capture.mainbox tools=$smarty.capture.tools}
{/if}




