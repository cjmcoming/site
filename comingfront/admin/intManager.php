<?php
	require_once('reqAdmin.php');

	$do = isset($_REQUEST['do']) ? $_REQUEST['do'] : '';
	$callblack = isset($_REQUEST['callblack']) ? $_REQUEST['callblack'] : '';

	$column_id = isset($_REQUEST['column_id']) ? $_REQUEST['column_id'] : '';
	$column_id = ctype_digit($column_id) ? $column_id : 0;

	$column_parent_id = isset($_REQUEST['column_parent_id']) ? $_REQUEST['column_parent_id'] : '';
	$column_parent_id = ctype_digit($column_parent_id) ? $column_parent_id : 0;

	$return = array();
	$return['state'] = 'failed';

	if(!checkLogin()) {
		$return['err'] = '请先登录。';
	}else if($do == '') {
		$return['err'] = '未能获取操作代码，请联系管理员处理。';
	}else if($do == 'add') {
		$dbt = new dbtemplate();

		$sql1 = "INSERT INTO t_column (column_parent_id, column_create_time, column_modify_time, column_create_user, column_modify_user) VALUES (?, ?, ?, ?, ?)";
		$parameters1 = array($column_parent_id, date("YmdHis"), date("YmdHis"), $_SESSION['user_name'], $_SESSION['user_name']);
		$rs1 = $dbt->insert($sql1, $parameters1);

		if($rs1['affectedrows']) {
			$return['state'] = 'success';
			$return['newId'] = $rs1['newId'];
		}else{
			$return['err'] = '数据库操作出错，请联系管理员处理。';
		}
	}else if($do == 'modify') {
		if($column_id == 0) {
			$return['err'] = '未能获取栏目id。';
		}else{
			$column_name = isset($_POST['column_name']) ? $_POST['column_name'] : '';
			$column_img = isset($_POST['column_img']) ? $_POST['column_img'] : '';
			$column_title = isset($_POST['column_title']) ? $_POST['column_title'] : '';
			$column_key = isset($_POST['column_key']) ? $_POST['column_key'] : '';
			$column_intro = isset($_POST['column_intro']) ? $_POST['column_intro'] : '';
			$column_type = isset($_REQUEST['column_type']) ? $_REQUEST['column_type'] : '';
			$column_type = ctype_digit($column_type) ? $column_type : 0;
			$column_template = isset($_POST['column_template']) ? $_POST['column_template'] : '';
			$column_template_art = isset($_POST['column_template_art']) ? $_POST['column_template_art'] : '';
			$column_address = isset($_POST['column_address']) ? $_POST['column_address'] : '';

			$dbt = new dbtemplate();
			$sql = "UPDATE t_column SET column_name=?, column_img=?, column_title=?, column_key=?, column_intro=?, column_type=?, column_template=?, column_template_art=?, column_address=?, column_modify_user=?, column_modify_time=? WHERE column_id=?";
			$parameters = array($column_name, $column_img, $column_title, $column_key, $column_intro, $column_type, $column_template, $column_template_art, $column_address, $_SESSION['user_name'], date("YmdHis"), $column_id);
			if($dbt->update($sql, $parameters)) {
				$return['state'] = 'success';
			}else{
				$return['err'] = '数据库操作出错，请联系管理员处理。';
			}
		}
	}else if($do == 'del') {
		if($column_id == 0) {
			$return['err'] = '未能获取栏目id。';
		}else{
			if(count(getDataColumn('parent', $column_id) ) ) {
				$return['err'] = '请先删除子栏目。';
			}else{
				$rsSon = getDataColumn('column', $column_id);
				if(!count($rsSon) ){
					$return['err'] = '数据库找不到该id所对应的栏目。';
				}else{
					$parentId = $rsSon['column_parent_id'];

					$sql1 = "UPDATE t_column SET column_state=0 WHERE column_id=?";
					$parameters1 = array($column_id);

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