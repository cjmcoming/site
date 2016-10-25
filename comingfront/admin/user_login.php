<?php
	require_once('reqSet.php');
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

</head>

<body>

	<!--top star-->
	<div class="bHeader" id="bHeader">
		<div class="inner cf">
			<a class="logo" href="javascript:void(0);">
				<strong><?=$web['name']?>后台管理系统</strong>
			</a>
		</div>
	</div>
	<!--top end-->



	<!--main star-->
	<div class="bMain pLogin">
		<div class="inner cf">
			<div class="bLog">
				<div class="bLogTit">用户登录</div>
				<div class="bLogCon">
					<dl class="bLogBox">
						<dt>请输入用户名：</dt>
						<dd>
							<div class="bLogInp"><p><input type="text" id="name" /></p></div>
						</dd>
					</dl>
					<dl class="bLogBox">
						<dt>请输入登录密码：</dt>
						<dd>
							<div class="bLogInp"><p><input type="password" id="pass" /></p></div>
						</dd>
					</dl>
					<div class="bLogBtn"><input class="submit" type="submit" value="登录" name="submit" id="submit" /></div>
					<div class="bLogTxt">还没有帐号？ <a href="user_register.php">立即注册</a></div>
				</div>
			</div>
		</div>
	</div>
	<!--main end-->
	


	<div class="bFooter">Copyright © comingcoder</div>
	<div class="bPopMark" id="bPopMark"></div>
	<div class="bPopBox" id="bPopBox"></div>



<script type="text/javascript" src="<?=$web['http']?>static/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?=$web['http']?>static/js/admin_base.js"></script>

<script>

	!function() {

		var $submit = $("#submit");
		var $val = $("#name,#pass"),
			$name = $("#name"),
			$pass = $("#pass");

		$val.each(function() {
			$(this).on("keyup", function() {
				if($(this).val() != "") {
					$(this).removeClass("err");
				}
			});
		});

		$submit.click(function() {
			if($name.val() == '') {
				$name.focus().addClass("err").attr("placeholder", "用户名不能为空！");
			}else if($pass.val() == '') {
				$pass.focus().addClass("err").attr("placeholder", "密码不能为空！");
			}else{
				fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
				$.ajax({
					url: 'intLogin.php?do=log',
					type: 'post',
					data: {
						name:$name.val(),
						pass:$pass.val()
					},
					success: function(data) {
						if(data.state == "success") {
							fPop.change('<div class="bPopTxt"><strong class="tit">登录成功！</strong><p class="con">正在跳转到后台首页</p></div>');
							setTimeout(function() {
								location.href = "user_index.php";
							}, 1000);
						}else{
							fPop.change('<div class="bPopTxt"><strong class="tit">登录失败！</strong><p class="con">'+data.err+'</p></div>');
							setTimeout(function() {
								fPop.fade();
							}, 1500);
						}
					}
				});
			}
		});

	}();

</script>

</body>

</html>



