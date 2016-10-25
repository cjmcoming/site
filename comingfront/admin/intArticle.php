<?php
	require_once('reqAdmin.php');

	$do = isset($_REQUEST['do']) ? $_REQUEST['do'] : '';
	$callblack = isset($_REQUEST['callblack']) ? $_REQUEST['callblack'] : '';

	$col_id = isset($_REQUEST['col_id']) ? $_REQUEST['col_id'] : '';
	$col_id = ctype_digit($col_id) ? $col_id : 0;

	$art_id = isset($_REQUEST['art_id']) ? $_REQUEST['art_id'] : '';
	$art_id = ctype_digit($art_id) ? $art_id : 0;

	$return = array();
	$return['state'] = 'failed';

	if(!checkLogin()) {
		$return['err'] = '请先登录。';
	}else if($do == '') {
		$return['err'] = '未能获取操作代码，请联系管理员处理。';
	}else if($do == 'add') {
		if($col_id == 0) {
			$return['err'] = '获取栏目id参数出错。';
		}else{
			$sql = "INSERT INTO t_article (art_col_id, art_create_user, art_modify_user, art_create_time, art_modify_time) VALUES (?, ?, ?, ?, ?)";
			$parameters = array($col_id, $_SESSION['user_name'], $_SESSION['user_name'], date("YmdHis"), date("YmdHis"));
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
		$art_title = isset($_POST['art_title']) ? $_POST['art_title'] : '';
		$art_author = isset($_POST['art_author']) ? $_POST['art_author'] : '';
		$art_img = isset($_POST['art_img']) ? $_POST['art_img'] : '';
		$art_tag = isset($_POST['art_tag']) ? $_POST['art_tag'] : '';
		$art_source = isset($_POST['art_source']) ? $_POST['art_source'] : '';
		$art_con = isset($_POST['art_con']) ? $_POST['art_con'] : '';

		$art_intro = isset($_POST['art_intro']) ? $_POST['art_intro'] : '';

		if($art_intro == '') {
			$art_intro = preg_replace('/\s+/', '', $art_con);
			$art_intro = preg_replace('/\<[^>]+>/', '', $art_intro);
			$art_intro = mb_substr($art_intro, 0, 100, 'utf-8');
		}

		if($art_id == 0) {
			$return['err'] = '未能获取文章id。';
		}else{
			$rsArt = getDataArticle($art_id);
			$art_state = $rsArt['art_state'];
			if($art_state == 2) {
				$sql = "UPDATE t_article SET art_title=?, art_author=?, art_img=?, art_tag=?, art_source=?, art_intro=?, art_con=?, art_state=?, art_modify_user=?, art_modify_time=? WHERE art_id=?";
				$parameters = array($art_title, $art_author, $art_img, $art_tag, $art_source, $art_intro, $art_con, 3, $_SESSION['user_name'], date("YmdHis"), $art_id);
			}else{
				$sql = "UPDATE t_article SET art_title=?, art_author=?, art_img=?, art_tag=?, art_source=?, art_intro=?, art_con=?, art_modify_user=?, art_modify_time=? WHERE art_id=?";
				$parameters = array($art_title, $art_author, $art_img, $art_tag, $art_source, $art_intro, $art_con, $_SESSION['user_name'], date("YmdHis"), $art_id);
			}
			$dbt = new dbtemplate();
			if($dbt->update($sql, $parameters)) {
				$return['state'] = 'success';
			}else{
				$return['err'] = '数据库操作出错，请联系管理员处理。';
			}
		}
	}else if($do == 'modTime'){
		$art_create_time = isset($_POST['art_create_time']) ? $_POST['art_create_time'] : '';
		$art_create_time = ctype_digit($art_create_time) ? $art_create_time : 0;

		if($art_id == 0 || $art_create_time == 0) {
			$return['err'] = '未能获取文章id或者修改日期。';
		}else{
			$rsArt = getDataArticle($art_id);
			$art_state = $rsArt['art_state'];
			if($art_state == 2) {
				$sql = "UPDATE t_article SET art_state=?, art_create_time=?, art_modify_user=?, art_modify_time=? WHERE art_id=?";
				$parameters = array(3, $art_create_time, $_SESSION['user_name'], date("YmdHis"), $art_id);
			}else{
				$sql = "UPDATE t_article SET art_create_time=?, art_modify_user=?, art_modify_time=? WHERE art_id=?";
				$parameters = array($art_create_time, $_SESSION['user_name'], date("YmdHis"), $art_id);
			}
			$dbt = new dbtemplate();
			if($dbt->update($sql, $parameters)) {
				$return['state'] = 'success';
			}else{
				$return['err'] = '数据库操作出错，请联系管理员处理。';
			}
		}
	}else if($do == 'del') {
		if($art_id == 0) {
			$return['err'] = '未能获取文章id，请联系管理员处理。';
		}else{
			$sql = "UPDATE t_article SET art_state=0 WHERE art_id=?";
			$parameters = array($art_id);
			$dbt = new dbtemplate();
			if($dbt->update($sql, $parameters)) {
				$return['state'] = 'success';
			}else{
				$return['err'] = '数据库操作出错，请联系管理员处理。';
			}
		}
	}else if($do == 'sort') {
		$articleId = isset($_POST['articleId']) ? $_POST['articleId'] : '';
		$sort = isset($_POST['sort']) ? $_POST['sort'] : '';
		$aArticleId = explode(',', $articleId);
		$aSort= explode(',', $sort);

		$aData = array();
		for ($i=0; $i < count($aArticleId); $i++) {
			if(ctype_digit($aArticleId[$i]) && is_numeric($aSort[$i]) ) {
				$aData[$aArticleId[$i]] = $aSort[$i];
			}
		}

		$ids = implode(',', array_keys($aData) );

		$sql = "UPDATE t_article SET art_sort = CASE art_id ";
		foreach ($aData as $id => $ordinal) {
			$sql .= sprintf("WHEN %d THEN %d ", $id, $ordinal);
		}
		$sql .= "END WHERE art_id IN ($ids)";
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