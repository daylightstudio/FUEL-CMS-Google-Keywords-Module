<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FUEL CMS
 * http://www.getfuelcms.com
 *
 * An open source Content Management System based on the 
 * Codeigniter framework (http://codeigniter.com)
 *
 * @package		FUEL CMS
 * @author		David McReynolds @ Daylight Studio
 * @copyright	Copyright (c) 2013, Run for Daylight LLC.
 * @license		http://docs.getfuelcms.com/general/license
 * @link		http://www.getfuelcms.com
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Page analysis 
 *
 * @package		FUEL CMS
 * @subpackage	Libraries
 * @category	Libraries
 * @author		David McReynolds @ Daylight Studio
 * @link		http://docs.getfuelcms.com/modules/page_analysis
 */

// --------------------------------------------------------------------

class Fuel_google_keywords extends Fuel_advanced_module {
	
	public $domain = '';
	public $keywords = '';
	public $num_results = 100;
	public $additional_params = array();
	
	/**
	 * Constructor - Sets Fuel_google_keywords preferences
	 *
	 * The constructor can be passed an array of config values
	 */
	function __construct($params = array())
	{
		parent::__construct();

		if (!extension_loaded('curl')) 
		{
			$this->_add_error(lang('error_no_curl_lib'));
		}
		
		if (empty($params))
		{
			$params['name'] = 'google_keywords';
		}
		$this->initialize($params);
	
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Initialize the object
	 *
	 * Accepts an associative array as input, containing preferences.
	 * Also will set the values in the config as properties of this object
	 *
	 * @access	public
	 * @param	array	config preferences
	 * @return	void
	 */	
	function initialize($params = array())
	{
		parent::initialize($params);
		$this->set_params($this->_config);
	}
	
	// 
	// --------------------------------------------------------------------
	
	/**
	 * Returns an array with the keywords being the key and the value being a comma separated value of the rankings
	 *
	 * Accepts an associative array as input, containing preferences.
	 * Also will set the values in the config as properties of this object
	 *
	 * @access	public
	 * @param	array	sets the class properties of $domain, $keywords, $num_results, $additional_params (optional)
	 * @return	void
	 */	
	function results($params  = array())
	{
		$this->CI->load->module_helper(FUEL_FOLDER, 'scraper');
		$this->set_params($params);
		
		// normalize keywords into an array
		if (is_string($this->keywords))
		{
			$this->keywords = explode(',', $this->keywords);
		}
		
		// normalize domain
		if (empty($this->domain))
		{
			$this->domain = $_SERVER['SERVER_NAME'];
		}
		$this->domain = str_replace(array('http://', 'www'), '', $this->domain);
		
		// start CURL and loop through the keywords to test against the domain
		$ch = curl_init();
		$found = array();
		foreach($this->keywords as $keyword)
		{
			$keyword = trim($keyword);
			
			$url = 'https://www.google.com/search?q='.rawurlencode($keyword).'&num='.$this->num_results.'&'.http_build_query($this->additional_params);

			// scrape html from page running on localhost
			$google_page = scrape_html($url);

			// OLD
			/*preg_match_all('|<h3 class=(["\'])?r\\1?><a.+href="(.+)".+</h3>|Umis', $google_page, $matches);*/
			preg_match_all('|<cite.+>(.+)</cite>|Umis', $google_page, $matches);
			// echo "<pre style=\"text-align: left;\">";
			// print_r($matches);
			// echo "</pre>";
			// exit();
			
			$results = array();
			if (!empty($matches[1]))
			{
				$results = $matches[1];
			}

			$num = 1;
			foreach($results as $uri)
			{
				if (strpos($uri, $this->domain) !== FALSE)
				{
					if (!isset($found[$keyword]))
					{
						$found[$keyword] = array();
					}
					$found[$keyword][] = $num;
				}
				$num++;
			}
		}
		curl_close($ch);
		return $found;
	}

}

/* End of file Fuel_page_analysis.php */
/* Location: ./modules/fuel/libraries/Fuel_page_analysis.php */