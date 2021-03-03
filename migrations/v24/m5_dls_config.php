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

class m5_dls_config extends \phpbb\db\migration\migration
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
			// Add the config variables we want to be able to set
			['config.add', ['dls_core_version', '2.4.0-dev']],
			['config.add', ['dls_news_fid', 2]],
			['config.add', ['dls_the_team_fid', 8]],
			['config.add', ['dls_top_posters_fid', 0]],
			['config.add', ['dls_recent_posts_fid', 0]],
			['config.add', ['dls_recent_topics_fid', 0]],
			['config.add', ['dls_profile_tabs', 1]],
			['config.add', ['dls_pagination', 1]],
			['config.add', ['dls_title_length', 26]],
			['config.add', ['dls_content_length', 150]],
			['config.add', ['dls_limit', 5]],
			['config.add', ['dls_user_limit', 5]],
			// Blocks
			['config.add', ['dls_blocks', 1]],
			['config.add', ['dls_special', 1]],
			['config.add', ['dls_right', 1]],
			['config.add', ['dls_left', 0]],
			['config.add', ['dls_middle', 1]],
			['config.add', ['dls_top', 0]],
			['config.add', ['dls_bottom', 0]],
			// Plugins
			['config.add', ['dls_zodiac', 1]],
			['config.add', ['dls_astro', 0]],
			['config.add', ['dls_weather', 0]],
			['config.add', ['dls_achievements', 0]],
		];
	}
}
