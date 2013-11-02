{** block-description:tmpl_copyright **}
<p class="bottom-copyright">&copy; {if $smarty.const.TIME|date_format:"%Y" != $settings.Company.company_start_year}{$settings.Company.company_start_year}-{/if}{$smarty.const.TIME|date_format:"%Y"} {$settings.Company.company_name}. &nbsp;{$lang.powered_by} {$lang.copyright_shopping_cart}
</p>