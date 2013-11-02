{if $tooltip}
    {if $tooltip_mark}
        <a class="cm-tooltip{if $params} {$params}{/if}" title="{$tooltip|escape:html}">{$tooltip_mark}</a>
    {else}
        <a class="cm-tooltip{if $params} {$params}{/if}" title="{$tooltip|escape:html}">(?)</a>
    {/if}
{/if}