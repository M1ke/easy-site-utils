<?php
function a_an($string,$cap=null){
	$first=substr(strtolower($string),0,1);
	switch ($first){
		case 'a':
		case 'e':
		case 'i':
		case 'o':
		case 'u':
			$string=($cap ? 'An' : 'an').' '.$string;
		break;
		default:
			$string=($cap ? 'A' : 'a').' '.$string;
	}
	return $string;
}

function address_road($address){
	$seps=array(',',"\r\n","\n");
	$n=0;
	do {
		$sep=strpos($address,$seps[$n]);
		$n++;
	}
	while (!$sep and $n<count($seps));
	if ($sep){
		$address=substr($address,0,$sep);
	}
	return $address;
}

function apos_ess($string){
	$last=substr($string,strlen($string)-1);
	if ($last=='s'){
		return $string.'&#39;';
	}
	return $string.'&#39;s';
}

function append_query($url,$query,$enc=true){
	$url.=strpos($url,'?')!==false ? ($enc ? '&amp;' : '&').$query : '?'.$query;
	return $url;
}

function arraytolower($arr){
	foreach ($arr as &$val){
		$val=is_array($val) ? arraytolower($val) : strtolower($val);
	}
	return $arr;
}

function build_query($arr){
	foreach ($arr as $key => $val){
		if (empty($val)){
			unset($arr[$key]);
		}
	}
	return http_build_query($arr);
}

function camel_case($str,$first=false){
    if ($first){
		$str[0]=strtoupper($str[0]);
	}
    $func=create_function('$c','return strtoupper($c[1]);');
    return preg_replace_callback('/_([a-z])/',$func,$str);
}

function comma_list($array,$conj='and'){
	$count=count($array);
	if ($count>1){
		$n=0;
		foreach ($array as $item){
			$return.=$item;
			$n++;
			if ($n==($count-1)){
				$return.=' '.$conj.' ';
			}
			elseif ($n<($count-1)){
				$return.=', ';
			}
		}
	}
	else {
		$return=reset($array);
	}
	return $return;
}

function currency_type($country){
	$country=strtolower($country);
	switch ($country){
		case 'us':
		case 'usa':
		case 'america':
		case 'united states':
		case 'united states of america':
			$currency='$';
		break;
		case 'uk':
		case 'united kingdom':
		case 'england':
		case 'britain':
		case 'great britain':
		default:
			$currency='&#163;';
		break;
	}
	return $currency;
}

function custom_currency($val,$sep=',',$decimal='.'){
	$val=explode($decimal,$val);
	$op=$val[0];
	$op=custom_number($op,$sep);
	return $op.($val[1] ? $decimal.$val[1] : '');
}

function custom_number($num,$sep=','){
	$i=0;
	$post_decimal=substr_after($num,'.',false,true);
	$num=substr_before($num,'.');
	$num=(string)$num;
	$strlen=strlen($num)-1;
	for ($n=$strlen;$n>-1;$n--){
		$string=($i>0 and $i%3==0) ? $num[$n].$sep.$string : $num[$n].$string;
		$i++;
	}
	if (!empty($post_decimal)){
		$string.='.'.$post_decimal;
	}
	return $string;
}

function data_template($string, $keys, $vals){
	foreach ($keys as $key){
		$find[] = '['.$key.']';
		$replace[] = $vals[$key];
	}
	return str_replace($find, $replace, $string);
}

function data_template_all($string, $vals){
	$keys = array_keys($vals);
	return data_template($string, $keys, $vals);
}

function file_name($file,$return=null){
	$file=string_check($file);
	$dot=strrpos($file,'.');
	$ext=strtolower(substr($file,($dot+1)));
	$name=substr($file,0,$dot);
	$name=str_replace(' ','-',$name);
	$file=array('ext'=>$ext,'name'=>$name);
	return !empty($return) ? $file[$return] : $file;
}

function file_name_uploaded($file,$return=null){
	$temp=$file['tmp_name'];
	$file=string_check($file['name']);
	$dot=strrpos($file,'.');
	$ext=strtolower(substr($file,($dot+1)));
	$name=substr($file,0,$dot);
	$name=str_replace(' ','-',$name);
	$file=array('ext'=>$ext,'name'=>$name,'temp'=>$temp);
	return !empty($return) ? $file[$return] : $file;
}

function first_paragraph($text){
	preg_match("/<p>(.*)<\/p>/",$text,$results);
	$text=$results[1];
	return $text;
}

function first_word($string){
	return substr_until($string,' ');
}

function forename_split($name){
	$name=strtolower($name);
	$names=explode(',',$name);
	$forename.=ucfirst($names[0]).' '.ucfirst($names[1]);
	return $forename;
}

function abbr_number($num){
	switch (true){
		case ($num>999999999):
			$num=substr($num,0,-9).'B';
		break;
		case ($num>999999):
			$num=substr($num,0,-6).'M';
		break;
		case ($num>999):
			$num=substr($num,0,-3).'k';
		break;
	}
	return $num;
}

function explode_multiple(&$arr,$keys,$sep=','){
	foreach ($keys as $key){
		$arr[$key]=explode($sep,$arr[$key]);
	}
	return true;
}

function html_words($html,$count){
	$html=strip_tags($html);
	$len=strlen($html);
	$html=substr_words($html,$count);
	if (strlen($html)<$len){
		$html.='&hellip;';
	}
	return $html;
}

function insert_subdomain($url,$sub){
	if (strpos($url,'http://')!==false){
		$url='http://'.$sub.'.'.substr($url,7);
	}
	elseif (strpos($url,'https://')!==false){
		$url='https://'.$sub.'.'.substr($url,8);
	}
	return $url;
}

function invert_name($name,$sep=',',$cap=false){
	$names=explode($sep,$name);
	if ($cap){
		foreach ($names as &$name){
			$name=ucfirst(strtolower($name));
		}
		unset($name);
	}
	$name=$names[1].' '.$names[0];
	return $name;
}

function in_string($needle,$haystack,$multiple_and=false){
	if (is_array($needle)){
		$result=$multiple_and;
		foreach ($needle as $sub_needle){
			$in_string=in_string($sub_needle,$haystack);
			$result=$multiple_and ? ($in_string && $result) : ($result || $in_string);
		}
	}
	else {
		$result=(stripos($haystack,$needle)!==false);
	}
	return $result;
}

function join_name($name){
	$space=strpos($name,' ');
	if ($space>0){
		$split['fore']=substr($name,0,$space);
		$split['sur']=substr($name,($space+1));
	}
	else {
		$split=$name;
	}
	return $split;
}

function make_currency(&$num,$zero=false){
	$num=preg_replace('/[^0-9.-]+/','',$num);
	if (strlen($num)>0){
		if (is_numeric($num) and ($zero or abs($num)>0)){
			return true;
		}
		return false;
	}
	elseif ($zero){
		$num=0;
		return true;
	}
	return false;
}

function make_email(&$string,$blank=false){
	// limit to the first candidate if someone tries to pass in a list

	// remove commas
	$string = explode(',', $string)[0];

	// remove spaces
	$string = trim($string);
	$string = explode(' ', $string)[0];

	if (strlen($string)>0){
		$string=strtolower(trim($string));
		$string=str_replace('\u0040','@',$string);
		$pattern="/\b['a-z0-9_%+-]+(?:\.['a-z0-9_%+-]+)*@[a-z0-9-]+\.(?:[a-z0-9-]+\.)*[a-z]{2,4}\b(?!\S)/";
		$result=preg_match($pattern,$string,$matches,PREG_OFFSET_CAPTURE);
		$matches=end($matches);
		if ($result>0 and end($matches)==0){
			return true;
		}
	}
	elseif ($blank){
		return true;
	}
	return false;
}

function sql_slashes($string){
	$string = stripslashes($string);
	$string = addslashes($string);
	return $string;
}

/**
 * @todo test this!
 */
function make_html($text, $tags = null){
	if (!empty($tags)){
		$text = strip_tags($text, $tags);
	}
	$text = str_replace(['// <![CDATA['."\r\n","\r\n".'// ]]>'], '', $text);

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
	$text = str_replace(["\r\n","\r"], "\n", $text);

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
	$text = preg_replace('|<p>\s*?</p>|',' ', $text);
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
 * @return bool
 */
function not_empty($string){
	$string = (string) $string;
	$string = trim($string);
	return (strlen($string)>0);
}

/**
 * @param string $string
 * @return bool
 */
function string_empty($string){
	return !not_empty($string);
}

function make_name($name){
    $name=ucwords(strtolower($name));
	foreach (array('-',"'") as $delimiter){
		if (strpos($name, $delimiter)!==false){
			$name=implode($delimiter,array_map('ucfirst',explode($delimiter, $name)));
		}
	}
	$name=string_check($name);
    return $name;
}

function make_permalink($string,$blank=null){
	if (strlen($string)<1){
		$string=$blank;
	}
	$string=strtolower($string);
	$string=str_replace(array('&#39;','&amp;'),'',$string);
	$string=preg_replace("([\W])",'-',$string);
	while (strpos($string,'--')!==false){
		$string=str_replace('--','-',$string);
	}
	return $string;
}

function make_phone(&$num,$blank=false,&$nat=null){
	$num=preg_replace('/[^0-9 -]+/','',$num);
	$copy=str_replace(array(' ','-'),'',$num);
	$nat='';
	if (strlen($num)>0){
		if (strlen($copy)>15 or strlen($copy)<8){
			return false;
		}
		$pattern="/(\+|00)?(44)?([\d]{11})/";
		$result=preg_match($pattern,$copy);
		if ($result>0){
			$nat='uk';
		}
		else {
			$pattern="/\(?\d{3}\W?\s?\d{3}\W?\d{4}/";
			$result=preg_match($pattern,$copy);
			if ($result>0){
				$nat='us';
			}
		}
		return true;
	}
	elseif ($blank){
		$num=null;
		return true;
	}
	return false;
}

function phone_country($num,$code,$zero=true){
	$num=str_replace(array('+','.'),'',$num);
	if (strpos($num,$code)===0){
		$num=substr($num,strlen($code));
		$num=($zero ? '0' : '').$num;
	}
	return $num;
}

function plus_minus($num,$minus='-',$plus='+'){
	return ($num<0 ? $minus : $plus).abs($num);
}

function strip_non_numeric($string){
	return preg_replace('/[^0-9]+/','',$string);
}

function make_phones(&$phone1,&$phone2){
	$one=make_phone($phone1);
	$two=make_phone($phone2);
	if ($one and $two) return true;
	elseif ($one and !$two){
		$two=null;
		return true;
	}
	elseif (!$one and $two){
		$one=null;
		return true;
	}
	return false;
}

function make_postcode(&$string,$blank=null){
	$string=trim($string);
	if (!empty($string)){
		// Permitted letters depend upon their position in the postcode.
		$alpha1="[abcdefghijklmnoprstuwyz]"; // Character 1
		$alpha2="[abcdefghklmnopqrstuvwxy]"; // Character 2
		$alpha3="[abcdefghjkpmnrstuvwxy]"; // Character 3
		$alpha4="[abehmnprvwxy]"; // Character 4
		$alpha5="[abdefghjlnpqrstuwxyz]"; // Character 5
		// Expression for postcodes: AN NAA, ANN NAA, AAN NAA, and AANN NAA with a space
		$pcexp[0]='/^('.$alpha1.'{1}'.$alpha2.'{0,1}[0-9]{1,2})([\s]{0,})([0-9]{1}'.$alpha5.'{2})$/';
		// Expression for postcodes: ANA NAA
		$pcexp[1]='/^('.$alpha1.'{1}[0-9]{1}'.$alpha3.'{1})([\s]{0,})([0-9]{1}'.$alpha5.'{2})$/';
		// Expression for postcodes: AANA NAA
		$pcexp[2]='/^('.$alpha1.'{1}'.$alpha2.'{1}[0-9]{1}'.$alpha4.')([\s]{0,})([0-9]{1}'.$alpha5.'{2})$/';
		// Exception for the special postcode GIR 0AA
		$pcexp[3]='/^(gir)(0aa)$/';
		// Standard BFPO numbers
		$pcexp[4]='/^(bfpo)([0-9]{1,4})$/';
		// c/o BFPO numbers
		$pcexp[5]='/^(bfpo)(c\/o[0-9]{1,3})$/';
		// Overseas Territories
		$pcexp[6]='/^([a-z]{4})(1zz)$/i';
		// Load up the string to check, converting into lowercase
		$string=strtolower($string);
		// Assume we are not going to find a valid postcode
		$valid=false;
		// Check the string against the six types of postcodes
		foreach ($pcexp as $regexp){
			if (preg_match($regexp,$string,$matches)){
				// Load new postcode back into the form element
				$string=strtoupper($matches[1].' '.$matches[3]);
				// Take account of the special BFPO c/o format
				$string=preg_replace ('/C\/O/','c/o ',$string);
				// Remember that we have found that the code is valid and break from loop
				$valid=true;
				break;
			}
		}
		return $valid;
	}
	elseif ($blank){
		$string=null;
		return true;
	}
	return false;
}

function make_position($num){
	switch ($num){
		case 1:
			$pos='1st';
		break;
		case 2:
			$pos='2nd';
		break;
		case 3:
			$pos='3rd';
		break;
		default:
			$pos=$num.'th';
	}
	return $pos;
}

function make_website(&$string,$blank=null){
	// for the idiots out there
	if (strtolower($string)=='no'){
		$string=null;
		return true;
	}
	if (strlen($string)>0){
		$string=string_check($string);
		$pattern="/((http)|(https)|(ftp)|(HTTP)|(HTTPS)|(FTP)):\/\//";
		if (preg_match($pattern,$string)<1){
			$string='http://'.$string;
		}
		return true;
	}
	elseif ($blank){
		return true;
	}
	return false;
}

function message_split($message,$length=160){
	if (strlen($message)<=$length){
		return array($message);
	}
	$length-=4;
	$split=array();
	while (strlen($message)>$length){
		$split[]=substr($message,0,$length);
		$message=substr($message,$length);
	}
	$split[]=substr($message,0,$length);
	$n=1;
	foreach ($split as &$segment){
		$segment.=' '.$n.'/'.count($split);
		$n++;
	}
	return $split;
}

function number_unique_string($num,$alpha_choice=0,$depth=0){
	/*
	0 - alpa both cases
	1 - alpha lower case
	2 - alpha upper case
	3 - alphanumeric
	4 - alpa both cases no vowels
	5 - alpha lower case no vowels
	6 - alpha upper case no vowels
	7 - alphanumeric no vowels or 1
	*/
	$alphas=array(
		'CGNpEhfelHrYXmUFByaRPtxnQKkwOASWVdjuqgJLivMZDTIscbzo',
		'wklptzbqcxjenhiuovygfdarms',
		'GWCZDJIRYKVUSBXQTFOHAEPNLM',
		'3DGrctJTwolgsh0PHn679yNFRMZBEzi5UqadOkjKxWeVfYmv1b4uS2IA8pLCQX',
		'VhXTpbLgNCHwKWnkzMRmfYDdrjJcsGyZtxSFqPQvB',
		'rhvpsqdjymfkgxwntbcz',
		'YHMPTRBVNQCXZFWJKDLGS',
		'p6dZkKB98GXj4LxgRNQCrtTmMJynD3WF5wb2cqvhfHz7SPsYV0',
	);
	if (isset($alphas[$alpha_choice])){
		$alpha=$alphas[$alpha_choice];
	}
	else {
		$alpha=$alpha_choice;
	}
	$n=floor($num/strlen($alpha));
	if ($n>0){
		$string.=number_unique_string($n,$alpha_choice,($depth+1));
	}
	$num+=$depth;
	$string.=$alpha[$num%strlen($alpha)];
	return $string;
}

function number_unique_string_length($num,$alpha_choice,$min_length=0){
	$string=number_unique_string($num,$alpha_choice);
	$padding=1;
	while (strlen($string)<$min_length){
		$string.=number_unique_string($padding,$alpha_choice);
		$padding++;
	}
	return $string;
}

function parse_headers($headers){
	$arr=array();
	foreach ($headers as $header){
		$pos=strpos($header,': ');
		if ($pos===false){
			$arr['http'][]=substr($header,strpos($header,' ')+1);
		}
		else {
			$key=substr($header,0,$pos);
			$val=substr($header,$pos+2);
			if (!@in_array($val,$arr[$key])){
				$arr[$key][]=$val;
			}
		}
	}
	return $arr;
}

function parse_url_imp($url){
	$r="^(?:(?P<scheme>\w+)://)?";
	$r.="(?:(?P<login>\w+):(?P<pass>\w+)@)?";
	$ip="(?:[0-9]{1,3}+\.){3}+[0-9]{1,3}";//ip check
	$s="(?P<subdomain>[-\w\.]+)\.)?";//subdomain
	$d="(?P<domain>[-\w]+\.)";//domain
	$e="(?P<extension>\w+)";//extension
	$r.="(?P<host>(?(?=".$ip.")(?P<ip>".$ip.")|(?:".$s.$d.$e."))";
	$r.="(?::(?P<port>\d+))?";
	$r.="(?P<path>[\w/]*/(?P<file>\w+(?:\.\w+)?)?)?";
	$r.="(?:\?(?P<arg>[\w=\-&]+))?";
	$r.="(?:#(?P<anchor>\w+))?";
	$r="!$r!";   // Delimiters
	preg_match($r,$url,$out);
	return $out;
}

function phone_international($phone,$code='44'){
	if (strpos($phone,'+'.$code)===false){
		if (substr($phone,0,2)==$code){
			$phone='+'.$phone;
		}
		else {
			if (substr($phone,0,1)=='0'){
				$phone=substr($phone,1);
			}
			$phone='+'.$code.$phone;
		}
	}
	return $phone;
}

function plural(){
	$args=func_get_args();
	foreach ($args as $arg){
		switch(true){
			case is_numeric($arg):
				$int=$arg;
			break;
			case is_array($arg):
				$extra=$arg;
			break;
			default:
				$string=$arg;
		}
	}
	if (empty($extra['sep'])){
		$extra['sep']=' ';
	}
	if ($int==1){
		$string=$int.$extra['sep'].$string;
	}
	else {
		if (!empty($extra['end'])){
			$string=str_replace($extra['end'][0],$extra['end'][1],$string);
		}
		else {
			$string.='s';
		}
		$string=$int.$extra['sep'].$string;
	}
	return $string;
}

function salt_string($string,$salt='abcdef123456789',$chars=null){
	$string=$salt.$string;
	$hash=sha1($string);
	if ($chars){
		$hash=substr($hash,0,$chars);
	}
	return $hash;
}

function select_array($array,$key=null,$chars=null,$string=' selected'){
	$n=0;
	foreach ($array as $item){
		if ($n==$key){
			$item=string_insert($item,$chars,$string);
		}
		$select.=$item;
		$n++;
	}
	return $select;
}

function select_options($array,$options=array('string'=>' selected')){
	$n=0;
	foreach ($array as $item){
		$values=explode('~',$item);
		$select.='<option';
		if ($values[1]) $select.=' value="'.$values[1].'"';
		if ($options['key']==$n) $select.=$options['string'];
		if ($options['extra']) $select.=str_replace('%val%',$values[1],$options['extra']);
		$select.='>'.$values[0].'</option>';
		$n++;
	}
	return $select;
}

function shorten($string,$length=20,$nospan=null){
	if (strlen($string)>$length+1){
		$short=substr($string,0,$length).'&hellip;';
		if (!$nospan) $short='<span title="'.$string.'">'.$short.'</span>';
	}
	else $short=$string;
	return $short;
}

function split_name($string,$full=true){
	$parts=explode(' ',$string);
	$count=count($parts);
	if (($count<2) and ($full)) error('This is not a full name - at least one forename and a surname must be provided.');
	for ($n=0;$n<($count-1);$n++){
		if ($n==0) $name['fore']=$parts[$n];
		else $name['fore'].=' '.$parts[$n];
	}
	$name['sur']=$parts[$count-1];
	return $name;
}

function str_replace_once($search,$replace,$string){
	$pos=strpos($string,$search);
	if ($pos!==false){
		$string=substr_replace($string,$replace,$pos,strlen($search));
	}
	return $string;
}

function string_check($string,$strip=1,$trim=true,$multi_byte=false){
	if ($trim){
		$string=trim($string);
	}
	$string=stripslashes($string);
	switch ($strip){
		case 1:
			$string=strip_tags($string);
		break;
		case 3:
			$string=htmlspecialchars($string);
		break;
		default:
			if (!empty($strip)){
				$string=strip_tags($string,$strip); // used to pass in $l['tags']
			}
	}
	if (!empty($strip)){
		$string=preg_replace('/&(?![A-Za-z0-9#]+;)/s','&amp;',$string);
	}
	if ($multi_byte){
		$string=mb_replace(array("'","�","�",'`'),array('&#39;'),$string);
	}
	else {
		$string=str_replace(array("'","�","�",'`'),array('&#39;'),$string);
	}
	return $string;
}

function mb_replace($search, $replace, $subject, &$count=0){
	mb_regex_encoding('utf-8');
    if (!is_array($search) && is_array($replace)){
        return false;
    }
    if (is_array($subject)){
        // call mb_replace for each single string in $subject
        foreach ($subject as &$string){
            $string = &mb_replace($search, $replace, $string, $c);
            $count += $c;
        }
    } elseif (is_array($search)){
        if (!is_array($replace)){
            foreach ($search as &$string){
                $subject = mb_replace($string, $replace, $subject, $c);
                $count += $c;
            }
        } else {
            $n = max(count($search), count($replace));
            while ($n--){
                $subject = mb_replace(current($search), current($replace), $subject, $c);
                $count += $c;
                next($search);
                next($replace);
            }
        }
    } else {
        $parts = mb_split(preg_quote($search), $subject);
        $count = count($parts)-1;
	    $subject = implode($replace, $parts);
    }
    return $subject;
}

function string_check_de($string,$strip=false){
	$string=str_replace('&amp;','&',$string);
	$string=str_replace('&#39;',"'",$string);
	return $string;
}

function string_compare($str1,$str2){
	return strcasecmp($str1,$str2)===0;
}

function string_data($data,$string){
	foreach ($data as $key => $val){
		if (!is_numeric($key)){
			$find[]='['.$key.']';
			$replace[]=$val;
		}
	}
	if (!empty($replace)){
		$string=str_replace($find,$replace,$string);
	}
	return $string;
}

function string_insert($string,$chars,$ins){
	$start=substr($string,0,$chars);
	$end=substr($string,$chars);
	return $start.$ins.$end;
}

function string_split($string,$split=','){
	$string=str_replace([$split.' ',' '.$split],$split,$string);
	return explode($split,$string);
}

function string_uncheck($string){
	$string=stripslashes($string);
	$string=str_replace(array('&amp;','&#39;','&#39;','&#39;','"','&#8220;','&#8221;','&#233;','&#163;','...','-'),array('&',"'","�","�",'`','�','�','�','�','�','�'),$string);
	// some characters produce this after replacement for some reason - this gets rid of that
	$string=str_replace('é','�',$string);
	return $string;
}

function strip_decimal($num){
	$dot=strpos($num,'.');
	if ($dot>0) $num=substr($num,0,$dot);
	return $num;
}

function strip_end($string,$chars){
	$string=substr($string,0,strlen($string)-$chars);
	return $string;
}

function strip_query($string){
	return substr_until($string,'?');
}

function string_replace_once($needle,$replace,$haystack){
	$pos=strpos($haystack,$needle);
	if ($pos!==false) {
	    $newstring=substr_replace($haystack,$replace,$pos,strlen($needle));
	}
	return $newstring;
}

function string_replace_last($find,$replace,$string){
    $pos=strrpos($string,$find);
    if ($pos!==false){
        $string=substr_replace($string,$replace,$pos,strlen($find));
    }
    return $string;
}

function substr_between($string,$start='=',$end=false,$inc=false){
	$loc_start=strpos($string,$start);
	if (!$inc){
		$loc_start+=strlen($start);
	}
	$loc_end=!empty($end) ? strpos($string,$end) : false;
	if ($loc_end===false){
		return substr($string,$loc_start);
	}
	return substr($string,$loc_start,($loc_end-$loc_start));
}

function substr_after($string,$after,$inc=false,$blank=false){
	$query=strrpos($string,$after);
	if ($query!==false){
		$string=substr($string,$query+($inc ? 0 : 1));
	}
	elseif ($blank){
		$string='';
	}
	return $string;
}

function substr_before($string,$before,$inc=false){
	$query=strrpos($string,$before);
	if ($query!==false){
		$string=substr($string,0,$query+($inc ? 1 : 0));
	}
	return $string;
}

function substr_words($string,$words,$sep=' '){
	$len=strlen($string);
	if ($string[$len-1]==$sep){
		$len--;
	}
	for ($n=0;$n<$len;$n++){
		if ($string[$n]==$sep){
			$count++;
		}
		if ($count==$words){
			break;
		}
	}
	return substr($string,0,$n);
}

function substr_from($string,$from,$inc=false){
	$query=strpos($string,$from);
	if ($query!==false){
		$string=substr($string,$query+($inc ? 0 : 1));
	}
	return $string;
}

function substr_until($string,$until,$inc=false){
	if (is_array($until)){
		foreach ($until as $until_sub){
			$string=substr_until($string,$until_sub,$inc);
		}
	}
	else {
		$query=strpos($string,$until);
		if ($query!==false){
			$string=substr($string,0,$query+($inc ? 1 : 0));
		}
	}
	return $string;
}
// legacy
function strip_string_from($string,$from){
	return substr_until($string,$from);
}

function switch_string($string,$sep=' '){
	$string=explode($sep,$string);
	$string=$string[1].$sep.$string[0];
	return $string;
}

function unfilter($post){
	$post=str_replace('&#39;',"'",$post);
	$post=str_replace('&#233;','�',$post);
	$post=str_replace('&amp;','&',$post);
	$post=str_replace('<ol>','<ol><br/>',$post);
	$post=str_replace('<ul>','<ul><br/>',$post);
	$post=str_replace('</li>','</li><br/>',$post);
	$post=str_replace('</ol>'."\n".'<p>','</ol><br/>',$post);
	$post=str_replace('</ul>'."\n".'<p>','</ul><br/>',$post);
	$post=str_replace('</p>'."\n".'<ul>','<br/>'."\n".'<ul>',$post);
	$post=str_replace('</p>'."\n".'<embed>','<br/>'."\n".'<embed>',$post);
	$post=str_replace('</embed>'."\n".'<p>','</embed><br/>',$post);
	$post=str_replace('</p>'."\n".'<object>','<br/>'."\n".'<object>',$post);
	$post=str_replace('</object>'."\n".'<p>','</object><br/>',$post);
	$post=str_replace('</p>'."\n".'<blockquote>','<br/>'."\n".'<blockquote>',$post);
	$post=str_replace('</blockquote>'."\n".'<p>','</blockquote><br/>',$post);
	$post=str_replace('</h4>'."\n".'<ul>','</h4><br/>'."\n".'<ul>',$post);
	$post=str_replace('</ul>'."\n".'<h4>','</ul><br/>'."\n".'<h4>',$post);
	$post=str_replace('</p>'."\n".'<h4>','<br/>'."\n".'<h4>',$post);
	$post=str_replace('</h4>'."\n".'<p>','</h4><br/>',$post);
	$post=str_replace('</p>'."\n".'<p>','<br/>'."\n".'<br/>',$post);
	$post=str_replace('<p>','',$post);
	$post=str_replace('</p>','',$post);
	$post=str_replace('<br/>',"\r",$post);
	return $post;
}

function url_domain($string){
	$string=substr($string,strpos($string,'://')+3);
	$string=substr($string,0,strpos($string,'/'));
	return $string;
}

function url_fragment($url,$fragment){
	$url=substr_until($url,'#');
	$url.='#'.$fragment;
	return $url;
}

function word_to_number(&$number){
	switch (strtolower($number)){
		case 'one':
			$number=1;
		break;
		case 'two':
			$number=2;
		break;
		case 'three':
			$number=3;
		break;
		case 'four':
			$number=4;
		break;
		case 'five':
			$number=5;
		break;
		case 'six':
			$number=6;
		break;
		case 'seven':
			$number=7;
		break;
		case 'eight':
			$number=8;
		break;
		case 'nine':
			$number=9;
		break;
		case 'ten':
			$number=10;
		break;
		default:
			return false;
	}
	return true;
}

function word_wrap($string,$word,$el,$class=null){
	$string=explode(' ',$string);
	$string[$word-1]='<'.$el.($class?' class="'.$class.'"':'').'>'.$string[$word-1].'</'.$el.'>';
	$string=implode(' ',$string);
	return $string;
}

function worldpay_address($string){
	if (strpos($string,"\n")!==false){
		$address_separator="\r\n";
	}
	if (strpos($string,"\r\n")!==false){
		$address_separator="\n";
	}
	if (strpos($string,",")!==false){
		$address_separator=",";
	}
	if (!empty($address_separator)){
		$string=explode($address_separator,$string);
		$i=0;
		for ($n=count($string)-1;$n>-1;$n--){
			$address.='&'.($i==0 ? 'postcode' : ($i==1 ? 'town' : 'address'.($n+1))).'='.urlencode($string[$n]);
			$i++;
		}
	}
	return $address;
}

function zero_pad($num){
	$num='0'.$num;
	return substr($num,strlen($num)-2);
}

function starts_with($haystack, $needle){
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
}

function ends_with($haystack, $needle){
        $length = strlen($needle);
        if ($length == 0) {
                return true;
        }
        return (substr($haystack, -$length) === $needle);
}
