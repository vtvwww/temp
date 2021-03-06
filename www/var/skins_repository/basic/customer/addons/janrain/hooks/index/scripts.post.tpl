{if $smarty.request.return_url}
	{assign var="escaped_return_url" value=$smarty.request.return_url|escape:url}
{else}
	{assign var="escaped_return_url" value=$config.current_url|escape:url}
{/if}

<script type="text/javascript">
//<![CDATA[
{literal}
(function() {
	if (typeof window.janrain !== 'object') window.janrain = {};
	var _languages = ['ar', 'bg', 'cs', 'da', 'de', 'el', 'en', 'es', 'fi', 'fr', 'he', 'hr', 'hu', 'id', 'it', 'ja', 'lt', 'nb', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sk', 'sl', 'sv', 'th', 'uk', 'zh'];
	window.janrain.settings = {
		type: 'modal',
		language: ($.inArray(cart_language.toLowerCase(), _languages) ? cart_language.toLowerCase() : 'en'),
{/literal}
		tokenUrl: '{"auth.login?return_url=`$escaped_return_url`"|fn_url:"C":"current":"&"}'
{literal}
	};

	function isReady() { janrain.ready = true; };
	if (document.addEventListener) {
		document.addEventListener("DOMContentLoaded", isReady, false);
	} else {
		window.attachEvent('onload', isReady);
	}

	var e = document.createElement('script');
	e.type = 'text/javascript';
	e.id = 'janrainAuthWidget';

	if (document.location.protocol === 'https:') {
{/literal}
		e.src = 'https://rpxnow.com/js/lib/{$addons.janrain.appdomain|fn_janrain_parse_app_domain}/engage.js';
{literal}
	} else {
{/literal}
		e.src = 'http://widget-cdn.rpxnow.com/js/lib/{$addons.janrain.appdomain|fn_janrain_parse_app_domain}/engage.js';
{literal}
	}

	var s = document.getElementsByTagName('script')[0];
	s.parentNode.insertBefore(e, s);
})();
{/literal}
//]]>
</script>