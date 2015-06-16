<?php
function form_regenerate($p,$dimension=array(),$key=null){
	if (!is_null($key)){
		$dimension[]=$key;
	}
	foreach ($p as $key => $val){
		if (is_array($val)){
			$form.=form_regenerate($val,$dimension,$key);
		}
		else {
			if (!empty($dimension)){
				for ($n=0;$n<count($dimension);$n++){
					if ($n==0){
						$name=$dimension[$n];
					}
					else {
						$name.='['.$dimension[$n].']';
					}
				}
				$name.='['.$key.']';
			}
			else {
				$name=$key;
			}
			$form.='<input type="hidden" name="'.$name.'" value="'.$val.'"/>';
		}
	}
	return $form;
}

function gen_select($db=array('id'=>'','select'=>'','db'=>'','where'=>'','order'=>'','limit'=>''),$id=null,$permalink=null,&$found=null){
	$records=query("SELECT ".$db['id']."_id,".$db['select']." FROM ".$db['db']." WHERE ".$db['where']." ORDER BY ".$db['order'],'2d');
	foreach ($records as $record){
		if ($permalink) $ident=$record['permalink'];
		else $ident=$record[$db['id'].'_id'];
		$select.='<option value="'.$ident.'"';
		if ($ident==$id){
			$select.=' selected="selected"';
			$found=true;
		}
		$select.='>'.$record['title'].'</option>';
	}
	return $select;
}

function html_attrs($attrs){
	if (is_array($attrs)){
		foreach ($attrs as $key => $val){
			$html.=' '.$key.'="'.$val.'"';
		}
	}
	return $html;
}

function html_close($die=false){
	return '<script type="text/JavaScript">window.close();</script>';
}

function html_data($data,$test_numeric=true){
	if (is_array($data)){
		foreach ($data as $key => $val){
			// $val=is_array($val) ? json_array($val,false,$test_numeric) : $val;
			$val=is_array($val) ? json_encode($val) : $val;
			$html.=' data-'.$key.'=\''.$val.'\'';
		}
	}
	return $html;
}

function html_implode($arr, $el, $class = ''){
	if (!is_array($arr)){
		return '';
	}
	$open_tag = "<$el".(!empty($class) ? "class='$class'" : '').">";
	return $open_tag.implode("</$el>$open_tag", $arr)."</$el>";
}

function html_table_cols($cols){
	return html_implode($cols,'td');
}

function html_table_rows($rows){
	return html_implode($rows,'tr');
}

function html_table_body($rows,$table,$class=''){
	if (isset($table['head'])){
		$thead=$table['head'];
	}
	elseif (is_array($table)){
		$thead=make_table_head($table);
	}
	else {
		$thead=$table;
	}
	return '<table'.(!empty($class) ? ' class="'.$class.'"' : '').'>'.$thead.'<tbody>'.(is_array($rows) ? html_table_rows($rows) : $rows).'</tbody></table>';
}

function html_ie_html5(){
	$html='var e=("abbr,article,aside,audio,canvas,datalist,details,figure,footer,header,hgroup,mark,menu,meter,nav,output,progress,section,time,title,video")'.".split(',');for (var i=0;i<e.length;i++){document.createElement(e[i]);}";
	$html='<!--[if lt IE 9]><script>'.$html.'</script><![endif]-->';
	return $html;
}

function html_meta_refresh($url,$msg=null,$time=0){
	return '<meta http-equiv="refresh" content="'.$time.'; url='.$url.'">'.($msg ? '<h1>'.$msg.'</h1>' : '');
}
// here for legacy, renamed to better match other return html functions
function meta_refresh($url,$msg=null,$time=0){
	return html_meta_refresh($url,$msg,$time);
}

function html_qr_url($url=null,$alt=''){
	return '<img src="http://chart.apis.google.com/chart?cht=qr&chs=230x230&chl='.urlencode($url).'" alt="QR Code"/>';
}

function html_twitter_share($p=null){
	$html='<a href="https://twitter.com/share" class="twitter-share-button" data-url="'.$p['url'].'" data-text="'.$p['text'].'" data-via="'.$p['via'].'"'.(!empty($p['related']) ? ' data-related="'.$p['related'].'"' : '').(empty($p['count']) ? ' data-count="none"' : '').'>'.(!empty($p['link-text']) ? $p['link-text'] : 'Tweet').'</a>';
	$html.='<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+\'://platform.twitter.com/widgets.js\';fjs.parentNode.insertBefore(js,fjs);}}(document,\'script\',\'twitter-wjs\');</script>';
	return $html;
}

function html_worldpay($exc=array(),$secure=false){
	$protocol='http'.($secure ? 's' : '').'://';
	if (!in_array('visa',$exc)){
		$html.='<img src="'.$protocol.'www.worldpay.com/images/cardlogos/VISA.gif" alt="Visa Credit payments"/>';
	}
	if (!in_array('visa-d',$exc)){
		$html.=' <img src='.$protocol.'www.worldpay.com/images/cardlogos/visa_debit.gif border=0 alt="Visa Debit payments supported by WorldPay">';
	}
	if (!in_array('electron',$exc)){
		$html.=' <img src='.$protocol.'www.worldpay.com/images/cardlogos/visa_electron.gif border=0 alt="Visa Electron payments supported by WorldPay">';
	}
	if (!in_array('mastercard',$exc)){
		$html.=' <img src="'.$protocol.'www.worldpay.com/images/cardlogos/mastercard.gif" alt="Mastercard payments"/>';
	}
	if (!in_array('maestro',$exc)){
		$html.=' <img src="'.$protocol.'www.worldpay.com/images/cardlogos/maestro.gif" alt="Maestro payments supported by WorldPay"/>';
	}
	if (!in_array('jcb',$exc)){
		$html.=' <img src='.$protocol.'www.worldpay.com/images/cardlogos/JCB.gif border=0 alt="JCB payments supported by WorldPay">';
	}
	if (!in_array('amex',$exc)){
		$html.=' <img src='.$protocol.'www.worldpay.com/images/cardlogos/AMEX.gif border=0 alt="American Express payments supported by WorldPay">';
	}
	$html.=' <a href="http://www.worldpay.com/" target="_blank" title="Payment Processing - WorldPay - Opens in new browser window"><img src="'.$protocol.'www.worldpay.com/images/poweredByWorldPay.gif" alt="WorldPay Payments Processing"></a>';
	return $html;
}

function make_calendar($options=array()){
	$months=arr_month();
	if (!empty($options['date'])){
		$cal_date=string_time($options['date']);
	}
	if (empty($cal_date)){
		$month=isset($months[$options['month']]) ? $options['month'] : date('n');
		$year=!empty($options['year']) ? $options['year'] : date('Y');
		$cal_date=strtotime($year.'-'.$month.'-01 00:00:00');
	}
	else {
		$month=date('n',$cal_date);
	}

	$start_day=!empty($options['start_day']) ? $options['start_day'] : 1;
	$weeks_to_show=!empty($options['weeks']) ? $options['weeks'] : null;

	$day_balance=date('N',$cal_date)-$start_day+7;
	$day_balance%=7;
	$cal_date=inc_date($cal_date,array('day'=>-1*$day_balance),1);
	$today=strtotime(date('Y-m-d'));
	$n=0;
	$calendar=array();
	$end_day=$start_day>1 ? $start_day-1 : 7;
	while (!$complete){
		if (date('N',$cal_date)==$start_day){
			$week=array();
		}
		$diff_month=(date('n',$cal_date)!=$month);
		$week[date('Y-m-d',$cal_date)]=array('diff'=>$diff_month,'today'=>($cal_date==$today),'content'=>'<strong>'.date('j'.(($diff_month or !empty($weeks_to_show)) ? ' M' : ''),$cal_date).'</strong>');
		if (date('N',$cal_date)==$end_day){
			$calendar[]=$week;
		}
		$cal_date=inc_date($cal_date,array('day'=>1),1);
		if ($n>100 or (empty($weeks_to_show) and date('n',$cal_date)!=$month and date('N',$cal_date)==$start_day) or (!empty($weeks_to_show) and count($calendar)==$weeks_to_show)){
			$complete=true;
		}
		$n++;
	}
	return $calendar;
}

function make_calendar_html($calendar=array(),$options=array()){
	$start_day=!empty($options['start_day']) ? $options['start_day'] : 1;
	if (empty($calendar)){
		$calendar=make_calendar($options);
	}
	foreach ($calendar as $week){
		$row='<tr>';
		foreach ($week as $date => $day){
			if (!empty($day['diff'])){
				$day['class'].=' month';
			}
			if (!empty($day['today'])){
				$day['class'].=' today';
			}
			$row.='<td'.(!empty($day['class']) ? ' class="'.$day['class'].'"' : '').' data-date="'.$date .'"'.html_data($day['data']).'>'.$day['content'].'</td>';
		}
		$row.='</tr>';
		$tbody.=$row;
	}
	$tbody='<tbody>'.$tbody.'</tbody>';
	$days=arr_day();
	for ($n=$start_day;$i<7;$i++){
		$thead.='<th>'.$days[$n].'</th>';
		$n=$n==7 ? 1 : $n+1;
	}
	$thead='<thead><tr class="head">'.$thead.'</tr></thead>'; // we use the class `head` here for legacy compatability
	$html='<table class="calendar">'.$thead.$tbody.'</table>';
	return $html;
}

function make_options_($arr,$current_value=null,$vals=null,$order=false){
	$assoc=is_assoc($arr);
	unset($arr['_']);
	foreach ($arr as $key => $name){
		$selected=$disabled=$class=$data=false;
		if (is_array($name)){
			$data=$name['data'];
			$class=$name['class'];
			$disabled=!empty($name['disabled']);
			$selected=!empty($name['selected']);
			$name=$name['name']; // must be last if we add others
		}
		if (is_array($vals)){
			$val=$vals[$key];
		}
		elseif (empty($assoc)){
			$val=$name;
		}
		else {
			$val=$key;
		}
		if ((string)$val===(string)$current_value or ($order!==false and $n==$order)){
			$selected=true;
		}
		$options.='<option'
			.' value="'.$val.'"'
			.($selected ? ' selected' : '')
			.(!empty($data) ? html_data($data) : '')
			.(!empty($class) ? ' class="'.$class.'"' : '')
			.($disabled ? ' disabled' : '')
			.'>'.$name.'</option>';
		$n++;
	}
	return $options;
}

function make_table_csv($table){
	$arr=[];
	foreach ($table['head'] as $key => $head){
		if (!empty($head['colspan'])){
			for ($n=1;$n<$head['colspan'];$n++){
				$arr[]=$head['title'];
			}
		}
		$arr[]=$head['title'];
	}
	return implode(',',$arr);
}

function html_table_head($arr){
	$html=html_implode($arr,'th');
	$html='<tr>'.$html.'</tr>';
	$html='<thead>'.$html.'</thead>';
	return $html;
}
// DEPRECATED
function make_table_head($arr){
	return html_table_head($arr);
}

function html_table_quick($arr){
	if (empty($arr)){
		return '';
	}
	$args = get_args_smart(func_get_args(), 1);
	$head_overwrite = $args['array'];
	$process = $args['callable'];
	$class = $args['string'];
	$rows = [];
	foreach ($arr as $item){
		if (is_callable($process)){
			$item = $process($item);
			if ($item===false){
				continue;
			}
		}
		if (empty($keys)){
			$keys = array_keys($item);
		}
		$rows[] = html_table_cols($item);
	}
	if (!empty($head_overwrite)){
		$keys = array_overwrite($keys, $head_overwrite);
	}
	$thead = make_table_head($keys);
	$html = '<table'.(!empty($class) ? ' class="'.$class.'"' : '').'>'.$thead.'<tbody>'.html_table_rows($rows).'</tbody></table>';
	return $html;
}

function html_csv($arr){
	if (empty($arr)){
		return '';
	}
	$args=get_args_smart(func_get_args(),1);
	$head_overwrite=$args['array'];
	$process=$args['callable'];
	foreach ($arr as &$item){
		if (is_callable($process)){
			$item=$process($item);
		}
		$rows[]='"'.implode('","',$item).'"';
	}
	$keys=array_keys_2d($arr);
	if (!empty($head_overwrite)){
		$keys=array_overwrite($keys,$head_overwrite);
	}
	array_unshift($rows,'"'.implode('","',$keys).'"');
	$csv=implode("\n",$rows);
	return $csv;
}

// DEPRECATED
function make_options($names,$vals=null,$current_value=false,$order=false){
	return make_options_($names,$current_value,$vals,$order);
}

// DEPRECATED
function make_options_assoc($arr,$current_value=null){
	return make_options_($arr,$current_value);
}

function select_months($sel=null,$opts=array()){
	return make_options_(list_months($opts['length'],$opts['month'],$opts['year']),$sel);
}
