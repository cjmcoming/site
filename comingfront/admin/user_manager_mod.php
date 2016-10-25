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



	<!--main star-->
	<div class="bMain pLogin">
		<div class="inner cf">
			<div class="bLog">
				<div class="bLogTit">修改密码</div>
				<div class="bLogCon">
					<dl class="bLogBox">
						<dt>用户名：</dt>
						<dd style="line-height: 34px; text-align: center;">
							<?=$_SESSION['user_name']?>
						</dd>
					</dl>
					<dl class="bLogBox">
						<dt>请输入原密码：</dt>
						<dd>
							<div class="bLogInp"><p><input type="password" id="pass" /></p></div>
						</dd>
					</dl>
					<dl class="bLogBox">
						<dt>请输入新密码：</dt>
						<dd>
							<div class="bLogInp"><p><input type="password" id="passNewA" /></p></div>
						</dd>
					</dl>
					<dl class="bLogBox">
						<dt>请再次输入新密码：</dt>
						<dd>
							<div class="bLogInp"><p><input type="password" id="passNewB" /></p></div>
						</dd>
					</dl>
					<div class="bLogBtn"><input class="submit" type="button" value="提交" onclick="modifyPass();" /></div>
				</div>
			</div>
		</div>
	</div>
	<!--main end-->
	


	<!--footer star-->
	<div class="bFooter">Copyright © runingfront<br />粤ICP备xxx号</div>
	<!--footer end-->

	<!--pop star-->
	<div class="bPopMark" id="bPopMark"></div>
	<div class="bPopBox" id="bPopBox"></div>
	<!--pop end-->



<script type="text/javascript" src="../static/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../static/js/admin_base.js"></script>

<script>

	function modifyPass(){

		var $pass = $("#pass"),
			$passNewA = $("#passNewA"),
			$passNewB = $("#passNewB");


	    if($pass.val() == '') {
			$pass.focus().addClass("err").attr("placeholder", "密码不能为空！");
		}else if($passNewA.val() == '') {
			$passNewA.focus().addClass("err").attr("placeholder", "新密码不能为空！");
		}else if($passNewB.val() == '') {
			$passNewB.focus().addClass("err").attr("placeholder", "重复新密码不能为空！");
		}else if($passNewA.val() != $passNewB.val()) {
			$passNewB.focus().addClass("err").attr("placeholder", "2次输入的新密码不一样！");
		}else{
			fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
			$.ajax({
				url: 'intLogin.php?do=modify',
				type: 'post',
				data: {
					pass:$pass.val(),
					passNewA: $("#passNewA").val(),
					passNewB: $("#passNewB").val()
				},
				success: function(data) {
					if(data.state == "success") {
						fPop.change('<div class="bPopTxt"><strong class="tit">修改密码成功！</strong><p class="con">请重新登录！</p></div>');
						setTimeout(function() {logout();}, 1000);
					}else{
						fPop.change('<div class="bPopTxt"><strong class="tit">修改密码失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				},
				error: function() {
					fPop.open('<div class="bPopTxt"><strong class="tit">修改密码失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}
	}

</script>

</body>

</html>



