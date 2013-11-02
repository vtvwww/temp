{assign var="r_url" value="payment_notification.result?payment=robokassa"|fn_url:'C':'http'}
{assign var="text_robokassa_notice" value=$lang.text_robokassa_notice|replace:"[r_url]":$r_url}
{assign var="p_url" value="payment_notification.return?payment=robokassa"|fn_url:'C':'http'}
{assign var="text_robokassa_notice" value=$text_robokassa_notice|replace:"[p_url]":$p_url} 
{assign var="f_url" value="payment_notification.cancel?payment=robokassa"|fn_url:'C':'http'}
{assign var="text_robokassa_notice" value=$text_robokassa_notice|replace:"[f_url]":$f_url}

<div>
	{$text_robokassa_notice}
</div> 
<hr />

<div class="form-field">
	<label for="rbx_merchantid">{$lang.merchantid}:</label>
	<input type="text" name="payment_data[processor_params][merchantid]" id="rbx_merchantid" value="{$processor_params.merchantid}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="rbx_password1">{$lang.password1}:</label>
	<input type="text" name="payment_data[processor_params][password1]" id="rbx_password1" value="{$processor_params.password1}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="rbx_password2">{$lang.password2}:</label>
	<input type="text" name="payment_data[processor_params][password2]" id="rbx_password2" value="{$processor_params.password2}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="rbx_descr">{$lang.description}:</label>
	<input type="text" name="payment_data[processor_params][details]" id="rbx_descr" value="{$processor_params.details}" class="input-text" size="60" />
</div>

<div class="form-field">
	<label for="rbx_mode">{$lang.test_live_mode}:</label>
	<select name="payment_data[processor_params][mode]" id="rbx_mode">
		<option value="test"{if $processor_params.mode == 'test'} selected="selected"{/if}>{$lang.test}</option>
		<option value="live"{if $processor_params.mode == 'live'} selected="selected"{/if}>{$lang.live}</option>
	</select>
</div>

<div class="form-field">
	<label for="rbx_currency">{$lang.currency}:</label>
	<select name="payment_data[processor_params][currency]" id="rbx_currency">
		<optgroup label="Bank cards">
		<option value="BANKOCEANMR" {if $processor_params.currency == "BANKOCEANMR"}selected="selected"{/if}>By bank card</option>
		<option value="OceanBankOceanR" {if $processor_params.currency == "WMGM"}selected="selected"{/if}>By bank card via Platezh.ru</option>
		</optgroup>
		<optgroup label="Electronic currencies">
		<option value="QiwiR" {if $processor_params.currency == "QiwiR"}selected="selected"{/if}>QIWI Wallet</option>
		<option value="PCR" {if $processor_params.currency == "PCR"}selected="selected"{/if}>Yandex.Money</option>
		<option value="WMRM" {if $processor_params.currency == "WMRM"}selected="selected"{/if}>WMR</option>
		<option value="WMZM" {if $processor_params.currency == "WMZM"}selected="selected"{/if}>WMZ</option>
		<option value="WMEM" {if $processor_params.currency == "WMEM"}selected="selected"{/if}>WME</option>
		<option value="WMUM" {if $processor_params.currency == "WMUM"}selected="selected"{/if}>WMU</option>
		<option value="WMBM" {if $processor_params.currency == "WMBM"}selected="selected"{/if}>WMB</option>
		<option value="WMGM" {if $processor_params.currency == "WMGM"}selected="selected"{/if}>WMG</option>
		<option value="MoneyMailR" {if $processor_params.currency == "MoneyMailR"}selected="selected"{/if}>RUR MoneyMail</option>
		<option value="RuPayR" {if $processor_params.currency == "RuPayR"}selected="selected"{/if}>RUR RBK Money</option>
		<option value="W1R" {if $processor_params.currency == "W1R"}selected="selected"{/if}>RUR W1</option>
		<option value="EasyPayB" {if $processor_params.currency == "EasyPayB"}selected="selected"{/if}>EasyPay</option>
		<option value="LiqPayZ" {if $processor_params.currency == "LiqPayZ"}selected="selected"{/if}>USD LiqPay</option>
		<option value="WebCredsR" {if $processor_params.currency == "WebCredsR"}selected="selected"{/if}>RUR WebCreds</option>
		<option value="MailRuR" {if $processor_params.currency == "MailRuR"}selected="selected"{/if}>Money@Mail.Ru</option>
		<option value="ZPaymentR" {if $processor_params.currency == "ZPaymentR"}selected="selected"{/if}>RUR Z-Payment</option>
		<option value="VKontakteMerchantR" {if $processor_params.currency == "VKontakteMerchantR"}selected="selected"{/if}>RUR VKontakte</option>
		<option value="TeleMoneyR" {if $processor_params.currency == "TeleMoneyR"}selected="selected"{/if}>RUR TeleMoney</option>
		</optgroup>
		<optgroup label="Internet Banking">
		<option value="AlfaBankR" {if $processor_params.currency == "AlfaBankR"}selected="selected"{/if}>Alfa-Click</option>
		<option value="HandyBankMerchantR" {if $processor_params.currency == "HandyBankMerchantR"}selected="selected"{/if}>HandyBank</option>
		</optgroup>
		<optgroup label="Mobile phone retailers">
		<option value="RapidaOceanEurosetR" {if $processor_params.currency == "RapidaOceanEurosetR"}selected="selected"{/if}>Via Euroset</option>
		<option value="RapidaOceanSvyaznoyR" {if $processor_params.currency == "RapidaOceanSvyaznoyR"}selected="selected"{/if}>Via Svyaznoy</option>
		</optgroup>
		<optgroup label="Via terminals">
		<option value="ElecsnetR" {if $processor_params.currency == "ElecsnetR"}selected="selected"{/if}>Elecsnet</option>
		<option value="TerminalsUnikassaR" {if $processor_params.currency == "TerminalsUnikassaR"}selected="selected"{/if}>Unikassa</option>
		<option value="TerminalsMElementR" {if $processor_params.currency == "TerminalsMElementR"}selected="selected"{/if}>Mobile Element</option>
		<option value="TerminalsNovoplatR" {if $processor_params.currency == "TerminalsNovoplatR"}selected="selected"{/if}>Novoplat</option>
		<option value="TerminalsAbsolutplatR" {if $processor_params.currency == "TerminalsAbsolutplatR"}selected="selected"{/if}>Absolutplat</option>
		<option value="TerminalsPinpayR" {if $processor_params.currency == "TerminalsPinpayR"}selected="selected"{/if}>Pinpay</option>
		<option value="TerminalsMoneyMoneyR" {if $processor_params.currency == "TerminalsMoneyMoneyR"}selected="selected"{/if}>Money-Money</option>
		</optgroup>
		<optgroup label="Via ATM">
		<option value="TerminalsPkbR" {if $processor_params.currency == "TerminalsPkbR"}selected="selected"{/if}>Petrokommerts</option>
		<option value="VTB24R" {if $processor_params.currency == "VTB24R"}selected="selected"{/if}>RUR VTB24</option>
		</optgroup>
		<optgroup label="Other payment methods">
		<option value="MtsR" {if $processor_params.currency == "MtsR"}selected="selected"{/if}>Mts</option>
		<option value="MegafonR" {if $processor_params.currency == "MegafonR"}selected="selected"{/if}>RUR Megafon</option>
		<option value="BANKOCEANCHECKR" {if $processor_params.currency == "BANKOCEANCHECKR"}selected="selected"{/if}>via iPhone</option>
		<option value="IFreeR" {if $processor_params.currency == "IFreeR"}selected="selected"{/if}>Via SMS</option>
		<option value="ContactR" {if $processor_params.currency == "ContactR"}selected="selected"{/if}>Via CONTACT system</option>
		</optgroup>
	</select>
</div>