<?php

	/**
	* 验证用户名与密码
	* @param  (string)$user_name 用户名
	* @param  (string)$user_pass 用户密码，这里传进来的密码已加密
	* @return  (array)[0=>user_name,1=>user_pass'] 正常
	*          (string)'unCheck' 用户未审核
	*          (string)'unCorrect' 未找到数据
	*/
	function checkUser($user_name, $user_pass) {
		$dbt = new dbtemplate();
		$sql1 = "SELECT * FROM t_user WHERE user_name = ? and user_pass = ?";
		$parameters1 = array($user_name, $user_pass);
		$rs1 = $dbt->queryrow($sql1, $parameters1);
		if(count($rs1) > 0) {
			if($rs1['user_state'] > 1) {
				$userArray = array();
				$userArray[0] = $rs1['user_name'];
				$userArray[1] = $rs1['user_state'];
				return $userArray;
			}else{
				return 'unCheck';
			}
		}else{
			return 'unCorrect';
		}
	}


	/**
	* 初级验证用户是否登录，检测 $_SESSION['user_name'] 与 $_SESSION['user_state'] 的值是否为非空
	* @return  (boolean)true 正常
	*          (boolean)false 用户未登录
	*/
	function checkLogin() {
		if(isset($_SESSION['user_name']) && $_SESSION['user_state'] != '' && isset($_SESSION['user_name']) && $_SESSION['user_state'] != '') {
			return true;
		}else{
			return false;
		}
	}


	/**
	* 高级验证用户是否登录，检测 $_SESSION['user_name'] 与 $_SESSION['user_state'] 的值是否为非空，然后再通过 checkUser 方法验证是否正确
	* @return  (array)[0=>user_name,1=>user_pass'] 正常
	*          (string)'unCheck' 用户未审核
	*          (string)'unCorrect' 未找到数据
	*          (string)'unLogin' 无session
	*/
	function chenkLoginUltra() {
		if(isset($_SESSION['user_name']) && $_SESSION['user_state'] != '' && isset($_SESSION['user_name']) && $_SESSION['user_state'] != '') {
			return checkUser($_SESSION['session_adminName'],$_SESSION['session_adminPass']);
		}else{
			return 'unLogin';
		}
	}


	/**
	* 安全过虑字符串函数
	* @param  (string)$text 用户提交的字符串
	* @return  (string) 已过滤的字符串
	*/
	function safe($text) {
		if(is_numeric($text)) {
			return $text;
		}else{
			return htmlentities(strip_tags(trim($text)));
		}
	}


	/**
	* 弹出错误警告方法
	* @param  (string)$info 需要警告的文字
	*         (string)$link 需要跳转的url，默认为(boolean)false
	* @return 中止脚本执行
	*/
	function err($info, $link = false) {
		if(!$link) {
			echo '
				<script language="javascript" type="text/javascript">
					alert("'.$info.'");
					history.back(-1);
				</script>';
		}else{
			echo '
				<script language="javascript" type="text/javascript">
					alert("'.$info.'");
					location.href = "'.$link.'";
				</script>';
		}
		die;
	}


	/**
	* 获取分页代码函数
	* @param  (number)$pageSize 每页条数
	*         (number)$viewPage 显示页码的数量
	*         (number)$totalPage 总页数
	*         (number)$page 当前页码
	*         (number)$totalNum 总条数
	*         (string)$pageLink 页面url
	* @return  (string) 分页代码
	*/
	function showPageAdmin($pageSize, $viewPage, $totalPage, $page, $totalNum, $pageLink) {
		$nextPage = $page + 1;
		$nextPage= $nextPage > $totalPage ? $totalPage : $nextPage ;
		$lastPage = $page - 1;
		$lastPage= ($lastPage < 1) ? 1 : $lastPage ;
		$for_end = ($totalPage > ($page + $viewPage)) ? ($page +$viewPage) : $totalPage;
		$for_begin = (($page - $viewPage)>1) ? ($page - $viewPage) : 1;
		$pageHtml = '';
		if($page > 1){
			$lastPageLink = ($page == 2) ? $pageLink : $pageLink.'page='.($page-1);
			$pageHtml .= '<a title="首页" class="bPageLast" href="'.$pageLink.'"><<</a> <a title="上一页" class="bPageLast" href="'.$lastPageLink.'"><</a>';
		}
		if($for_begin > 1) {
			$pageHtml .= '<i>...</i>';
		}
		for($i = $for_begin; $i <= $for_end; $i++){
			if ($i != $page){
				if ($i != 1) {
					$pageHtml .= '<a title="第'.$i.'页" href="'.$pageLink.'page='.$i.'">'.$i.'</a>';
				} else {
					$pageHtml .= '<a title="第'.$i.'页" href="'.$pageLink.'">'.$i.'</a>';
				}
			}else{
				$pageHtml .= '<em>'.$i.'</em> ';
			}
		}
		if($for_end < $totalPage) {
			$pageHtml .= '<i>...</i>';
		}
		if($page < $totalPage){
			$pageHtml .= '<a title="下一页" class="bPageNext" href="'.$pageLink.'page='.$nextPage.'">></a><a title="最后一页" class="bPageNext" href="'.$pageLink.'page='.$totalPage.'">>></a>';
		}
		$pageHtml .= '<span class="bPageTxt">每页 '.$pageSize.' 条　共 '.$totalPage.' 页 '.$totalNum.' 条</span>';
		return '<div class="bPage">'.$pageHtml.'</div>';
	}

	function showPagePublish($pageSize, $pageView, $pageTotal, $page, $totalNum, $pageLink) {
		$nextPage = $page + 1;
		$nextPage= $nextPage > $pageTotal ? $pageTotal : $nextPage ;
		$lastPage = $page - 1;
		$lastPage= ($lastPage < 1) ? 1 : $lastPage ;
		$for_end = ($pageTotal > ($page + $pageView)) ? ($page +$pageView) : $pageTotal;
		$for_begin = (($page - $pageView)>1) ? ($page - $pageView) : 1;
		$pageHtml = '';
		if($page > 1){
			$lastPage = ($page == 2) ? $pageLink.'.html' : $pageLink.'_'.($page-1).'.html';
			$pageHtml .= '<a title="首页" class="bPageLast" href="'.$pageLink.'.html">首页</a> <a title="上一页" class="bPageLast" href="'.$lastPage.'">上一页</a>';
		}
		for($i = $for_begin; $i <= $for_end; $i++){
			if ($i != $page){
				if ($i != 1) {
					$pageHtml .= '<a title="第'.$i.'页" href="'.$pageLink.'_'.($i-1).'.html">'.$i.'</a> ';
				} else {
					$pageHtml .= '<a title="第'.$i.'页" href="'.$pageLink.'.html">'.$i.'</a> ';
				}
			}else{
				$pageHtml .= '<em>'.$i.'</em> ';
			}
		}
		if($page < $pageTotal){
			$pageHtml .= '<a title="下一页" class="bPageNext" href="'.$pageLink.'_'.($nextPage-1).'.html">下一页</a> <a title="最后一页" class="bPageNext" href="'.$pageLink.'_'.($pageTotal-1).'.html">尾页</a> ';
		}
		return '<div class="bPage">'.$pageHtml.'</div>';
	}





	/**
	* 日期格式化的入口函数
	* @param  (number)$num 14位整数
	*         (string)$mode 需要的格式，y代表年、m代表月、d代表日，如："yyyy-mm-dd"、"yyyy-m-d h:i:s"、"m月d日"
	* @return  (string) 格式化后的字符串
	*/
	function formatDate($num, $mode = "yyyy-mm-dd hh:ii") {
		if($num == 0) {
			return '';
		}
		preg_match_all('/y+/i', $mode, $matchs);
		$y = $matchs[0];
		$y_new = array();
		for($i=0; $i<count($y); $i++) {
			$len = mb_strlen($y[$i]);
			if($len < 5) {
				$star = 4 - $len;
				$y_new[$i] = substr($num, $star, $len);
			}else{
				$y_new[$i] = $y[$i];
			}
		}

		$temp = formatDateItem(substr($num, 4, 2), $mode, '/m+/i');
		$m = $temp[0];
		$m_new = $temp[1];

		$temp = formatDateItem(substr($num, 6, 2), $mode, '/d+/i');
		$d = $temp[0];
		$d_new = $temp[1];

		$temp = formatDateItem(substr($num, 8, 2), $mode, '/h+/i');
		$h = $temp[0];
		$h_new = $temp[1];

		$temp = formatDateItem(substr($num, 10, 2), $mode, '/i+/i');
		$i = $temp[0];
		$i_new = $temp[1];

		$temp = formatDateItem(substr($num, 12, 2), $mode, '/s+/i');
		$s = $temp[0];
		$s_new = $temp[1];

		$find = array_merge($y, $m, $d, $h, $i, $s);
		$replace = array_merge($y_new, $m_new, $d_new, $h_new, $i_new, $s_new);
		return str_replace($find, $replace, $mode);
	}

	/**
	* 日期格式化的子函数
	* @param  (number)$num 2位整数
	*         (string)$mode 需要的格式，y代表年、m代表月、d代表日，如："yyyy-mm-dd"、"yyyy-m-d h:i"、"m月d日"
	*         (string)$preg 正则表达式字符串
	* @return  (array)[$arrayA, $arrayB] 匹配数组和替换数组
	*/
	function formatDateItem($num, $mode, $preg) {
		preg_match_all($preg, $mode, $matchs);
		$arrayA = $matchs[0];
		$arrayB = array();
		for($i=0; $i<count($arrayA); $i++) {
			$len = mb_strlen($arrayA[$i]);
			if($len == 1) {
				$arrayB[$i] = intval($num);
			}else if($len == 2){
				$arrayB[$i] = $num;
			}else{
				$arrayB[$i] = $arrayA[$i];
			}
		}
		return array($arrayA, $arrayB);
	}


	/**
	* tag 格式化函数
	* @param  (string)$tag 以特殊符号分隔的 tag 字符串
	*         (string)$url 生成 tag 的 url 格式，如：'search.html?tag=__tag__'
	*         (string)$separate 分隔符，默认为半角逗号
	* @return  (string) 以 a 标签包裹的 tag html 代码
	*/
	function formatTag($tag, $url, $separate = ",") {
		$temp = explode($separate, $tag);
		$returnHtml = '';
		for ($i=0; $i<count($temp); $i++) {
			$returnHtml .= '<a href="'.str_replace('__tag__', $temp[$i], $url).'">'.$temp[$i].'</a>';
		}
		return $returnHtml;
	}


	function getColumnSon($rs = false, $id = false) {
		if(!$rs || !$id || !count($rs) ) {
			return array();
		}
		$aRs = array();
		for ($i=0; $i < count($rs); $i++) {
			if($rs[$i]['col_parent_id'] == $id) {
				$aRs[] = $rs[$i];
				$aRs = array_merge($aRs, getColumnSon($rs, $rs[$i]['col_id']) );
			}
		}
		return $aRs;
	}





	/**
	* 获取文章列表数据函数
	* @param  (number)$start 开始位置
	*         (number)$num 需要条数
	* @return  (array)[(array)列表数据, (number)总条数]
	*/
	function getDataArtList($id, $start, $num, $state = 0, $contain = false) {
		$sqlRange = "";
		if($state == 0) {
			$sqlRange = "t_article.art_state = 0";
		}else if($state == 1) {
			$sqlRange = "t_article.art_state = 1";
		}else if($state == 2) {
			$sqlRange = "t_article.art_state = 2";
		}else if($state == 3) {
			$sqlRange = "t_article.art_state = 3";
		}else if($state == 123) {
			$sqlRange = "t_article.art_state > 0";
		}else if($state == 13) {
			$sqlRange = "(t_article.art_state = 1 OR t_article.art_state = 3)";
		}

		if($id == 0) {
			$sql1 = "SELECT t_article.*, t_column.col_name, t_column.col_type, t_column.col_template_art, t_column.col_address FROM t_article, t_column WHERE ".$sqlRange." and t_article.art_col_id = t_column.col_id ORDER BY t_article.art_sort DESC, t_article.art_id DESC LIMIT ".$start.",".$num;
			$sql2 = "SELECT COUNT(*) from t_article WHERE ".$sqlRange;
		}else{
			if($contain == true) {
				$rsColumn = getColumnSon(getDataColumn('all', true), $id);
				$aColumnId = array();
				$aColumnId[] = $id;
				for ($i = 0; $i < count($rsColumn); $i++) { 
					$aColumnId[] = $rsColumn[$i]['col_id'];
				}
				$sColumnId = implode(',', $aColumnId);
				$sql1 = "SELECT t_article.*, t_column.col_name, t_column.col_type, t_column.col_template_art, t_column.col_address FROM t_article, t_column WHERE ".$sqlRange." and t_article.art_col_id in (".$sColumnId.") and t_article.art_col_id = t_column.col_id ORDER BY t_article.art_sort DESC, t_article.art_id DESC LIMIT ".$start.",".$num;
				$sql2 = "SELECT COUNT(*) from t_article WHERE ".$sqlRange." and art_col_id in (".$sColumnId.")";
			}else{
				$sql1 = "SELECT t_article.*, t_column.col_name, t_column.col_type, t_column.col_template_art, t_column.col_address FROM t_article, t_column WHERE ".$sqlRange." and t_article.art_col_id = ".$id." and t_article.art_col_id = t_column.col_id ORDER BY t_article.art_sort DESC, t_article.art_id DESC LIMIT ".$start.",".$num;
				$sql2 = "SELECT COUNT(*) from t_article WHERE ".$sqlRange." and art_col_id  = ".$id;
			}
		}
		$dbt = new dbtemplate();

		$rs = $dbt->queryrows($sql1);
		$allNum = $dbt->queryforint($sql2);
		return array('rs'=>$rs, 'num'=>$allNum);
	}

	function getDataHotArtList($id, $start, $num, $contain = false) {
		if($id == 0) {
			$sql = "SELECT t_article.*, t_column.col_name, t_column.col_type, t_column.col_template_art, t_column.col_address FROM t_article, t_column WHERE t_article.art_state = 2 and t_article.art_col_id = t_column.col_id ORDER BY t_article.art_count DESC, t_article.art_id DESC LIMIT ".$start.",".$num;
		}else{
			if($contain == true) {
				$rsCol = getColumnSon(getDataColumn('all', true), $id);
				$aColId = array();
				$aColId[] = $id;
				for ($i = 0; $i < count($rsCol); $i++) { 
					$aColId[] = $rsCol[$i]['col_id'];
				}
				$sColumnId = implode(',', $aColumnId);
				$sql = "SELECT t_article.*, t_column.col_name, t_column.col_type, t_column.col_template_art, t_column.col_address FROM t_article, t_column WHERE t_article.art_col_id = ".$id." and t_article.art_state = 2 and t_article.art_col_id in (".$sColumnId.") and t_article.art_col_id = t_column.col_id ORDER BY t_article.art_count DESC, t_article.art_id DESC LIMIT ".$start.",".$num;
			}else{
				$sql = "SELECT t_article.*, t_column.col_name, t_column.col_type, t_column.col_template_art, t_column.col_address FROM t_article, t_column WHERE t_article.art_col_id = ".$id." and t_article.art_state = 2 and t_article.art_col_id = t_column.col_id ORDER BY t_article.art_count DESC, t_article.art_id DESC LIMIT ".$start.",".$num;
			}
		}
		$dbt = new dbtemplate();

		return $dbt->queryrows($sql);
	}

	function getDataArticle($id = false) {
		if(!$id) {
			return array();
		}
		$sql = "SELECT t_article.*, t_column.col_name, t_column.col_type, t_column.col_address, t_column.col_template_art FROM t_article, t_column WHERE t_article.art_state > 0 and t_article.art_id = ".$id." and t_article.art_col_id = t_column.col_id";
		$dbt = new dbtemplate();
		return $dbt->queryrow($sql);
	}

	function getDataArtNum($art_col_id = 0, $state = 123, $contain = false) {
		$sqlRange = "";
		if($state == 0) {
			$sqlRange = "art_state = 0";
		}else if($state == 1) {
			$sqlRange = "art_state = 1";
		}else if($state == 2) {
			$sqlRange = "art_state = 2";
		}else if($state == 3) {
			$sqlRange = "art_state = 3";
		}else if($state == 123) {
			$sqlRange = "art_state > 0";
		}else if($state == 13) {
			$sqlRange = "(art_state = 1 OR art_state = 3)";
		}

		if($art_col_id == 0) {
			$sql = "SELECT COUNT(*) from t_article WHERE ".$sqlRange;
			$dbt = new dbtemplate();
			return $dbt->queryforobject($sql);
		}else{
			if($contain == true) {
				$rsColumn = getColumnSon(getDataColumn('all', true), $art_col_id);
				$aColumnId = array();
				$aColumnId[] = $art_col_id;
				for ($i = 0; $i < count($rsColumn); $i++) { 
					$aColumnId[] = $rsColumn[$i]['col_id'];
				}
				$sColumnId = implode(',', $aColumnId);
				$sql = "SELECT COUNT(*) from t_article WHERE ".$sqlRange." and art_col_id in (".$sColumnId.")";
			}else{
				$sql = "SELECT COUNT(*) from t_article WHERE ".$sqlRange." and art_col_id  = ".$art_col_id;
			}
			$dbt = new dbtemplate();
			return $dbt->queryforobject($sql);
		}
	}

	function getDataArtNumGroup($state = 123) {
		$sqlRange = "";
		if($state == 0) {
			$sqlRange = "art_state = 0";
		}else if($state == 1) {
			$sqlRange = "art_state = 1";
		}else if($state == 2) {
			$sqlRange = "art_state = 2";
		}else if($state == 3) {
			$sqlRange = "art_state = 3";
		}else if($state == 123) {
			$sqlRange = "art_state > 0";
		}else if($state == 13) {
			$sqlRange = "(art_state = 1 OR art_state = 3)";
		}

		$sql = "SELECT art_col_id, COUNT(*) as art_num FROM t_article WHERE ".$sqlRange." GROUP BY art_col_id";
		$dbt = new dbtemplate();
		return $dbt->queryrows($sql);
	}

	/**
	* 获取栏目数据函数
	* @param  (string)$type 类型
	*         (number)$id
	* @return  (array)列表数据
	*/
	function getDataColumn($type = false, $id = false) {
		if(!$type || !$id) {
			return array();
		}else{
			$dbt = new dbtemplate();
			if($type == 'column') {
				$sql = "SELECT * FROM t_column WHERE col_state = 1 and col_id = ".$id;
				return $dbt->queryrow($sql);
			}else if($type == 'parent') {
				$sql = "SELECT * FROM t_column WHERE col_state = 1 and col_parent_id = ".$id." ORDER BY col_sort DESC, col_id";
				return $dbt->queryrows($sql);
			}else if($type == 'all') {
				$sql = "SELECT * FROM t_column WHERE col_state = 1 ORDER BY col_sort DESC, col_id";
				return $dbt->queryrows($sql);
			}else{
				return array();
			}
		}
	}

	/**
	* 获取图片数据函数
	* @param  (number)$column_id 栏目id，该id不为-1时，父级栏目id将不起作用
	*         (number)$column_parent_id 父级栏目id
	* @return  (array)列表数据
	*/
	function getDataImg($type = false, $id = false) {
		if(!$type || !$id) {
			return array();
		}else{
			if($type == 'column') {
				$sql = "SELECT * FROM t_image WHERE img_state = 1 and img_col_id = ".$id." ORDER BY img_id DESC";
			}else if($type == 'block') {
				$sql = "SELECT * FROM t_image WHERE img_state = 1 and img_blo_id = ".$id." ORDER BY img_id DESC";
			}else if($type == 'article') {
				$sql = "SELECT * FROM t_image WHERE img_state = 1 and img_art_id = ".$id." ORDER BY img_id DESC";
			}else{
				return array();
			}
			$dbt = new dbtemplate();
			return $dbt->queryrows($sql);
		}
	}

	/**
	* 获取栏目代码块数据函数
	* @param  (string)$type 类型，'column'表示获取一个栏目所有的 block，'block'表示获取指定 block_id 的 block；
	* 		  (number)$id column_id 或 block_id；
	* @return  (array)数组
	*/
	function getDataBlock($type = false, $id = false) {
		if(!$type || !$id) {
			return array();
		}else{
			$dbt = new dbtemplate();
			if($type == 'column') {
				$sql = "SELECT t_block.*, t_column.col_name FROM t_block, t_column WHERE t_block.blo_state = 1 and t_block.blo_col_id = ".$id." and t_block.blo_col_id = t_column.col_id ORDER BY t_block.blo_sort DESC, t_block.blo_id";
				return $dbt->queryrows($sql);
			}else if($type == 'block') {
				$sql = "SELECT t_block.*, t_column.col_name FROM t_block, t_column WHERE t_block.blo_state = 1 and t_block.blo_id = ".$id." and t_block.blo_col_id = t_column.col_id";
				return $dbt->queryrow($sql);
			}else{
				return array();
			}
		}
	}

	/**
	* 获取栏目代码块数据函数
	* @param  (string)$type 类型，'column'表示获取一个栏目所有的 block，'block'表示获取指定 block_id 的 block；
	* 		  (number)$id column_id 或 block_id；
	* @return  (array)数组
	*/
	function getDataPubList($type, $start, $num) {
		if($type == 'add') {
			$sql1 = "SELECT * FROM t_publish WHERE pub_state = 0 ORDER BY pub_id LIMIT ".$start.",".$num;
			$sql2 = "SELECT COUNT(*) from t_publish WHERE pub_state = 0";
		}else if($type == 'unfinish') {
			$sql1 = "SELECT * FROM t_publish WHERE pub_state = 0 or pub_state = 1 ORDER BY pub_id LIMIT ".$start.",".$num;
			$sql2 = "SELECT COUNT(*) from t_publish WHERE pub_state = 0 or pub_state = 1";
		}else if($type == 'running') {
			$sql1 = "SELECT * FROM t_publish WHERE pub_state = 1 ORDER BY pub_id LIMIT ".$start.",".$num;
			$sql2 = "SELECT COUNT(*) from t_publish WHERE pub_state = 1";
		}else if($type == 'finish') {
			$sql1 = "SELECT * FROM t_publish WHERE pub_state > 1 ORDER BY pub_id DESC LIMIT ".$start.",".$num;
			$sql2 = "SELECT COUNT(*) from t_publish WHERE pub_state > 1";
		}
		$dbt = new dbtemplate();
		$rs = $dbt->queryrows($sql1);
		$allNum = $dbt->queryforint($sql2);
		return array('rs'=>$rs, 'num'=>$allNum);
	}

	/**
	* 获取栏目代码块数据函数
	* @param  (string)$type 类型，'column'表示获取一个栏目所有的 block，'block'表示获取指定 block_id 的 block；
	* 		  (number)$id column_id 或 block_id；
	* @return  (array)数组
	*/
	function getDataPublish($id = 0) {
		if($id == 0) {
			$sql = "SELECT * FROM t_publish WHERE pub_state = 0 or pub_state = 1 ORDER BY pub_id limit 1";
		}else{
			$sql = "SELECT * FROM t_publish WHERE pub_id = ".$id;
		}

		$dbt = new dbtemplate();
		return $dbt->queryrow($sql);
	}




	//写文件
	function f_write($file,$text){
		if(!$text){
			//err('无传递需要写入的内容');
			return false;
		}
		if(!file_exists($file)){
			if(!@touch($file)){
				//err('无法创建文件——'.$file.'！当前运行环境权限不足！<br />原因分析：<br />1、文件名不合法 2、如此程序是在本地测试运行，请：linux系统 设各目录权限为0777；window系统 设根目录为web共享；3、如此程序已上传至虚拟空间，请：linux系统 设各目录权限为0777；若此项执行后仍失败，则以管理员身份后台进行“ftp附加设置”再试');
				return false;
			}
		}
		@chmod($file,0777);
		if($fp=@fopen($file,'rb+')) {
			f_lock($fp);
			@ftruncate($fp,0);
			@fwrite($fp,$text);
			@flock($fp,LOCK_UN);
			fclose($fp);
			return true;
		}else{
			//err('操作失败！原因分析：文件'.$file.'不存在或不可读写');
			return false;
		}
	}

	//锁定文件
	function f_lock($fp){
	  if(!$fp) return;
	  if(@flock($fp,LOCK_EX)){
		 return;
	  }else{
		 sleep(1);
		 f_lock($fp);
	  }
	}

	//删除文件
	function f_del($file){
		global $web;
		if(!file_exists($file)) {return true;}
		if(@unlink($file)) {return true;}
		if(($conn=@ftp_connect($web['ftp_http'])) && @ftp_login($conn,$web['ftp_username'],$web['ftp_password'])){
			@ftp_set_option($conn,FTP_TIMEOUT_SEC,$web['ftp_timeout']);
			if(!(@function_exists('ftp_chmod') && @ftp_chmod($conn,0777,dirname($file)))){
				if(!@ftp_site($conn,'CHMOD 0777 '.dirname($file).'')){
					@ftp_exec($conn,'SITE CHMOD 0777 '.dirname($file).'');
				}
			}
			@ftp_delete($conn,$file);
			@ftp_close($conn);
			return true;
		}
		err('无法删除文件——'.$file.'！当前运行环境权限不足！<br />原因分析：<br />1、文件名不合法 2、如此程序是在本地测试运行，请：linux系统 设各目录权限为0777；window系统 设根目录为web共享；3、如此程序已上传至虚拟空间，请：linux系统 设各目录权限为0777；若此项执行后仍失败，则以管理员身份后台进行“ftp附加设置”再试');
		return false;
	}

	//创建目录
	function f_mkdir($addr) {
		$direc = '../'.$addr.'/';
		if (!is_dir($direc) ) {
			if(!mkdir($direc, 0777, true) ) {
				return false;
			}
		}
		return true;
	}




?>










