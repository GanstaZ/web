<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\plugins\astro\zodiac;

/**
* DLS Web Myanmar zodiac
*/
class myanmar extends base
{
	/** @var helper */
	protected $helper;

	/**
	* Constructor
	*
	* @param helper $helper Zodiac helper object
	*/
	public function __construct(helper $helper)
	{
		$this->helper = $helper;
	}

	/**
	* {@inheritdoc}
	*/
	public function get_name(): string
	{
		return 'myanmar';
	}

	/**
	* {@inheritdoc}
	*/
	public function get_type(): string
	{
		return 'zodiac';
	}

	/**
	* {@inheritdoc}
	*/
	public function load(string $dob): array
	{
		$row = $this->helper->zodiac_data(6)[$dob];

		if (!empty($row))
		{
			return [$this->get_data($row)];
		}
	}
}
