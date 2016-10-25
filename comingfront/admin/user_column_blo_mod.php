<?php
	require_once('reqAdmin.php');

	$blo_id = isset($_REQUEST['blo_id']) ? $_REQUEST['blo_id'] : '';
	$blo_id = ctype_digit($blo_id) ? $blo_id : 0;
	if($blo_id == 0) {
		err("未能获取到内嵌id！");
	}

	$rsBlo = getDataBlock('block', $blo_id);
	if(!count($rsBlo) ){
		err("数据库中未能找到该 id 对应的内嵌！");
	}

	$rsImg = getDataImg('block', $rsBlo['blo_id']);
?>
<!doctype HTML>
<html>

<head>

	<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
	<meta content="telephone=no" name="format-detection" />
	<title><?=$web['name']?></title>
	<meta name="keywords" content="<?=$web['keywords']?>" />
	<meta name="description" content="<?=$web['description']?>" />
	<link rel="stylesheet" type="text/css" href="<?=$web['http']?>static/css/admin.css" />
	<script type="text/javascript" src="<?=$web['http']?>static/js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="<?=$web['http']?>static/js/admin_base.js"></script>

</head>

<body>

	<!--top star-->
	<?php include 'incHeader.php'; ?>
	<!--top end-->


	<div class="bBread">
		<div class="inner">
			当前位置：<a href="user_index.php">后台首页</a> > 
			<a href="user_column.php">栏目管理</a> > 
			<a href="user_column_blo.php?col_id=<?=$rsBlo['blo_col_id']?>"><?=$rsBlo['col_name']?>-内嵌列表</a> > 
			<em>修改内嵌</em>
		</div>
	</div>


	<!--main star-->
	<div class="main pBloMod">

		<div class="inner cf">

			<div class="bLayA">
				<dl class="bBoxA bBoxAa">
					<dt>
						<strong>修改内嵌</strong>
					</dt>
					<dd>
						<ul class="bListA bListAa">
							<li class="cf">
								<i class="td1">标题：</i>
								<i class="td2"><input type="text" class="bInpA" id="blo_title" value="<?=$rsBlo['blo_title']?>" /></i>
							</li>

							<li class="cf">
								<i class="td1">内容：</i>
								<i class="td2">
									<textarea class="bloCon" id="blo_con"><?=$rsBlo['blo_con']?></textarea>
								</i>
							</li>

							<li class="submit">
								<input type="hidden" id="blo_id" value="<?=$rsBlo['blo_id']?>" />
								<a class="bBtnA" href="javascript:blockModify();">保存</a>
								<a class="bBtnA" href="user_column_blo.php?col_id=<?=$rsBlo['blo_col_id']?>">返回</a>
							</li>
						</ul>
					</dd>
				</dl>
			</div>

			<div class="bLayB">
				<dl class="bBoxA bBoxA_img">
					<dt>
						<strong>内嵌图片(<i id="bPicListA_num"><?=count($rsImg)?></i>)</strong>
					</dt>
					<dd>
						<ul class="bPicListA cf" id="bPicListA">
							<?php
								if(count($rsImg) == 0) {
									echo '<li class="none">暂无图片</li>';
								}else{
									$html = '';
									for ($i=0; $i < count($rsImg); $i++) {
										$d = $rsImg[$i];
										$html .= '<li id="bPicListA_'.$d['img_id'].'"><a class="pic" href="javasrcpt:void(0);"><i></i><img src="'.$web['http'].$d['img_url'].'" /></a><i class="btn"><a href="'.$web['http'].$d['img_url'].'" target="_blank">链接</a><a href="javascript:delImgConfirm('.$d['img_id'].');">删除</a></i></li>';
									}
									echo $html;
								}
							?>
						</ul>
					</dd>
					<dd class="add">
						<a href="javascript:upLoadImg('block', <?=$blo_id?>);">+上传图片</a>
						<iframe class="bPicListA_target" id="bPicListA_target" name="bPicListA_target"></iframe>
					</dd>
				</dl>
			</div>

		</div>

	</div>
	<!--main end-->
	


	<!--footer star-->
	<?php include 'incFooter.php'; ?>
	<!--footer end-->


	<script>
		function blockModify() {
			fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
			$.ajax({
				url: 'intBlock.php?do=modify',
				type: 'post',
				data: {
					blo_id: $("#blo_id").val(),
					blo_title: $("#blo_title").val(),
					blo_con: $("#blo_con").val()
				},
				success: function(data) {
					if(data.state == "success") {
						fPop.open('<div class="bPopTxt"><strong class="tit">内嵌保存成功！</strong></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}else{
						fPop.open('<div class="bPopTxt"><strong class="tit">内嵌保存失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1000);
					}
				},
				error: function() {
					fPop.open('<div class="bPopTxt"><strong class="tit">内嵌保存失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}



		//上传图片入口
		function upLoadImg(type, id) {
			if(!type || !id) {
				fPop.open('<div class="bPopTxt"><strong class="tit">上传失败！</strong><p class="con">请联系管理员处理。</p></div>');
				setTimeout(function() {fPop.fade();}, 1500);
			}else{
				window['uploadImgType'] = type;
				window['uploadImgId'] = id;
				fPop.open('<div class="bPopUpload"><form id="submit_form" method="post" action="user_upload.php" target="bPicListA_target" enctype="multipart/form-data"><div class="top"><input type="file" name="file" id="file" onclick="$(\'#submit_txt\').hide();" /></div><div class="mid"><span id="submit_txt"><em>*</em>请先选择图片。</span></div><div class="bot cf"><a class="bBtnA" href="javascript:upLoadImgSubmit();">上传</a><a class="bBtnA" href="javascript:fPop.fade();">取消</a></div></form></div>');
			}
		}

		//提交上传图片表单
		function upLoadImgSubmit() {
			if($("#submit_form").length && $("#file").length) {
				if($("#file").val() != '') {
					$("#submit_form").submit();
					fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在上传</div>');
				}else{
					$("#submit_txt").show();
				}
			}else{
				fPop.open('<div class="bPopTxt"><strong class="tit">上传失败！</strong><p class="con">请联系管理员处理。</p></div>');
				setTimeout(function() {fPop.fade();}, 1500);
			}
		}

		//把图片加入数据库中
		function addImg(url, type, id) {
			window['uploadImgUrl'] = url;
			var col_id = 0,
				blo_id = 0,
				article_id = 0;
			if(type == 'column') {
				col_id = id;
			}else if(type == 'block'){
				blo_id = id;
			}else if(type == 'article'){
				article_id = id;
			}
			$.ajax({
				url: 'intImg.php?do=add',
				type: 'post',
				data: {
					col_id: col_id,
					blo_id: blo_id,
					article_id: article_id,
					img_url: url
				},
				success: function(data) {
					if(data.state == "success") {
						fPop.open('<div class="bPopTxt"><strong class="tit">图片保存数据库成功！</strong></div>');
						setTimeout(function() {fPop.fade();}, 1500);
						var html = '<li id="bPicListA_'+data.newId+'"><a class="pic" href="javascript:void(0);"><i></i><img src="<?=$web['http']?>'+window['uploadImgUrl']+'" /></a><i class="btn"><a href="<?=$web['http']?>'+window['uploadImgUrl']+'" target="_blank">链接</a><a href="javascript:delImgConfirm('+data.newId+');">删除</a></i></li>';
						if($('#bPicListA_num').html() == '0') {
							$('#bPicListA').html(html);
						}else{
							$('#bPicListA').prepend(html);
						}
						$('#bPicListA_num').html(parseInt($('#bPicListA_num').html())+1);
					}else{
						fPop.open('<div class="bPopTxt"><strong class="tit">图片保存数据库失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				},
				error: function() {
					fPop.open('<div class="bPopTxt"><strong class="tit">图片保存数据库失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}

		//从数据库中删除图片
		function delImgConfirm(id) {
			fPop.open('<div class="bPopTxt"><strong class="tit">删除图片！</strong><p class="con">您确定要删除该图片吗？</p><div class="btn cf"><a class="bBtnA" href="javascript:fPop.fade();">取消</a><a class="bBtnA" href="javascript:delImg('+id+');">确定</a></div>');
		}
		function delImg(id) {
			window['delImgId'] = id;
			fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
			$.ajax({
				url: 'intImg.php?do=del&img_id=' + id,
				type: 'get',
				success: function(data) {
					if(data.state == "success") {
						fPop.open('<div class="bPopTxt"><strong class="tit">删除图片成功！</strong></div>');
						setTimeout(function() {fPop.fade();}, 1500);
						$('#bPicListA_'+window['delImgId']).remove();
						$('#bPicListA_num').html(parseInt($('#bPicListA_num').html())-1);
					}else{
						fPop.open('<div class="bPopTxt"><strong class="tit">删除图片失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {fPop.fade();}, 1500);
					}
				},
				error: function() {
					fPop.open('<div class="bPopTxt"><strong class="tit">删除图片失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			});
		}


		$("#bPicListA_target").load(function(){
			var sData = $(window.frames['bPicListA_target'].document.body).find("textarea").val();
			if(!!sData){
				var aData = sData.split('|');
				if(aData[0] == '1') {
					addImg(aData[1], window['uploadImgType'], window['uploadImgId']);
				}else{
					fPop.open('<div class="bPopTxt"><strong class="tit">上传失败！</strong><p class="con">'+aData[1]+'</p></div>');
					setTimeout(function() {fPop.fade();}, 1500);
				}
			}
		});
	</script>
</body>

</html>



