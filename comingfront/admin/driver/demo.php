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
	<meta name="keywords" content="<?php echo $web['keywords']; ?>" />
	<meta name="description" content="<?=$web['description']?>" />
	<link rel="stylesheet" type="text/css" href="<?=$web['http']?>static/css/reset.css" />

</head>

<body>

	<?=$rsArt['art_con']?>

</body>

</html>



