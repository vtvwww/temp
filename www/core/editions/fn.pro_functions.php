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


function fn_pro_get_product_filter_fields(&$fields)
{
	if (Registry::get('settings.Suppliers.enable_suppliers') == 'Y') {
		$fields['S'] = array (
			'db_field' => 'company_id',
			'table' => 'products',
			'description' => 'supplier',
			'condition_type' => 'F',
			'range_name' => 'company',
			'foreign_table' => 'companies',
			'foreign_index' => 'company_id'
		);
	}
}



?>