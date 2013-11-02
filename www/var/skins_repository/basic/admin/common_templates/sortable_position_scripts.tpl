<script type="text/javascript">
//<![CDATA[


$(
	function() {$ldelim}

		var text_position_updating = '{$lang.text_position_updating}';
		var update_sortable_url = '{"tools.update_position?table=`$sortable_table`&id_name=`$sortable_id_name`"|fn_url:'A':'rel':'&'}';
		var positionids = [];

{literal}
		$('.cm-sortable').sortable( {
			accept: 'cm-sortable-row',
			items: '.cm-row-item',
			tolerance: 'pointer',
			axis: 'vertically',
			{/literal}
			{if $handle_class}
			opacity: 0.5,
			handle: '.{$handle_class}',
			placeholder: 'ui-select',
			{literal}
			helper: function(e, elm) {
				var h_height = 100;
				var drag_height = $(elm).height() > h_height ? h_height : $(elm).height();
				var jelm = $('<div class="ui-drag"></div>');
				jelm.css({'height': drag_height});
				return jelm;
			},
			{/literal}
			{else}
			opacity: 0.9,
			containment: '.cm-sortable',
			{/if}
			{literal}
			update: function(event, ui) {
				var positions = [], ids = [];
				var container = $(ui.item).closest('.cm-sortable');

				$('.cm-row-item').each(function(){
					var matched = $(this).attr('class').match(/cm-sortable-id-([^\s]+)/i);
					var index = $(this).index();

					positions[index] = index;
					ids[index] = matched[1];
				});

				var data_obj = {};
					data_obj['positions'] = positions.join(',');
					data_obj['ids'] = ids.join(',');
					$.ajaxRequest(update_sortable_url, {method: 'get', caching: false, message: text_position_updating, data: data_obj});
				
				return true;
			}
		});
{/literal}
		{if $handle_class}
		$('.{$handle_class}').hover(
			function () {$ldelim}
				$(this).parents('.cm-sortable-box:first').addClass("cm-sortable-box-active");
			{$rdelim},
			function () {$ldelim}
				$(this).parents('.cm-sortable-box:first').removeClass("cm-sortable-box-active");
			{$rdelim}
		);
		{/if}
	{$rdelim}
);
//]]>
</script>