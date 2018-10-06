<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\migrations\v23;

class m_dls_blocks extends \phpbb\db\migration\migration
{
	/**
	* This migration depends on dls's m_dls_main migration
	* already being installed.
	*/
	static public function depends_on()
	{
		return ['\dls\web\migrations\v23\m_dls_main'];
	}

	public function update_data()
	{
		return [
			// Add blocks
			['custom', [[$this, 'add_categories']]],
			['custom', [[$this, 'add_blocks']]],
		];
	}

	public function add_categories()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'blocks'))
		{
			$sql_ary = [
				[
					'category_name' => 'side_blocks',
				],
				[
					'category_name' => 'bottom_blocks',
				],
			];
			$this->db->sql_multi_insert($this->table_prefix . 'blocks', $sql_ary);
		}
	}

	public function add_blocks()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'blocks_data'))
		{
			$sql_ary = [
				[
					'block_name'  => 'dls_information',
					'vendor'	  => 'dls_web',
					'position'	  => 1,
					'active'	  => 1,
					'category_id' => 1,
				],
				[
					'block_name'  => 'dls_the_team',
					'vendor'	  => 'dls_web',
					'position'	  => 2,
					'active'	  => 1,
					'category_id' => 1,
				],
				[
					'block_name'  => 'dls_top_posters',
					'vendor'	  => 'dls_web',
					'position'	  => 3,
					'active'	  => 1,
					'category_id' => 1,
				],
				[
					'block_name'  => 'dls_recent_posts',
					'vendor'	  => 'dls_web',
					'position'	  => 4,
					'active'	  => 1,
					'category_id' => 1,
				],
				[
					'block_name'  => 'dls_recent_topics',
					'vendor'	  => 'dls_web',
					'position'	  => 5,
					'active'	  => 0,
					'category_id' => 1,
				],
				[
					'block_name'  => 'dls_whos_online',
					'vendor'	  => 'dls_web',
					'position'	  => 1,
					'active'	  => 1,
					'category_id' => 2,
				],
			];
			$this->db->sql_multi_insert($this->table_prefix . 'blocks_data', $sql_ary);
		}
	}
}
