<?php
	require_once('reqDriver.php');

	$do = isset($_REQUEST['do']) ? $_REQUEST['do'] : '';
	$callblack = isset($_REQUEST['callblack']) ? $_REQUEST['callblack'] : '';

	$user_id = isset($_REQUEST['user_id']) ? $_REQUEST['user_id'] : '';
	$user_id = ctype_digit($user_id) ? $user_id : 0;

	$name = isset($_POST['name']) ? $_POST['name'] : '';
	$email = isset($_POST['email']) ? $_POST['email'] : '';
	$pass = isset($_POST['pass']) ? md5(md5($_POST['pass'])) : '';
	$verification = isset($_POST['verification']) ? md5($_POST['verification']) : '';

	$return = array();
	$return['state'] = 'failed';

	if ($do == '') {
		$return['err'] = '未能获取操作代码，请联系管理员处理。';
	}else if($do == "logout") {
		$_SESSION['user_name'] = '';
		$_SESSION['user_state'] = '';
		session_unset();
		$return['state'] = 'success';
	}else if($do == 'add') {
		if($name == '') {
			$return['err'] = '请输入用户名。';
		}else if($email == '') {
			$return['err'] = '请输入用email。';
		}else if($pass == '') {
			$return['err'] = '请输入用户密码。';
		}else if($verification == '') {
			$return['err'] = '请输入用验证码。';
		}else if($verification != $_SESSION["verification"]) {
			$return['err'] = '验证码不正确。';
		}else{
			$dbt = new dbtemplate();

			$sql1 = "SELECT * FROM t_user WHERE user_name = ?";
			$parameters1 = array($name);

			if(count($dbt->queryrow($sql1, $parameters1)) > 0) {
				$return['err'] = '该用户名已存在。';
			}else{
				$sql2 = "INSERT INTO t_user (user_name, user_email, user_pass, user_login_time, user_create_time, user_modify_time, user_modify_user) VALUES (?, ?, ?, ?, ?, ?, ?)";
				$parameters2 = array($name, $email, $pass, date("YmdHis"), date("YmdHis"), date("YmdHis"), $name);
				
				if($dbt->update($sql2, $parameters2) ) {
					$return['state'] = 'success';
				}else{
					$return['err'] = '数据库操作出错，请联系管理员处理。';
				}
			}
		}
	}else if($do == 'log') {
		if($name == '') {
			$return['err'] = '请输入用户名。';
		}else if($pass == '') {
			$return['err'] = '请输入用户密码。';
		}else if(checkLogin() == true) {
			$return['err'] = '您已登录，如需重新登录请先退出登录。';
		}else{
			$loginCon = checkUser($name, $pass);

			if ($loginCon == 'unCorrect') {
				$return['err'] = '用户名或者密码错误。';
			} else if ($loginCon == 'unCheck') {
				$return['err'] = '您注册的用户名还未通过审核。';
			} else {
				$dbt = new dbtemplate();
				$sql = "update t_user set user_login_time=? where user_name=?";
				$parameters = array(date("YmdHis"), $loginCon[0]);
				$dbt->update($sql, $parameters);

				$_SESSION['user_name'] = $loginCon[0];
				$_SESSION['user_state'] = $loginCon[1];
				$return['state'] = 'success';
			}
		}
	}else if($do == 'del') {
		if($_SESSION['user_state'] < 3) {
			$return['err'] = '您没有该权限。';
		}else{
			if($user_id == 0) {
				$return['err'] = '未能获取用户id，请联系管理员处理。';
			}else{
				$sql = "UPDATE t_user SET user_state = ?, user_modify_time = ?, user_modify_user = ? WHERE user_id=?";
				$parameters = array(0, date("YmdHis"), $_SESSION['user_name'], $user_id);
				$dbt = new dbtemplate();
				if($dbt->update($sql, $parameters)) {
					$return['state'] = 'success';
				}else{
					$return['err'] = '数据库操作出错，请联系管理员处理。';
				}
			}
		}
	}else if($do == 'disable') {
		if($_SESSION['user_state'] < 3) {
			$return['err'] = '您没有该权限。';
		}else{
			if($user_id == 0) {
				$return['err'] = '未能获取用户id，请联系管理员处理。';
			}else{
				$sql = "UPDATE t_user SET user_state = ?, user_modify_time = ?, user_modify_user = ? WHERE user_id=?";
				$parameters = array(1, date("YmdHis"), $_SESSION['user_name'], $user_id);
				$dbt = new dbtemplate();
				if($dbt->update($sql, $parameters)) {
					$return['state'] = 'success';
				}else{
					$return['err'] = '数据库操作出错，请联系管理员处理。';
				}
			}
		}
	}else if($do == 'normal') {
		if($_SESSION['user_state'] < 3) {
			$return['err'] = '您没有该权限。';
		}else{
			if($user_id == 0) {
				$return['err'] = '未能获取用户id，请联系管理员处理。';
			}else{
				$sql = "UPDATE t_user SET user_state = ?, user_modify_time = ?, user_modify_user = ? WHERE user_id=?";
				$parameters = array(2, date("YmdHis"), $_SESSION['user_name'], $user_id);
				$dbt = new dbtemplate();
				if($dbt->update($sql, $parameters)) {
					$return['state'] = 'success';
				}else{
					$return['err'] = '数据库操作出错，请联系管理员处理。';
				}
			}
		}
	}else if($do == 'manager') {
		if($_SESSION['user_state'] < 3) {
			$return['err'] = '您没有该权限。';
		}else{
			if($user_id == 0) {
				$return['err'] = '未能获取用户id，请联系管理员处理。';
			}else{
				$sql = "UPDATE t_user SET user_state = ?, user_modify_time = ?, user_modify_user = ? WHERE user_id=?";
				$parameters = array(3, date("YmdHis"), $_SESSION['user_name'], $user_id);
				$dbt = new dbtemplate();
				if($dbt->update($sql, $parameters)) {
					$return['state'] = 'success';
				}else{
					$return['err'] = '数据库操作出错，请联系管理员处理。';
				}
			}
		}
	}else if($do == 'sort') {
		if($_SESSION['user_state'] < 3) {
			$return['err'] = '您没有排序权限。';
		}else{
			$userId = isset($_POST['userId']) ? $_POST['userId'] : '';
			$sort = isset($_POST['sort']) ? $_POST['sort'] : '';
			$aUserId = explode(',', $userId);
			$aSort= explode(',', $sort);

			$aData = array();
			for ($i=0; $i < count($aUserId); $i++) {
				if(ctype_digit($aUserId[$i]) && is_numeric($aSort[$i]) ) {
					$aData[$aUserId[$i]] = $aSort[$i];
				}
			}

			$ids = implode(',', array_keys($aData) );

			$sql = "UPDATE t_user SET user_sort = CASE user_id ";
			foreach ($aData as $id => $ordinal) {
				$sql .= sprintf("WHEN %d THEN %d ", $id, $ordinal);
			}
			$sql .= "END WHERE user_id IN ($ids)";
			$dbt = new dbtemplate();
			if($dbt->update($sql) ) {
				$return['state'] = 'success';
			}else{
				$return['err'] = '数据库操作出错，请联系管理员处理。';
			}
		}
	}else if($do == 'modify') {
		if(!checkLogin()) {
			$return['err'] = '请先登录。';
		}else{
			$passNewA = isset($_POST['passNewA']) ? md5(md5($_POST['passNewA'])) : '';
			$passNewB = isset($_POST['passNewB']) ? md5(md5($_POST['passNewB'])) : '';

			$sql1 = "SELECT * FROM t_user WHERE user_name = ?";
			$parameters1 = array($_SESSION['user_name']);

			$dbt = new dbtemplate();
			$rsUser = $dbt->queryrow($sql1, $parameters1);

			if($rsUser['user_pass'] == $pass) {
				if($passNewA == $passNewB) {
					$sql2 = "UPDATE t_user SET user_pass = ?, user_modify_time = ?, user_modify_user = ? WHERE user_id=?";
					$parameters2 = array($passNewA, date("YmdHis"), $_SESSION['user_name'], $rsUser['user_id']);
					$dbt = new dbtemplate();
					if($dbt->update($sql2, $parameters2)) {
						$return['state'] = 'success';
					}else{
						$return['err'] = '数据库操作出错，请联系管理员处理。';
					}
				}else{
					$return['err'] = '2次输入的新密码不一样。';
				}
			}else{
				$return['err'] = '原密码输入不正确。';
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