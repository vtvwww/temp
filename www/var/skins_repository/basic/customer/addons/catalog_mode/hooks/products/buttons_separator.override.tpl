{if $product.company_id|fn_catalog_mode_enabled == 'Y'}
	{if $product.buy_now_url != ''}
		<span class="product-buttons-separator">{$lang.or}</span>
	{elseif $addons.catalog_mode.add_to_cart_empty_buy_now_url != 'Y'}
		&nbsp;
	{/if}
{/if}