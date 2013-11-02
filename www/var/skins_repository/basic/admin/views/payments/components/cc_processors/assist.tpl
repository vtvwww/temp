<div> 
{$lang.text_assist_notice}
</div>
<hr />

<div class="form-field">
	<label for="merchant_id">{$lang.merchant_id}:</label>
	<input type="text" name="payment_data[processor_params][merchant_id]" id="merchant_id" value="{$processor_params.merchant_id}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="login">{$lang.login}:</label>
	<input type="text" name="payment_data[processor_params][login]" id="login" value="{$processor_params.login}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="password">{$lang.password}:</label>
	<input type="text" name="payment_data[processor_params][password]" id="password" value="{$processor_params.password}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="language">{$lang.language}:</label>
	<select name="payment_data[processor_params][language]" id="language">
		<option value="RU" {if $processor_params.language == "RU"}selected="selected"{/if}>{$lang.russian}</option>
		<option value="EN" {if $processor_params.language == "EN"}selected="selected"{/if}>{$lang.english}</option>
	</select>
</div>

<div class="form-field">
	<label for="mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="mode">
		<option value="T" {if $processor_params.mode == "T"}selected="selected"{/if}>{$lang.test}</option>
		<option value="L" {if $processor_params.mode == "L"}selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="order_prefix">{$lang.order_prefix}:</label>
	<input type="text" name="payment_data[processor_params][order_prefix]" id="order_prefix" value="{$processor_params.order_prefix}" class="input-text" />
</div>