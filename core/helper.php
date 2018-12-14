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

use phpbb\template\template;

/**
* DLS Web helper class
*/
class helper
{
	/** @var \phpbb\group\helper */
	protected $group_helper;

	/** @var \phpbb\template\template */
	protected $template;

	/**
	* Constructor
	*
	* @param \phpbb\group\helper $group_helper Group helper object
	* @param \phpbb\template\template $template Template object
	*/
	public function __construct(\phpbb\group\helper $group_helper, template $template)
	{
		$this->group_helper = $group_helper;
		$this->template = $template;
	}

	/**
	* Assign key variable pairs from an array to a specified block
	*
	* @param string $type Template function [var, vars, block_vars]
	* @param array $data Template data
	* @return true
	*/
	public function assign($type, ...$data)
	{
		$this->template->{"assign_$type"}($data[0], $data[1]);
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
	* @param array $values Array of values
	* @param int $active Currently active value
	* @return string $options
	*/
	public function get_options($values, $active)
	{
		$options = '';
		foreach ($values as $value)
		{
			$s_selected = ($value == $active) ? ' selected="selected"' : '';
			$options .= '<option value="' . $value . '"' . $s_selected . '>' . $value . '</option>';
		}

		return $options;
	}
}
