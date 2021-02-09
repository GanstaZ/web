<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2021, GanstaZ, http://www.github.com/GanstaZ/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\twig;

use phpbb\template\twig\environment;
use dls\web\core\blocks\event;
use phpbb\group\helper as group_helper;

/**
* DLS Web template loader extension for blocks
*/
class extension extends \Twig\Extension\AbstractExtension
{
	/** @var \phpbb\template\twig\environment */
	protected $environment;

	/** @var \dls\web\core\blocks\event */
	protected $event;

	/** @var helper */
	protected $group_helper;

	/**
	* Constructor
	*
	* @param environment  $environment
	* @param event		  $event Block helper object
	* @param group_helper $group_helper Group helper object
	*/
	public function __construct(environment $environment, event $event, group_helper $group_helper)
	{
		$this->environment = $environment;
		$this->event = $event;
		$this->group_helper = $group_helper;
	}

	/**
	* Get block data
	*
	* @param string $cat_name Category name
	* @return method
	*/
	public function get_block_loader($cat_name)
	{
		return $this->event->get($cat_name);
	}

	/**
	* Returns the token parser instance to add to the existing list.
	*
	* @return array An array of Twig_TokenParser instances
	*/
	public function getTokenParsers()
	{
		return [
			new \dls\web\core\twig\tokenparser\blocks($this->environment),
		];
	}

	/**
	* Returns a list of global functions to add to the existing list.
	*
	* @return array An array of global functions
	*/
	public function getFunctions()
	{
		return [
			new \Twig_SimpleFunction('blocks', [$this, 'blocks'], ['needs_environment' => true, 'needs_context' => true]),
			new \Twig_SimpleFunction('get_group_name', [$this, 'get_group_name']),
		];
	}

	/**
	* Load blocks
	*
	* @param \Twig_Environment $env		 Twig_Environment instance
	* @param string			   $context	 Current context
	* @param string			   $cat_name Name of the category
	*
	* @return mixed
	*/
	public function blocks(\Twig_Environment $env, $context, $cat_name)
	{
		foreach ($this->event->get($cat_name) as $name => $path)
		{
			$path = $path . '/block';

			if ($env->getLoader()->exists("@{$path}/{$name}.html"))
			{
				$env->loadTemplate("@{$path}/{$name}.html")->display($context);
			}
		}
	}

	/**
	* Get group name
	*
	* @param string $group_name name of the group
	* @return string
	*/
	public function get_group_name($group_name)
	{
		return $this->group_helper->get_name($group_name);
	}
}
