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
class extension extends \Twig_Extension
{
	/** @var \dls\web\core\block\block_helper */
	protected $block_helper;

	/**
	* Constructor
	*
	* @param \dls\web\core\block\block_helper $block_helper Block helper object
	*/
	public function __construct(\dls\web\core\block\block_helper $block_helper)
	{
		$this->block_helper = $block_helper;
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
		return $this->block_helper->get_vars($cat_name);
	}

	/**
	* Returns the token parser instance to add to the existing list.
	*
	* @return array An array of Twig_TokenParser instances
	*/
	public function getTokenParsers()
	{
		return [
			new \dls\web\core\twig\tokenparser\blocks(),
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
		foreach ($this->block_helper->get_vars($cat_name) as $name => $path)
		{
			$path = $path . '/block';

			if ($env->getLoader()->exists("@{$path}/{$name}.html"))
			{
				$env->loadTemplate("@{$path}/{$name}.html")->display($context);
			}
		}
	}
}
