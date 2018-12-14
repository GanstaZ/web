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

class m3_dls_blocks extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	static public function depends_on()
	{
		return ['\dls\web\migrations\v23\m1_dls_main'];
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
			['custom', [[$this, 'add_blocks']]],
		];
	}

	/**
	* Custom function to add blocks data
	*/
	public function add_blocks()
	{
		if ($this->db_tools->sql_table_exists($this->table_prefix . 'blocks'))
		{
			$sql_ary = [
				[
					'block_name' => 'dls_mini_profile',
					'ext_name'	 => 'dls_web',
					'position'	 => 1,
					'active'	 => 1,
					'cat_name'	 => 'side',
				],
				[
					'block_name' => 'dls_information',
					'ext_name'	 => 'dls_web',
					'position'	 => 2,
					'active'	 => 1,
					'cat_name'	 => 'side',
				],
				[
					'block_name' => 'dls_the_team',
					'ext_name'	 => 'dls_web',
					'position'	 => 3,
					'active'	 => 1,
					'cat_name'	 => 'side',
				],
				[
					'block_name' => 'dls_top_posters',
					'ext_name'	 => 'dls_web',
					'position'	 => 4,
					'active'	 => 1,
					'cat_name'	 => 'side',
				],
				[
					'block_name' => 'dls_recent_posts',
					'ext_name'	 => 'dls_web',
					'position'	 => 5,
					'active'	 => 1,
					'cat_name'	 => 'side',
				],
				[
					'block_name' => 'dls_recent_topics',
					'ext_name'	 => 'dls_web',
					'position'	 => 6,
					'active'	 => 0,
					'cat_name'	 => 'side',
				],
				[
					'block_name' => 'dls_whos_online',
					'ext_name'	 => 'dls_web',
					'position'	 => 1,
					'active'	 => 1,
					'cat_name'	 => 'bottom',
				],
			];
			$this->db->sql_multi_insert($this->table_prefix . 'blocks', $sql_ary);
		}
	}
}
