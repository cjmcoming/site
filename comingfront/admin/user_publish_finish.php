<?php
	require_once('reqAdmin.php');

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
	$page = ctype_digit($page) ? $page : 1;

	$rsPub = getDataPubList('finish', ($page-1)*$web['page_size'], $web['page_size']);

	$totalPage = ceil($rsPub['num'] / $web['page_size']);
	if( ($totalPage > 0 && $page > $totalPage) || ($totalPage == 0 && $page > 1) ) {
		err("该页不存在");
	}
?>
<!doctype HTML>
<html>

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta content="telephone=no" name="format-detection" />
	<title><?=$web['name']?></title>
	<meta name="keywords" content="<?=$web['keywords']?>" />
	<meta name="description" content="<?=$web['description']?>" />
	<link rel="stylesheet" type="text/css" href="<?=$web['http']?>static/css/admin.css" />
	<script type="text/javascript" src="<?=$web['http']?>static/js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="<?=$web['http']?>static/js/admin_base.js"></script>

</head>

<body>

	<!--top star-->
	<?php include 'incHeader.php'; ?>
	<!--top end-->


	<div class="bBread">
		<div class="inner">
			当前位置：<a href="user_index.php">后台首页</a> > <em>发布管理</em>
		</div>
	</div>


	<!--main star-->
	<div class="main pPublish">

		<div class="inner">

			<dl class="bSwitchA" id="bSwitchA">
				<dt>
					<strong class="title">发布管理</strong>
					<span class="tab">
						<a href="user_publish.php">未发布列表</a>
						<a href="javascript:void(0);" class="cur">已发布列表</a>
					</span>
				</dt>
				<dd>
					<?php
						if(!count($rsPub['rs']) ) {
							echo '<div style="text-align:center; line-height:100px;">暂无完成发布任务</div>';
						}else{
							$html = '';
							for ($i = 0; $i < count($rsPub['rs']); $i++) { 
								$d = $rsPub['rs'][$i];
								$type = '';
								$id = 0;
								if($d['pub_col_id'] != 0) {
									$type = '栏目';
									$id = $d['pub_col_id'];
								}else if($d['pub_art_id'] != 0) {
									$type = '文章';
									$id = $d['pub_art_id'];
								}
								$state = '未发';
								if($d['pub_state'] == 1) {
									$state = '正在发布';
								}else if($d['pub_state'] == 2) {
									$state = '发布完成';
								}else if($d['pub_state'] == 3) {
									$state = '发布失败';
								}
								$html .= '<li class="cf">';
								$html .= '<i class="td1">'.$d['pub_id'].'</i>';
								$html .= '<i class="td2">'.$d['pub_create_user'].'</i>';
								$html .= '<i class="td3">'.formatDate($d['pub_create_time'], 'yyyy-mm-dd hh:ii:ss').'</i>';
								$html .= '<i class="td4">['.$type.'] '.$id.'</i>';
								$html .= '<i class="td5"><a target="_blank" href="'.$d['pub_address'].'">'.$d['pub_address'].'</a></i>';
								$html .= '<i class="td6">'.$d['pub_template'].'</i>';
								$html .= '<i class="td1">'.$d['pub_page'].'</i>';
								$html .= '<i class="td1">'.$d['pub_num'].'</i>';
								$html .= '<i class="td7">'.$state.'</i>';
								$html .= '<i class="td8">'.$d['pub_reason'].'</i>';
								$html .= '</li>';
							}
							$html = '<ul class="bListA"><li class="title cf"><i class="td1">id</i><i class="td2">提交人</i><i class="td3">提交时间</i><i class="td4">发布内容</i><i class="td5">发布地址</i><i class="td6">模板</i><i class="td1">总页</i><i class="td1">已发页</i><i class="td7">状态</i><i class="td8">备注</i></li>'.$html.'</ul>';
							echo $html;
						}
					?>

<!--
					<ul class="bListA">
						<li class="title cf">
							<i class="td1">id</i>
							<i class="td2">提交人</i>
							<i class="td2">发布人</i>
							<i class="td3">提交时间</i>
							<i class="td3">发布时间</i>
							<i class="td4">发布内容</i>
							<i class="td5">发布地址</i>
							<i class="td6">模板</i>
							<i class="td7">状态</i>
							<i class="td8">备注</i>
						</li>
						<li class="cf">
							<i class="td1">id</i>
							<i class="td2">提交人</i>
							<i class="td2">-</i>
							<i class="td3">2016-06-01 12:12</i>
							<i class="td3">-</i>
							<i class="td4">[栏目] 123</i>
							<i class="td5">发布地址</i>
							<i class="td6">模板</i>
							<i class="td7">未发</i>
							<i class="td8">-</i>
						</li>
						<li class="cf">
							<i class="td1">id</i>
							<i class="td2">提交人</i>
							<i class="td2">-</i>
							<i class="td3">提交时间</i>
							<i class="td3">-</i>
							<i class="td4">[文章] 123</i>
							<i class="td5">发布地址</i>
							<i class="td6">模板</i>
							<i class="td7">未发</i>
							<i class="td8">-</i>
						</li>
					</ul>
-->
				</dd>
			</dl>

		</div>

	</div>
	<!--main end-->
	


	<!--footer star-->
	<?php include 'incFooter.php'; ?>
	<!--footer end-->

</body>

</html>



