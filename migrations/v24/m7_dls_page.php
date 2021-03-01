<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\migrations\v24;

class m7_dls_page extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	static public function depends_on()
	{
		return ['\dls\web\migrations\v24\m1_dls_main'];
	}

	/**
	* Add the initial data in the database
	*
	* @return array Array of table data
	* @access public
	*/
	public function update_data()
	{
		return [
			['custom', [[$this, 'add_page']]],
		];
	}

	/**
	* Custom function to add page data
	*/
	public function add_page()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'dls_page'))
		{
			$sql_ary = [
				[
					'name'		  => 'app',
					'active'	  => 1,
					'dls_special' => 1,
					'dls_right'	  => 1,
					'dls_left'	  => 0,
					'dls_middle'  => 1,
					'dls_top'	  => 0,
					'dls_bottom'  => 0,
				],
				[
					'name'		  => 'news',
					'active'	  => 1,
					'dls_special' => 1,
					'dls_right'	  => 1,
					'dls_left'	  => 0,
					'dls_middle'  => 1,
					'dls_top'	  => 0,
					'dls_bottom'  => 0,
				],
				[
					'name'		  => 'article',
					'active'	  => 1,
					'dls_special' => 1,
					'dls_right'	  => 1,
					'dls_left'	  => 0,
					'dls_middle'  => 1,
					'dls_top'	  => 0,
					'dls_bottom'  => 0,
				],
				[
					'name'		  => 'faq',
					'active'	  => 0,
					'dls_special' => 0,
					'dls_right'	  => 0,
					'dls_left'	  => 0,
					'dls_middle'  => 0,
					'dls_top'	  => 0,
					'dls_bottom'  => 0,
				],
				[
					'name'		  => 'index',
					'active'	  => 1,
					'dls_special' => 0,
					'dls_right'	  => 1,
					'dls_left'	  => 0,
					'dls_middle'  => 0,
					'dls_top'	  => 0,
					'dls_bottom'  => 0,
				],
				[
					'name'		  => 'memberlist',
					'active'	  => 0,
					'dls_special' => 0,
					'dls_right'	  => 0,
					'dls_left'	  => 0,
					'dls_middle'  => 0,
					'dls_top'	  => 0,
					'dls_bottom'  => 0,
				],
				[
					'name'		  => 'ucp',
					'active'	  => 0,
					'dls_special' => 0,
					'dls_right'	  => 0,
					'dls_left'	  => 0,
					'dls_middle'  => 0,
					'dls_top'	  => 0,
					'dls_bottom'  => 0,
				],
				[
					'name'		  => 'mcp',
					'active'	  => 0,
					'dls_special' => 0,
					'dls_right'	  => 0,
					'dls_left'	  => 0,
					'dls_middle'  => 0,
					'dls_top'	  => 0,
					'dls_bottom'  => 0,
				],
				[
					'name'		  => 'search',
					'active'	  => 0,
					'dls_special' => 0,
					'dls_right'	  => 0,
					'dls_left'	  => 0,
					'dls_middle'  => 0,
					'dls_top'	  => 0,
					'dls_bottom'  => 0,
				],
			];
			$this->db->sql_multi_insert($this->table_prefix . 'dls_page', $sql_ary);
		}
	}
}
