<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
Copyright (C) 2003 - 2015 EllisLab, Inc.

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
ELLISLAB, INC. BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Except as contained in this notice, the name of EllisLab, Inc. shall not be
used in advertising or otherwise to promote the sale, use or other dealings
in this Software without prior written authorization from EllisLab, Inc.
*/

/**
 * Valid URL
 *
 * This plugin generates a usable URL for anchor tags, or anything else you desire.
 *
 * @package			ExpressionEngine
 * @category		Plugins
 * @author			EllisLab
 * @copyright		Copyright (c) 2004 - 2015, EllisLab, Inc.
 * @link			https://github.com/EllisLab/Valid-Url
 */
$plugin_info = array(
						'pi_name'			=> 'Valid URL',
						'pi_version'		=> '2.2',
						'pi_author'			=> 'EllisLab - edited by Onstuimig ~ Jeroen Rothbauer',
						'pi_author_url'		=> 'https://ellislab.com/',
						'pi_description'	=> 'Generates a URL valid for use in XHTML as an href or src attribute',
						'pi_usage'			=> Valid_url::usage()
					);
					
class Valid_url {

	public $return_data = '';

	/**
	 * Valid URL
	 *
	 * @access	public
	 * @return	void	// sets $return_data
	 */
	function __construct($str = '')
	{
		// characters we don't want urlencoded
		$protected = array('&' => 'AMPERSANDMARKER', '/' => 'SLASHMARKER', '=' => 'EQUALSMARKER', '#' => 'HASHMARKER');

		if ($str == '')
		{
			$str = ee()->TMPL->tagdata;
		}

		// decode first since we'll do our own encoding later
		$str = str_replace(SLASH, '/', trim(urldecode(str_replace('&amp;', '&', $str))));

		// error trapping for seriously malformed URLs, take 1
		if (($url = @parse_url($str)) === FALSE)
		{
			ee()->TMPL->log_item('Valid URL Plugin error: unable to parse URL '.htmlentities($str));
			return;
		}

		// error trapping for seriously malformed URLs, take 2
		if( $str[0]=='/' || $str[0]=='#' ){
			if (($url = @parse_url($str)) === FALSE)
			{
				ee()->TMPL->log_item('Valid URL Plugin error: unable to parse URL '.htmlentities($str));
				return;
			}
		}else{
			if ( ! isset($url['scheme']) && ($url = @parse_url("http://{$str}")) === FALSE)
			{
				ee()->TMPL->log_item('Valid URL Plugin error: unable to parse URL '.htmlentities($str));
				return;
			}
		}

		foreach ($url as $key => $value)
		{
			switch($key)
			{
				case 'path':
					$url[$key] = urlencode(str_replace(array_keys($protected), $protected, $value));
					break;
				case 'query':
					$url[$key] = '?'.urlencode(str_replace(array_keys($protected), $protected, $value));
					break;
				case 'scheme':
					$url[$key] .= ($value == 'file') ? ':///' : (($value == 'mailto') ? ':' : '://');
					break;
				case 'fragment': 
					$url[$key] = '#'.urlencode(str_replace(array_keys($protected), $protected, $value));
					break;
			}
		}

		$this->return_data = implode('', str_replace('&', '&amp;', str_replace($protected, array_keys($protected), $url)));
	}
	// --------------------------------------------------------------------
	
	/**
	 * Usage
	 *
	 * @access	public
	 * @return	string
	 */
	static function usage()
	{
		return <<<ONESY
		Makes sure that a URL has a protocol, that ampersands are converted to entities, and all
		other characters are properly URL encoded.
		
		{exp:valid_url}www.example.com/foo bar/bat?=bag&mice=men!{/exp:valid_url}
		
		Produces:
		http://www.example.com/foo+bar/bat?=bag&amp;mice=men%21
		
		Version 2.2 - Onstuimig
		******************
		- Support URLs starting with hash
		
		Version 2.1 - Onstuimig
		******************
		- Added mailto, relative URL and hash fragment support
		
		Version 2.0
		******************
		- Updated plugin to be 3.0 compatible
		
		Version 1.1
		******************
		- Updated plugin to be 2.0 compatible
		
ONESY;
	}
}
