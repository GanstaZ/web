<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core;

use phpbb\language\language;

/**
* DLS Web time ago
*/
class dls_time
{
	/** @var language */
	protected $language;

	/** @var string used in our time ago output */
	protected $length = 1;

	/**
	* Constructor
	*
	* @param language $language Language object
	*/
	public function __construct(language $language)
	{
		$this->language = $language;
	}

	/**
	* Time ago
	*
	* @param string $date
	* @return string
	*/
	public function ago($date): string
	{
		$interval = date_create('now')->diff(new \DateTime($date));

		// Assign units that we will use for our time ago method.
		$units = array_filter([
			'year'	 => $interval->y,
			'month'	 => $interval->m,
			'day'	 => $interval->d,
			'hour'	 => $interval->h,
			'minute' => $interval->i,
			'second' => $interval->s,
		]);

		if (!$units || $this->length !== 1)
		{
			return $this->language->lang('UNKNOWN');
		}

		return $this->plural(array_slice($units, 0, (int) $this->length));
	}

	/**
	* Plural
	*
	* @param array $unit Time units (1, 2, 3... [numbers] & s, i, h... [sec, min aso])
	* @return string
	*/
	protected function plural($unit): string
	{
		$uot = (string) key($unit);
		$int = (int) $unit[$uot];

		return $this->language->lang('dls_ago', $int, $this->language->lang($uot, $int));
	}

	/**
	* Calculate decade (Will be removed.. maybe not)
	*
	* @param string $uot Unit of time [second, minute...]
	* @param int	$int Time value [1, 2...]
	* @return string
	*/
	protected function calculate($uot, $int): ?string
	{
		$arr = [];
		if ($uot !== 'year' || $int < 10)
		{
			return null;
		}

		$arr['decade'] = substr($int, 0, -1);

		return $arr;
	}
}
