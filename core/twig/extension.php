<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\twig;

/**
* DLS Web template loader extension for blocks
*/
class extension extends \Twig\Extension\AbstractExtension
{
	/** @var \phpbb\template\twig\environment */
	protected $environment;

	/** @var \dls\web\core\blocks\event */
	protected $event;

	/**
	* Constructor
	*
	* @param \phpbb\template\twig\environment $environment
	* @param \dls\web\core\blocks\event $event Block helper object
	*/
	public function __construct(\phpbb\template\twig\environment $environment, \dls\web\core\blocks\event $event)
	{
		$this->environment = $environment;
		$this->event = $event;
	}

	/**
	* Get the name of this extension
	*
	* @return string
	*/
	public function getName()
	{
		return 'dls';
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
}
