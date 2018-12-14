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

class m5_dls_config extends \phpbb\db\migration\migration
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
			// Add the config variables we want to be able to set
			['config.add', ['dls_core_version', '2.3.7']],
			['config.add', ['dls_news_fid', 2]],
			['config.add', ['dls_the_team_fid', 8]],
			['config.add', ['dls_top_posters_fid', 0]],
			['config.add', ['dls_recent_topics_fid', 0]],
			['config.add', ['dls_show_pagination', 1]],
			['config.add', ['dls_show_news', 1]],

			['config.add', ['dls_biz', 0]],
			['config.add', ['dls_dp', 0]],
			['config.add', ['dls_cp', 0]],
			['config.add', ['dls_zodiac', 1]],
			['config.add', ['dls_moon', 1]],
			['config.add', ['dls_weather', 0]],
			['config.add', ['dls_achievements', 0]],

			['config.add', ['dls_title_length', 26]],
			['config.add', ['dls_content_length', 150]],
			['config.add', ['dls_limit', 5]],
			['config.add', ['dls_user_limit', 5]],

			// Add points data
			['config_text.add', ['dls_points', json_encode(['p1' => 10, 'p2' => 20, 'p3' => 30, 'p4' => 40, 'p5' => 50,])]],
		];
	}
}
