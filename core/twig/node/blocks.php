<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\twig\node;

class blocks extends \Twig_Node
{
	/** @var \Twig_Environment */
	protected $environment;

	public function __construct(\Twig_Node_Expression $expr, \phpbb\template\twig\environment $environment, $lineno, $tag = null)
	{
		$this->environment = $environment;

		parent::__construct(array('expr' => $expr), array(), $lineno, $tag);
	}

	/**
	* Compiles the node to PHP.
	*
	* @param \Twig_Compiler A Twig_Compiler instance
	*/
	public function compile(\Twig_Compiler $compiler)
	{
		$compiler->addDebugInfo($this);

		$block = $this->getNode('expr')->getAttribute('name');
		$block_loader = $this->environment->getExtension('dls\web\core\twig\extension')->get_block_loader($block);

		foreach ($block_loader as $name => $path)
		{
			$path = $path . '/block';

			if ($this->environment->isDebug() || $this->environment->getLoader()->exists("@{$path}/{$name}.html"))
			{
				$compiler
					->write("\$this->env->loadTemplate('@{$path}/{$name}.html')->display(\$context);\n")
				;
			}
		}
	}
}
