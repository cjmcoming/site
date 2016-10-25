<?php
	require_once('reqAdmin.php');

	$do = isset($_REQUEST['do']) ? $_REQUEST['do'] : '';
	$callblack = isset($_REQUEST['callblack']) ? $_REQUEST['callblack'] : '';

	$img_id = isset($_REQUEST['img_id']) ? $_REQUEST['img_id'] : '';
	$img_id = ctype_digit($img_id) ? $img_id : 0;

	$col_id = isset($_REQUEST['col_id']) ? $_REQUEST['col_id'] : '';
	$col_id = ctype_digit($col_id) ? $col_id : 0;

	$blo_id = isset($_REQUEST['blo_id']) ? $_REQUEST['blo_id'] : 0;
	$blo_id = ctype_digit($blo_id) ? $blo_id : 0;

	$art_id = isset($_REQUEST['art_id']) ? $_REQUEST['art_id'] : 0;
	$art_id = ctype_digit($art_id) ? $art_id : 0;

	$return = array();
	$return['state'] = 'failed';

	if(!checkLogin()) {
		$return['err'] = '请先登录。';
	}else if($do == '') {
		$return['err'] = '未能获取操作代码，请联系管理员处理。';
	}else if($do == 'add') {
		$img_url = isset($_POST['img_url']) ? $_POST['img_url'] : '';
		$sql = "INSERT INTO t_image (img_col_id, img_blo_id, img_art_id, img_url, img_create_time, img_modify_time, img_create_user, img_modify_user) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
		$parameters = array($col_id, $blo_id, $art_id, $img_url, date("YmdHis"), date("YmdHis"), $_SESSION['user_name'], $_SESSION['user_name']);
		$dbt = new dbtemplate();
		$rs = $dbt->insert($sql, $parameters);
		if($rs['affectedrows']) {
			$return['state'] = 'success';
			$return['newId'] = $rs['newId'];
		}else{
			$return['err'] = '数据库操作出错，请联系管理员处理。';
		}
	}else if($do == 'del') {
		if($img_id == '') {
			$return['err'] = '未能获取图片id。';
		}else{
			$dbt = new dbtemplate();
			$sql = "UPDATE t_image SET img_state=0 WHERE img_id=?";
			$parameters = array($img_id);
			if($dbt->update($sql, $parameters)) {
				$return['state'] = 'success';
			}else{
				$return['err'] = '数据库操作出错，请联系管理员处理。';
			}
		}
	}

	header('Content-type: text/json');
	if($callblack != '') {
		echo 'if('.$callblack.'){'.$callblack.'('.json_encode($return).');}';
	}else{
		echo json_encode($return);
	}
?>