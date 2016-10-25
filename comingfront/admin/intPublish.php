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
	}else if($do == 'addCol') {
		if($col_id == 0) {
			$return['err'] = '获取栏目id参数出错。';
		}else{
			$rsCol = getDataColumn('column', $col_id);
			if(!count($rsCol) ) {
				$return['err'] = '数据库未能找到该id对应的栏目。';
			}else if($rsCol['col_template'] == '' || $rsCol['col_address'] == '') {
				$return['err'] = '该栏目未设置栏目模板或者未设置发布地址，请联系管理员处理。';
			}else{
				$sql = "INSERT INTO t_publish (pub_col_id, pub_create_time, pub_create_user) VALUES (?, ?, ?)";
				$parameters = array($col_id, date("YmdHis"), $_SESSION['user_name']);

				$dbt = new dbtemplate();
				if($dbt->update($sql, $parameters)) {
					$return['state'] = 'success';
				}else{
					$return['err'] = '数据库操作出错，请联系管理员处理。';
				}
			}
		}	
	}else if($do == 'addArt') {
		if($art_id == 0) {
			$return['err'] = '获取栏目id参数出错。';
		}else{
			$rsArt = getDataArticle($art_id);
			if(!count($rsArt) ) {
				$return['err'] = '数据库未能找到该id对应的文章。';
			}else if($rsArt['col_template_art'] == '') {
				$return['err'] = '该栏目未设置文章模板，请联系管理员处理。';
			}else{
				$sql = "INSERT INTO t_publish (pub_art_id, pub_create_time, pub_create_user) VALUES (?, ?, ?)";
				$parameters = array($art_id, date("YmdHis"), $_SESSION['user_name']);

				$dbt = new dbtemplate();
				if($dbt->update($sql, $parameters)) {
					$return['state'] = 'success';
				}else{
					$return['err'] = '数据库操作出错，请联系管理员处理。';
				}
			}
		}	
	}else if($do == 'addColArt') {
		if($col_id == 0) {
			$return['err'] = '获取栏目id参数出错。';
		}else{
			$rsCol = getDataColumn('column', $col_id);
			if(!count($rsCol) ) {
				$return['err'] = '数据库未能找到该id对应的栏目。';
			}else if($rsCol['col_template_art'] == '' || $rsCol['col_address'] == '') {
				$return['err'] = '该栏目未设置文章模板或者未设置发布地址，请联系管理员处理。';
			}else{
				$artNum = getDataArtNum($col_id);
				if(!$artNum) {
					$return['err'] = '该栏目暂无文章。';
				}else{
					$rsArtList1 = getDataArtList($col_id, 0, $artNum, 123);
					$rsArtList = $rsArtList1['rs'];
					$sql = "INSERT INTO t_publish (pub_art_id, pub_create_time, pub_create_user) VALUES ";
					$l = count($rsArtList);
					for ($i=0; $i < $l; $i++) {
						$d = $rsArtList[$i];
						if($i != $l - 1) {
							$sql .= "(".$d['art_id'].", ".date("YmdHis").", '".$_SESSION['user_name']."'),";
						}else{
							$sql .= "(".$d['art_id'].", ".date("YmdHis").", '".$_SESSION['user_name']."')";
						}
					}

					$dbt = new dbtemplate();
					$rsNum = $dbt->update($sql);

					if($rsNum == $artNum) {
						$return['state'] = 'success';
					}else if($rsNum == 0) {
						$return['err'] = '数据库操作失败，请联系管理员处理。';
					}else{
						$return['err'] = '部分文章发布任务添加失败，请联系管理员处理。';
					}
				}
			}
		}	
	}else if($do == 'getList') {
		$rsPubList = getDataPubList('unfinish', 0, $web['page_size']);
		$return['state'] = 'success';
		$return['data'] = $rsPubList['rs'];
		$return['showNum'] = count($rsPubList['rs']);
		$return['allNum'] = $rsPubList['num'];
	}else if($do == 'run') {
		$rsPub = getDataPublish();
		if(!count($rsPub) ) {
			$return['err'] = 'finished';
		}else{
			$dbt = new dbtemplate();
			$sql1 = "UPDATE t_publish SET pub_state=?, pub_modify_time=?, pub_modify_user=? WHERE pub_id=?";
			$parameters1 = array(1, date("YmdHis"), $_SESSION['user_name'], $rsPub['pub_id']);
			if($dbt->update($sql1, $parameters1) ) {
				if($rsPub['pub_col_id'] != 0) {
					$rsCol = getDataColumn('column', $rsPub['pub_col_id']);
					if(!count($rsCol) ) {
						$sql2 = "UPDATE t_publish SET pub_state=?, pub_reason=? WHERE pub_id=?";
						$parameters2 = array(3, '栏目不存在', $rsPub['pub_id']);
						$dbt->update($sql2, $parameters2);
						$return['state'] = 'success';
					}else{
						$tempName = $rsCol['col_template'];
						$pubAddress = $rsCol['col_address'];
						if($tempName == '' || $pubAddress == '') {
							$sql2 = "UPDATE t_publish SET pub_state=?, pub_reason=? WHERE pub_id=?";
							$parameters2 = array(3, '缺少模板或者发布地址参数', $rsPub['pub_id']);
							$dbt->update($sql2, $parameters2);
							$return['state'] = 'success';
						}else{
							$tempFile = 'driver/'.$tempName;
							if(!file_exists($tempFile)) {
								$sql2 = "UPDATE t_publish SET pub_state=?, pub_template=?, pub_reason=? WHERE pub_id=?";
								$parameters2 = array(3, '模板文件不存在', $tempFile, $rsPub['pub_id']);
								$dbt->update($sql2, $parameters2);
								$return['state'] = 'success';
							}else{
								$tempCon = file_get_contents($tempFile);
								//{{getPageHtml(col_id,contain,page_size,page_view)}}
								//{{getPageHtml(123,false,10,3)}}
								$rule = '/\{\{getPageHtml\((-?\d+),(true|false),(\d+),(\d+)\)\}\}/i';
								preg_match($rule, $tempCon, $getPageReg);

								if(!count($getPageReg) ) {
									ob_start();
									include($tempFile);
									$pubCon = ob_get_contents();
									ob_end_clean();

									if($pubAddress == '/') {
										$pubUrl = '../index.html';
										$dataUrl = 'index.html';
									}else{
										$pubUrl = '../'.$pubAddress.'/index.html';
										$dataUrl = $pubAddress.'/index.html';
									}
									if(f_write($pubUrl,$pubCon) ) {
										$sql2 = "UPDATE t_publish SET pub_state=?, pub_template=?, pub_address=?, pub_page=?, pub_num=? WHERE pub_id=?";
										$parameters2 = array(2, $tempName, $dataUrl, 1, 1, $rsPub['pub_id']);
										$dbt->update($sql2, $parameters2);
										$return['state'] = 'success';
									}else{
										$sql2 = "UPDATE t_publish SET pub_state=? WHERE pub_id=?";
										$parameters2 = array(0, $rsPub['pub_id']);
										$dbt->update($sql2, $parameters2);
										$return['err'] = '写文件操作失败，可能权限不足，请联系管理员处理！';
									}
								}else{
									$sReplace = $getPageReg[0];
									$col_id = $getPageReg[1];
									if($col_id == 0) {
										$col_id = $rsCol['col_id'];
									}
									$bContain = $getPageReg[2];
									$pageSize = $getPageReg[3];
									$pageView = $getPageReg[4];
									$pageLink = $web['http'].$pubAddress.'/index';
									$dataUrl = $pubAddress.'/index.html';
									if($col_id == -1) {
										$artNum = getDataArtNum(0, 2);
									}else{
										$artNum = getDataArtNum($col_id, 2, $bContain);
									}
									$pageTotal = $artNum == 0 ? 1 : ceil($artNum/$pageSize);

									if($pageTotal > $rsPub['pub_num']) {
										$page = $rsPub['pub_num'] + 1;
										ob_start();
										include($tempFile);
										$pubCon = ob_get_contents();
										ob_end_clean();

										$pageHtml = showPagePublish($pageSize, $pageView, $pageTotal, $page, $artNum, $pageLink);
										$pubCon = str_replace($sReplace, $pageHtml, $pubCon);

										$pubUrl = $page == 1 ? '../'.$pubAddress.'/index.html' : '../'.$pubAddress.'/index_'.($page-1).'.html';

										if(!f_write($pubUrl,$pubCon) ) {
											$sql2 = "UPDATE t_publish SET pub_state=? WHERE pub_id=?";
											$parameters2 = array(0, $rsPub['pub_id']);
											$dbt->update($sql2, $parameters2);
											$return['err'] = '写文件操作失败，可能权限不足，请联系管理员处理！';
										}else{
											if($pageTotal == $page) {
												$sql2 = "UPDATE t_publish SET pub_state=?, pub_template=?, pub_address=?, pub_page=?, pub_num=? WHERE pub_id=?";
												$parameters2 = array(2, $tempName, $dataUrl, $pageTotal, $page, $rsPub['pub_id']);
												$dbt->update($sql2, $parameters2);
												$return['state'] = 'success';
											}else{
												$sql2 = "UPDATE t_publish SET pub_state=?, pub_template=?, pub_address=?, pub_page=?, pub_num=? WHERE pub_id=?";
												$parameters2 = array(1, $tempName, $dataUrl, $pageTotal, $page, $rsPub['pub_id']);
												$dbt->update($sql2, $parameters2);
												$return['state'] = 'success';
											}
										}
									}else{
										$sql2 = "UPDATE t_publish SET pub_state=? WHERE pub_id=?";
										$parameters2 = array(2);
										$dbt->update($sql2, $parameters2);
										$return['state'] = 'success';
									}

									$bWrite = true;
									for ($iPub=0; $iPub < $pageTotal; $iPub++) {
										$page = $iPub + 1;
										ob_start();
										include($tempFile);
										$pubCon = ob_get_contents();
										ob_end_clean();

										$pageHtml = showPagePublish($pageSize, $pageView, $pageTotal, $page, $artNum, $pageLink);
										$pubCon = str_replace($sReplace, $pageHtml, $pubCon);

										$pubUrl = $iPub == 0 ? '../'.$pubAddress.'/index.html' : '../'.$pubAddress.'/index_'.$iPub.'.html';

										if(!f_write($pubUrl,$pubCon) ) {
											$bWrite = false;
											break;
										}
										//$pubUrl = $i == 0 ? '../'.$pubAddress.'/index.html' : '../'.$pubAddress.'/index_'.$i.'.html';
										//$pubUrl = $i == 0 ? '../'.$pubAddress.'/index.html' : '../'.$pubAddress.'/index_'.$i.'.html';
										//f_write($pubUrl,'xxx');
									}
									if(!$bWrite) {
										$sql2 = "UPDATE t_publish SET pub_state=? WHERE pub_id=?";
										$parameters2 = array(0, $rsPub['pub_id']);
										$dbt->update($sql2, $parameters2);
										$return['err'] = '写文件操作失败，可能权限不足，请联系管理员处理！';
									}else{
										$sql2 = "UPDATE t_publish SET pub_state=?, pub_template=?, pub_address=? WHERE pub_id=?";
										$parameters2 = array(2, $tempName, $dataUrl, $rsPub['pub_id']);
										$dbt->update($sql2, $parameters2);
										$return['state'] = 'success';
									}
								}
							}
						}
					}
				}else if($rsPub['pub_art_id'] != 0) {
					$rsArt = getDataArticle($rsPub['pub_art_id']);
					if(!count($rsArt) ) {
						$sql2 = "UPDATE t_publish SET pub_state=?, pub_reason=? WHERE pub_id=?";
						$parameters2 = array(3, '文章不存在', $rsPub['pub_id']);
						$dbt->update($sql2, $parameters2);
						$return['state'] = 'success';
					}else{
						$tempName = $rsArt['col_template_art'];
						$pubDir = $rsArt['col_address'].'/'.substr($rsArt['art_create_time'], 0, 6);
						$pubUrl = '../'.$pubDir.'/'.$rsArt['art_id'].'.html';
						if($tempName == '') {
							$sql2 = "UPDATE t_publish SET pub_state=?, pub_reason=? WHERE pub_id=?";
							$parameters2 = array(3, '缺少模板或者发布地址参数', $rsPub['pub_id']);
							$dbt->update($sql2, $parameters2);
							$return['state'] = 'success';
						}else{
							$tempFile = 'driver/'.$tempName;
							if(!file_exists($tempFile)) {
								$sql2 = "UPDATE t_publish SET pub_state=?, pub_reason=?, pub_template=?, pub_address=? WHERE pub_id=?";
								$parameters2 = array(3, '模板文件不存在', $tempName, '/'.$pubDir.'/'.$rsArt['art_id'].'.html', $rsPub['pub_id']);
								$dbt->update($sql2, $parameters2);
								$return['state'] = 'success';
							}else{
								if(f_mkdir($pubDir) ) {
									ob_start();
									include($tempFile);
									$pubCon = ob_get_contents();
									ob_end_clean();

									if(f_write($pubUrl,$pubCon) ) {
										$sql2 = "UPDATE t_publish SET pub_state=?, pub_template=?, pub_address=?, pub_page=?, pub_num=? WHERE pub_id=?";
										$parameters2 = array(2, $tempName, '/'.$pubDir.'/'.$rsArt['art_id'].'.html', 1, 1, $rsPub['pub_id']);
										
										$sql3 = "UPDATE t_article SET art_state=? WHERE art_id=?";
										$parameters3 = array(2, $rsArt['art_id']);

										$dbt->update($sql2, $parameters2);
										$dbt->update($sql3, $parameters3);

										$return['state'] = 'success';
									}else{
										$sql2 = "UPDATE t_publish SET pub_state=? WHERE pub_id=?";
										$parameters2 = array(0, $rsPub['pub_id']);
										$dbt->update($sql2, $parameters2);
										$return['err'] = '写文件操作失败，可能权限不足，请联系管理员处理！';
									}
								}else{
									$sql2 = "UPDATE t_publish SET pub_state=? WHERE pub_id=?";
									$parameters2 = array(0, $rsPub['pub_id']);
									$dbt->update($sql2, $parameters2);
									$return['err'] = '创建目录失败，可能权限不足，请联系管理员处理！';
								}
							}
						}
					}
				}
			}else{
				$return['err'] = '修改数据库失败，请联系管理员处理！';
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