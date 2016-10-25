<?php
	require_once('../admin/reqDriver.php');

	if(!$rsArt || count($rsArt) == 0) {
		die('文章不存在，请联系管理员处理。');
	}

	$colName = $rsArt['column_name'];
?>
<!doctype HTML>
<html>

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta content="telephone=no" name="format-detection" />
	<title><?=$web['name']?>_<?=$rsArt['art_title']?></title>
	<meta name="keywords" content="<?php echo $web['keywords'].','.$rsArt['art_tag']; ?>" />
	<meta name="description" content="<?=$rsArt['art_intro']?>" />
	<link rel="stylesheet" type="text/css" href="<?=$web['http']?>static/css/style.css" />

</head>

<body>

	<?php include 'inc_header.php'; ?>

	<!--main star-->
	<div class="bMain">
		<div class="inner cf">
			<div class="bMainL">
				<div class="bArticle">
					<div class="sTit">
						<h1><?=$rsArt['art_title']?></h1>
						<div class="sEditor">
							<?php
								if($rsArt['art_author'] != '') {
									echo '作者：'.$rsArt['art_author'].'　';
								}
								echo '日期：'.formatDate($rsArt['art_create_time'], 'yyyy-m-d');
							?>
						</div>
					</div>
					<div class="sArtCon" id="sArtCon">
						<?=$rsArt['art_con']?>
					</div>
					<div class="sTags">
						<?php
							if($rsArt['art_tag'] != '') {
								echo '<strong>tags：</strong>'.$rsArt['art_tag'];
							}
						?>
					</div>
				</div>
			</div>
			<div class="bMainR">
				<dl class="bSide">
					<dt>热门</dt>
					<dd>
						<ul class="bArtList">
							<?php
								$rsHotArtList = getDataHotArtList(0, 0, 10);
								$htmlHotArtList = '';
								for ($i=0; $i < count($rsHotArtList); $i++) {
									$d = $rsHotArtList[$i];
									$artHref = '/'.$d['col_address'].'/'.substr($d['art_create_time'], 0, 6).'/'.$d['art_id'].'.html';
									$htmlHotArtList .= '<li><a href="'.$artHref.'" target="_blank">'.$d['art_title'].'</a></li>';
								}
								echo $htmlHotArtList;
							?>
						</ul>
					</dd>
				</dl>
			</div>
		</div>
	</div>
	<!--main end-->
	

	<?php include 'inc_footer.php'; ?>
	<script type="text/javascript" src="<?=$web['http']?>static/js/article.js"></script>

</body>

</html>



