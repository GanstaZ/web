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
	* Get vendor name
	*
	* @param string $ext_name Name of the extension
	* @return string
	*/
	public function get_vendor($ext_name)
	{
		return strstr($ext_name, '_', true);
	}

	/**
	* Check if our block name is valid
	*
	* @param array $data Stores data that we need to validate
	* @return bool Depending on whether or not the block is valid
	*/
	public function is_valid_name($data)
	{
		$vendor = $this->get_vendor($data['vendor']);
		$validate = utf8_strpos($data['block_name'], $vendor);

		return ($validate !== false) ? true : false;
	}

	/**
	* If extension name is dls, remove prefix.
	*
	* @param array $data Data array
	* @return string $data['block_name']
	*/
	public function is_dls(array $data)
	{
		if ($this->get_vendor($data['vendor']) === 'dls')
		{
			$data['block_name'] = str_replace('dls_', '', $data['block_name']);
		}

		return $data['block_name'];
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
