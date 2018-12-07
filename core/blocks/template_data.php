<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\blocks;

/**
* DLS Web blocks helper class
*/
class template_data
{
	/** @var array Contains validated blocks data */
	protected $data;

	/**
	* Set template data
	*
	* @param string $name Name of the category
	* @param array	$data Block data
	* @return void
	*/
	public function set_template_data($name, $data)
	{
		if (!$this->get($name))
		{
			$this->data[$name] = $data;
		}

		$this->data[$name] = array_merge($this->data[$name], $data);
	}

	/**
	* Get data from a given category
	* Get template data for enabled blocks from a given category
	*
	* @param string $category Category name
	* @return array $this->blocks_data
	*/
	public function get($category)
	{
		if ($this->data[$category])
		{
			return $this->data[$category];
		}
	}
}
