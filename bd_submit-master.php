<?php
/*
Plugin Name: 百度自动推送
Version: 1.2
Plugin URL: http://blog.alw.pw
Description: 自动向百度提交文章链接，有利于百度的快速收录
Author: 安乐窝
Author URL: http://blog.alw.pw
*/
!defined('EMLOG_ROOT') && exit('access deined!');
function bd_submit_menu(){//添加导航
echo '<li><a href="./plugin.php?plugin=bd_submit-master" id="bd_submit_menu">百度链接提交</a></li>';
}
addAction('adm_sidebar_ext', 'bd_submit_menu');


function bd_submit_main($logid){//提交链接
	$logid_file = dirname(__FILE__).'/logid_log.txt';
	$logids = file_get_contents($logid_file);
	$logids_info = explode("|", $logids);
	$is_newlog = !in_array($logid, $logids_info);
	$log_model = new  Log_Model();
	$log = $log_model->getOneLogForAdmin($logid);
	include(EMLOG_ROOT.'/content/plugins/bd_submit-master/bd_submit-master_config.php');
	if($log['hide'] !== 'y' && $config["site"] !== "" && $config["token"] !== "" && $is_newlog ){
		$site = $config["site"];
		$token = $config["token"];
		$url = Url::log($logid);
		$api = 'http://data.zz.baidu.com/urls?site='.$site.'&token='.$token;
		$ch = curl_init();
		$options =  array(
			CURLOPT_URL => $api,
			CURLOPT_POST => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POSTFIELDS => $url,
			CURLOPT_HTTPHEADER => array('Content-Type: text/plain')
			);
		curl_setopt_array($ch, $options);
		$result = curl_exec($ch);
		$result = json_decode($result, true);
		if ($result) {
			$time = nowtime();
			$submit_file = dirname(__FILE__).'/submit_log.txt';	

			if ($result['success']) {
				$success = "1";
			}else {
				$success = "0||".$result["message"];
			}
			
			$handle = fopen($submit_file,"a");
			fwrite($handle,"$success||$time||$url\r\n");
			fclose($handle);

			$handle_logid = fopen($logid_file ,"a");
			fwrite($handle_logid,"$logid|");
			fclose($handle_logid);
		}
	}
	

}
addAction("save_log","bd_submit_main");

function nowtime($time = ''){
	date_default_timezone_set('Asia/Shanghai');
	if($time != ''){
		$date = date("Y-m-d H:i:s", $time);
	}else{
		$date = date("Y-m-d H:i:s");
	}
	return $date;  
}