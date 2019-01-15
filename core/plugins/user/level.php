<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\plugins\user;

/**
* DLS Web level plugin
*/
class level implements \dls\web\core\plugins\plugin_interface
{
	/**
	* Constructor
	*
	*/
	public function __construct()
	{
	}

	/**
	* {@inheritdoc}
	*/
	public function get_name(): string
	{
		return 'level';
	}

	/**
	* {@inheritdoc}
	*/
	public function get_type(): string
	{
		return 'achievement';
	}

	/**
	* Get member exp level
	*
	* @param int $points
	*
	* @return array
	*/
	public function get_member_exp($points)
	{
		$start_lvl = 0;	 // Level 1 start xp
		$end_lvl   = 10; // Level 1 end xp
		$increase  = 0;	 // Increase by extra how many per level?
		$multiply  = 6;	 // Multiply by how many per level? (1- easy / 20- hard)

		$level	 = 0;	 // Current level
		$counter = 0;	 // Counter

		do
		{
			$counter = $counter + 1;
			if ($counter % 2 === 0)
			{
				$increase = $increase + $multiply;
			}

			if (($points < $end_lvl) && ($points >= $start_lvl))
			{
				$level = $counter;
				$start = $start_lvl;
				$end = $end_lvl;
			}

			$start_lvl = $end_lvl;
			$end_lvl = $end_lvl + $increase;
		}
		while ($level === 0);
		$level--;

		// Calculate progress to next level
		$percent = (($points - $start) / ($end - $start)) * 100;
		$percent = ($percent === 0) ? '1%' : sprintf('%.1d%%', round($percent));

		return ['percent' => $percent, 'level' => $level, 'end' => 'start: ' . $start . ' end: ' .$end,];
	}
}
