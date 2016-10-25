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

	$rsBlock = getDataBlock('column', $col_id);
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
			当前位置：<a href="user_index.php">后台首页</a> >
			<a href="user_column.php">栏目管理</a> >
			<em><?=$col_name?>-内嵌列表</em>
		</div>
	</div>


	<!--main star-->
	<div class="main pBloList">

		<div class="inner">

			<dl class="bBoxA">
				<dt>
					<strong><?=$col_name?>-内嵌列表</strong>
					<span><a href="javascript:addBlock(<?=$col_id?>);">+添加内嵌</a></span>
				</dt>
				<dd>
					<?php
						if(!count($rsBlock) ) {
							echo '<div style="text-align:center; line-height:100px;">暂无内嵌</div>';
						}else{
							$html = '<ul class="bListA" id="blockList"><li class="title cf"><i class="td1">ID</i><i class="td2">内嵌标题</i><i class="td3">创建时间</i><i class="td4">创建人</i><i class="td3">更新时间</i><i class="td4">更新人</i><i class="td5">操作</i><i class="td6">排序</i></li>';
							for ($i=0; $i < count($rsBlock); $i++) {
								$d = $rsBlock[$i];
								$name = $d['blo_title'] == '' ? '暂无标题' : $d['blo_title'];
								$html .= '<li class="cf">';
								$html .= '<i class="td1">'.$d['blo_id'].'</i>';
								$html .= '<i class="td2">'.$name.'</i>';
								$html .= '<i class="td3">'.formatDate($d['blo_create_time'], 'yyyy-mm-dd').'</i>';
								$html .= '<i class="td4">'.$d['blo_create_user'].'</i>';
								$html .= '<i class="td3">'.formatDate($d['blo_modify_time'], 'yyyy-mm-dd').'</i>';
								$html .= '<i class="td4">'.$d['blo_modify_user'].'</i>';
								$html .= '<i class="td5"><a href="user_column_blo_mod.php?blo_id='.$d['blo_id'].'">修改</a><a href="javascript:delBlockConfirm('.$d['blo_id'].');">删除</a></i>';
								$html .= '<i class="td6"><input type="text" class="inpSort" blockId="'.$d['blo_id'].'" value="'.$d['blo_sort'].'" /></i>';
								$html .= '</li>';
							}
							$html .= '<li class="cf"><i class="sortBtn"><a class="bBtnA" href="javascript:sortBlock();">保存排序</a></i></li></ul>';
							echo $html;
						}
					?>
				</dd>
			</dl>

		</div>

	</div>
	<!--main end-->
	


	<!--footer star-->
	<?php include 'incFooter.php'; ?>
	<!--footer end-->

	<script type="text/javascript">
		function addBlock(id) {
			fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
			$.ajax({
				url: 'intBlock.php?do=add&col_id=' + id,
				type: 'get',
				success: function(data) {
					if(data.state == "success") {
						fPop.change('<div class="bPopTxt"><strong class="tit">添加内嵌成功！</strong><p class="con">正在跳转到内嵌修改页面</p></div>');
						setTimeout(function() {location.href = "user_column_blo_mod.php?blo_id=" + data.newId;}, 1500);
					}else{
						fPop.change('<div class="bPopTxt"><strong class="tit">添加内嵌失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				},
				error: function() {
					fPop.change('<div class="bPopTxt"><strong class="tit">添加内嵌失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}


		function delBlockConfirm(id) {
			fPop.open('<div class="bPopTxt"><strong class="tit">删除内嵌！</strong><p class="con">您确定要删除该内嵌吗？</p><div class="btn cf"><a class="bBtnA" href="javascript:fPop.fade();">取消</a><a class="bBtnA" href="javascript:delBlock('+id+');">确定</a></div>');
		}

		function delBlock(id) {
			fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
			$.ajax({
				url: 'intBlock.php?do=del&blo_id=' + id,
				type: 'get',
				success: function(data) {
					if(data.state == "success") {
						fPop.open('<div class="bPopTxt"><strong class="tit">删除内嵌成功！</strong><p class="con">正在刷新页面</p></div>');
						setTimeout(function() {location.reload();}, 1500);
					}else{
						fPop.open('<div class="bPopTxt"><strong class="tit">删除内嵌失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				},
				error: function() {
					fPop.open('<div class="bPopTxt"><strong class="tit">删除内嵌失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}


		function sortBlock() {
			var $inpSort = $("#blockList .inpSort");
			var sId = '', sSort = '';
			$inpSort.each(function(i) {
				var val = $(this).val(), oldVal = $(this)[0].defaultValue, id = $(this).attr('blockId');
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
					url: 'intBlock.php?do=sort',
					type: 'post',
					data: {
						blockId: sId,
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
	</script>

</body>

</html>



