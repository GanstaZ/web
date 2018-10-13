<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\block;

/**
* DLS Web blocks data provaider class
*/
class data_helper
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var blocks table */
	protected $blocks;

	/** @var blocks data table */
	protected $b_data;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\driver_interface $db	   Db object
	* @param string							   $blocks Blocks category table
	* @param string							   $b_data Blocks data table
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $blocks, $b_data)
	{
		$this->db = $db;
		$this->blocks = $blocks;
		$this->b_data = $b_data;
	}

	/**
	* Get
	*
	* @param string $table_name Name of the table we want to use.
	*
	* @return object
	*/
	public function get($table_name)
	{
		return $this->{$table_name};
	}

	public function update($name, $data)
	{
		$this->db->sql_query('UPDATE ' . $this->b_data . ' SET ' . $this->db->sql_build_array('UPDATE', $data) . "
			WHERE block_name = '" . $this->db->sql_escape($name) . "'");
	}

	public function count_blocks($category_id)
	{
		$sql = 'SELECT position, COUNT(position) as block_pos
					FROM ' . $this->b_data . '
					WHERE category_id = ' . (int) $category_id;
		$result = $this->db->sql_query($sql, 3600);
		$total = (int) $this->db->sql_fetchfield('block_pos');
		$this->db->sql_freeresult($result);

		return (!$total) ? false : $total;
	}

	public function is_enabled($block_name)
	{
		$sql = 'SELECT active
				FROM ' . $this->b_data . "
				WHERE block_name = '" . $this->db->sql_escape($block_name) . "'
					AND active = 1";
		$result = $this->db->sql_query($sql, 3600);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return ($row) ? $row['active'] : false;
	}

	/**
	* Get vendor name
	*
	* @param string $ext_name Name of the extension.
	*
	* @return string
	*/
	public function get_vendor_name($ext_name)
	{
		return strstr($ext_name, '_', true);
	}

	/**
	* Check if our block name is valid
	*
	* @param string $data Stores data that we need to validate.
	*
	* @return bool Depending on whether or not the block is valid
	*/
	public function is_valid_name($data)
	{
		$vendor = $this->get_vendor_name($data['vendor']);
		$validate = utf8_strpos($data['block_name'], $vendor);

		return ($validate !== false) ? true : false;
	}

	/**
	* Count data
	*
	* @param  array		 $data Data
	* @param  string	 $column Column
	* @param  string|int $field	 Field
	* @return int
	*/
	public function count($data, $column, $field)
	{
		return count(array_keys(array_column($data, $column), $field));
	}

	/**
	* Get position options
	*
	* @param  int	 $max Highest number to use in a loop
	* @param  int	 $current_position Current position of a block
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
