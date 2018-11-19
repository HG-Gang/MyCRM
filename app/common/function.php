<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function BankCcode($code) {
  	$arr=str_split($code,4);//4的意思就是每4个为一组
	$str=implode('-',$arr);
	return $str;
    
}

/*
 * 自定义函数
 */

function Curl($url, $method = 'get', $data = '') {
    $ch = curl_init(); //初始化
    $headers = array('Accept-Charset: utf-8');
    //设置URL和相应的选项
    curl_setopt($ch, CURLOPT_URL, $url); //指定请求的URL
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method)); //提交方式
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); //不验证SSL
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); //不验证SSL
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); //设置HTTP头字段的数组
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible;MSIE 5.01;Windows NT 5.0)'); //头的字符串
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1); //自动设置header中的Referer:信息
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); //提交数值
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //是否输出到屏幕上,true不直接输出
    $temp = curl_exec($ch); //执行并获取结果
    curl_close($ch);
    return $temp; //return 返回值
}

function RoleName($id) {
    if (empty($id)) {
        return "无角色";
    }
    $role = \App\Model\Role::find($id);
    if (empty($role)) {
        return "无角色";
    } else {
        return $role->username;
    }
    }
    
    
    function GroupId($str){
     if (empty($str)) {
        return "";
    }
     $role = \App\Model\UserGroup::where('user_group_name',$str)->first();
     if (empty($role)) {
        return "";
    } else {
        return $role->group_id;
    }
    }

    function resource_version_number ()
    {
    	return '0.0.15';
    }
    
    function Official_web_address ()
    {
    	//www.jjafx.com
    	return 'www.padafx.com';
    }
    
    //TODO 更改路由前缀时，请同步更改form.core.js 同名函数的路由前缀
    function route_prefix()
    {
    	return '/pada/admin';
    }
    
    function MT4_download()
    {
    	return 'https://download.mql5.com/cdn/web/12674/mt4/padaholding4setup.exe';
    }
	
	/**
	* 取得上个周一
	* @return string
	 * var_dump(getLastMonday());
	var_dump(date('l',time()));
	var_dump(date('Y-m-d',strtotime('last friday'))); 上个周五
	dd(date('Y-m-d', strtotime('-1 friday', time()))); 上个周五
	*/
    function getLastMonday()
	{
		if (date('l',time()) == 'Monday') return date('Y-m-d',strtotime('last monday'));
		
		return date('Y-m-d',strtotime('-1 week last monday'));
	}
	
	/**
	 * 取得上个周日，上个周五
	 * @return string
	 * last sunday, dd(date('Y-m-d', strtotime('-2 sunday', time())));上个周日
	 * last monday 上个周五
	 */
	function getLastFriday()
	{
		return date('Y-m-d',strtotime('last friday'));
	}
	
	function getLastSunday() {
		return date('Y-m-d',strtotime('last sunday'));
	}
	
	function getLastSaturday()
	{
		return date('Y-m-d',strtotime('last saturday'));
	}
	
	function getClientId()
	{
		//获取当前用户IP
		if (getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} elseif (getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = getenv('HTTP_X_FORWARDED_FOR');
		} elseif (getenv('HTTP_X_FORWARDED')) {
			$ip = getenv('HTTP_X_FORWARDED');
		} elseif (getenv('HTTP_FORWARDED_FOR')) {
			$ip = getenv('HTTP_FORWARDED_FOR');
		} elseif (getenv('HTTP_FORWARDED')) {
			$ip = getenv('HTTP_FORWARDED');
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		
		return $ip;
	}
	
	function getUserTerminal()
	{
		$ter = '';
		//获取USER AGENT
		
		$agent = strtolower($_SERVER['HTTP_USER_AGENT']);
		
		//分析数据
		
		$is_pc = (strpos($agent, 'windows nt')) ? true : false;
		
		$is_iphone = (strpos($agent, 'iphone')) ? true : false;
		
		$is_ipad = (strpos($agent, 'ipad')) ? true : false;
		
		$is_android = (strpos($agent, 'android')) ? true : false;
		
		//输出数据
		
		if($is_pc){
			$ter = "PC";
		}else if($is_iphone){
			$ter = "H5";
		} else if($is_ipad){
			$ter = "IOS";
		} else if($is_android){
			$ter = "ANDROID";
		}
		
		return $ter;
	}
