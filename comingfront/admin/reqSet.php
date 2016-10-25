<?php
    //页面执行开始时间
    $time_start = getmicrotime();

    //设置页面编码
    header("Content-Type: text/html; charset=utf-8");

    //获取时间函数
    function getmicrotime() { 
        list($usec, $sec) = explode(" ",microtime()); 
        return ((float)$usec + (float)$sec); 
    }

    //计算页面执行时间函数
    function executiveTime($time_start) {
    	$time_end = getmicrotime();
    	$time = $time_end - $time_start;
    	return $time;
    }

    //error_reporting(E_ERROR | E_WARNING | E_PARSE);
    if(PHP_VERSION<"4.1.0"){
        $_GET=&$HTTP_GET_VARS;
        $_POST=&$HTTP_POST_VARS;
        $_COOKIE=&$HTTP_COOKIE_VARS;
        $_SERVER=&$HTTP_SERVER_VARS;
        $_ENV=&$HTTP_ENV_VARS;
        $_FILES=&$HTTP_POST_FILES;
    }
    $web_http='http://'.(!empty($_SERVER['HTTP_X_FORWARDED_HOST'])?$_SERVER['HTTP_X_FORWARDED_HOST']:$_SERVER['HTTP_HOST']);

    /* -----【网站设置】能不用尽量不要用特殊符号，如 \ / : ; * ? ' < > | ，必免导致错误 ----- */
    //基本设置：
    $web['version'] = '1.0'; //网站版本
    $web['name'] = 'ComingFront';  //站点名称
    $web['keywords'] = 'web前端开发';  //站点关键字
    $web['description'] = 'ComingFront-web前端开发资料和经验分享，专注web前端开发资料、经验、职场面试分享的博客,帮助前端工程师在web前端开发中获取第一手的实用资料。';  //站点描述
    //$web['http']=$web_http.'/';  //站点网址
    // $web['http'] = 'http://www.comingfront.com/';
    $web['http'] = 'http://www.comingfront.com/';
    $web['path']=dirname($web_http.$_SERVER['SCRIPT_NAME']).'/';  //路径
    $web['cookie_path']=(($coo=ltrim(dirname($web_http.$_SERVER['SCRIPT_NAME']),$web_http))?'/'.$coo.'':'').'/';

    //页码控制设置：
    $web['page_size']=15;  //每页显示数量
    $web['page_view']=6;

    //上传设置
    $web['max_file_size'][0]=500;  //限定上传图片尺寸（单位KB）
    $web['max_file_size'][1]=1000;  //限定上传其它文件尺寸（单位KB）
    $web['imgType']='jpg|jpeg|gif|png|bmp';
    $web['fileType']='rar|zip|exe|doc|xls|chm|hlp';  //设置允许上传的其它文件类型，用|分开写

    //数据库设置
    $data['dbms'] = 'mysql';
    $data['server'] = 'localhost';
    $data['port'] = '3306';
    $data['dbname'] = 'coming_front';
    $data['user'] = 'coming_front';
    $data['password'] = 'coming801220()';
    $data['charset'] = 'utf8';

    //设置时区
    date_default_timezone_set('PRC');

    session_start();
?>