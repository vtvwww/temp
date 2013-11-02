{$lang.do_not_change}

<div id="advanced_settings">
<fieldset>

	<div class="form-field">
		<label class="cm-required" for="elm_tw_http_location">{$lang.http_location}:</label>
		<input type="text" id="elm_tw_http_location" name="tw_settings[HTTPLocation]"  value="{if $tw_settings.HTTPLocation}{$tw_settings.HTTPLocation}{else}http://{$config.http_host}{$config.http_path}/{/if}" class="input-text-large" size="60" />
	</div>

	<div class="form-field">
		<label class="cm-required" for="elm_tw_https_location">{$lang.https_location}:</label>
		<input type="text" id="elm_tw_https_location" name="tw_settings[HTTPSLocation]"  value="{if $tw_settings.HTTPSLocation}{$tw_settings.HTTPSLocation}{else}https://{$config.https_host}{$config.https_path}/{/if}" class="input-text-large" size="60" />
	</div>

	<div class="form-field">
		<label class="cm-required" for="elm_tw_customer_index">{$lang.customer_index}:</label>
		<input type="text" id="elm_tw_customer_index" name="tw_settings[customerIndex]"  value="{$tw_settings.customerIndex|default:$config.customer_index}" class="input-text-large" size="60" />
	</div>

	<div class="form-field">
		<label for="elm_tw_enable_direct_requests">{$lang.enable_direct_requests}:</label>
		<input type="hidden" name="tw_settings[directRequestsEnabled]" value="N" />
		<input type="checkbox" class="checkbox" id="elm_tw_enable_direct_requests" name="tw_settings[directRequestsEnabled]" value="Y" {if !$tw_settings.directRequestsEnabled || $tw_settings.directRequestsEnabled == "Y"}checked="checked"{/if} />
	</div>

</fieldset>
<!--advanced_settings--></div>