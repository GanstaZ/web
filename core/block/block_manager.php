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
	/** @var array Contains enabled block services */
	protected $blocks;

	/**
	* Constructor
	*
	* @param \ContainerInterface $container A container
	*/
	public function __construct(ContainerInterface $container)
	{
		$this->register_blocks($container);
	}

	/**
	* Register all available blocks
	*
	* @param ContainerInterface
	* @return null
	*/
	protected function register_blocks($container)
	{
		foreach ($container->get('dls.web.blocks.collection') as $block)
		{
			$block->set_config($container->get('config'));
			$data = $block->get_data();

			// Validate services
			if ($container->get('dls.web.blocks.provaider')->is_valid_service($data))
			{
				$this->blocks[$data['cat_name']][$data['block_name']] = $block;
			}
		}
	}

	/**
	* Load blocks
	*
	* @param string|null $cat_name
	* @return null
	*/
	public function load($cat_name = null)
	{
		if ($blocks = $this->get_blocks($cat_name))
		{
			$this->loading($blocks);
		}
	}

	/**
	* Get blocks
	*
	* @param string|null $cat_name
	* @return array
	*/
	public function get_blocks($cat_name = null)
	{
		if (null !== $cat_name)
		{
			if (!$this->blocks[$cat_name])
			{
				return [];
			}

			return $this->blocks[$cat_name];
		}

		$load_all = [];
		foreach (array_keys($this->blocks) as $service_name)
		{
			$load_all = array_merge($load_all, $this->blocks[$service_name]);
		}

		return array_filter($load_all);
	}

	/**
	* Loading
	*
	* @param array $blocks Array of enabled blocks
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
	* @param string|null $cat_name
	*
	* @return bool
	*/
	public function has_blocks($cat_name = null)
	{
		return (bool) count($this->get_blocks($cat_name));
	}
}
