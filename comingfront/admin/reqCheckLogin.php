<?php
	if(!checkLogin()) {
		err("请先登录！", "user_login.php");
	}
?>