<?php
class Menu {
	/**
	 * Returns menu name by id
	 * @param int $id Menu identifier
	 * @param string $lang_code 2 letters laguage code
	 * @return type
	 */
	public static function get_name($id, $lang_code = DESCR_SL) 
	{
		return db_get_field("SELECT name FROM ?:menus_descriptions WHERE menu_id  = ?i", $id);
	}

	/**
	 * Return list of product menus
	 * @static
	 * @param string $condition SQL condition
	 * @param string $lang_code 
	 * @return array List of product menus sorted by position by menu_id
	 */
	public static function get_list($condition = '', $lang_code = CART_LANGUAGE) 
	{
		/**
		 * Prepare params for sql query before get menus list
		 * @param string $lang_code
		 */
		fn_set_hook('get_menus_pre', $join, $condition, $lang_code);

		$menus = db_get_hash_array(
			"SELECT * FROM ?:menus "				
				. "LEFT JOIN ?:menus_descriptions "
					. "ON ?:menus.menu_id = ?:menus_descriptions.menu_id "				
				. "?p "				
			. "WHERE ?:menus_descriptions.lang_code = ?s ?p ?p",
			'menu_id',
			$join,
			$lang_code,
			fn_get_company_condition('?:menus.company_id'),
			$condition
		);

		/**
		 * Process menus list after sql query
		 * @param array $menus Array of menus data
		 * @param string $lang_code
		 */
		fn_set_hook('get_menus_post', $menus, $lang_code);
		
		return $menus;
	}

	/**
	 * Deletes product tab with reliated descriptions
	 * @static
	 * @param integer $menu_id ID of tab for delete
	 * @return bool
	 */
	public static function delete($menu_id) 
	{
		
		if (!empty($menu_id) && fn_check_company_id('menus', 'menu_id', $menu_id)) {
			/**
			 * Before delete product tab
			 * @param int $menu_id Id of product tab for delete
			 */
			fn_set_hook('delete_menu_pre', $menu_id);

			db_query("DELETE FROM ?:menus WHERE menu_id = ?i", $menu_id);
			db_query("DELETE FROM ?:menus_descriptions WHERE menu_id = ?i", $menu_id);

			// Remove data from static data
			$static_datas = fn_get_static_data(array(
				'section' => 'A',
				'multi_level' => 'Y',
				'plain' => 'Y',
				'request' => array(
					'menu_id' => $menu_id,
				)
			));

			foreach ($static_datas as $static_data) {
				fn_delete_static_data($static_data['param_id']);
			}

			/**
			 * After delete product tab
			 * @param int $menu_id Id of product tab for delete
			 */
			fn_set_hook('delete_menu_post', $menu_id);

			return true;		
		}

		return false;
	}

	/**
	 * Updates product tab data.
	 * $menu_data must be array in this format:
	 * array(
	 *   menu_id - if not exists will be created new record
	 *   tab_type - 'T' for template content or 'B' for block content
	 *   template - path to template if tab_type = 'T'
	 *   block_id - id of block from Block Manager if tab_type = 'B' @see Bm_Block for additiona information
	 *   addon - addon name that created this tab
	 *   position - position
	 * 	 status - 'A' (active) or 'D' (disabled)
	 *   company_id
	 *   name
	 *   lang_code
	 * )
	 * 
	 * @static
	 * @param array $menu_data Array of product tab data
	 * @return integer|bool
	 */
	public static function update($menu_data)
	{	
		if (!isset($menu_data['company_id']) && defined('COMPANY_ID')) {
			$menu_data['company_id'] = COMPANY_ID;
		}

		/**
		 * Prepare params for sql query before update menu
		 * @param array $menu_data
		 */
		fn_set_hook('update_menu_pre', $menu_data);

		$db_result = db_replace_into('menus', $menu_data);

		if (!empty($menu_data['menu_id'])) {
			// Update record
			$menu_id = $menu_data['menu_id'];

			if (!empty($menu_data['name']) && !empty($menu_data['lang_code'])) {
				self::update_description($menu_id, array(
					'lang_code' => $menu_data['lang_code'],
					'name' => $menu_data['name'],
				));
			}
		} else {
			// Create new record
			$menu_id = $db_result;		

			if (!empty($menu_data['name']) && !empty($menu_data['lang_code'])) {
				foreach ((array)Registry::get('languages') as $menu_data['lang_code'] => $v) {
					self::update_description($menu_id, array(
						'lang_code' => $menu_data['lang_code'],
						'name' => $menu_data['name'],
					));
				}
			}		
		}

		return $menu_id;
	}

	/**
	 * Updates menu description
	 * $description must be array in this format:
	 * array (
	 *   lang_code (required)
	 *   name (required)
	 * )
	 * @static
	 * @param int $menu_id 
	 * @param array $description 
	 * @return type
	 */
	public static function update_description($menu_id, $description)
	{	
		if (!empty($menu_id) && !empty($description['lang_code'])) {
			$description['menu_id'] = $menu_id;

			/**
			 * Prepare params for sql query before update menu description
			 * @param array $description
			 */
			fn_set_hook('update_menu_pre', $description);

			return db_replace_into('menus_descriptions', $description);;
		} else {
			return false;	
		}
	}

	/**
	 * Returns status of menu
	 * @static
	 * @param int $menu_id Menu identifier
	 * @return string Status A for active or D for disabled
	 */
	public static function get_status($menu_id)
	{
		return db_get_field("SELECT status FROM ?:menus WHERE menu_id = ?i", $menu_id);
	}
}
?>