<?php
	require_once('reqAdmin.php');

	$col_id = isset($_REQUEST['col_id']) ? $_REQUEST['col_id'] : '';
	$col_id = ctype_digit($col_id) ? $col_id : 0;
	if($col_id == 0) {
		err("未能获取到栏目id！");
	}

	$rsCol = getDataColumn('column', $col_id);
	if(!count($rsCol) ) {
		err("数据库未能找到该id所对应的栏目！");
	}
	$col_name = $rsCol['col_name'] == '' ? '暂无栏目名' : $rsCol['col_name'];

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
	$page = ctype_digit($page) ? $page : 1;

	$rsArtList = getDataArtList($col_id, ($page-1)*$web['page_size'], $web['page_size'], 123);
	$totalPage = ceil($rsArtList['num'] / $web['page_size']);
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
			当前位置：<a href="user_index.php">后台首页</a> > <a href="user_column.php">栏目管理</a> > <em>文章列表</em>
		</div>
	</div>



	<!--main star-->
	<div class="main pArtList">

		<div class="inner">

			<dl class="bBoxA">
				<dt>
					<strong><?=$col_name?>-文章列表</strong>
					<span><a href="javascript:addArticle(<?=$col_id?>);">+添加文章</a></span>
				</dt>
				<dd>
					<?php
						if(!count($rsArtList['rs']) ) {
							echo '<div style="text-align:center; line-height:100px;">暂无文章</div>';
						}else{
							$html = '<ul class="bListA" id="articleList"><li class="title cf"><i class="td1">ID</i><i class="td2">文章标题</i><i class="td3">点击</i><i class="td4">状态</i><i class="td5">创建时间</i><i class="td6">创建人</i><i class="td7">操作</i><i class="td8">排序</i></li>';
							for ($i = 0; $i < count($rsArtList['rs']); $i++) {
								$d = $rsArtList['rs'][$i];
								$title = $d['art_title'] == '' ? '暂无标题' : $d['art_title'];
								$state = $d['art_state'] == 1 ? '草稿' : '已发';
								if($d['art_state'] == 1) {
									$state = '草稿';
								}else if($d['art_state'] == 2) {
									$state = '已发';
								}else if($d['art_state'] == 3) {
									$state = '返工';
								}
								$modify = '';
								if($rsCol['col_type'] == 1) {
									$modify = '<a href="user_column_art_mod.php?art_id='.$d['art_id'].'">修改</a>';
								}else if($rsCol['col_type'] == 2) {
									$modify = '<a href="user_column_demo_mod.php?art_id='.$d['art_id'].'">修改</a>';
								}
								$html .= '<li class="cf"><i class="td1">'.$d['art_id'].'</i>';
								$html .= '<i class="td2">'.$title.'</i>';
								$html .= '<i class="td3">'.$d['art_count'].'</i>';
								$html .= '<i class="td4">'.$state.'</i>';
								$html .= '<i class="td5">'.formatDate($d['art_create_time'], 'yyyy-mm-dd').'</i>';
								$html .= '<i class="td6">'.$d['art_create_user'].'</i>';
								$html .= '<i class="td7"><a href="user_preview.php?type=article&id='.$d['art_id'].'" target="_blank">预览</a>';
								$html .= '<a href="javascript:addPublish(\'article\', '.$d['art_id'].')">发布</a>';
								$html .= $modify;
								$html .= '<a href="javascript:modTimeConfirm('.$d['art_id'].','.$d['art_create_time'].')">日期</a>';
								$html .= '<a href="javascript:delArticleConfirm('.$d['art_id'].')">删除</a>';
								$html .= '<a href="'. $web['http'] .$d['col_address'].'/'.substr($d['art_create_time'], 0, 6).'/'.$d['art_id'].'.html" target="_blank">链接</a></i>';
								$html .= '<i class="td8"><input type="text" class="inpSort" articleId="'.$d['art_id'].'" value="'.$d['art_sort'].'" /></i></li>';
							}
							$html .= '<li class="cf"><i class="sortBtn"><a class="bBtnA" href="javascript:sortArticle();">保存排序</a></i></li></ul>';
							echo $html;

							echo showPageAdmin($web['page_size'], $web['page_view'], $totalPage, $page, $rsArtList['num'], 'user_column_art.php?col_id='.$col_id.'&');
						}
					?>
				</dd>
				<!--
				<dd>
					<ul class="bListA">
						<li class="title cf">
							<i class="td1">ID</i>
							<i class="td2">文章标题</i>
							<i class="td3">点击</i>
							<i class="td4">状态</i>
							<i class="td5">更新时间</i>
							<i class="td6">更新人</i>
							<i class="td7">操作</i>
							<i class="td8">排序</i>
						</li>
						<li class="cf">
							<i class="td1">123</i>
							<i class="td2">文章标题文章标题文章标题文章标题文章标题文章标题文章标题文章标题</i>
							<i class="td3">332</i>
							<i class="td4">草稿</i>
							<i class="td5">2016-06-06</i>
							<i class="td6">admin</i>
							<i class="td7">
								<a href="#">预览</a>
								<a href="#">发布</a>
								<a href="user_column_art_mod.php">修改</a>
								<a href="#">删除</a>
							</i>
							<i class="td8"><input type="text" /></i>
						</li>
						<li class="cf">
							<i class="sortBtn">
								<a class="bBtnA" href="javascript:void(0);">保存排序</a>
							</i>
						</li>
					</ul>
					<div class="bPage">
						<a title="上一页" class="bPageLast" href="#">&lt;</a>
						<i>...</i>
						<a href="#">3</a>
						<a href="#">4</a>
						<em>5</em>
						<a href="#">6</a>
						<a href="#">77</a>
						<i>...</i>
						<a title="下一页" class="bPageNext" href="#">&gt;</a>
						<span class="bPageTxt">每页 10 条　共 1 页 9 条</span>
					</div>
				</dd>
				-->
			</dl>

		</div>

	</div>
	<!--main end-->
	


	<!--footer star-->
	<?php include 'incFooter.php'; ?>
	<!--footer end-->



	<script type="text/javascript">
		function addArticle(id) {
			fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
			$.ajax({
				url: 'intArticle.php?do=add&col_id=' + id,
				type: 'get',
				success: function(data) {
					if(data.state == "success") {
						fPop.change('<div class="bPopTxt"><strong class="tit">添加文章成功！</strong><p class="con">正在跳转到文章修改页面</p></div>');
						setTimeout(function() {location.href = "user_column_art_mod.php?art_id=" + data.newId;}, 1500);
					}else{
						fPop.change('<div class="bPopTxt"><strong class="tit">添加文章失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				},
				error: function() {
					fPop.change('<div class="bPopTxt"><strong class="tit">添加文章失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}


		function modTimeConfirm(id, val) {
			fPop.open('<div class="bPopModDate"><strong class="tit">修改文章日期！</strong><p class="con"><input type="text" id="newArtTime" value="'+val+'" /></p><div class="btn cf"><a class="bBtnA" href="javascript:fPop.fade();">取消</a><a class="bBtnA" href="javascript:modTime('+id+');">确定</a></div>');
		}
		function modTime(id) {
			var newTime = $("#newArtTime").val();
			if(!isNaN(newTime) && newTime.length == 14) {
				fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
				$.ajax({
					url: 'intArticle.php?do=modTime&art_id=' + id,
					type: 'post',
					data: {
						art_create_time: newTime
					},
					success: function(data) {
						if(data.state == "success") {
							fPop.open('<div class="bPopTxt"><strong class="tit">修改文章日期成功！</strong><p class="con">正在刷新页面</p></div>');
							setTimeout(function() {location.reload();}, 1500);
						}else{
							fPop.open('<div class="bPopTxt"><strong class="tit">修改文章日期失败！</strong><p class="con">'+data.err+'</p></div>');
							setTimeout(function() {fPop.fade();}, 1500);
						}
					},
					error: function() {
						fPop.open('<div class="bPopTxt"><strong class="tit">修改文章日期失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				});
			}else{
				fPop.open('<div class="bPopTxt"><strong class="tit">修改文章日期失败！</strong><p class="con">格式不正确。</p></div>');
				setTimeout(function() {fPop.fade();}, 1500);
			}
		}


		function delArticleConfirm(id) {
			fPop.open('<div class="bPopTxt"><strong class="tit">删除文章！</strong><p class="con">您确定要删除该文章吗？</p><div class="btn cf"><a class="bBtnA" href="javascript:fPop.fade();">取消</a><a class="bBtnA" href="javascript:delArticle('+id+');">确定</a></div>');
		}
		function delArticle(id) {
			fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
			$.ajax({
				url: 'intArticle.php?do=del&art_id=' + id,
				type: 'get',
				success: function(data) {
					if(data.state == "success") {
						fPop.open('<div class="bPopTxt"><strong class="tit">删除文章成功！</strong><p class="con">正在刷新页面</p></div>');
						setTimeout(function() {location.reload();}, 1500);
					}else{
						fPop.open('<div class="bPopTxt"><strong class="tit">删除文章失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				},
				error: function() {
					fPop.open('<div class="bPopTxt"><strong class="tit">删除文章失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}


		function sortArticle() {
			var $inpSort = $("#articleList .inpSort");
			var sId = '', sSort = '';
			$inpSort.each(function(i) {
				var val = $(this).val(), oldVal = $(this)[0].defaultValue, id = $(this).attr('articleId');
				if(val != oldVal) {
					sId += id + ',';
					sSort += val + ',';
				}
			});

			if(sId == '') {
				fPop.open('<div class="bPopTxt"><strong class="tit">修改排序</strong><p class="con">没有可更新的排序。</p></div>');
				setTimeout(function() {fPop.fade();}, 1500);
			}else{
				sId = sId.substr(0, sId.length-1);
				sSort = sSort.substr(0, sSort.length-1);

				$.ajax({
					url: 'intArticle.php?do=sort',
					type: 'post',
					data: {
						articleId: sId,
						sort: sSort
					},
					success: function(data) {
						if(data.state == "success") {
							fPop.open('<div class="bPopTxt"><strong class="tit">更新排序成功！</strong><p class="con">正在刷新页面</p></div>');
							setTimeout(function() {location.reload();}, 1500);
						}else{
							fPop.open('<div class="bPopTxt"><strong class="tit">更新排序失败！</strong><p class="con">'+data.err+'</p></div>');
							setTimeout(function() {fPop.fade();}, 1500);
						}
					},
					error: function() {
						fPop.open('<div class="bPopTxt"><strong class="tit">更新排序失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				});
			}
		}

		function addPublish(type, id) {
			var url = '';
			if(type == 'column') {
				url = 'intPublish.php?do=addCol&col_id=' + id;
			}else if(type == 'colArt') {
				url = 'intPublish.php?do=addColArt&col_id=' + id;
			}else if(type == 'article') {
				url = 'intPublish.php?do=addArt&art_id=' + id;
			}
			$.ajax({
				url: url,
				type: 'get',
				success: function(data) {
					if(data.state == "success") {
						fPop.open('<div class="bPopTxt"><strong class="tit">添加发布任务成功！</strong><p class="con">请到发布管理栏目执行发布。</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}else{
						fPop.open('<div class="bPopTxt"><strong class="tit">添加发布任务失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				},
				error: function() {
					fPop.open('<div class="bPopTxt"><strong class="tit">添加发布任务失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}
	</script>

</body>

</html>



