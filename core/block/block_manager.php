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

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* DLS Web blocks manager
*/
class block_manager
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \dls\web\core\block\block_helper */
	protected $block_helper;

	/** @var array Contains enabled block services */
	protected $blocks;

	/**
	* Constructor
	*
	* @param \phpbb\config\config			  $config Config object
	* @param \dls\web\core\block\block_helper $block_helper Data helper object
	* @param \phpbb\di\service_collection	  $blocks_data Service container
	*/
	public function __construct(\phpbb\config\config $config, \dls\web\core\block\block_helper $block_helper, \phpbb\di\service_collection $blocks_collection)
	{
		$this->config = $config;
		$this->block_helper = $block_helper;

		$this->register_blocks($blocks_collection);
	}

	/**
	* Register all available blocks
	*
	* @param Service collection of blocks
	* @return null
	*/
	protected function register_blocks($blocks_collection)
	{
		foreach ($blocks_collection as $block)
		{
			$block->set_config($this->config);
			$data = $block->get_data();

			// Validate services
			if ($this->block_helper->is_valid_data($data))
			{
				$this->blocks[$data['cat_name']][$data['block_name']] = $block;
			}
		}
	}

	/**
	* Set helper data
	*
	* @param string $cat_name
	* @return null
	*/
	protected function set_helper_data($cat_name)
	{
		return $this->block_helper->set($cat_name, array_flip(array_keys($this->blocks[$cat_name])));
	}

	/**
	* Get block data
	*
	* @param string $cat_name
	* @return array
	*/
	protected function get_block($cat_name)
	{
		return $this->blocks[$cat_name];
	}

	/**
	* Load blocks
	*
	* @param null|mixed $cat_name
	* @param null|array $blocks_ary Array of block names
	* @return null
	*/
	public function load($cat_name = null, $blocks_ary = null)
	{
		if ($blocks = $this->get_blocks($cat_name, $blocks_ary))
		{
			$this->loading($blocks);
		}
	}

	/**
	* Get blocks
	*
	* @param null|mixed $cat_name
	* @param null|array $blocks_ary Array of block names
	* @return array
	*/
	public function get_blocks($cat_name = null, $blocks_ary = null)
	{
		if (null !== $cat_name)
		{
			if (is_array($cat_name))
			{
				return array_filter($this->get_requested_categories($cat_name));
			}

			$this->set_helper_data($cat_name);

			return $this->get_block($cat_name);
		}

		return array_filter($this->get_all_blocks($blocks_ary));
	}

	/**
	* Get requested categories
	*
	* @param array $categories Array of category names
	* @return array
	*/
	protected function get_requested_categories($categories)
	{
		$requested = [];
		foreach ($categories as $cat_name)
		{
			if ($this->get_block($cat_name))
			{
				$this->set_helper_data($cat_name);
				$requested = array_merge($requested, $this->get_block($cat_name));
			}
		}

		return $requested;
	}

	/**
	* Get all blocks
	*
	* @param null|array $blocks_ary Array of block names
	* @return array
	*/
	protected function get_all_blocks($blocks_ary = null)
	{
		$all = $requested = [];
		foreach (array_keys($this->blocks) as $cat_name)
		{
			$all = array_merge($all, $this->get_block($cat_name));

			if (is_array($blocks_ary))
			{
				$requested = array_merge($requested, $this->get_requested_blocks($cat_name, $blocks_ary));
			}
		}

		return ($blocks_ary) ? $requested : $all;
	}

	/**
	* Get requested blocks
	*
	* @param string $cat_name
	* @param array	$blocks_ary Array of block names
	* @return array
	*/
	protected function get_requested_blocks($cat_name, array $blocks_ary)
	{
		$array = [];
		foreach ($blocks_ary as $block)
		{
			if ($this->blocks[$cat_name][$block])
			{
				$this->block_helper->set($cat_name, $block);
				$array[$block] = $this->blocks[$cat_name][$block];
			}
		}

		return $array;
	}

	/**
	* Loading
	*
	* @param array $blocks Array of enabled blocks
	* @return void
	*/
	protected function loading($blocks)
	{
		foreach ($blocks as $block_service)
		{
			$block_service->load();
		}
	}

	/**
	* Has blocks
	*
	* @param null|string $cat_name
	* @return bool
	*/
	public function has_blocks($cat_name = null)
	{
		return (bool) count($this->get_blocks($cat_name));
	}
}
