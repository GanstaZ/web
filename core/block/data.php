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
* DLS Web blocks data
*/
class data
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\event\dispatcher */
	protected $dispatcher;

	/** @var string */
	protected $_dls = 'dls_web';

	/** @var side_blocks */
	protected $_type = 'side_blocks';

	/**
	* Constructor
	*
	* @param \phpbb\config\config	 $config	 Config object
	* @param \phpbb\event\dispatcher $dispatcher Dispatcher object
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\event\dispatcher $dispatcher)
	{
		$this->config = $config;
		$this->dispatcher = $dispatcher;
	}

	/**
	* Get all blocks
	*
	* @return array
	*/
	public function get_all()
	{
		$block_data_ary = [
			[
				'pos' => $this->config['dls_information_b'], 'ext_name' => $this->_dls, 'name' => 'dls_information', 'type' => $this->_type
			],
			[
				'pos' => $this->config['dls_the_team_b'], 'ext_name' => $this->_dls, 'name' => 'dls_the_team', 'type' => $this->_type
			],
			[
				'pos' => $this->config['dls_top_posters_b'], 'ext_name' => $this->_dls, 'name' => 'dls_top_posters', 'type' => $this->_type
			],
			[
				'pos' => $this->config['dls_recent_posts_b'], 'ext_name' => $this->_dls, 'name' => 'dls_recent_posts', 'type' => $this->_type
			],
			[
				'pos' => $this->config['dls_recent_topics_b'], 'ext_name' => $this->_dls, 'name' => 'dls_recent_topics', 'type' => $this->_type
			],
			[
				'pos' => $this->config['dls_whos_online_b'], 'ext_name' => $this->_dls, 'name' => 'dls_whos_online', 'type' => 'bottom_blocks'
			],
		];

		/**
		* Event to modify blocks
		*
		* @event dls.web.blocks_data
		* @var array $block_data_ary
		* @since 2.3.6-dev
		*/
		$vars = ['block_data_ary'];
		extract($this->dispatcher->trigger_event('dls.web.blocks_data', compact($vars)));

		return ($block_data_ary) ? $block_data_ary : [];
	}

	/**
	* Get validated block data
	*
	* @param string|null $type Data type
	*
	* @return array
	*/
	public function get($type = null)
	{
		$data_ary = $acp_data_ary = [];

		// Validate blocks & inject into new array
		foreach ($this->get_all() as $key => $value)
		{
			if ($this->is_valid_acp_data($value))
			{
				$acp_data_ary[] = ['name' => $value['name']];

				$data_ary[$this->get_all()[$key]['type']][] = [
					'cat'  => $value['type'],
					'name' => $value['name'],
					'pos'  => (int) $value['pos'],
				];
			}
		}

		return ($type === 'acp') ? $acp_data_ary : $data_ary;
	}

	/**
	* Check if our block is set in config
	*
	* @param string $config_name Name of the block.
	*
	* @return bool
	*/
	protected function is_set($config_name)
	{
		return isset($this->config[$config_name]);
	}

	/**
	* Check if our block is set & enabled in config
	*
	* @param string $config_name Name of the block.
	*
	* @return bool
	*/
	protected function is_enabled($config_name)
	{
		return $this->is_set($config_name) && $this->config[$config_name];
	}

	/**
	* Get vendor name
	*
	* @param string $ext_name Name of the extension.
	*
	* @return string
	*/
	public function get_vendor_name($ext_name)
	{
		return strstr($ext_name, '_', true);
	}

	/**
	* Check if our block name is valid
	*
	* @param string $data Stores data that we need to validate.
	*
	* @return bool Depending on whether or not the block is valid
	*/
	protected function is_valid_name($data)
	{
		$vendor = $this->get_vendor_name($data['ext_name']);
		$validate = utf8_strpos($data['name'], $vendor);

		return ($validate !== false) ? true : false;
	}

	/**
	* Check if our acp block data is valid & enabled
	*
	* @param array $data Stores data that we need to validate.
	*
	* @return bool Depending on whether or not the block is valid
	*/
	public function is_valid_acp_data($data)
	{
		return $this->is_set($data['name']) && $this->is_valid_name($data);
	}

	/**
	* Check if our block data is valid & enabled
	*
	* @param string $data Stores data that we need to validate.
	*
	* @return bool Depending on whether or not the block is valid
	*/
	protected function is_valid($data)
	{
		return $this->is_enabled($data['name']) && $this->is_valid_name($data);
	}

	/**
	* Check if our block data is valid & enabled
	*
	* @param array	$data Data array
	* @param string $name Category name
	*
	* @return bool
	*/
	public function is_valid_block($data, $name)
	{
		return ($data['type'] === $name) && $this->is_valid($data);
	}

	/**
	* If extension name is dls, remove prefix.
	*
	* @param array $data Data array
	*
	* @return $data['name']
	*/
	public function is_dls($data)
	{
		if ($this->get_vendor_name($data['ext_name']) === 'dls')
		{
			$data['name'] = str_replace('dls_', '', $data['name']);
		}

		return $data['name'];
	}

	/**
	* Count blocks in the given category
	*
	* @param  string $type Name of the category
	* @return int
	*/
	public function count($data, $col, $name)
	{
		return count(array_keys(array_column($data, $col), $name));
	}

	/**
	* Get position options
	*
	* @param  int	 $max Highest number to use in a loop
	* @param  int	 $current_position Current position of a block
	* @return string $options
	*/
	public function get_options($max, $current_position)
	{
		$options = '';
		foreach ($max as $pos)
		{
			$s_selected = ($pos == $current_position) ? ' selected="selected"' : '';
			$options .= '<option value="' . $pos . '"' . $s_selected . '>' . $pos . '</option>';
		}

		return $options;
	}
}
