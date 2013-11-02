
{if $completed_steps.step_two}
	<h4>{$lang.billing_address}:</h4>

	{assign var="profile_fields" value="I"|fn_get_profile_fields}
	<ul class="shipping-adress clearfix">
		{foreach from=$profile_fields.B item="field"}
			<li class="{$field.field_name|replace:"_":"-"}">{$cart.user_data[$field.field_name]}</li>
		{/foreach}
	</ul>

	<hr />

	<h4>{$lang.shipping_address}:</h4>
	<ul class="shipping-adress clearfix">
	{foreach from=$profile_fields.S item="field"}
		<li class="{$field.field_name|replace:"_":"-"}">{$cart.user_data[$field.field_name]}</li>
	{/foreach}
	</ul>

	{if $cart.shipping}
		<hr /><h4>{$lang.shipping_method}:</h4>
		<ul>
			{foreach from=$cart.shipping item="shipping"}
				<li>{$shipping.shipping}</li>
			{/foreach}
		</ul>
	{/if}
{/if}

{assign var="block_wrap" value="checkout_order_info_`$block.snapping_id`_wrap"}
