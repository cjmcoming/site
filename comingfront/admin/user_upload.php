<?php
	require_once('reqAdmin.php');

	$value = '';
	if($_FILES["file"]["size"] < 10000000) {
		if($_FILES["file"]["error"] > 0) {
			$nErr = $_FILES["file"]["error"];
			if($nErr == 1 || $nErr == 2) {
				$value = '0|文件k数过大，请压缩小一些再上传！';
			}else if($nErr == 3) {
				$value = '0|文件只有部分被上传，请重新上传！';
			}else if($nErr == 4) {
				$value = '0|没有文件被上传！';
			}else{
				$value = '0|'.$_FILES["file"]["error"];
			}
		}else{
			$aExtName = explode('|', $web['imgType']);
			$fExtName = strtolower(substr(strrchr($_FILES["file"]['name'], '.'), 1) );
			if(in_array($fExtName, $aExtName) ) {
				$fileName = date('Ymd_His').'_'.mt_rand(100,999).'.'.$fExtName;
				if(file_exists('../img/'.date("Ymd").'/'.$fileName)) {
					$value = '0|文件：'.$fileName.'已经存在，请重新上传！';
				}else{
					if(f_mkdir('img/'.date("Ymd")) ) {
						if(move_uploaded_file($_FILES["file"]["tmp_name"], '../img/'.date("Ymd").'/'.$fileName) ) {
							$link = 'img/'.date("Ymd").'/'.$fileName;
							$value = '1|'.$link.'|'.$_FILES["file"]["name"].'|'.$_FILES["file"]["type"].'|'.($_FILES["file"]["size"] / 1024).'k|'.$_FILES["file"]["tmp_name"];
						}else{
							$value = '0|未能把文件从临时文件夹移到网站目录，请联系管理员处理。';
						}
					}else{
						$value = '0|未能创建文件夹，可能权限不足，请联系管理员处理。';
					}
				}
			}else{
				$value = '0|只能上传图片类型的文件！';
			}
		}
	}else{
		$value = '0|文件不合法或者文件容量超过1M！';
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

</head>

<body>
	<?php echo '<textarea>'.$value.'</textarea>'; ?>
</body>
</html>