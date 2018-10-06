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

class m_dls_main extends \phpbb\db\migration\migration
{
	/**
	* If our config variable already exists in the db
	* skip this migration.
	*/
	public function effectively_installed()
	{
		return isset($this->config['dls_core_version']) && $this->db_tools->sql_table_exists($this->table_prefix . 'zodiac');
	}

	public function update_data()
	{
		return [
			// Add the config variables we want to be able to set
			['config.add', ['dls_core_version', '2.3.6']],
			['config.add', ['dls_news_fid', 2]],
			['config.add', ['dls_the_team_fid', 8]],
			['config.add', ['dls_top_posters_fid', 0]],
			['config.add', ['dls_recent_topics_fid', 0]],
			['config.add', ['dls_show_pagination', 1]],
			['config.add', ['dls_show_news', 1]],

			['config.add', ['dls_estate', 0]],
			['config.add', ['dls_dp', 0]],
			['config.add', ['dls_cp', 0]],
			['config.add', ['dls_zodiac', 1]],
			['config.add', ['dls_moon', 1]],
			['config.add', ['dls_weather', 0]],
			['config.add', ['dls_badges', 0]],

			['config.add', ['dls_title_length', 26]],
			['config.add', ['dls_content_length', 150]],
			['config.add', ['dls_limit', 5]],
			['config.add', ['dls_user_limit', 5]],

			// Add points data
			['config_text.add', ['dls_points', json_encode(['p1' => 10, 'p2' => 20, 'p3' => 30, 'p4' => 40, 'p5' => 50,])]],
		];
	}

	public function update_schema()
	{
		return [
			'add_tables' => [
				$this->table_prefix . 'blocks' => [
					'COLUMNS' => [
						'category_id'	=> ['UINT', null, 'auto_increment'],
						'category_name' => ['VCHAR', ''],
					],
					'PRIMARY_KEY' => ['category_id'],
				],
				$this->table_prefix . 'blocks_data' => [
					'COLUMNS' => [
						'block_id'	  => ['UINT', null, 'auto_increment'],
						'block_name'  => ['VCHAR', ''],
						'vendor'	  => ['VCHAR', ''],
						'position'	  => ['UINT', 0],
						'active'	  => ['BOOL', 0],
						'category_id' => ['UINT', 0],
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

	public function revert_schema()
	{
		return [
			// Remove the config variables
			['config.remove', ['dls_core_version']],
			['config.remove', ['dls_news_fid']],
			['config.remove', ['dls_the_team_fid']],
			['config.remove', ['dls_top_posters_fid']],
			['config.remove', ['dls_recent_topics_fid']],
			['config.remove', ['dls_show_pagination']],
			['config.remove', ['dls_show_news']],

			['config.remove', ['dls_estate']],
			['config.remove', ['dls_dp']],
			['config.remove', ['dls_cp']],
			['config.remove', ['dls_zodiac']],
			['config.remove', ['dls_moon']],
			['config.remove', ['dls_weather']],
			['config.remove', ['dls_badges']],

			['config.remove', ['dls_title_length']],
			['config.remove', ['dls_content_length']],
			['config.remove', ['dls_limit']],
			['config.remove', ['dls_user_limit']],

			// Remove points data
			['config_text.remove', ['dls_points']],

			// Drop tables
			'drop_tables' => [
				$this->table_prefix . 'blocks',
				$this->table_prefix . 'blocks_data',
				$this->table_prefix . 'zodiac',
				$this->table_prefix . 'zodiac_data',
				$this->table_prefix . 'zodiac_heavenly_stems',
			],
		];
	}
}
