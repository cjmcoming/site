<?php
	require_once('reqAdmin.php');

	$do = isset($_REQUEST['do']) ? $_REQUEST['do'] : '';
	$callblack = isset($_REQUEST['callblack']) ? $_REQUEST['callblack'] : '';

	$col_id = isset($_REQUEST['col_id']) ? $_REQUEST['col_id'] : '';
	$col_id = ctype_digit($col_id) ? $col_id : 0;

	$col_parent_id = isset($_REQUEST['col_parent_id']) ? $_REQUEST['col_parent_id'] : '';
	$col_parent_id = ctype_digit($col_parent_id) ? $col_parent_id : 0;

	$return = array();
	$return['state'] = 'failed';

	if(!checkLogin()) {
		$return['err'] = '请先登录。';
	}else if($do == '') {
		$return['err'] = '未能获取操作代码，请联系管理员处理。';
	}else if($do == 'add') {
		$dbt = new dbtemplate();

		$sql1 = "INSERT INTO t_column (col_parent_id, col_create_time, col_modify_time, col_create_user, col_modify_user) VALUES (?, ?, ?, ?, ?)";
		$parameters1 = array($col_parent_id, date("YmdHis"), date("YmdHis"), $_SESSION['user_name'], $_SESSION['user_name']);
		$rs1 = $dbt->insert($sql1, $parameters1);

		if($rs1['affectedrows']) {
			$return['state'] = 'success';
			$return['newId'] = $rs1['newId'];
		}else{
			$return['err'] = '数据库操作出错，请联系管理员处理。';
		}
	}else if($do == 'modify') {
		if($col_id == 0) {
			$return['err'] = '未能获取栏目id。';
		}else{
			$col_name = isset($_POST['col_name']) ? $_POST['col_name'] : '';
			$col_title = isset($_POST['col_title']) ? $_POST['col_title'] : '';
			$col_key = isset($_POST['col_key']) ? $_POST['col_key'] : '';
			$col_intro = isset($_POST['col_intro']) ? $_POST['col_intro'] : '';
			$col_type = isset($_REQUEST['col_type']) ? $_REQUEST['col_type'] : '';
			$col_type = ctype_digit($col_type) ? $col_type : 0;
			$col_template = isset($_POST['col_template']) ? $_POST['col_template'] : '';
			$col_template_art = isset($_POST['col_template_art']) ? $_POST['col_template_art'] : '';
			$col_address = isset($_POST['col_address']) ? $_POST['col_address'] : '';

			if($col_address != '') {
				$dbt = new dbtemplate();

				$sql1 = "SELECT * FROM t_column WHERE col_id <> $col_id AND col_address = '".$col_address."'";
				if(count($dbt->queryrow($sql1) ) || $col_address == 'admin' || $col_address == 'static' || $col_address == 'detail' || $col_address == 'other' || $col_address == 'plugins' || $col_address == 'driver') {
					$return['err'] = '发布地址已被占用，请用别的路径';
				}else{
					$sql2 = "UPDATE t_column SET col_name=?, col_title=?, col_key=?, col_intro=?, col_type=?, col_template=?, col_template_art=?, col_address=?, col_modify_user=?, col_modify_time=? WHERE col_id=?";
					$parameters2 = array($col_name, $col_title, $col_key, $col_intro, $col_type, $col_template, $col_template_art, $col_address, $_SESSION['user_name'], date("YmdHis"), $col_id);
					if($dbt->update($sql2, $parameters2) ) {
						if($col_address != '/') {
							if(f_mkdir($col_address) ) {
								$return['state'] = 'success';
							}else{
								$return['err'] = '未能创建该目录，可能是权限不足，请联系管理员处理。';
							}
						}else{
							$return['state'] = 'success';
						}
					}else{
						$return['err'] = '数据库操作出错，请联系管理员处理。';
					}
				}
			}else{
				$return['err'] = '发布地址不能为空。';
			}
		}
	}else if($do == 'del') {
		if($col_id == 0) {
			$return['err'] = '未能获取栏目id。';
		}else{
			if(count(getDataColumn('parent', $col_id) ) ) {
				$return['err'] = '请先删除子栏目。';
			}else{
				$rsSon = getDataColumn('column', $col_id);
				if(!count($rsSon) ){
					$return['err'] = '数据库找不到该id所对应的栏目。';
				}else{
					$parentId = $rsSon['col_parent_id'];

					$sql1 = "UPDATE t_column SET col_state=0 WHERE col_id=?";
					$parameters1 = array($col_id);

					$dbt = new dbtemplate();
					if($dbt->update($sql1, $parameters1) ) {
						$return['state'] = 'success';
					}else{
						$return['err'] = '数据库操作出错，请联系管理员处理。';
					}
				}
				
			}
		}
	}else if($do == 'sort') {
		$columnId = isset($_POST['columnId']) ? $_POST['columnId'] : '';
		$sort = isset($_POST['sort']) ? $_POST['sort'] : '';
		$aColumnId = explode(',', $columnId);
		$aSort= explode(',', $sort);

		$aData = array();
		for ($i=0; $i < count($aColumnId); $i++) {
			if(ctype_digit($aColumnId[$i]) && is_numeric($aSort[$i]) ) {
				$aData[$aColumnId[$i]] = $aSort[$i];
			}
		}

		$ids = implode(',', array_keys($aData) );

		$sql = "UPDATE t_column SET column_sort = CASE column_id ";
		foreach ($aData as $id => $ordinal) { 
			$sql .= sprintf("WHEN %d THEN %d ", $id, $ordinal);
		}
		$sql .= "END WHERE column_id IN ($ids)";
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