{if $cart.points_info.in_use}
{assign var="_redirect_url" value=$config.current_url|escape:url}
	<tr>
		<td>
			{$lang.points_in_use}&nbsp;({$cart.points_info.in_use.points}&nbsp;{$lang.points}){if $settings.General.checkout_style != "multi_page"}{include file="buttons/button.tpl" but_href="checkout.delete_points_in_use?redirect_url=`$_redirect_url`" but_meta="delete-icon" but_role="delete" but_rev="checkout_totals,subtotal_price_in_points,checkout_steps`$additional_ids`"}{/if}
		</td>
		<td class="right discount-price">
			-{include file="common_templates/price.tpl" value=$cart.points_info.in_use.cost}
		</td>
	</tr>
{/if}