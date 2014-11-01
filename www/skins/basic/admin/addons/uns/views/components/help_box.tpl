{strip}
    <fieldset class="help close">
        <legend title="Развернуть/Свернуть" onclick="help_box($(this));">{if $legend|strlen}{$legend}{else}Справка{/if}</legend>
        {$info_text}
    </fieldset>
{/strip}