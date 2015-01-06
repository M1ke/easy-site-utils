<?php
function arr_genders(){
	return array('m'=>'Male','f'=>'Female');
}

function arr_letters(){
	return array(
		1=>'a',
		2=>'b',
		3=>'c',
		4=>'d',
		5=>'e',
		6=>'f',
		7=>'g',
		8=>'h',
		9=>'i',
		10=>'j',
		11=>'k',
		12=>'l',
		13=>'m',
		14=>'n',
		15=>'o',
		16=>'p',
		17=>'q',
		18=>'r',
		19=>'s',
		20=>'t',
		21=>'u',
		22=>'v',
		23=>'w',
		24=>'x',
		25=>'y',
		26=>'z',
	);
}

function country_abbr($lower=false){
	return $lower ? array('gb'=>'united kingdom','usa'=>'united states','fr'=>'france') : array('gb'=>'United Kingdom','usa'=>'United States','fr'=>'France');
}

function country_list($lower=false){
	if ($lower){
		$arr=array('united kingdom','united states','afghanistan','albania','algeria','andorra','angola','antigua and barbuda','argentina','armenia','australia','austria','azerbaijan','bahamas','bahrain','bangladesh','barbados','belarus','belgium','belize','benin','bhutan','bolivia','bosnia and herzegovina','botswana','brazil','brunei','bulgaria','burkina faso','burundi','cambodia','cameroon','canada','cape verde','central african republic','chad','chile','china','colombi','comoros','congo (brazzaville)','congo','costa rica','cote d&#39;ivoire','croatia','cuba','cyprus','czech republic','denmark','djibouti','dominica','dominican republic','east timor (timor timur)','ecuador','egypt','el salvador','equatorial guinea','eritrea','estonia','ethiopia','fiji','finland','france','gabon','gambia, the','georgia','germany','ghana','greece','grenada','guatemala','guinea','guinea-bissau','guyana','haiti','honduras','hungary','iceland','india','indonesia','iran','iraq','ireland','israel','italy','jamaica','japan','jordan','kazakhstan','kenya','kiribati','korea, north','korea, south','kuwait','kyrgyzstan','laos','latvia','lebanon','lesotho','liberia','libya','liechtenstein','lithuania','luxembourg','macedonia','madagascar','malawi','malaysia','maldives','mali','malta','marshall islands','mauritania','mauritius','mexico','micronesia','moldova','monaco','mongolia','morocco','mozambique','myanmar','namibia','nauru','nepa','netherlands','new zealand','nicaragua','niger','nigeria','norway','oman','pakistan','palau','panama','papua new guinea','paraguay','peru','philippines','poland','portugal','qatar','romania','russia','rwanda','saint kitts and nevis','saint lucia','saint vincent','samoa','san marino','sao tome and principe','saudi arabia','senegal','serbia and montenegro','seychelles','sierra leone','singapore','slovakia','slovenia','solomon islands','somalia','south africa','spain','sri lanka','sudan','suriname','swaziland','sweden','switzerland','syria','taiwan','tajikistan','tanzania','thailand','togo','tonga','trinidad and tobago','tunisia','turkey','turkmenistan','tuvalu','uganda','ukraine','united arab emirates','uruguay','uzbekistan','vanuatu','vatican city','venezuela','vietnam','yemen','zambia','zimbabwe');
	}
	else {
		$arr=array('United Kingdom','United States','Afghanistan','Albania','Algeria','Andorra','Angola','Antigua and Barbuda','Argentina','Armenia','Australia','Austria','Azerbaijan','Bahamas','Bahrain','Bangladesh','Barbados','Belarus','Belgium','Belize','Benin','Bhutan','Bolivia','Bosnia and Herzegovina','Botswana','Brazil','Brunei','Bulgaria','Burkina Faso','Burundi','Cambodia','Cameroon','Canada','Cape Verde','Central African Republic','Chad','Chile','China','Colombi','Comoros','Congo (Brazzaville)','Congo','Costa Rica','Cote d&#39;Ivoire','Croatia','Cuba','Cyprus','Czech Republic','Denmark','Djibouti','Dominica','Dominican Republic','East Timor (Timor Timur)','Ecuador','Egypt','El Salvador','Equatorial Guinea','Eritrea','Estonia','Ethiopia','Fiji','Finland','France','Gabon','Gambia, The','Georgia','Germany','Ghana','Greece','Grenada','Guatemala','Guinea','Guinea-Bissau','Guyana','Haiti','Honduras','Hungary','Iceland','India','Indonesia','Iran','Iraq','Ireland','Israel','Italy','Jamaica','Japan','Jordan','Kazakhstan','Kenya','Kiribati','Korea, North','Korea, South','Kuwait','Kyrgyzstan','Laos','Latvia','Lebanon','Lesotho','Liberia','Libya','Liechtenstein','Lithuania','Luxembourg','Macedonia','Madagascar','Malawi','Malaysia','Maldives','Mali','Malta','Marshall Islands','Mauritania','Mauritius','Mexico','Micronesia','Moldova','Monaco','Mongolia','Morocco','Mozambique','Myanmar','Namibia','Nauru','Nepa','Netherlands','New Zealand','Nicaragua','Niger','Nigeria','Norway','Oman','Pakistan','Palau','Panama','Papua New Guinea','Paraguay','Peru','Philippines','Poland','Portugal','Qatar','Romania','Russia','Rwanda','Saint Kitts and Nevis','Saint Lucia','Saint Vincent','Samoa','San Marino','Sao Tome and Principe','Saudi Arabia','Senegal','Serbia and Montenegro','Seychelles','Sierra Leone','Singapore','Slovakia','Slovenia','Solomon Islands','Somalia','South Africa','Spain','Sri Lanka','Sudan','Suriname','Swaziland','Sweden','Switzerland','Syria','Taiwan','Tajikistan','Tanzania','Thailand','Togo','Tonga','Trinidad and Tobago','Tunisia','Turkey','Turkmenistan','Tuvalu','Uganda','Ukraine','United Arab Emirates','Uruguay','Uzbekistan','Vanuatu','Vatican City','Venezuela','Vietnam','Yemen','Zambia','Zimbabwe');
	}
	return $arr;
}

function country_continents(){
	$arr=array('algeria'=>'africa','angola'=>'africa','benin'=>'africa','botswana'=>'africa','burkina'=>'africa','burundi'=>'africa','cameroon'=>'africa','cape verde'=>'africa','central african republic'=>'africa','chad'=>'africa','comoros'=>'africa','congo'=>'africa','democratic republic of congo'=>'africa','djibouti'=>'africa','egypt'=>'africa','equatorial guinea'=>'africa','eritrea'=>'africa','ethiopia'=>'africa','gabon'=>'africa','gambia'=>'africa','ghana'=>'africa','guinea'=>'africa','guinea-bissau'=>'africa','ivory coast'=>'africa','kenya'=>'africa','lesotho'=>'africa','liberia'=>'africa','libya'=>'africa','madagascar'=>'africa','malawi'=>'africa','mali'=>'africa','mauritania'=>'africa','mauritius'=>'africa','morocco'=>'africa','mozambique'=>'africa','namibia'=>'africa','niger'=>'africa','nigeria'=>'africa','rwanda'=>'africa','sao tome and principe'=>'africa','senegal'=>'africa','seychelles'=>'africa','sierra leone'=>'africa','somalia'=>'africa','south africa'=>'africa','south sudan'=>'africa','sudan'=>'africa','swaziland'=>'africa','tanzania'=>'africa','togo'=>'africa','tunisia'=>'africa','uganda'=>'africa','zambia'=>'africa','zimbabwe'=>'africa','afghanistan'=>'asia','bahrain'=>'asia','bangladesh'=>'asia','bhutan'=>'asia','brunei'=>'asia','burma (myanmar)'=>'asia','cambodia'=>'asia','china'=>'asia','east timor'=>'asia','india'=>'asia','indonesia'=>'asia','iran'=>'asia','iraq'=>'asia','israel'=>'asia','japan'=>'asia','jordan'=>'asia','kazakhstan'=>'asia','north korea'=>'asia','south korea'=>'asia','kuwait'=>'asia','kyrgyzstan'=>'asia','laos'=>'asia','lebanon'=>'asia','malaysia'=>'asia','maldives'=>'asia','mongolia'=>'asia','nepal'=>'asia','oman'=>'asia','pakistan'=>'asia','philippines'=>'asia','qatar'=>'asia','russia'=>'asia','saudi arabia'=>'asia','singapore'=>'asia','sri lanka'=>'asia','syria'=>'asia','tajikistan'=>'asia','thailand'=>'asia','turkey'=>'asia','turkmenistan'=>'asia','united arab emirates'=>'asia','uzbekistan'=>'asia','vietnam'=>'asia','yemen'=>'asia','albania'=>'europe','andorra'=>'europe','armenia'=>'europe','austria'=>'europe','azerbaijan'=>'europe','belarus'=>'europe','belgium'=>'europe','bosnia'=>'europe','bulgaria'=>'europe','croatia'=>'europe','cyprus'=>'europe','czech republic'=>'europe','denmark'=>'europe','estonia'=>'europe','finland'=>'europe','france'=>'europe','georgia'=>'europe','germany'=>'europe','greece'=>'europe','hungary'=>'europe','iceland'=>'europe','ireland'=>'europe','italy'=>'europe','latvia'=>'europe','liechtenstein'=>'europe','lithuania'=>'europe','luxembourg'=>'europe','macedonia'=>'europe','malta'=>'europe','moldova'=>'europe','monaco'=>'europe','montenegro'=>'europe','netherlands'=>'europe','norway'=>'europe','poland'=>'europe','portugal'=>'europe','romania'=>'europe','san marino'=>'europe','serbia'=>'europe','slovakia'=>'europe','slovenia'=>'europe','spain'=>'europe','sweden'=>'europe','switzerland'=>'europe','ukraine'=>'europe','united kingdom'=>'europe','vatican city'=>'europe','antigua and barbuda'=>'north america','bahamas'=>'north america','barbados'=>'north america','belize'=>'north america','canada'=>'north america','costa rica'=>'north america','cuba'=>'north america','dominica'=>'north america','dominican republic'=>'north america','el salvador'=>'north america','grenada'=>'north america','guatemala'=>'north america','haiti'=>'north america','honduras'=>'north america','jamaica'=>'north america','mexico'=>'north america','nicaragua'=>'north america','panama'=>'north america','saint kitts and nevis'=>'north america','saint lucia'=>'north america','saint vincent and the grenadines'=>'north america','trinidad and tobago'=>'north america','united states'=>'north america','australia'=>'australia','fiji'=>'australia','kiribati'=>'australia','marshall islands'=>'australia','micronesia'=>'australia','nauru'=>'australia','new zealand'=>'australia','palau'=>'australia','papua new guinea'=>'australia','samoa'=>'australia','solomon islands'=>'australia','tonga'=>'australia','tuvalu'=>'australia','vanuatu'=>'australia','argentina'=>'south america','bolivia'=>'south america','brazil'=>'south america','chile'=>'south america','colombia'=>'south america','ecuador'=>'south america','guyana'=>'south america','paraguay'=>'south america','peru'=>'south america','suriname'=>'south america','uruguay'=>'south america','venezuela'=>'south america');
	return $arr;
}