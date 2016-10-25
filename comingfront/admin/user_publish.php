<?php
	require_once('reqAdmin.php');
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

			<dl class="bSwitchA">
				<dt>
					<strong class="title">发布管理</strong>
					<span class="tab">
						<a href="javascript:void(0);" class="cur">未发布列表</a>
						<a href="user_publish_finish.php">已发布列表</a>
					</span>
					<span class="titleSub">
						<a class="bBtnA" id="pubBtn" state="stop" href="javascript:void(0);">开始发布</a>
					</span>
				</dt>
				<dd id="pubList">
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

	<script type="text/javascript">
		window.bPubStart = false;
		window.bGetRequest = false;
		window.bPubRequest = false;
		window.bPubChange = false;

		function renderPublish() {
			window.bGetRequest = true;
			$.ajax({
				url: 'intPublish.php?do=getList',
				type: 'get',
				success: function(data) {
					if(data.state == "success") {
						if(data.showNum > 0) {
							var html = '';
							for (var i = 0; i < data.data.length; i++) {
								var d = data.data[i];
								var type = '';
								var id = 0;
								if(d['pub_col_id'] != 0) {
									type = '栏目';
									id = d['pub_col_id'];
								}else if(d['pub_art_id'] != 0) {
									type = '文章';
									id = d['pub_art_id'];
								}
								html += '<li class="cf">';
								html += '<i class="td1">'+d['pub_id']+'</i>';
								html += '<i class="td2">'+d['pub_create_user']+'</i>';
								html += '<i class="td3">'+formatDate(d['pub_create_time'], 'yyyy-mm-dd hh:ii:ss')+'</i>';
								html += '<i class="td4">['+type+'] '+id+'</i>';
								html += '<i class="td5"><a target="_blank" href="'+d['pub_address']+'">'+d['pub_address']+'</a></i>';
								html += '<i class="td6">'+d['pub_template']+'</i>';
								html += '<i class="td1">'+d['pub_page']+'</i>';
								html += '<i class="td1">'+d['pub_num']+'</i>';
								html += '<i class="td7">未发</i>';
								html += '<i class="td8">-</i>';
								html += '</li>';
							}
							html = '<ul class="bListA"><li class="title cf"><i class="td1">id</i><i class="td2">提交人</i><i class="td3">提交时间</i><i class="td4">发布内容</i><i class="td5">发布地址</i><i class="td6">模板</i><i class="td1">总页</i><i class="td1">已发页</i><i class="td7">状态</i><i class="td8">备注</i></li>'+html+'</ul>';
							$("#pubList").html(html);
						}else{
							$("#pubList").html('<div style="text-align:center; line-height:100px;">暂无发布任务</div>');
						}
						window.bGetRequest = false;
					}else{
						fPop.open('<div class="bPopTxt"><strong class="tit">发布任务列表刷新失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
						window.bGetRequest = false;
					}
				},
				error: function() {
					fPop.open('<div class="bPopTxt"><strong class="tit">添加发布任务失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}

		renderPublish();
		setInterval(function() {
			if( (window.bPubStart && !window.bGetRequest) || window.bPubChange) {
				window.bPubChange = false;
				renderPublish();
			}
		}, 5000);




		$("#pubBtn").click(function() {
			if($(this).attr('state') == 'stop') {
				pubStart();
			}else if($(this).attr('state') == 'start') {
				pubStop();
			}
		});

		function pubStart() {
			$("#pubBtn").attr('state', 'start').html('暂停发布');
			window.bPubStart = true;
			pubRun();
		}

		function pubStop() {
			$("#pubBtn").attr('state', 'stop').html('开始发布');
			window.bPubStart = false;
		}

		function pubRun() {
			if(window.bPubStart && !window.bPubRequest) {
				window.bPubRequest = true;
				$.ajax({
					url: 'intPublish.php?do=run',
					type: 'get',
					success: function(data) {
						if(data.state == "success") {
							window.bPubRequest = false;
							window.bPubChange = true;
							setTimeout(function(){
								pubRun();
							}, 500);
						}else{
							if(data.err == 'finished') {
								fPop.open('<div class="bPopTxt"><strong class="tit">发布完成！</strong><p class="con">请到 已发布列表 页面查看完成情况。</p></div>');
							}else{
								fPop.open('<div class="bPopTxt"><strong class="tit">发布失败！</strong><p class="con">'+data.err+'</p></div>');
							}
							setTimeout(function() {fPop.fade();}, 1500);
							window.bPubRequest = false;
							pubStop();
						}
					},
					error: function() {
						fPop.open('<div class="bPopTxt"><strong class="tit">发布失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
						window.bPubRequest = false;
						pubStop();
					}
				});
			}
		}
	</script>

</body>

</html>



