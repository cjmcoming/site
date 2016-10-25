<div class="bHeader" id="bHeader">
	<div class="inner cf">
		<a class="logo" href="user_index.php">
			<strong><?=$web['name']?> CMS</strong>
		</a>
		<div class="right">
			<div class="top">您好：<em><?=$_SESSION['user_name']?></em>，欢迎登陆网站后台系统！</div>
			<div class="menu">
				<a href="user_index.php" key="index">首 页</a>
				<a href="user_column.php" key="column">栏目管理</a>
				<a href="user_publish.php" key="publish">发布管理</a>
				<a href="user_manager.php" key="manager">用户管理</a>
				<a href="javascript:logout();">退出登录</a>
			</div>
		</div>
	</div>
</div>

<script>
	function logout() {
		fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
		$.ajax({
			url: 'intLogin.php?do=logout',
			type: 'get',
			success: function(data) {
				if(data.state == "success") {
					fPop.change('<div class="bPopTxt"><strong class="tit">退出登录成功！</strong><p class="con">正在跳转到登录页面...</p></div>');
					setTimeout(function() {
						location.href = "user_login.php";
					}, 1000);
				}
			},
			error: function() {
				fPop.change('<div class="bPopTxt"><strong class="tit">退出登录失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
				setTimeout(function() {
					fPop.fade();
				}, 1500);
			}
		});
	}

	!function() {
		var $a = $("#bHeader .menu a");
		for (var i = 0; i < $a.length; i++) {
			var _sel = $a.eq(i);
			if(location.href.indexOf(_sel.attr('key') ) != -1) {
				_sel.addClass('cur');
				break;
			}
		}
	}();
</script>