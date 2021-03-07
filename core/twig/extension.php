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
use phpbb\group\helper as group;

/**
* DLS Web template loader extension for blocks
*/
class extension extends \Twig\Extension\AbstractExtension
{
	/** @var environment */
	protected $environment;

	/** @var event */
	protected $event;

	/** @var helper */
	protected $group;

	/**
	* Constructor
	*
	* @param environment $environment Environment object
	* @param event		 $event		  Block helper object
	* @param group		 $group		  Group helper object
	*/
	public function __construct(environment $environment, event $event, group $group)
	{
		$this->environment = $environment;
		$this->event = $event;
		$this->group = $group;
	}

	/**
	* Get block data
	*
	* @param string $section Section
	* @return method
	*/
	public function get_block_loader($section)
	{
		return $this->event->get($section);
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
	* @param \Twig_Environment $env		Twig_Environment instance
	* @param string			   $context Current context
	* @param string			   $section Section name
	* @return mixed
	*/
	public function blocks(\Twig_Environment $env, $context, $section)
	{
		foreach ($this->get_block_loader($section) as $name => $path)
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
		return $this->group->get_name($group_name);
	}
}
