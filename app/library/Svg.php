<?php

namespace Library;

class Svg
{

	/**
	 * @param int $score
	 * @return string
	 */
	public function generate($score, $type = 'default')
	{
		$score = min($score, 9999);
		$scoreColor = '#2c3e50';
		$siteName = 'Phalconist';
		$siteColor = '#18bc9c';
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="123" height="20">'.
			'<linearGradient id="b" x2="0" y2="100%">'.
				'<stop offset="0" stop-color="#bbb" stop-opacity=".1"/><stop offset="1" stop-opacity=".1"/>'.
			'</linearGradient>'.
			'<mask id="a"><rect width="123" height="20" rx="3" fill="#fff"/></mask>'.
			'<g mask="url(#a)">'.
				'<path fill="' . $siteColor . '" d="M0 0h70v20H0z"/><path fill="' . $scoreColor . '" d="M70 0h53v20H70z"/>'.
				'<path fill="url(#b)" d="M0 0h123v20H0z"/>'.
			'</g>'.
			'<g fill="#fff" text-anchor="middle" font-family="DejaVu Sans,Verdana,Geneva,sans-serif" font-size="11">'.
				'<text x="36" y="15" fill="#010101" fill-opacity=".3">' . $siteName . '</text>'.
				'<text x="36" y="14">' . $siteName . '</text>'.
				'<rect x="76" y="11" width="2" height="3" style="fill:white;stroke:white;stroke-width:1;"/>'.
				'<rect x="80" y="5" width="2" height="9" style="fill:white;stroke:white;stroke-width:1;"/>'.
				'<rect x="84" y="8" width="2" height="6" style="fill:white;stroke:white;stroke-width:1;"/>'.
				'<text x="106" y="15" fill="#010101" fill-opacity=".3">' . $score . '</text><text x="106" y="14">' . $score . '</text>'.
			'</g></svg>';

		return $svg;
	}
}
