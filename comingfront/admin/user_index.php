<?php
	require_once('reqAdmin.php');

	$sqlUser = "SELECT COUNT(*) FROM t_user WHERE user_state > 0 and user_state < 4";
	$sqlCol = "SELECT COUNT(*) FROM t_column WHERE col_state > 0";
	$sqlArt = "SELECT COUNT(*) FROM t_article WHERE art_state > 0";
	$sqlImg = "SELECT COUNT(*) FROM t_image WHERE img_state = 1";
	$sqlPub = "SELECT COUNT(*) FROM t_publish";

	$dbt = new dbtemplate();

	$countUser = $dbt->queryforobject($sqlUser);
	$countCol = $dbt->queryforobject($sqlCol);
	$countArt = $dbt->queryforobject($sqlArt);
	$countImg = $dbt->queryforobject($sqlImg);
	$countPub = $dbt->queryforobject($sqlPub);
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
			当前位置：<em>后台首页</em>
		</div>
	</div>


	<!--main star-->
	<div class="main pIndex">

		<div class="inner">

			<dl class="bBoxA">
				<dt>网站统计信息</dt>
				<dd>
					<ul class="bListA">
						<li class="cf">
							<i class="tit">用户数：</i>
							<i class="con"><?=$countUser?></i>
						</li>
						<li class="cf">
							<i class="tit">栏目数：</i>
							<i class="con"><?=$countCol?></i>
						</li>
						<li class="cf">
							<i class="tit">文章数：</i>
							<i class="con"><?=$countArt?></i>
						</li>
						<li class="cf">
							<i class="tit">图片数：</i>
							<i class="con"><?=$countImg?></i>
						</li>
						<li class="cf">
							<i class="tit">发布量：</i>
							<i class="con"><?=$countPub?></i>
						</li>
					</ul>
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



