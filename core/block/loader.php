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
* DLS Web blocks loader
*/
class loader
{
	/** @var \dls\web\core\block\data */
	protected $data;

	/**
	* Constructor
	*
	* @param \dls\web\core\block\data $data Blocks data object
	*/
	public function __construct(\dls\web\core\block\data $data)
	{
		$this->data = $data;
	}

	/**
	* Load all enabled blocks in a given category
	*
	* @param string $block_name Category name
	*
	* @return array $blocks_data
	*/
	public function load($block_name)
	{
		$array = $this->data->get_all();

		// Sort by block position
		array_multisort($array, SORT_ASC);

		$blocks_data = [];
		foreach ($array as $data)
		{
			// Validate data... If we pass validation, add it into $blocks_data array
			if ($this->data->is_valid_block($data, $block_name))
			{
				$data['name'] = $this->data->is_dls($data);

				$blocks_data[$data['name']] = $data['ext_name'];
			}
		}

		return $blocks_data;
	}
}
