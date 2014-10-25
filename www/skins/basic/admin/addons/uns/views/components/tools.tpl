{strip}
    {if $type == "edit"}
        <a class="uns-tool {$type}" {if $name}name="{$name}"{/if} {if $target}target="{$target}"{/if} href="{"`$href`"|fn_url}"></a>
    {/if}
    {if $type == "view_list"}
        <a class="uns-tool {$type}" {if $name}name="{$name}"{/if} {if $target}target="{$target}"{/if} href="{"`$href`"|fn_url}"></a>
    {/if}
{/strip}