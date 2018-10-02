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

class m_dls_blocks_data extends \phpbb\db\migration\migration
{
	/**
	* This migration depends on dls's m_dls_acp_module migration
	* already being installed.
	*/
	static public function depends_on()
	{
		return ['\dls\web\migrations\v23\m_dls_acp_module'];
	}

	public function update_data()
	{
		return [
			// Add blocks
			['config.add', ['dls_mini_profile', 1]],
			['config.add', ['dls_information', 1]],
			['config.add', ['dls_the_team', 1]],
			['config.add', ['dls_top_posters', 1]],
			['config.add', ['dls_recent_posts', 1]],
			['config.add', ['dls_recent_topics', 0]],
			// Add positions
			['config.add', ['dls_information_b', 1]],
			['config.add', ['dls_the_team_b', 2]],
			['config.add', ['dls_top_posters_b', 3]],
			['config.add', ['dls_recent_posts_b', 4]],
			['config.add', ['dls_recent_topics_b', 5]],
		];
	}

	public function revert_schema()
	{
		return [
			// Remove blocks
			['config.remove', ['dls_mini_profile']],
			['config.remove', ['dls_information']],
			['config.remove', ['dls_the_team']],
			['config.remove', ['dls_top_posters']],
			['config.remove', ['dls_recent_posts']],
			['config.remove', ['dls_recent_topics']],
			// Remove positions
			['config.remove', ['dls_information_b']],
			['config.remove', ['dls_the_team_b']],
			['config.remove', ['dls_top_posters_b']],
			['config.remove', ['dls_recent_posts_b']],
			['config.remove', ['dls_recent_topics_b']],
		];
	}
}
