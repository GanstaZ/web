<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dls.org/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core;

/**
* DLS Web helper class
*/
class helper
{
	/** @var \phpbb\group\helper */
	protected $group_helper;

	/**
	* Constructor
	*
	* @param \phpbb\group\helper $group_helper Group helper object
	*/
	public function __construct(\phpbb\group\helper $group_helper)
	{
		$this->group_helper = $group_helper;
	}

	/**
	* Get group name
	*
	* @param string $group_name name of the group
	*
	* @return string group_name
	*/
	public function get_name($group_name)
	{
		return $this->group_helper->get_name($group_name);
	}

	/**
	* Truncate title
	*
	* @param string $title Truncate title
	* @param string $length Max length of the string
	*
	* @return mixed
	*/
	public function truncate($title, $length)
	{
		return truncate_string(censor_text($title), $length, 255, false, '...');
	}

	/**
	* Count data
	*
	* @param array $data Data
	* @param string $column Column
	* @param int|string $field Field
	* @return int
	*/
	public function count($data, $column, $field)
	{
		return count(array_keys(array_column($data, $column), $field));
	}

	/**
	* Get position options
	*
	* @param int $max Highest number to use in a loop
	* @param int $current_position Current position of a block
	* @return string $options
	*/
	public function get_options($max, $current_position)
	{
		$options = '';
		foreach ($max as $pos)
		{
			$s_selected = ($pos == $current_position) ? ' selected="selected"' : '';
			$options .= '<option value="' . $pos . '"' . $s_selected . '>' . $pos . '</option>';
		}

		return $options;
	}
}
