{strip}


{******************************************************************************}
{* HIDDEN                                                                     *}
{******************************************************************************}
{if $f_type == "hidden"}
    <input type="hidden" name="{$f_name}" value="{$f_value}" {if $f_class} class="{$f_class}" {/if} />
{/if}



{******************************************************************************}
{* INPUT                                                                      *}
{******************************************************************************}
{if $f_type == "input"}
    {if $f_simple_text}
        <span {if $f_class}class="{$f_class}"{/if}>{if (strlen($f_default) && !strlen($f_value))}{$f_default}{else}{$f_value}{/if}</span>
    {elseif $f_simple}
        <input type="{if $f_number}number{else}text{/if}" {if $f_id} id="{$f_id}" {/if} {if $f_name}name="{$f_name}"{/if} size="35" value="{if (strlen($f_default) && !strlen($f_value))}{$f_default}{else}{$f_value}{/if}" {if $f_class}class="{$f_class}"{else}class="input-text-short{*medium*} main-input {$f_add_class} "{/if} {if $f_style} style="{$f_style}" {/if}  {if $f_disabled}disabled="disabled"{/if}  {if $f_readonly}readonly="readonly"{/if} {if $f_autocomplete}autocomplete="{$f_autocomplete}"{/if} />
    {else}
        <div class="form-field">
             <label class="{if $f_required}cm-required{/if}{if $f_integer} cm-integer{/if}" for="{$f_name}_{$f_id}">{$f_description}:</label>
             <input type="text" id="{$f_name}_{$f_id}" name="data[{$f_name}]" size="35" value="{if (strlen($f_default) && !strlen($f_value))}{$f_default}{else}{$f_value}{/if}" class="input-text-large main-input" />
         </div>
    {/if}
{/if}

{if $f_type == "input_2"}
    {if $f_simple_text}
        {*<span {if $f_class}class="{$f_class}"{/if}>{if (strlen($f_default) && !strlen($f_value))}{$f_default}{else}{$f_value}{/if}</span>*}
    {elseif $f_simple}
        {*<input type="text" {if $f_id} id="{$f_id}" {/if} {if $f_name}name="{$f_name}"{/if} size="35" value="{if (strlen($f_default) && !strlen($f_value))}{$f_default}{else}{$f_value}{/if}" {if $f_class}class="{$f_class}"{else}class="input-text-short*}{*medium*}{* main-input {$f_add_class} "{/if} {if $f_style} style="{$f_style}" {/if}  {if $f_disabled}disabled="disabled"{/if}  {if $f_readonly}readonly="readonly"{/if}  />*}
    {else}
        <div class="form-field">
             <label class="{if $f_required}cm-required{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" {if $f_id}for="{$f_id}"{/if}>{$f_description}:</label>
             <input {if $f_autocomplete}autocomplete="{$f_autocomplete}"{/if} type="{if $f_number}number{else}text{/if}" {if $f_disabled}disabled="disabled"{/if} {if $f_id}id="{$f_id}"{/if} {if $f_name}name="{$f_name}"{/if} {if $f_size}size="{$f_size}"{else}size="35"{/if} value="{if (strlen($f_default) && !strlen($f_value))}{$f_default}{else}{$f_value}{/if}" {if $f_class}class="{$f_class}"{else}class="input-text-large"{/if} />
         </div>
    {/if}
{/if}


{******************************************************************************}
{* CHECKBOX                                                                   *}
{******************************************************************************}
{if $f_type == "checkbox"}
    {if $f_simple}
        <input type="hidden"                               {if $f_name}name="{$f_name}"{/if} value="N"/>
        <input type="checkbox" {if $f_id}id="{$f_id}"{/if} {if $f_name}name="{$f_name}"{/if} value="Y" {if $f_value=="Y"} checked="checked" {/if} {if $f_class} class="{$f_class}" {/if} {if $f_style} style="{$f_style}" {/if} {if $f_disabled} disabled="disabled" {/if} />
    {else}
        {*<div class="form-field">*}
             {*<label class="{if $f_required}cm-required{/if}{if $f_integer} cm-integer{/if}" for="{$f_name}_{$f_id}">{$f_description}:</label>*}
             {*<input type="text" id="{$f_name}_{$f_id}" name="data[{$f_name}]" size="35" value="{if (strlen($f_default) && !strlen($f_value))}{$f_default}{else}{$f_value}{/if}" class="input-text-large main-input" />*}
         {*</div>*}
    {/if}
{/if}



{******************************************************************************}
{* STATUS                                                                     *}
{******************************************************************************}
{if $f_type == "status"}
    {if $f_simple_text}
        {if $f_value == "A"}<span {if $f_class}class="{$f_class}"{/if}>{$lang.active}</span>{/if}
        {if $f_value == "D"}<span {if $f_class}class="{$f_class}"{/if}>{$lang.disabled}</span>{/if}
    {elseif $f_simple}
        <select name="{$f_name}" {if $f_disabled}disabled="disabled"{/if} {if $f_id}id="{$f_id}"{/if} >
            <option value="A" {if $f_value == "A"}selected="selected"{/if}>{$lang.active}</option>
            <option value="D" {if $f_value == "D"}selected="selected"{/if}>{$lang.disabled}</option>
        </select>
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_name}_{$f_id}">{$f_description}:</label>
            <select name="data[{$f_name}]" id="{$f_name}_{$f_id}">
                <option value="A" {if $f_value == "A"}selected="selected"{/if}>{$lang.active}</option>
                <option value="D" {if $f_value == "D"}selected="selected"{/if}>{$lang.disabled}</option>
            </select>
        </div>
    {/if}
{/if}



{******************************************************************************}
{* TYPESIZE                                                                   *}
{******************************************************************************}
{if $f_type == "typesize"}
    {assign var="size_m_name" value="Ном."}
    {assign var="size_a_name" value="исп.А"}
    {assign var="size_b_name" value="исп.Б"}
    {assign var="size_m" value="M"}
    {assign var="size_a" value="A"}
    {assign var="size_b" value="B"}
    {if $f_simple_text}
        {if $f_target == "M"}<span {if $f_class}class="{$f_class}"{/if}>{$size_m_name}</span>{/if}
        {if $f_target == "A"}<span {if $f_class}class="{$f_class}"{/if}>{$size_a_name}</span>{/if}
        {if $f_target == "B"}<span {if $f_class}class="{$f_class}"{/if}>{$size_b_name}</span>{/if}
    {elseif $f_simple}
        <select name="{$f_name}" {if $f_id} id="{$f_id}" {/if}  {if $f_size}size="{$f_size}" {/if} {if $f_style}style="{$f_style}"{/if} {if $f_disabled} disabled="disabled" {/if}>
            <option value="{$size_m}" {if $f_target == $size_m} selected="selected" {/if}>{$size_m_name}</option>
            <option value="{$size_a}" {if $f_target == $size_a} selected="selected" {/if} {if $f_a == "D"}disabled="disabled"{/if}>{$size_a_name}</option>
            <option value="{$size_b}" {if $f_target == $size_b} selected="selected" {/if} {if $f_b == "D"}disabled="disabled"{/if}>{$size_b_name}</option>
        </select>
    {elseif $f_simple_2}
        <option value="{$size_m}">{$size_m_name}</option>
        <option value="{$size_a}" {if $f_a == "D"}disabled="disabled"{/if}>{$size_a_name}</option>
        <option value="{$size_b}" {if $f_b == "D"}disabled="disabled"{/if}>{$size_b_name}</option>
    {else}
        <select name="{$f_name}" {if $f_id} id="{$f_id}" {/if}>
            {if !$f_empty}
                <option value="{$size_m}" {if $f_target == $size_m} selected="selected" {/if}>{$size_m_name}</option>
                <option value="{$size_a}" {if $f_target == $size_a} selected="selected" {/if} {if $f_a == "D"} disabled="disabled" {/if}>{$size_a_name}</option>
                <option value="{$size_b}" {if $f_target == $size_b} selected="selected" {/if} {if $f_b == "D"} disabled="disabled" {/if}>{$size_b_name}</option>
            {/if}
        </select>
    {/if}
{/if}



{******************************************************************************}
{* TEXTAREA                                                                   *}
{******************************************************************************}
{if $f_type == "textarea"}
    <div class="form-field">
        <label class="" {if $f_full_name} for="{$f_id}" {else} for="{$f_name}_{$f_id}" {/if}>{$f_description}:</label>
        <textarea {if $f_full_name} id="{$f_id}" name="{$f_full_name}" {else} id="{$f_name}_{$f_id}" name="data[{$f_name}]" {/if} rows="{$f_row|default:"5"}" class="input-textarea-long">{if $f_value_prefix}{$f_value_prefix}{/if}{$f_value}{if $f_value_suffix}{$f_value_suffix}{/if}</textarea>
    </div>
{/if}



{******************************************************************************}
{* SELECT                                                                     *}
{******************************************************************************}
{if $f_type == "select"}
    {if $f_simple_text}
        {if is__array($f_options)}
            {foreach from=$f_options item="j"}
                {if $j.$f_option_id == $f_option_target_id}<span {if $f_class}class="{$f_class}"{/if}>{$j.$f_option_value}</span>{/if}
            {/foreach}
        {/if}
    {elseif $f_simple}
        <select name="{$f_name}" {if $f_id} id="{$f_id}" {/if} {if $f_disabled}disabled="disabled"{/if} >
            {if $f_blank}<option value="0">---</option>{/if}
            {if is__array($f_options)}
            {foreach from=$f_options item="j"}
                <option value="{$j.$f_option_id}" {if $j.$f_option_id == $f_option_target_id} selected="selected" {/if}>{if $f_value_prefix}{$f_value_prefix}{/if}{$j.$f_option_value}{if $f_option_value_add && $j.$f_option_value_add}{$f_option_value_add_prefix}{$j.$f_option_value_add}{$f_option_value_add_suffix}{/if}{if $f_value_suffix}{$f_value_suffix}{/if}</option>
            {/foreach}
            {/if}
        </select>
    {elseif $f_simple_2}
        {if $f_blank}<option value="0">---</option>{/if}
        {if is__array($f_options)}
        {foreach from=$f_options item="j"}
            <option {if $j.material_status == "D"} title="Заготовка больше не используется в производстве" style="text-decoration: line-through; color: red;" {/if} value="{$j.$f_option_id}" {if $j.$f_option_id == $f_option_target_id} selected="selected" {/if}>{if $f_value_prefix}{$f_value_prefix}{/if}{$j.$f_option_value}{if $f_option_value_add && $j.$f_option_value_add} ({$j.$f_option_value_add}) {/if}{if $f_value_suffix}{$f_value_suffix}{/if}{if $f_add_value and strlen($j.$f_add_value)}&nbsp;&nbsp;&nbsp;({$j.$f_add_value}){/if}</option>
        {/foreach}
        {/if}
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_name}_{$f_id}">{$f_description}:</label>
            <select name="data[{$f_name}]" id="{$f_name}_{$f_id}">
                {if $f_blank}<option value="0" {if $f_option_target_id == 0} selected="selected" {/if}>---</option>{/if}
                {if is__array($f_options)}
                {foreach from=$f_options item="j"}
                    <option value="{$j.$f_option_id}" {if $j.$f_option_id == $f_option_target_id} selected="selected" {/if}>{if $f_value_prefix}{$f_value_prefix}{/if}{$j.$f_option_value}{if $f_option_value_add && $j.$f_option_value_add} ({$j.$f_option_value_add}) {/if}{if $f_value_suffix}{$f_value_suffix}{/if}</option>
                {/foreach}
                {/if}
            </select>
        </div>
    {/if}
{/if}



{******************************************************************************}
{* MCATEGORIES_PLAIN                                                          *}
{******************************************************************************}
{if $f_type == "mcategories_plain"}
    {if $f_simple_2}
        {if $f_blank}<option value="0" {if $f_target.mcat_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
        {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.mcat_id_path|strpos:"`$f_target.mcat_id_path`/" === false && $j.mcat_id != $f_target.mcat_id || !$f_target.mcat_id || !$f_option_target_id}
                    {*{if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}*}
                    <option value="{$j.mcat_id}" {if $j.mcat_status == "D"}disabled="disabled"{/if} {if $j.mcat_id == $f_target.mcat_parent_id}selected="selected"{/if} {if $j.mcat_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.mcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}{if $f_with_q_ty}&nbsp;&nbsp;&nbsp;({$j.q_ty} шт.){/if}</option>
                {/if}
            {/foreach}
        {/if}
    {elseif $f_simple_text}
        {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.mcat_id_path|strpos:"`$f_target.mcat_id_path`/" === false && $j.mcat_id != $f_target.mcat_id || !$f_target.mcat_id || !$f_option_target_id}
                    {if $j.mcat_id == $f_option_target_id}<span {if $f_class}class="{$f_class}"{/if}>{$j.mcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</span>{/if}
                {/if}
            {/foreach}
        {/if}
    {elseif $f_simple}
        <select {if $f_id} id="{$f_id}" {/if} name="{$f_name}" {if $f_disabled}disabled="disabled"{/if} >
            {if $f_blank}<option value="0" {if $f_target.mcat_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
            {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.mcat_id_path|strpos:"`$f_target.mcat_id_path`/" === false && $j.mcat_id != $f_target.mcat_id || !$f_target.mcat_id || !$f_option_target_id}
                    {if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}
                    <option value="{$j.mcat_id}" {if $j.mcat_status == "D"}disabled="disabled"{/if} {if $j.mcat_id == $f_target.mcat_parent_id}selected="selected"{/if} {if $j.mcat_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.mcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}{if $f_with_q_ty}&nbsp;&nbsp;&nbsp;({$j.q_ty} шт.){/if}</option>
                {/if}
            {/foreach}
            {/if}
        </select>
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_name}_{$f_id}">{$f_description}:</label>
            <select name="data[{$f_name}]" id="{$f_name}_{$f_id}">
                {if $f_blank}<option value="0" {if $f_target.mcat_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
                <option disabled="disabled">&nbsp;</option>
                {if is__array($f_options)}
                {foreach from=$f_options item="j" name="j"}
                    {if $j.mcat_id_path|strpos:"`$f_target.mcat_id_path`/" === false && $j.mcat_id != $f_target.mcat_id || !$f_target.mcat_id || !$f_option_target_id}
                        {if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}
                        <option value="{$j.mcat_id}" {if ($j.mcat_status == "D") or (is__more_0($f_exclude) and ($f_exclude==$j.mcat_id))}disabled="disabled"{/if} {if $j.mcat_id == $f_target.mcat_parent_id}selected="selected"{/if} {if $j.mcat_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.mcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}&nbsp;&nbsp;&nbsp;({$j.q_ty} шт.)</option>
                    {/if}
                {/foreach}
                {/if}
            </select>
        </div>
    {/if}
{/if}



{******************************************************************************}
{* DCATEGORIES_PLAIN                                                          *}
{******************************************************************************}
{if $f_type == "dcategories_plain"}
    {if $f_simple_2}
        {if $f_blank}<option value="0" {if $f_target.dcat_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
        {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.dcat_id_path|strpos:"`$f_target.dcat_id_path`/" === false && $j.dcat_id != $f_target.dcat_id || !$f_target.dcat_id || !$f_option_target_id}
                    {*{if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}*}
                    <option value="{$j.dcat_id}" {if $j.dcat_status == "D"}disabled="disabled"{/if} {if $j.dcat_id == $f_target.dcat_parent_id}selected="selected"{/if} {if $j.dcat_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.dcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}&nbsp;&nbsp;&nbsp;({$j.q_ty} шт.)</option>
                {/if}
            {/foreach}
        {/if}
    {elseif $f_simple_text}
        {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.dcat_id_path|strpos:"`$f_target.dcat_id_path`/" === false && $j.dcat_id != $f_target.dcat_id || !$f_target.dcat_id || !$f_option_target_id}
                    {if $j.dcat_id == $f_option_target_id}<span {if $f_class}class="{$f_class}"{/if}>{$j.dcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</span>{/if}
                {/if}
            {/foreach}
        {/if}
    {elseif $f_simple}
        <select {if $f_name}name="{$f_name}"{/if} {if $f_id}id="{$f_id}"{/if} {if $f_disabled}disabled="disabled"{/if} >
            {if $f_blank}<option value="0" {if $f_target.dcat_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
            {if is__array($f_options)}
                {foreach from=$f_options item="j" name="j"}
                    {if $j.dcat_id_path|strpos:"`$f_target.dcat_id_path`/" === false && $j.dcat_id != $f_target.dcat_id || !$f_target.dcat_id || !$f_option_target_id}
                        {*{if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}*}
                        <option value="{$j.dcat_id}" {if $j.dcat_status == "D"}disabled="disabled"{/if} {if $j.dcat_id == $f_target.dcat_parent_id}selected="selected"{/if} {if $j.dcat_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.dcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}{if $f_with_q_ty}&nbsp;&nbsp;&nbsp;({$j.q_ty} шт.){/if}</option>
                    {/if}
                {/foreach}
            {/if}
        </select>
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_name}_{$f_id}">{$f_description}:</label>
            <select name="data[{$f_name}]" id="{$f_name}_{$f_id}">
                {if $f_blank}<option value="0" {if $f_target.dcat_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
                <option disabled="disabled">&nbsp;</option>
                {if is__array($f_options)}
                {foreach from=$f_options item="j" name="j"}
                    {if $j.dcat_id_path|strpos:"`$f_target.dcat_id_path`/" === false && $j.dcat_id != $f_target.dcat_id || !$f_target.dcat_id || !$f_option_target_id}
                        {if !$j.level && $smarty.foreach.j.index>0}<option disabled="disabled">&nbsp;</option>{/if}
                        <option value="{$j.dcat_id}" {if ($j.dcat_status == "D") or (is__more_0($f_exclude) and ($f_exclude==$j.dcat_id))}disabled="disabled"{/if} {if $j.dcat_id == $f_target.dcat_parent_id}selected="selected"{/if} {if $j.dcat_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.dcat_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}&nbsp;&nbsp;&nbsp;({$j.q_ty} шт.)</option>
                    {/if}
                {/foreach}
                {/if}
            </select>
        </div>
    {/if}
{/if}



{******************************************************************************}
{* OBJECTS_PLAIN                                                              *}
{******************************************************************************}
{if $f_type == "objects_plain"}
    {if $f_simple_2}
        {if $f_blank}<option value="0" {if $f_target.o_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
        {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.o_id_path|strpos:"`$f_target.o_id_path`/" === false && $j.o_id != $f_target.o_id || !$f_target.o_id || !$f_option_target_id}
                    <option value="{$j.o_id}" {if (!in_array($j.o_id, $f_options_enabled))}disabled="disabled"{/if} {if $j.o_id == $f_target}selected="selected"{/if} >{$j.path|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_view_id} [{$j.o_id}]{/if}</option>
                {/if}
            {/foreach}
        {/if}
    {elseif $f_simple_text}
        {if is__array($f_options)}
            {foreach from=$f_options item="j" name="j"}
                {if $j.o_id_path|strpos:"`$f_target.o_id_path`/" === false && $j.o_id != $f_target.o_id || !$f_target.o_id || !$f_option_target_id}
                    {if $j.o_id == $f_option_target_id}<span {if $f_class}class="{$f_class}"{/if}>{$j.o_name|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}</span>{/if}
                {/if}
            {/foreach}
        {/if}
    {elseif $f_simple}
        <select {if $f_name}name="{$f_name}"{/if} {if $f_disabled}disabled="disabled"{/if} >
            {if $f_blank}<option value="0" {if $f_target.o_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}- корневой уровень -{/if}</option>{/if}
            {if is__array($f_options)}
                {foreach from=$f_options item="j" name="j"}
                    {if $j.o_id_path|strpos:"`$f_target.o_id_path`/" === false && $j.o_id != $f_target.o_id || !$f_target.o_id || !$f_option_target_id}
                        <option value="{$j.o_id}" {if $j.o_status == "D" or (!in_array($j.o_id, $f_options_enabled))}disabled="disabled"{/if} {if $j.o_id == $f_target.o_parent_id}selected="selected"{/if} {if $j.o_id == $f_option_target_id}selected="selected"{/if} >{if $f_value_prefix}{$f_value_prefix}{/if}{$j.path|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_value_suffix}{$f_value_suffix}{/if}</option>
                    {/if}
                {/foreach}
            {/if}
        </select>
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" {if $f_full_name} for="{$f_id}" {else} for="{$f_name}_{$f_id}" {/if}>{$f_description}:</label>
            <select {if $f_full_name} name="{$f_full_name}" {if $f_id} id="{$f_id}"{/if} {else} name="data[{$f_name}]" id="{$f_name}_{$f_id}" {/if} {if $f_disabled} disabled="disabled" {/if}>
                {if $f_blank}<option value="0" {if $f_target.o_id == 0} selected="selected" {/if}>{if $f_blank_name}{$f_blank_name}{else}---{/if}</option>{/if}
                {if is__array($f_options)}
                {foreach from=$f_options item="j" name="j"}
                    {if $j.o_id_path|strpos:"`$f_target.o_id_path`/" === false && $j.o_id != $f_target.o_id || !$f_target.o_id || !$f_option_target_id}
                        <option value="{$j.o_id}" {if (!in_array($j.o_id, $f_options_enabled))}disabled="disabled"{/if} {if $j.o_id == $f_target}selected="selected"{/if} >{$j.path|indent:$j.level:"&#166;&nbsp;&nbsp;&nbsp;&nbsp;":"&#166;--&nbsp;"}{if $f_view_id} [{$j.o_id}]{/if}</option>
                    {/if}
                {/foreach}
                {/if}
            </select>
        </div>
    {/if}
{/if}



{******************************************************************************}
{* SELECT_BY_GROUP                                                            *}
{******************************************************************************}
{if $f_type == "select_by_group"}
  {*Array
  (
      [3] => Array
          (
              [uc_id] => 3
              [uc_name] => Единицы штук
              [uc_position] => 100
              [uc_comment] =>
              [units] => Array
                  (
                      [9] => Array
                          (
                              [u_id] => 9
                              [u_name] => шт
                              [u_type] => M
                              [u_coefficient] => 1.0000
                              [u_status] => A
                              [u_position] => 0
                              [u_comment] =>
                              [uc_id] => 3
                              [uc_name] => Единицы штук
                          )

                      [11] => Array
                          (
                              [u_id] => 11
                              [u_name] => упак
                              [u_type] => A
                              [u_coefficient] => 1.0000
                              [u_status] => A
                              [u_position] => 10
                              [u_comment] =>
                              [uc_id] => 3
                              [uc_name] => Единицы штук
                          )
  *}

   {if $f_simple}
       <select name="{$f_name}" {if $f_id} id="{$f_id}" {/if} {if $f_disabled} disabled="disabled"{/if}>
           {if $f_blank}<option value="0">---</option>{/if}
           {if is__array($f_optgroups)}
           {foreach from=$f_optgroups item="optgroup"}
               {if is__array($optgroup.$f_options)}
                <optgroup label="{$optgroup.$f_optgroup_label}">
                   {foreach from=$optgroup.$f_options item="j"}
                       <option value="{$j.$f_option_id}" {if $j.$f_option_id == $f_option_target_id} selected="selected" {/if}>{if $f_value_prefix}{$f_value_prefix}{/if}{$j.$f_option_value}{if $f_option_value_add && $j.$f_option_value_add}{$f_option_value_add_prefix}{$j.$f_option_value_add}{$f_option_value_add_suffix}{/if}{if $f_value_suffix}{$f_value_suffix}{/if}</option>
                   {/foreach}
                </optgroup>
               {/if}
           {/foreach}
           {/if}
       </select>
   {else}
       <div class="form-field">
           {*<label class="{if $f_required}cm-required{/if} {if $f_integer}cm_integer{/if} {if $f_integer_more_0}cm-integer-more-0{/if}"*}
                  {*for="{$id}">{$f_description}++:</label>*}
           {*<select name="data[{$f_name}]" id="{$f_name}_{$f_id}">*}
           <label class="{if $f_required}cm-required{/if} {if $f_integer}cm-integer{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_id}">{$f_description}:</label>
           <select name="{$f_name}" id="{$f_id}" {if $f_disabled} disabled="disabled"{/if}>
               {if $f_blank}<option>---</option>{/if}
               {if is__array($f_optgroups)}
               {foreach from=$f_optgroups item="optgroup"}
                   {if is__array($optgroup.$f_options)}
                    <optgroup label="{$optgroup.$f_optgroup_label}">
                       {foreach from=$optgroup.$f_options item="j"}
                           <option value="{$j.$f_option_id}" {if $j.$f_option_id == $f_option_target_id} selected="selected" {/if}>{if $f_value_prefix}{$f_value_prefix}{/if}{$j.$f_option_value}{if $f_option_value_add && $j.$f_option_value_add}{$f_option_value_add_prefix}{$j.$f_option_value_add}{$f_option_value_add_suffix}{/if}{if $f_value_suffix}{$f_value_suffix}{/if}</option>
                       {/foreach}
                    </optgroup>
                   {/if}
               {/foreach}
               {/if}
           </select>
       </div>

   {/if}
{/if}



{******************************************************************************}
{* DATE                                                                       *}
{******************************************************************************}
{if $f_type == "date"}
   {if $f_simple}
       {assign var="r" value=10000000|rand:99999999}
       {include file="addons/uns/views/components/calendar.tpl"
                date_id="date_`$r`__`$f_id`"
                date_name=$f_name
                date_val=$f_value|default:$smarty.now
                start_year="2000" end_year="2030"}
   {elseif $f_full}
       <div class="form-field {if $f_hidden} hidden {/if}">
           <label class="{if $f_hidden} {elseif $f_required and !$f_disabled}cm-required{/if}" for="{$f_id}">{$f_description}:</label>
       	   {include file="addons/uns/views/components/calendar.tpl"
               date_id=$f_id
               date_name=$f_name
               date_val=$f_value|default:$smarty.now
               start_year="2000"
               end_year="2030"
               date_style="width:120px;"
               date_disabled=$f_disabled}
       </div>
   {else}
       <div class="form-field">
           <label class="{if $f_required and !$f_disabled}cm-required{/if}" for="date_{$f_id}">{$f_description}:</label>
       	   {include file="addons/uns/views/components/calendar.tpl"
               date_id="date_`$f_id`"
               date_name="data[`$f_name`]"
               date_val=$f_value|default:$smarty.now
               start_year="2000"
               end_year="2030"
               date_style="width:120px;"
               date_disabled=$f_disabled}
       </div>
   {/if}
{/if}



{******************************************************************************}
{* DOCUMENT_TYPE                                                              *}
{******************************************************************************}
{if $f_type == "document_type"}
    {if $f_simple}
        <select {if $f_name} name="{$f_name}" {/if} {if $f_id} id="{$f_id}" {/if} {if $f_disabled} disabled="disabled" {/if}>
            {if $f_blank}<option>---</option>{/if}
            {foreach from=$f_options item="j"}
                {if  $f_enabled_items|is__array}
                    {if $j.type|in_array:$f_enabled_items }
                        <option value="{$j.dt_id}" {if $j.dt_id == $f_target}selected="selected"{/if}>{$j.name}{if $f_with_id} [{$j.dt_id}]{/if}</option>
                    {/if}
                {else}
                    <option value="{$j.dt_id}" {if $j.dt_id == $f_target}selected="selected"{/if}>{$j.name}{if $f_with_id} [{$j.dt_id}]{/if}</option>
                {/if}
            {/foreach}
        </select>
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer}cm-integer{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_id}">{$f_description}:</label>
            <select {if $f_name} name="{$f_name}" {/if} {if $f_id} id="{$f_id}" {/if} {if $f_disabled} disabled="disabled" {/if}>
                {if $f_blank}<option>---</option>{/if}
                {foreach from=$f_options item="j"}
                    {if  $f_enabled_items|is__array}
                        {if $j.type|in_array:$f_enabled_items }
                            <option value="{$j.dt_id}" {if $j.dt_id == $f_target}selected="selected"{/if}>{$j.name}{if $f_with_id} [{$j.dt_id}]{/if}</option>
                        {/if}
                    {else}
                        <option value="{$j.dt_id}" {if $j.dt_id == $f_target}selected="selected"{/if}>{$j.name}{if $f_with_id} [{$j.dt_id}]{/if}</option>
                    {/if}
                {/foreach}
            </select>
        </div>
    {/if}
{/if}



{******************************************************************************}
{* ITEM_TYPE                                                              *}
{******************************************************************************}
{if $f_type == "item_type"}
    {if $f_simple}
        <select name="{$f_name}" {if $f_id} id="{$f_id}" {/if} {if $f_disabled}disabled="disabled"{/if} >
            <option {if $f_value == 0} selected="selected" {/if} value="0">---</option>
            {if $f_detail}  <option {if $f_value == "D"} selected="selected" {/if} {if $f_disabled_detail} disabled="disabled" {/if} value="D">Дет.</option>{/if}
            {if $f_material}<option {if $f_value == "M"} selected="selected" {/if} {if $f_disabled_material} disabled="disabled" {/if} value="M">Мат.</option>{/if}
        </select>
    {else}
    {/if}
{/if}


{******************************************************************************}
{* PROCESSING                                                              *}
{******************************************************************************}
{if $f_type == "processing"}
    {if $f_simple}
        <select {if $f_name}name="{$f_name}"{/if} {if $f_id}id="{$f_id}"{/if} {if $f_disabled}disabled="disabled"{/if}>
            {if $f_blank}<option>---</option>{/if}
            <option {if $f_value == "P"}selected="selected"{/if} value="P">Обр.</option>
            <option {if $f_value == "C"}selected="selected"{/if} value="C">Зав.</option>
        </select>
    {else}
    {/if}
{/if}


{******************************************************************************}
{* DOCUMENT_STATUS                                                            *}
{******************************************************************************}
{if $f_type == "document_status"}
    {if $f_simple}
    {else}
        <div class="form-field">
            {* Статус *}
            <label class="" for="document_status_{$f_id}">{$f_description}:</label>
            <select {if $f_full_name} name="{$f_full_name}"  {if $f_id} id="{$f_id}" {/if} {else} name="data[{$f_name}]" id="document_status_{$f_id}" {/if} {if $f_disabled} disabled="disabled" {/if} >
                <option {if $f_value == "A"} selected="selected" {/if} value="A">Учитывать</option>
                <option {if $f_value == "D"} selected="selected" {/if} value="D">Не учитывать</option>
                {*<option {if $f_value == "H"} selected="selected" {/if} value="H">Спрятать</option>*}
            </select>
        </div>
    {/if}
{/if}


{******************************************************************************}
{* SELECT_RANGE                                                               *}
{******************************************************************************}
{if $f_type == "select_range"}
    {if $f_simple}
        <select autocomplete="off" {if $f_name}name="{$f_name}"{/if} {if $f_id} id="{$f_id}"{/if} {if $f_disabled}disabled="disabled"{/if} {if $f_style}style="{$f_style}"{/if} {if $f_onchange}onchange="{$f_onchange}"{/if} >
            {if $f_blank}
                <option {if $f_value == 0}  selected="selected" {/if} value="0">---</option>
            {/if}
            {foreach from=$f_from|range:$f_to item="i"}
                <option {if $f_value == $i}selected="selected"{/if} value="{$i}">{$i}</option>
            {/foreach}
        </select>
    {elseif $f_simple_2}
        {foreach from=$f_from|range:$f_to item="i"}
            <option {if $f_value == $i} selected="selected" {/if} value="{$i}">{$i}</option>
        {/foreach}
    {else}
        <div class="form-field">
            <label class="{if $f_required}cm-required{/if} {if $f_integer}cm-integer{/if} {if $f_integer_more_0}cm-integer-more-0{/if}" for="{$f_id}">{$f_description}:</label>
            <select name="{$f_name}" {if $f_id} id="{$f_id}" {/if} {if $f_disabled}disabled="disabled"{/if} >
                {if $f_blank}
                    <option {if $f_value == 0}  selected="selected" {/if} value="0">---</option>
                {/if}
                {foreach from=$f_from|range:$f_to item="i"}
                    <option {if $f_value == $i} selected="selected" {/if} value="{$i}">{$i}</option>
                {/foreach}
            </select>
        </div>
    {/if}
{/if}


{******************************************************************************}
{* TRANSACTION                                                                *}
{******************************************************************************}
{if $f_type == "transaction"}
    {if $f_simple}
    {else}
        <div class="form-field">
            {* Статус *}
            <label class="" for="transaction_{$f_id}">{$f_description}:</label>
            <select name="data[{$f_name}]" id="transaction_{$f_id}" {if $f_disabled} disabled="disabled" {/if} onchange="fn_toggle_transaction_{$f_id}(this)">
                <option {if $f_transaction_status == "N"} selected="selected" {/if} value="N">Не проведен</option>
                <option {if $f_transaction_status == "Y"} selected="selected" {/if} value="Y">Проведен</option>
            </select>

            {* Дата *}
            {if is__more_0($f_transaction_date)}
                {assign var="transaction_date" value=$f_transaction_date|fn_parse_date|date_format:"%d/%m/%Y"}
            {/if}
            &nbsp;&nbsp;&nbsp;
            <span class="transaction_date {if $f_transaction_status == "N"} hidden {/if}">{$transaction_date}</span>

            {literal}
                <script type="text/javascript">
                    function fn_toggle_transaction_{/literal}{$f_id}{literal}(sel) {
                        var value = sel.options[sel.selectedIndex].value;
                        if (value == "Y") $('span.transaction_date').removeClass('hidden');
                        else $('span.transaction_date').addClass('hidden');
                    }
                </script>
            {/literal}
        </div>
    {/if}
{/if}


{******************************************************************************}
{* TYPE_CASTING --- ТИП ЛИТЬЯ (Чугунное/Стальное)                                                              *}
{******************************************************************************}
{if $f_type == "type_casting"}
    {if $f_simple}
        <select name="{$f_name}" {if $f_id} id="{$f_id}" {/if} {if $f_disabled}disabled="disabled"{/if} >
            <option {if $f_value == 0}   selected="selected" {/if} value="0">---</option>
            <option {if $f_value == "C"} selected="selected" {/if} value="C">Чугун</option>
            <option {if $f_value == "S"} selected="selected" {/if} value="S">Сталь</option>
            <option {if $f_value == "A"} selected="selected" {/if} value="A">Алюминий</option>
            <option {if $f_value == "W"} selected="selected" {/if} value="W">Чугун белый</option>
        </select>
    {else}
        <div class="form-field {if $f_hidden} hidden {/if} type_casting">
            <label class="" for="type_casting">Тип литья:</label>
            <select name="data[{$f_name}]" id="type_casting" {if $f_disabled} disabled="disabled" {/if} >
                {if $f_blank}
                <option {if $f_value == 0}   selected="selected" {/if} value="0">---</option>
                {/if}
                <option {if $f_value == "C"} selected="selected" {/if} value="C">Чугунное</option>
                <option {if $f_value == "S"} selected="selected" {/if} value="S">Стальное</option>
                <option {if $f_value == "A"} selected="selected" {/if} value="A">Алюминий</option>
                <option {if $f_value == "W"} selected="selected" {/if} value="W">Чугун белый</option>
            </select>
        </div>
    {/if}
{/if}

{******************************************************************************}
{* TIME                                                                       *}
{******************************************************************************}
{if $f_type == "time"}
    {if $f_simple}
        {assign var="f_value" value=$f_value|fn_parse_date|date_format:"%H:%M"}
        <select {if $f_name}name="{$f_name}"{/if} {if $f_id} id="{$f_id}"{/if} {if $f_disabled}disabled="disabled"{/if} >
            <option {if $f_value == "00:00"}  selected="selected" {/if} value="00:00">00:00</option>
            <option {if $f_value == "01:00"}  selected="selected" {/if} value="01:00">01:00</option>
            <option {if $f_value == "02:00"}  selected="selected" {/if} value="02:00">02:00</option>
            <option {if $f_value == "03:00"}  selected="selected" {/if} value="03:00">03:00</option>
            <option {if $f_value == "04:00"}  selected="selected" {/if} value="04:00">04:00</option>
            <option {if $f_value == "05:00"}  selected="selected" {/if} value="05:00">05:00</option>
            <option {if $f_value == "06:00"}  selected="selected" {/if} value="06:00">06:00</option>
            <option {if $f_value == "07:00"}  selected="selected" {/if} value="07:00">07:00</option>
            <option {if $f_value == "08:00"}  selected="selected" {/if} value="08:00">08:00</option>
            <option {if $f_value == "09:00"}  selected="selected" {/if} value="09:00">09:00</option>
            <option {if $f_value == "10:00"}  selected="selected" {/if} value="10:00">10:00</option>
            <option {if $f_value == "11:00"}  selected="selected" {/if} value="11:00">11:00</option>
            <option {if $f_value == "12:00"}  selected="selected" {/if} value="12:00">12:00</option>
            <option {if $f_value == "13:00"}  selected="selected" {/if} value="13:00">13:00</option>
            <option {if $f_value == "14:00"}  selected="selected" {/if} value="14:00">14:00</option>
            <option {if $f_value == "15:00"}  selected="selected" {/if} value="15:00">15:00</option>
            <option {if $f_value == "16:00"}  selected="selected" {/if} value="16:00">16:00</option>
            <option {if $f_value == "17:00"}  selected="selected" {/if} value="17:00">17:00</option>
            <option {if $f_value == "18:00"}  selected="selected" {/if} value="18:00">18:00</option>
            <option {if $f_value == "19:00"}  selected="selected" {/if} value="19:00">19:00</option>
            <option {if $f_value == "20:00"}  selected="selected" {/if} value="20:00">20:00</option>
            <option {if $f_value == "21:00"}  selected="selected" {/if} value="21:00">21:00</option>
            <option {if $f_value == "22:00"}  selected="selected" {/if} value="22:00">22:00</option>
            <option {if $f_value == "23:00"}  selected="selected" {/if} value="23:00">23:00</option>
        </select>
    {else}
    {/if}
{/if}
{/strip}