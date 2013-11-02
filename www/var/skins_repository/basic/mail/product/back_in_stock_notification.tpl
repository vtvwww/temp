{include file="letter_header.tpl"}

{$lang.dear} {$lang.customer},<br /><br />

{$lang.back_in_stock_notification_header|unescape}<br /><br />

<b><a href="{"products.view?product_id=`$product_id`"|fn_url:'C':'http':'&amp;'}">{$product|unescape}</a></b><br /><br />

{$lang.back_in_stock_notification_footer|unescape}<br />

{include file="letter_footer.tpl"}