<?php
	require_once('../admin/reqDriver.php');
	if(!$rsCol || count($rsCol) == 0) {
		die('栏目不存在，请联系管理员处理。');
	}
	if(!$page || !$pageSize) {
		die('未能获取页码，请联系管理员处理。');
	}
	$rsArtList1 = getDataArtList(0, ($page-1)*$pageSize, $pageSize, 2);
	$rsArtList = $rsArtList1['rs'];
?>
<!doctype HTML>
<html>

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta content="telephone=no" name="format-detection" />
	<title><?=$web['name']?>_<?=$rsCol['col_title']?></title>
	<meta name="keywords" content="<?php echo $web['keywords'].','.$rsCol['col_key']; ?>" />
	<meta name="description" content="<?=$rsCol['col_intro']?>" />
	<link rel="stylesheet" type="text/css" href="<?=$web['http']?>static/css/style.css" />

</head>

<body>

	<?php include 'inc_header.php'; ?>


	<!--main star-->
	<div class="bMain">
		<div class="inner cf">
			<div class="bMainL">
				<ul class="bList">
					<?php
						$htmlArtList = '';
						for ($i=0; $i < count($rsArtList); $i++) {
							$d = $rsArtList[$i];
							$url = $web['http'].$d['col_address'].'/'.substr($d['art_create_time'], 0, 6).'/'.$d['art_id'].'.html';
							$htmlArtList .= '<li>';
							$htmlArtList .= '<strong class="sTit"><a href="'.$url.'">'.$d['art_title'].'</a></strong>';
							$htmlArtList .= '<span class="sDate">创建于 '.formatDate($d['art_create_time'], 'yyyy-m-d').'</span>';
							$htmlArtList .= '<p class="sCon">'.$d['art_intro'].'...</p>';
							$htmlArtList .= '<span class="sBot"><a href="'.$url.'">查看全文</a></span>';
							$htmlArtList .= '</li>';
						}
						echo $htmlArtList;
					?>
				</ul>
				
				{{getPageHtml(-1,false,20,2)}}

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

</body>

</html>



