<?php
	require_once('reqAdmin.php');

	$dbt = new dbtemplate();
	if($_SESSION['user_state'] == 4) {
		$sql = "SELECT * FROM t_user ORDER BY user_sort DESC, user_id DESC";
		$rs = $dbt->queryrows($sql);
	}else if($_SESSION['user_state'] == 3) {
		$sql = "SELECT * FROM t_user WHERE user_state > 0 and user_state < 4 ORDER BY user_sort DESC, user_id DESC";
		$rs = $dbt->queryrows($sql);
	}else{
		$sql = "SELECT * FROM t_user WHERE user_name = ?";
		$parameters = array($_SESSION['user_name']);
		$rs = $dbt->queryrows($sql, $parameters);
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
			当前位置：<a href="user_index.php">后台首页</a> > <em>用户管理</em>
		</div>
	</div>


	<!--main star-->
	<div class="main pManager">

		<div class="inner">

			<dl class="bBoxA">
				<dt>
					<strong>用户管理</strong>
				</dt>
				<dd>
					<ul class="bListA" id="userList">
						<li class="title cf">
							<i class="td1">用户名</i>
							<i class="td2">Email</i>
							<i class="td3">状态</i>
							<i class="td4">注册时间</i>
							<i class="td4">更新时间</i>
							<i class="td5">更新人</i>
							<i class="td6">操作</i>
							<i class="td7">排序</i>
						</li>
						<?php
							$html = '';
							for ($i = 0; $i < count($rs); $i++) {
								$d = $rs[$i];
								$state = '删除';
								if($d['user_state'] == 1) {
									$state = '禁用';
								}else if($d['user_state'] == 2) {
									$state = '正常';
								}else if($d['user_state'] == 3) {
									$state = '管理员';
								}else if($d['user_state'] == 4) {
									$state = '超级管理员';
								}
								$html .= '<li class="cf"><i class="td1">'.$d['user_name'].'</i>';
								$html .= '<i class="td2">'.$d['user_email'].'</i>';
								$html .= '<i class="td3">'.$state.'</i>';
								$html .= '<i class="td4">'.formatDate($d['user_login_time'], 'yyyy-mm-dd').'</i>';
								$html .= '<i class="td4">'.formatDate($d['user_modify_time'], 'yyyy-mm-dd').'</i>';
								$html .= '<i class="td5">'.$d['user_modify_user'].'</i>';
								$html .= '<i class="td6">';
								if($d['user_name'] == $_SESSION['user_name']) {
									$html .= '<a href="user_manager_mod.php">修改密码</a>';
								}else if($_SESSION['user_state'] > 2) {
									if($d['user_state'] == 0) {
										$html .= '<a href="javascript:operateConfirm(\'manager\','.$d['user_id'].');">设为管理员</a><a href="javascript:operateConfirm(\'normal\','.$d['user_id'].');">设为正常</a><a href="javascript:operateConfirm(\'disable\','.$d['user_id'].');">设为禁用</a>';
									}else if($d['user_state'] == 1) {
										$html .= '<a href="javascript:operateConfirm(\'manager\','.$d['user_id'].');">设为管理员</a><a href="javascript:operateConfirm(\'normal\','.$d['user_id'].');">设为正常</a><a href="javascript:operateConfirm(\'del\','.$d['user_id'].');">删除</a>';
									}else if($d['user_state'] == 2) {
										$html .= '<a href="javascript:operateConfirm(\'manager\','.$d['user_id'].');">设为管理员</a><a href="javascript:operateConfirm(\'disable\','.$d['user_id'].');">设为禁用</a><a href="javascript:operateConfirm(\'del\','.$d['user_id'].');">删除</a>';
									}else if($d['user_state'] == 3) {
										$html .= '<a href="javascript:operateConfirm(\'normal\','.$d['user_id'].');">设为正常</a><a href="javascript:operateConfirm(\'disable\','.$d['user_id'].');">设为禁用</a><a href="javascript:operateConfirm(\'del\','.$d['user_id'].');">删除</a>';
									}
								}
								$html .= '</i>';
								if($_SESSION['user_state'] > 2) {
									$html .= '<i class="td7"><input type="text" class="inpSort" userId="'.$d['user_id'].'" value="'.$d['user_sort'].'" /></i></li>';
								}
								$html .= '</li>';
							}
							if($_SESSION['user_state'] > 2) {
								$html .= '<li class="cf"><i class="sortBtn"><a class="bBtnA" href="javascript:sortUser();">保存排序</a></i></li>';
							}
							echo $html;
						?>
						
						
					</ul>
				</dd>
			</dl>

		</div>

	</div>
	<!--main end-->
	


	<!--footer star-->
	<?php include 'incFooter.php'; ?>
	<!--footer end-->

	<script type="text/javascript">

		function operateConfirm(type, id) {
			var tit, con;
			if(type == 'manager') {
				tit = '设为管理员';
				con = '您确定要把该用户权限设置为管理员吗？';
			}else if(type == 'normal') {
				tit = '设为正常';
				con = '您确定要把该用户权限设置为正常吗？';
			}else if(type == 'disable') {
				tit = '设为禁用';
				con = '您确定要把该用户权限设置为禁用吗？';
			}else if(type == 'del') {
				tit = '删除用户';
				con = '您确定要把该用户删除吗？';
			}
			fPop.open('<div class="bPopTxt"><strong class="tit">'+tit+'</strong><p class="con">'+con+'</p><div class="btn cf"><a class="bBtnA" href="javascript:fPop.fade();">取消</a><a class="bBtnA" href="javascript:operateFunc('+id+',\''+type+'\',\''+tit+'\');">确定</a></div>');
		}
		function operateFunc(id, type, tit) {
			fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
			$.ajax({
				url: 'intLogin.php?do='+type+'&user_id=' + id,
				type: 'get',
				success: function(data) {
					if(data.state == "success") {
						fPop.open('<div class="bPopTxt"><strong class="tit">'+tit+'成功！</strong><p class="con">正在刷新页面</p></div>');
						setTimeout(function() {location.reload();}, 1500);
					}else{
						fPop.open('<div class="bPopTxt"><strong class="tit">'+tit+'失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				},
				error: function() {
					fPop.open('<div class="bPopTxt"><strong class="tit">'+tit+'失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}


		function sortUser() {
			var $inpSort = $("#userList .inpSort");
			var sId = '', sSort = '';
			$inpSort.each(function(i) {
				var val = $(this).val(), oldVal = $(this)[0].defaultValue, id = $(this).attr('userId');
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
					url: 'intLogin.php?do=sort',
					type: 'post',
					data: {
						userId: sId,
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



