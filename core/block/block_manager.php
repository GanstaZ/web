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
* DLS Web blocks manager
*/
class block_manager
{
	/** @var \phpbb\event\dispatcher */
	protected $dispatcher;

	protected $blocks = ['dls.web_main_block', 'dls.web_special_block',];

	/**
	* Constructor
	*
	 * @param \phpbb\event\dispatcher $dispatcher Dispatcher object
	*/
	public function __construct(\phpbb\event\dispatcher $dispatcher)
	{
		$this->dispatcher = $dispatcher;
	}

	/**
	* Load block
	*
	* @param string $category Load all blocks in given category.
	*/
	public function load($category)
	{
		$category = 'dls.web_' . $category;

		if (!in_array($category, $this->blocks))
		{
			return;
		}

		$this->loading($category);
	}

	/**
	* Load blocks
	*
	* @param string $category Load all blocks in given category.
	*/
	public function load_all()
	{
		foreach ($this->blocks as $block)
		{
			if ($this->has_blocks($block))
			{
				$this->dispatcher->dispatch($block);
			}
		}
	}

	/**
	* Loading
	*
	* @param string $category Name of the category
	*/
	protected function loading($category = null)
	{
		return $this->dispatcher->dispatch($category);
	}

	/**
	* Has blocks
	*
	* @param string $category Search in category for blocks.
	*
	* @return bool
	*/
	public function has_blocks($category = null)
	{
		return (bool) count($this->dispatcher->hasListeners($category));
	}
}
