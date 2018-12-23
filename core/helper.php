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

use phpbb\group\helper as group_helper;
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

	/** @var array Contains phpBB vars */
	protected $phpbb_vars;

	/**
	* Constructor
	*
	* @param \phpbb\group\helper $group_helper Group helper object
	* @param \phpbb\template\template $template Template object
	* @param string $root_path Path to the phpbb includes directory
	* @param string $php_ext PHP file extension
	*/
	public function __construct(group_helper $group_helper, template $template, $root_path, $php_ext)
	{
		$this->group_helper = $group_helper;
		$this->template = $template;
		$this->phpbb_vars = ['root_path' => $root_path, 'php_ext' => $php_ext];
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
	* Get $phpbb_root_path or php_ext
	*
	* @return string
	*/
	public function get(string $var)
	{
		return ($this->phpbb_vars[$var]) ? $this->phpbb_vars[$var] : null;
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
