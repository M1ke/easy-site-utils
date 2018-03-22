<?php


function currency_neat($val){
	$round = round($val);
	if ($round==$val){
		return number_format($round, 0);
	}

	return number_format($val, 2);
}

function currency_type($country){
	$country = strtolower($country);
	switch ($country){
		case 'us':
		case 'usa':
		case 'america':
		case 'united states':
		case 'united states of america':
			$currency = '$';
		break;
		case 'uk':
		case 'united kingdom':
		case 'england':
		case 'britain':
		case 'great britain':
		default:
			$currency = '&#163;';
		break;
	}

	return $currency;
}

function custom_currency($val, $sep = ',', $decimal = '.'){
	$val = explode($decimal, $val);
	$op = $val[0];
	$op = custom_number($op, $sep);

	return $op.($val[1] ? $decimal.$val[1] : '');
}

function price_month_to_week($price){
	return (($price * 12) / 365) * 7;
}

function price_month_to_week_rounded($price){
	$price = price_month_to_week($price);

	return round($price, 2);
}

function price_week_to_month($price){
	return (($price / 7) * 365) / 12;
}

function price_week_to_month_rounded($price){
	$price = price_week_to_month($price);

	return round($price, 2);
}

/**
 *  Replaces all latin1 £'s in the text with their utf-8 versions
 */
function text_replace_pound($text){
	//Get the value of the string representing the 'bad' £'s
	$bad_pound = iconv("UTF-8", "ISO-8859-1", "£");

	// Change the 'good' £'s to match the bad £'s - this is because the good £'s are affected by the replacement of the bad ones. Changing to the 'bad' £ value means that they don't need to be changed back separately, and eliminates worries about collisions with whatever they are transformed into.
	$temp = str_replace('£', $bad_pound, $text);

	//Return the $string with the 'bad' pound signs replaced by good ones
	return str_replace($bad_pound, "£", $temp);
}

function text_replace_pound_html($text){
	$text = text_replace_pound($text);

	return str_replace("£", '&pound;', $text);
}
