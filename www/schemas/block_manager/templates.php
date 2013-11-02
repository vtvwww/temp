<?php
/***************************************************************************
*                                                                          *
*   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
*                                                                          *
* This  is  commercial  software,  only  users  who have purchased a valid *
* license  and  accept  to the terms of the  License Agreement can install *
* and use this program.                                                    *
*                                                                          *
****************************************************************************
* PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
* "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
****************************************************************************/

if ( !defined('AREA') ) { die('Access denied'); }

$schema = array (
/* General templates */ 
	'blocks/text_links.tpl' => array (
		'settings' => array(
			'item_number' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			)
		),
	),
	'blocks/short_list.tpl' => array (
		'bulk_modifier' => array (
			'fn_gather_additional_products_data' => array (
				'products' => '#this',
				'params' => array (
					'get_icon' => true,
					'get_detailed' => true,
					'get_options' => false,
				),
			),
		),
	),
	'blocks/grid_list.tpl' => array (
		'settings' => array(
			'item_number' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			),
			'number_of_columns' => array (
				'type' => 'input',
				'default_value' => 2
			)
		),
		'bulk_modifier' => array(
			'fn_gather_additional_products_data' => array (
				'products' => '#this',
				'params' => array (
					'get_icon' => true,
					'get_detailed' => true,
					'get_options' => false,
				),
			),
		)
	),
/* Categories templates */
	'blocks/categories/categories_dropdown_horizontal.tpl' => array (
		'settings' => array (
			'dropdown_second_level_elements' => array (
				'type' => 'input',
				'default_value' => '12'
			),
			'dropdown_third_level_elements' => array (
				'type' => 'input',
				'default_value' => '6'
			),
		),
		'fillings' => array('full_tree_cat', 'dynamic_tree_cat'),
		'params' => array (
			'plain' => false,
			'group_by_level' => true,
			'request' => array (
				'active_category_id' => '%CATEGORY_ID%',
			),
		)
	),
	'blocks/categories/categories_text_links.tpl' => array (		
		'fillings' => array('manually', 'newest', 'dynamic_tree_cat', 'full_tree_cat'),
		'params' => array (
			'request' => array (
				'active_category_id' => '%CATEGORY_ID%',
			),
		)
	),
	'blocks/categories/categories_dropdown_vertical.tpl' => array (
		'params' => array (
			'plain' => '',
			'request' => array (
				'active_category_id' => '%CATEGORY_ID%',
			),
		),
		'settings' => array(
			'right_to_left_orientation' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			)
		),
		'fillings' => array('full_tree_cat', 'dynamic_tree_cat'),
	),
	'blocks/categories/categories_multicolumns_list.tpl' => array (
		'params' => array (
		'get_images' => true
		),
		'settings' => array(
			'number_of_columns' =>  array (
				'type' => 'input',
				'default_value' => 2
			)
		),
	),
/* Pages templates */
	'blocks/pages/pages_dropdown.tpl' => array (
		'settings' => array(
			'dropdown_second_level_elements' => array (
				'type' => 'input',
				'default_value' => '12'
			),
			'dropdown_third_level_elements' => array (
				'type' => 'input',
				'default_value' => '6'
			),
		),		
		'fillings' => array('dynamic_tree_pages', 'full_tree_pages'),
		'params' => array (
			'get_tree' => 'tree',
			'request' => array (
				'active_page_id' => '%PAGE_ID%',
			),
		)
	),
	'blocks/pages/pages_text_links.tpl' => array (
		'fillings' => array ('manually', 'newest', 'dynamic_tree_pages', 'full_tree_pages', 'neighbours', 'vendor_pages'),
		'params' => array (
			'plain' => true,
			'request' => array (
				'active_page_id' => '%PAGE_ID%',
			),
		)
	),
	'blocks/pages/pages_emenu.tpl' => array (
		'fillings' => array('dynamic_tree_pages', 'full_tree_pages'),
		'params' => array (
			'get_tree' => 'tree',
			'request' => array (
				'active_page_id' => '%PAGE_ID%',
			),
		),
	),
/* Products templates */
	'blocks/products/products_text_links.tpl' => array (
		'settings' => array(
			'item_number' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			)
		),
	),
	'blocks/products/products_links_thumb.tpl' => array (
		'settings' => array(
			'item_number' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			)
		),
		'bulk_modifier' => array (
			'fn_gather_additional_products_data' => array (
				'products' => '#this',
				'params' => array (
					'get_icon' => true,
					'get_detailed' => true,
					'get_options' => false,
				),
			),
		),
	),
	'blocks/products/products_multicolumns.tpl' => array (
		'settings' => array(
			'item_number' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			),
			'number_of_columns' => array (
				'type' => 'input',
				'default_value' => 2
			)
		),
		'bulk_modifier' => array (
			'fn_gather_additional_products_data' => array (
				'products' => '#this',
				'params' => array (
					'get_icon' => true,
					'get_detailed' => true,
					'get_options' => false,
				),
			),
		),
	),
	'blocks/products/products_multicolumns2.tpl' => array (
		'bulk_modifier' => array (
			'fn_gather_additional_products_data' => array (
				'products' => '#this',
				'params' => array (
					'get_icon' => true,
					'get_detailed' => true,
					'get_options' => false,
				),
			),
		),
	),
	'blocks/products/products_multicolumns_small.tpl' => array (
		'settings' => array(
			'item_number' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			),
			'number_of_columns' =>  array (
				'type' => 'input',
				'default_value' => 3
			)
		),
		'bulk_modifier' => array (
			'fn_gather_additional_products_data' => array (
				'products' => '#this',
				'params' => array (
					'get_icon' => true,
					'get_detailed' => true,
					'get_options' => false,
				),
			),
		),
	),
	'blocks/products/products.tpl' => array (
		'settings' => array(
			'item_number' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			),
			'hide_options' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			)
		),
		'bulk_modifier' => array (
			'fn_gather_additional_products_data' => array (
				'products' => '#this',
				'params' => array (
					'get_icon' => true,
					'get_detailed' => true,
					'get_options' => true,
				),
			),
		),
		'params' => array (
			'extend' => array('description'),
		),		
	),
	'blocks/products/products2.tpl' => array (
		'settings' => array(
			'item_number' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			),
			'number_of_columns' =>  array (
				'type' => 'input',
				'default_value' => 2
			)
		),
		'bulk_modifier' => array (
			'fn_gather_additional_products_data' => array (
				'products' => '#this',
				'params' => array (
					'get_icon' => true,
					'get_detailed' => true,
					'get_options' => false,
				),
			),
		),
	),
	'blocks/products/products_sidebox_1_item.tpl' => array (
		'settings' => array(
			'item_number' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			)
		),
		'bulk_modifier' => array (
			'fn_gather_additional_products_data' => array (
				'products' => '#this',
				'params' => array (
					'get_icon' => true,
					'get_detailed' => true,
					'get_options' => false,
				),
			),
		),
	),
	'blocks/products/products_small_items.tpl' => array (
		'settings' => array(
			'item_number' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			)
		),
		'bulk_modifier' => array (
			'fn_gather_additional_products_data' => array (
				'products' => '#this',
				'params' => array (
					'get_icon' => true,
					'get_detailed' => true,
					'get_options' => false,
				),
			),
		),
	),
	'blocks/products/products_without_image.tpl' => array (
		'settings' => array(
			'item_number' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			)
		),
	),
	'blocks/products/products_scroller.tpl' => array (
		'settings' => array(
			'show_price' => array (
				'type' => 'checkbox',
				'default_value' => 'Y'
			),
			'enable_quick_view' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			),
			'not_scroll_automatically' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			),
			'scroller_direction' => array (
				'type' => 'selectbox',
				'values' => array (
					'up' => 'up',
					'down' => 'down',
					'left' => 'left',
					'right' => 'right'
				),
				'default_value' => 'left'
			),
			'speed' => array (
				'type' => 'selectbox',
				'values' => array (
					'slow' => 'slow',
					'normal' => 'normal',
					'fast' => 'fast'
				),
				'default_value' => 'normal'
			),
			'easing' => array (
				'type' => 'selectbox',
				'values' => array (
					'linear' => 'linear',
					'swing' => 'swing'
				),
				'default_value' => 'swing'
			),
			'pause_delay' =>  array (
				'type' => 'input',
				'default_value' => 3
			),
			'item_quantity' =>  array (
				'type' => 'input',
				'default_value' => 1
			),
			'thumbnail_width' =>  array (
				'type' => 'input',
				'default_value' => 80
			)
		),
		'bulk_modifier' => array (
			'fn_gather_additional_products_data' => array (
				'products' => '#this',
				'params' => array (
					'get_icon' => true,
					'get_detailed' => true,
					'get_options' => false,
				),
			),
		),
	),
/* Companies templates */
	'blocks/companies_list.tpl' => array (
		'fillings' => array ('all', 'manually'),		
		'params' => array (
			'status' => 'A',
		),
	),
/* Menues templates */	
	'blocks/menu/text_links.tpl' => array (
		'settings' => array(
			'show_items_in_line' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			)
		),
	),
	'blocks/menu/dropdown_vertical.tpl' => array (
		'settings' => array(
			'right_to_left_orientation' => array (
				'type' => 'checkbox',
				'default_value' => 'N'
			)
		),
	),
	'blocks/menu/dropdown_horizontal.tpl' => array (
		'settings' => array (
			'dropdown_second_level_elements' => array (
				'type' => 'input',
				'default_value' => '12'
			),
			'dropdown_third_level_elements' => array (
				'type' => 'input',
				'default_value' => '6'
			),
		),
	),
);
?>
