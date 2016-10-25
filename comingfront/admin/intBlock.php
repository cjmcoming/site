<?php
	require_once('reqAdmin.php');

	$do = isset($_REQUEST['do']) ? $_REQUEST['do'] : '';
	$col_id = isset($_REQUEST['col_id']) ? $_REQUEST['col_id'] : '';
	$blo_id = isset($_REQUEST['blo_id']) ? $_REQUEST['blo_id'] : '';
	$callblack = isset($_REQUEST['callblack']) ? $_REQUEST['callblack'] : '';

	$return = array();
	$return['state'] = 'failed';

	if(!checkLogin()) {
		$return['err'] = '请先登录。';
	}else if($do == '') {
		$return['err'] = '未能获取操作代码，请联系管理员处理。';
	}else if($do == 'add') {
		if($col_id == '') {
			$return['err'] = '未能获取栏目id，请联系管理员处理。';
		}else{
			$sql = "INSERT INTO t_block (blo_col_id, blo_create_time, blo_modify_time, blo_create_user, blo_modify_user) VALUES (?, ?, ?, ?, ?)";
			$parameters = array($col_id, date("YmdHis"), date("YmdHis"), $_SESSION['user_name'], $_SESSION['user_name']);
			$dbt = new dbtemplate();
			$rs = $dbt->insert($sql, $parameters);
			if($rs['affectedrows']) {
				$return['state'] = 'success';
				$return['newId'] = $rs['newId'];
			}else{
				$return['err'] = '数据库操作出错，请联系管理员处理。';
			}
		}	
	}else if($do == 'modify') {
		if($blo_id == '') {
			$return['err'] = '未能获取栏目代码块id，请联系管理员处理。';
		}else{
			$blo_title = isset($_POST['blo_title']) ? $_POST['blo_title'] : '';
			$blo_con = isset($_POST['blo_con']) ? $_POST['blo_con'] : '';

			$dbt = new dbtemplate();
			$sql = "UPDATE t_block SET blo_title=?, blo_con=?, blo_modify_time=?, blo_modify_user=? WHERE blo_id=?";
			$parameters = array($blo_title, $blo_con, date("YmdHis"), $_SESSION['user_name'], $blo_id);
			if($dbt->update($sql, $parameters)) {
				$return['state'] = 'success';
			}else{
				$return['err'] = '数据库操作出错，请联系管理员处理。';
			}
		}
	}else if($do == 'del') {
		if($blo_id == '') {
			$return['err'] = '未能获取栏目代码块id，请联系管理员处理。';
		}else{
			$sql = "UPDATE t_block SET blo_state=0 WHERE blo_id=?";
			$parameters = array($blo_id);
			$dbt = new dbtemplate();
			if($dbt->update($sql, $parameters)) {
				$return['state'] = 'success';
			}else{
				$return['err'] = '数据库操作出错，请联系管理员处理。';
			}
		}
	}else if($do == 'sort') {
		$blockId = isset($_POST['blockId']) ? $_POST['blockId'] : '';
		$sort = isset($_POST['sort']) ? $_POST['sort'] : '';
		$aBlockId = explode(',', $blockId);
		$aSort= explode(',', $sort);

		$aData = array();
		for ($i=0; $i < count($aBlockId); $i++) {
			if(ctype_digit($aBlockId[$i]) && is_numeric($aSort[$i]) ) {
				$aData[$aBlockId[$i]] = $aSort[$i];
			}
		}

		$ids = implode(',', array_keys($aData) );

		$sql = "UPDATE t_block SET blo_sort = CASE blo_id ";
		foreach ($aData as $id => $ordinal) { 
			$sql .= sprintf("WHEN %d THEN %d ", $id, $ordinal);
		}
		$sql .= "END WHERE blo_id IN ($ids)";
		$dbt = new dbtemplate();
		if($dbt->update($sql) ) {
			$return['state'] = 'success';
		}else{
			$return['err'] = '数据库操作出错，请联系管理员处理。';
		}
	}

	header('Content-type: text/json');
	if($callblack != '') {
		echo 'if('.$callblack.'){'.$callblack.'('.json_encode($return).');}';
	}else{
		echo json_encode($return);
	}
?>