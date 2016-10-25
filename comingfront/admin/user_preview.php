<?php
	require_once('reqAdmin.php');

	$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

	$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
	$id = ctype_digit($id) ? $id : 0;

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : '';
	$page = ctype_digit($page) ? $page : 1;

	if($type == '' || $id == '') {
		err('未能获取相关参数！');
	}

	if($type == 'column') {
		$rsCol = getDataColumn('column', $id);
		if(!count($rsCol) ){
			err('未能找到该id对应的栏目数据！');
		}
		$template = $rsCol['col_template'];
		if($template == '') {
			err('该栏目未设置栏目模板，请联系管理员处理！');
		}
		$file = 'driver/'.$template;
		if(!file_exists($file)) {
			err('栏目模板不存在，请联系管理员处理！');
		}

		$conTemplate = file_get_contents($file);
		$rule = '/\{\{getPageHtml\((-?\d+),(true|false),(\d+),(\d+)\)\}\}/i';
		preg_match($rule, $conTemplate, $result);
		if(!count($result) ) {
			ob_start();
			include($file);
			$temp = ob_get_contents();
			ob_end_clean();
			echo $temp;
		}else{
			$sReplace = $result[0];
			$col_id = $result[1];
			if($col_id == 0) {
				$col_id = $rsCol['col_id'];
			}
			$bContain = $result[2];
			$pageSize = $result[3];
			$pageView = $result[4];

			if($col_id == -1) {
				$artNum = getDataArtNum(0, 2);
			}else{
				$artNum = getDataArtNum($col_id, 2, $bContain);
			}

			$totalPage = ceil($artNum/$pageSize);
			if( ($totalPage > 0 && $page > $totalPage) || ($totalPage == 0 && $page > 1) ) {
				err("该页不存在");
			}

			ob_start();
			include($file);
			$temp = ob_get_contents();
			ob_end_clean();

			$pageHtml = showPageAdmin($pageSize, $pageView, $totalPage, $page, $artNum, 'user_preview.php?type='.$type.'&id='.$id.'&');
			$temp = str_replace($sReplace, $pageHtml, $temp);

			echo $temp;
		}
	}else if($type == 'article') {
		$rsArt = getDataArticle($id);
		if(!count($rsArt) ){
			err('未能找到该id对应的栏目数据！');
		}
		$template = $rsArt['col_template_art'];
		if($template == '') {
			err('该栏目未设置文章模板，请联系管理员处理！');
		}
		$file = 'driver/'.$template;
		if(!file_exists($file)) {
			err('文章模板不存在，请联系管理员处理！');
		}

		ob_start();
		include($file);
		$temp = ob_get_contents();
		ob_end_clean();
		echo $temp;
	}else{
		err('未能获取相关参数！');
	}
?>