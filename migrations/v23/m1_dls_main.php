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
	public function effectively_installed()
	{
		return $this->check('blocks') && $this->check('zodiac') && $this->check('zodiac_dates') && $this->check('zodiac_symbols') && $this->check('zodiac_heavenly_stems');
	}

	/**
	* Check condition exists for a given table name
	*
	* @param $name Name of the table
	* @return bool
	*/
	public function check($name)
	{
		return $this->db_tools->sql_table_exists($this->table_prefix . $name);
	}

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
						'sign_id' => ['UINT', null, 'auto_increment'],
						'sign'	  => ['VCHAR', ''],
						'plant'	  => ['VCHAR', ''],
						'gem'	  => ['VCHAR', ''],
						'ruler'	  => ['VCHAR', ''],
						'ext'	  => ['VCHAR', ''],
						'enr'	  => ['TINT:3', 0],
						'dir'	  => ['TINT:3', 0],
					],
					'PRIMARY_KEY' => ['sign_id'],
				],
				$this->table_prefix . 'zodiac_dates' => [
					'COLUMNS' => [
						'date_id' => ['UINT', null, 'auto_increment'],
						'zid'	  => ['UINT', 0],
						'type'	  => ['TINT:3', 0],
						'date'	  => ['VCHAR', ''],
					],
					'PRIMARY_KEY' => ['date_id'],
				],
				$this->table_prefix . 'zodiac_symbols' => [
					'COLUMNS' => [
						'symbol_id' => ['UINT', null, 'auto_increment'],
						'symbol'	=> ['VCHAR', ''],
						'type'		=> ['TINT:3', 0],
						'ruler'		=> ['VCHAR', ''],
						'ext'		=> ['VCHAR', ''],
						'dir'		=> ['TINT:3', 0],
					],
					'PRIMARY_KEY' => ['symbol_id'],
				],
				$this->table_prefix . 'zodiac_heavenly_stems' => [
					'COLUMNS' => [
						'stem_id' => ['UINT', null, 'auto_increment'],
						'snr'	  => ['TINT:3', 0],
						'enr'	  => ['TINT:3', 0],
						'sid'	  => ['TINT:3', 0],
						'aid'	  => ['TINT:3', 0],
						'bid'	  => ['TINT:3', 0],
						'cid'	  => ['TINT:3', 0],
						'did'	  => ['TINT:3', 0],
						'eid'	  => ['TINT:3', 0],
						'fid'	  => ['TINT:3', 0],
					],
					'PRIMARY_KEY' => ['stem_id'],
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
				$this->table_prefix . 'zodiac_dates',
				$this->table_prefix . 'zodiac_symbols',
				$this->table_prefix . 'zodiac_heavenly_stems',
			],
		];
	}
}
