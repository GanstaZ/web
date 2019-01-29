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
	/** @var helper */
	protected $group_helper;

	/** @var template */
	protected $template;

	/** @var array Contains phpBB vars */
	protected $phpbb_vars;

	/**
	* Constructor
	*
	* @param group_helper $group_helper Group helper object
	* @param template	  $template		Template object
	* @param string		  $root_path	Path to the phpbb includes directory
	* @param string		  $php_ext		PHP file extension
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
	* @param string $type Template function [var, vars, block_vars, block_vars_array]
	* @param mixed	$data Template data
	* @return void
	*/
	public function assign(string $type, ...$data): void
	{
		$this->template->{"assign_$type"}($data[0], $data[1]);
	}

	/**
	* Get $phpbb_root_path or php_ext
	*
	* @return string
	*/
	public function get(string $var): ?string
	{
		return $this->phpbb_vars[$var] ?? null;
	}

	/**
	* Get group name
	*
	* @param string $group_name name of the group
	* @return string
	*/
	public function get_name(string $group_name): string
	{
		return $this->group_helper->get_name($group_name);
	}

	/**
	* Truncate title
	*
	* @param string		 $title	 Truncate title
	* @param int		 $length Max length of the string
	* @param null|string $ellips Language ellips
	* @return string
	*/
	public function truncate(string $title, int $length, $ellips = null): string
	{
		return truncate_string(censor_text($title), $length, 255, false, $ellips ?? '...');
	}

	/**
	* Get categories
	*
	* @param object $db Database object
	* @return array
	*/
	public function get_categories($db): array
	{
		$sql = 'SELECT forum_id, forum_name
				FROM ' . FORUMS_TABLE . '
				WHERE forum_type = ' . FORUM_POST . '
					AND news_fid_enable = 1';
		$result = $db->sql_query($sql, 86400);

		while ($row = $db->sql_fetchrow($result))
		{
			$forum_ary[(int) $row['forum_id']] = (string) $row['forum_name'];
		}
		$db->sql_freeresult($result);

		return $forum_ary ?? [];
	}

	/**
	* Get position options
	*
	* @param array $values Array of values
	* @param int   $active Currently active value
	* @return string
	*/
	public function get_options(array $values, int $active): string
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
