<?php
	require_once('reqAdmin.php');

	$rs = getDataColumn('all', true);

	$rsArtNum = getDataArtNumGroup();
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
			当前位置：<a href="user_index.php">后台首页</a> > <em>栏目管理</em>
		</div>
	</div>


	<!--main star-->
	<div class="main pColumn">

		<div class="inner">

			<dl class="bBoxA">
				<dt>
					<strong>栏目管理</strong>
					<span><a href="javascript:addColumn();">+添加顶级栏目</a></span>
				</dt>
				<dd>
					<div class="bListB" id="columnList"></div>
				</dd>
			</dl>

		</div>

	</div>
	<!--main end-->
	


	<!--footer star-->
	<?php include 'incFooter.php'; ?>
	<!--footer end-->

	<script type="text/javascript">
		var dataColumn = <?php echo json_encode($rs);?>;
		var dataArtNum = <?php echo json_encode($rsArtNum);?>;
		
		!function renderColumn() {
			function package(id) {
				var html = '',
					data = Array();
				for (var i = 0; i < dataColumn.length; i++) {
					if(dataColumn[i]['col_parent_id'] == id) {
						data.push(dataColumn[i]);
					}
				}
				if(data.length) {
					for (var j = 0; j < data.length; j++) {
						var d = data[j];

						var colName = d['col_name'] ? d['col_name'] : '暂无栏目名';

						var colUrl = d['col_address'] == '/' ? '<?=$web['http']?>index.html' : '<?=$web['http']?>'+d['col_address']+'/index.html';

						var artNum = 0;
						for (var i = 0; i < dataArtNum.length; i++) {
							if(dataArtNum[i]['art_col_id'] == d['col_id']) {
								artNum = dataArtNum[i]['art_num'];
								break;
							}
						}

						var article = '';
						if(d['col_type'] == 1 || d['col_type'] == 2) {
							article = '<a href="user_column_art.php?col_id='+d['col_id']+'">文章</a>';
						}

						html += '<div class="tr"><div class="trIn cf">';
						html += '<i class="td2">· ['+d['col_id']+'] '+colName+' ('+artNum+')</i>';
						html += '<i class="td3"><input type="text" class="inpSort" colId="'+d['col_id']+'" value="' + d['col_sort'] + '" /></i>';
						html += '<i class="td4">';
						html += '<a href="user_preview.php?type=column&id='+d['col_id']+'" target="_blank">预览</a>';
						html += '<a href="javascript:addPublish(\'column\', '+d['col_id']+');">发布栏目</a>';
						html += '<a href="javascript:addPubColArtConfirm(\'colArt\', '+d['col_id']+');">发布文章</a>';
						html += '<a href="javascript:addColumn('+d['col_id']+');">增加子栏目</a>';
						html += '<a href="user_column_mod.php?col_id='+d['col_id']+'">修改</a>';
						html += '<a href="javascript:delColumnConfirm('+d['col_id']+');">删除</a>';
						html += '<a href="user_column_blo.php?col_id='+d['col_id']+'">内嵌</a>';
						html += article;
						html += '</i>';
						html += '<i class="td5"><a href="'+colUrl+'" target="_blank">'+d['col_address']+'</a></i></div>';
						html += arguments.callee(d['col_id']);
						html += '</div>';
					}
				}
				return html;
			}
			//console.log(package(0));
			var html = package(0);
			if(html == '') {
				html = '<div style="text-align:center; line-height:100px;">暂无栏目</div>';
			}else{
				html = '<div class="tr title"><div class="trIn cf"><i class="td2">[id] 栏目名称 (文章或产品数量)</i><i class="td3">排序</i><i class="td4">操作</i><i class="td5">发布地址</i></div></div>' + html + '<div class="tr"><div class="trIn cf"><i class="td3"><a class="bBtnA" href="javascript:sortColumn();">保存排序</a></i></div></div>';
			}
			$("#columnList").html(html);
		}();


		function addColumn(id) {
			var url;
			if(id) {
				url = 'intColumn.php?do=add&col_parent_id=' + id;
			}else{
				url = 'intColumn.php?do=add';
			}
			fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
			$.ajax({
				url: url,
				type: 'get',
				success: function(data) {
					if(data.state == "success") {
						fPop.change('<div class="bPopTxt"><strong class="tit">添加栏目成功！</strong><p class="con">正在跳转到栏目修改页面</p></div>');
						setTimeout(function() {location.href = "user_column_mod.php?col_id=" + data.newId;}, 1500);
					}else{
						fPop.change('<div class="bPopTxt"><strong class="tit">添加栏目失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				},
				error: function() {
					fPop.change('<div class="bPopTxt"><strong class="tit">添加栏目失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}


		function delColumnConfirm(id) {
			fPop.open('<div class="bPopTxt"><strong class="tit">删除栏目！</strong><p class="con">您确定要删除该栏目吗？要先删除子栏目才能删除父栏目。</p><div class="btn cf"><a class="bBtnA" href="javascript:fPop.fade();">取消</a><a class="bBtnA" href="javascript:delColumn('+id+');">确定</a></div>');
		}
		function delColumn(id) {
			fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
			$.ajax({
				url: 'intColumn.php?do=del&col_id=' + id,
				type: 'get',
				success: function(data) {
					if(data.state == "success") {
						fPop.open('<div class="bPopTxt"><strong class="tit">删除栏目成功！</strong><p class="con">正在刷新页面</p></div>');
						setTimeout(function() {location.reload();}, 1500);
					}else{
						fPop.open('<div class="bPopTxt"><strong class="tit">删除栏目失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				},
				error: function() {
					fPop.open('<div class="bPopTxt"><strong class="tit">删除栏目失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}


		function sortColumn() {
			var $inpSort = $("#columnList .inpSort");
			var sColId = '', sSort = '';
			$inpSort.each(function(i) {
				var val = $(this).val(), oldVal = $(this)[0].defaultValue, colId = $(this).attr('colId');
				console.log(oldVal);
				if(val != oldVal) {
					sColId += colId + ',';
					sSort += val + ',';
				}
			});

			if(sColId == '') {
				fPop.open('<div class="bPopTxt"><strong class="tit">修改排序</strong><p class="con">没有可更新的排序。</p></div>');
				setTimeout(function() {fPop.fade();}, 1500);
			}else{
				sColId = sColId.substr(0, sColId.length-1);
				sSort = sSort.substr(0, sSort.length-1);

				$.ajax({
					url: 'intColumn.php?do=sort',
					type: 'post',
					data: {
						colId: sColId,
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

		function addPubColArtConfirm(type, id) {
			fPop.open('<div class="bPopTxt"><strong class="tit">添加发布任务</strong><p class="con">您确定要重新发布该栏目下面的所有文章吗？</p><div class="btn cf"><a class="bBtnA" href="javascript:fPop.fade();">取消</a><a class="bBtnA" href="javascript:addPublish(\'colArt\', '+id+');">确定</a></div>');
		}
		function addPublish(type, id) {
			var url = '';
			if(type == 'column') {
				url = 'intPublish.php?do=addCol&col_id=' + id;
			}else if(type == 'colArt') {
				url = 'intPublish.php?do=addColArt&col_id=' + id;
			}else if(type == 'article') {
				url = 'intPublish.php?do=addArt&article_id=' + id;
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



