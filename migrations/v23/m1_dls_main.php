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

class m1_dls_main extends \phpbb\db\migration\migration
{
	/**
	* {@inheritdoc}
	*/
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v320\v320');
	}

	/**
	* Add the table schemas to the database:
	*
	* @return array Array of table schema
	* @access public
	*/
	public function update_schema()
	{
		return [
			'add_tables' => [
				$this->table_prefix . 'blocks' => [
					'COLUMNS' => [
						'block_id'	 => ['UINT', null, 'auto_increment'],
						'block_name' => ['VCHAR', ''],
						'ext_name'	 => ['VCHAR', ''],
						'position'	 => ['UINT', 0],
						'active'	 => ['BOOL', 0],
						'cat_name'	 => ['VCHAR', ''],
					],
					'PRIMARY_KEY' => ['block_id'],
				],
				$this->table_prefix . 'zodiac' => [
					'COLUMNS' => [
						'zodiac_id' => ['UINT', null, 'auto_increment'],
						'sign'	=> ['VCHAR', ''],
						'plant' => ['VCHAR', ''],
						'gems'	=> ['VCHAR', ''],
						'ruler' => ['VCHAR', ''],
						'ext'	=> ['VCHAR', ''],
						'enr'	=> ['TINT:3', 0],
					],
					'PRIMARY_KEY' => ['zodiac_id'],
				],
				$this->table_prefix . 'zodiac_data' => [
					'COLUMNS' => [
						'date_id' => ['UINT', null, 'auto_increment'],
						'zid'	=> ['UINT', 0],
						'type'	=> ['TINT:3', 0],
						'start' => ['VCHAR:255', ''],
						'end'	=> ['VCHAR:255', ''],
					],
					'PRIMARY_KEY' => ['date_id'],
				],
				$this->table_prefix . 'zodiac_heavenly_stems' => [
					'COLUMNS' => [
						'cycle_id' => ['UINT', null, 'auto_increment'],
						'stem'	=> ['VCHAR', ''],
						'a_id'	=> ['TINT:3', 0],
						'b_id'	=> ['TINT:3', 0],
						'c_id'	=> ['TINT:3', 0],
						'd_id'	=> ['TINT:3', 0],
						'e_id'	=> ['TINT:3', 0],
						'f_id'	=> ['TINT:3', 0],
					],
					'PRIMARY_KEY' => ['cycle_id'],
				],
			],
		];
	}

	/**
	* Drop the schemas from the database
	*
	* @return array Array of table schema
	* @access public
	*/
	public function revert_schema()
	{
		return [
			'drop_tables' => [
				$this->table_prefix . 'blocks',
				$this->table_prefix . 'zodiac',
				$this->table_prefix . 'zodiac_data',
				$this->table_prefix . 'zodiac_heavenly_stems',
			],
		];
	}
}
