<?php
/**
*
* DLS Web. An extension for the phpBB Forum Software package.
*
* @copyright (c) 2018, GanstaZ, http://www.dlsz.eu/
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace dls\web\core\twig\tokenparser;

class blocks extends \Twig\TokenParser\AbstractTokenParser
{
	/** @var \phpbb\template\twig\environment */
	protected $environment;

	/**
	* Constructor
	*
	* @param \phpbb\template\twig\environment $environment
	*/
	public function __construct(\phpbb\template\twig\environment $environment)
	{
		$this->environment = $environment;
	}

	/**
	* Parses a token and returns a node.
	*
	* @param \Twig\Token $token A Twig\Token instance
	*
	* @return \Twig\Node\Node A Twig\Node instance
	*/
	public function parse(\Twig_Token $token)
	{
		$expr = $this->parser->getExpressionParser()->parseExpression();

		$stream = $this->parser->getStream();
		$stream->expect(\Twig_Token::BLOCK_END_TYPE);

		return new \dls\web\core\twig\node\blocks($expr, $this->environment, $token->getLine(), $this->getTag());
	}

	/**
	* Gets the tag name associated with this token parser.
	*
	* @return string The tag name
	*/
	public function getTag()
	{
		return 'BLOCKS';
	}
}
