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
	<div class="bMain">
		<div class="inner cf">
			<div class="bLog">
				<div class="bLogTit">用户注册</div>
				<div class="bLogCon">
					<dl class="bLogBox">
						<dt>请输入用户名：</dt>
						<dd>
							<div class="bLogInp"><p><input type="text" name="name" id="name" onpropertychange="console.log(this.value);" /></p></div>
						</dd>
					</dl>
					<dl class="bLogBox">
						<dt>请输入Email：</dt>
						<dd>
							<div class="bLogInp"><p><input type="text" name="email" id="email" /></p></div>
						</dd>
					</dl>
					<dl class="bLogBox">
						<dt>请输入登录密码：</dt>
						<dd>
							<div class="bLogInp"><p><input type="password" name="pass" id="pass" /></p></div>
						</dd>
					</dl>
					<dl class="bLogBox" id="verificationBox">
						<dt>请输入验证码：</dt>
						<dd class="cf">
							<div class="bLogInpA"><p><input type="text" name="verification" id="verification" /></p></div>
							<i class="verification"><img id="checkpic" onclick="changing();" src='intVerification.php' /></i>
						</dd>
					</dl>
					<div class="bLogTxt">同意并接受<a href="#">《服务条款》</a></div>
					<div class="bLogBtn"><a class="submit" id="submit" href="javascript:void(0);">注册</a></div>
					<div class="bLogTxt">已有帐号？ <a href="user_login.php">立即登录</a></div>
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

	function changing() {
	    $("#checkpic")[0].src="intVerification.php?"+Math.random();
	    $("#verification").val('');
	}

	!function() {

		var $submit = $("#submit");
		var $val = $("#name,#email,#pass,#verification"),
			$name = $("#name"),
			$email = $("#email"),
			$pass = $("#pass"),
			$verification = $("#verification");

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
			}else if($email.val() == '') {
				$email.focus().addClass("err").attr("placeholder", "Email不能为空！");
			}else if($pass.val() == '') {
				$pass.focus().addClass("err").attr("placeholder", "密码不能为空！");
			}else if($verification.val() == '') {
				$verification.focus().addClass("err").attr("placeholder", "验证码不能为空！");
			}else{
				fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
				$.ajax({
					url: 'intLogin.php?do=add',
					type: 'post',
					data: {
						name: $name.val(),
						email: $email.val(),
						pass: $pass.val(),
						verification: $verification.val()
					},
					success: function(data) {
						if(data.state == "success") {
							fPop.change('<div class="bPopTxt"><strong class="tit">注册成功！</strong><p class="con">正在跳转到登录页面</p></div>');
							setTimeout(function() {location.href = "user_login.php";}, 1000);
						}else{
							fPop.change('<div class="bPopTxt"><strong class="tit">注册失败！</strong><p class="con">'+data.err+'</p></div>');
							changing();
							setTimeout(function() {fPop.fade();}, 1000);
						}
					},
					error: function() {
						fPop.open('<div class="bPopTxt"><strong class="tit">注册失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
						changing();
						setTimeout(function() {fPop.fade();}, 1500);
					}
				});
			}
		});

	}();

</script>

</body>

</html>



