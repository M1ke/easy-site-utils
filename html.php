<?php

function html_encode_lone_ampersands($text){
	// regex for a '&' not followed by a possible '#' then some letters/digits then a ';'
	// i.e. all & that do not seem to be in an html escaped character sequence
	return preg_replace("/&(?!#?[a-zA-Z0-9]*;)/", "&amp;", $text);
}

function html_words($html, $count){
	$html = strip_tags($html);
	$len = strlen($html);
	$html = substr_words($html, $count);
	if (strlen($html)<$len){
		$html .= '&hellip;';
	}

	return $html;
}

/**
 * Apply htmlentities to all non-empty flat entries, recursing on arrays
 *
 * @param $array
 *
 * @return array
 */
function htmlentities_fields(array $array){
	foreach ($array as $key => $field){
		if (empty($field)){
			continue;
		}

		if (is_array($field)){
			$array[$key] = htmlentities_fields($field);
		}
		else {
			$array[$key] = htmlentities($field);
		}
	}

	return $array;
}

/**
 * @todo test this!
 */
function make_html($text, $tags = null){
	if (!empty($tags)){
		$text = strip_tags($text, $tags);
	}
	$text = str_replace(['// <![CDATA['."\r\n", "\r\n".'// ]]>'], '', $text);

	// HTML block tags that can be used
	$htmltags = 'div|p|h[1-6]|blockquote';
	// lists
	$htmltags .= '|ul|ol|li|dl|dd|dt';
	// interactive stuff
	$htmltags .= '|embed|object|select|form';
	//tables
	$htmltags .= '|table|thead|tfoot|tbody|tr|td|th';

	// random ones that exist so someone will use them
	$htmltags .= '|address|math|caption|pre|code';

	// odd line, what does it do?
	$text = str_replace('', '', $text);
	$text = str_replace('<br />', '<br/>', $text);

	// make sure it uses a single line break character
	$text = str_replace(["\r\n", "\r"], "\n", $text);

	// add line breaks before and after
	$text = preg_replace('!(<(?:'.$htmltags.')[^>]*>)!', "\n$1", $text);
	$text = preg_replace('!(</(?:'.$htmltags.')>)!', "$1\n\n", $text);

	// tried adding this line to remove breaks after open tags but wouldn't work - regex works in regexr
	//$text=preg_replace('!(<(?:'.$htmltags.')>)/\n<br/>!',"$1",$text);

	// remove duplicate line breaks
	$text = preg_replace("/\n\n+/", "\n\n", $text);

	// make everything into paragraphs
	$text = preg_replace('|<p>(<br/>)*</p>|', '', $text);
	$text = preg_replace('|<p>(&nbsp;)*</p>|', '', $text);
	$text = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $text);

	// delete any empty paragraphs
	$text = preg_replace('|<p>\s*?</p>|', ' ', $text);
	$text = str_replace("\n<br/></p>", "", $text);

	// remove paragraphs if its round one of the tags above
	$text = preg_replace('!<p>\s*(</?(?:'.$htmltags.')[^>]*>)\s*</p>!', "$1", $text);

	// lists inside lists can screw stuff up
	$text = preg_replace("|<p>(<li.+?)</p>|", "$1", $text);
	$text = preg_replace(" /<p>([A-Za-z0-9 '-,.;]*)<\/li>/xsm", "<p>$1</p></li>", $text);

	// if the quote has attributes then keep them but remove the paragraph from around it. then stick them inside
	$text = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $text);
	$text = str_replace('</blockquote></p>', '</p></blockquote>', $text);

	// if the line breaks are odd it can start a paragraph
	//  before a close tag, or close one afterwards, which isn't good
	$text = preg_replace('!<p>\s*(</?(?:'.$htmltags.')[^>]*>)!', "$1", $text);
	$text = preg_replace('!(</?(?:'.$htmltags.')[^>]*>)\s*</p>!', "$1", $text);

	// turn any remaining single breaks into <br/> tags, then remove them
	//  from after block elements (or before the close tag)
	$text = preg_replace('|(?<!<br/>)\s*\n|', "<br/>\n", $text);
	$text = preg_replace('!(</?(?:'.$htmltags.')[^>]*>)\s*<br/>!', "$1", $text);
	$text = preg_replace('!<br/>(\s*</?(?:'.$htmltags.')>)!', '$1', $text);
	$text = preg_replace('|<div>|', "", $text);
	$text = preg_replace('|</div>|', "", $text);
	$text = str_replace('&nbsp;', ' ', $text);

	return $text;
}

/**
 * @param string $string
 *
 * @return bool
 */
function stringIsHtml($string){
	if (strlen($string)==0){
		return false;
	}

	return $string[0]=='<';
}
