<?php
namespace System;

class URL {
	/**
	 * Assembles URL address.
	 * Format: <controller>/<id>,<seo>,<action>.html
	 * @param array $args URL address arguments
	 * - controller - controller name,
	 * - action - action name,
	 * - id - item identifier,
	 * - args - array of additional SEO arguments,
	 * - ...additional query string arguments.
	 * @param string $base URL prefix
	 * @return string Assembled URL address
	 */
	public static function url($args=array(), $base='./')
	{
		$url = '';
		if ($args)
		{
			if (!empty($args['controller']) && strtolower($args['controller']) != 'index') $url .= implode('/', array_map('rawurlencode', explode('_', strtolower(preg_replace('/([\da-z])([A-Z])/e', '"$1-" . strtolower("$2")', $args['controller']))))) . '/';
			if (!empty($args['id']))
			{
				$url .= rawurlencode($args['id']) . ',';
				if (!empty($args['args'])) $url .= implode(',', array_map('seo', $args['args'])) . ',';
			}
			if (!empty($args['id']) || !empty($args['action']) && strtolower($args['action']) != 'index') $url .= (empty($args['action']) ? 'index' : rawurlencode(strtolower(preg_replace('/([\da-z])([A-Z])/e', '"$1-" . strtolower("$2")', $args['action'])))) . '.html';
			unset($args['controller'], $args['action'], $args['id'], $args['args']);
			if ($args)
			{
				$url .= '?';
				$first = true;
				foreach ($args as $key => $value)
				{
					if ($first) $first = false;
					else $url .= '&';
					$url .= urlencode($key) . '=' . urlencode($value);
				}
			}
		}
		return $url == '' ? $base : $base . $url;
	}

	/**
	 * Makes SEO frendly title.
	 * @param string $text Original title
	 * @return string SEO friendly title
	 */
	public static function seo($text){
		$text = str_replace(
			array(
				"\xA1", "\xC6", "\xCA", "\xA3", "\xD1", "\xD3", "\xA6", "\xAC", "\xAF",
				"\xB1", "\xE6", "\xEA", "\xB3", "\xF1", "\xF3", "\xB6", "\xBC", "\xBF",
				"\xC4\x84", "\xC4\x86", "\xC4\x98", "\xC5\x81", "\xC5\x83", "\xC3\x93", "\xC5\x9A", "\xC5\xB9", "\xC5\xBB",
				"\xC4\x85", "\xC4\x87", "\xC4\x99", "\xC5\x82", "\xC5\x84", "\xC3\xB3", "\xC5\x9B", "\xC5\xBA", "\xC5\xBC"
			),
			array(
				'a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z',
				'a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z',
				'a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z',
				'a', 'c', 'e', 'l', 'n', 'o', 's', 'z', 'z'
			),
			strtolower($text)
		);
		$text = trim(preg_replace('/[^\da-z_]+/', '-', $text), '-');
		return $text;
	}
}