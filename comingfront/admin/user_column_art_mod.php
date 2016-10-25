<?php
	require_once('reqAdmin.php');

	$art_id = isset($_REQUEST['art_id']) ? $_REQUEST['art_id'] : '';
	$art_id = ctype_digit($art_id) ? $art_id : 0;
	if($art_id == 0) {
		err("未能获取到文章id！");
	}

	$rsArt = getDataArticle($art_id);
	if(!count($rsArt) ){
		err("数据库中未能找到该 id 对应的文章！");
	}

	$rsImg = getDataImg('article', $rsArt['art_id']);
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
			<a href="user_column_art.php?col_id=<?=$rsArt['art_col_id']?>"><?=$rsArt['col_name']?>-文章列表</a> >
			<em>修改文章</em>
		</div>
	</div>


	<!--main star-->
	<div class="main pArtMod">

		<div class="inner cf">

			<div class="bLayA">
				<dl class="bBoxA bBoxAa">
					<dt>
						<strong>修改文章</strong>
					</dt>
					<dd>
						<ul class="bListA bListAa">
							<li class="cf">
								<i class="td1">标题：</i>
								<i class="td2"><input type="text" class="bInpA" id="art_title" value="<?=$rsArt['art_title']?>" /></i>
								<i class="td1">作者：</i>
								<i class="td2"><input type="text" class="bInpA" id="art_author" value="<?=$rsArt['art_author']?>" /></i>
							</li>
							<li class="cf">
								<i class="td1">导读图：</i>
								<i class="td2"><input type="text" class="bInpA" id="art_img" value="<?=$rsArt['art_img']?>" /></i>
								<i class="td5" id="art_img_preview"></i>
								<i class="td4"><em>*</em>直接填入图片地址</i>
							</li>
							<li class="cf">
								<i class="td1">tags：</i>
								<i class="td3"><input type="text" class="bInpA" id="art_tag" value="<?=$rsArt['art_tag']?>" /></i>
								<i class="td4"><em>*</em>多个请用半角逗号分隔</i>
							</li>
							<li class="cf">
								<i class="td1">来源：</i>
								<i class="td3"><input type="text" class="bInpA" id="art_source" value="<?=$rsArt['art_source']?>" /></i>
								<i class="td4"><em>*</em>直接用a标签包裹来源地址</i>
							</li>
							<li class="cf">
								<i class="td1">简介：</i>
								<i class="td3"><textarea class="bAreaA" id="art_intro"><?=$rsArt['art_intro']?></textarea></i>
								<i class="td4"><em>*</em>不填自动获取文章前100字</i>
							</li>
							<li class="cf">
								<textarea class="artCon" name="art_con" id="art_con"><?php echo htmlspecialchars($rsArt['art_con']); ?></textarea>
							</li>

							<li class="submit">
								<input type="hidden" id="art_id" value="<?=$rsArt['art_id']?>" />
								<a class="bBtnA" href="javascript:articleModify();">保存</a>
								<a class="bBtnA" href="user_column_art.php?col_id=<?=$rsArt['art_col_id']?>">返回</a>
							</li>
						</ul>
					</dd>
				</dl>
			</div>

			<div class="bLayB">
				<dl class="bBoxA bBoxA_img">
					<dt>
						<strong>文章图片(<i id="bPicListA_num"><?=count($rsImg)?></i>)</strong>
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
						<a href="javascript:upLoadImg('article', <?=$art_id?>);">+上传图片</a>
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


	<script charset="utf-8" src="plugins/kindeditor/kindeditor-all.js"></script>
	<script charset="utf-8" src="plugins/kindeditor/lang/zh-CN.js"></script>

	<script>
		KindEditor.lang({
            iframe : 'iframe',
            url: 'url'
        });
		KindEditor.ready(function(K) {
			K.create('#id', { 
                items : ['iframe'] 
            }); 
			window.editor1 = K.create('textarea[name="art_con"]', {
				items : ['source', '|', 'undo', 'redo', '|', 'justifyleft', 'justifycenter', 'justifyright', '|', 'fontsize', 'forecolor',  'bold', 'italic', 'underline', 'strikethrough', '|', 'code', 'link', 'unlink', '|', 'table', 'hr', 'iframe'],
				resizeType: 1
			});
			$("#bPicListA").on('click', 'img', function() {
				var url = $(this).attr('src');
				K.insertHtml('textarea[name="art_con"]', '<img src="'+url+'" />');
			});
		});



		function articleModify() {
			fPop.open('<div class="bPopLoading"><img width="32" height="32" src="../static/bg/loading.gif" /><br />正在加载</div>');
			editor1.sync();
			$.ajax({
				url: 'intArticle.php?do=modify',
				type: 'post',
				data: {
					art_id: $("#art_id").val(),
					art_title: $("#art_title").val(),
					art_author: $("#art_author").val(),
					art_img: $("#art_img").val(),
					art_tag: $("#art_tag").val(),
					art_source: $("#art_source").val(),
					art_intro: $("#art_intro").val(),
					art_con: $("#art_con").val()
				},
				success: function(data) {
					if(data.state == "success") {
						fPop.change('<div class="bPopTxt"><strong class="tit">文章保存成功！</strong></div>');
						setTimeout(function() {
							fPop.fade();
						}, 1500);
					}else{
						fPop.change('<div class="bPopTxt"><strong class="tit">文章保存失败！</strong><p class="con">'+data.err+'</p></div>');
						setTimeout(function() {
							fPop.fade();
						}, 1000);
					}
				},
				error: function() {
					fPop.change('<div class="bPopTxt"><strong class="tit">文章保存失败！</strong><p class="con">可能是网络原因，请稍后再试。</p></div>');
					setTimeout(function() {
						fPop.fade();
					}, 1500);
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
				block_id = 0,
				art_id = 0;
			if(type == 'column') {
				col_id = id;
			}else if(type == 'block'){
				block_id = id;
			}else if(type == 'article'){
				art_id = id;
			}
			$.ajax({
				url: 'intImg.php?do=add',
				type: 'post',
				data: {
					col_id: col_id,
					block_id: block_id,
					art_id: art_id,
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



		$("#art_img").on('change', function() {
			if($("#art_img").val() != '') {
				$("#art_img_preview").html('<img src="'+$("#art_img").val()+'" />');
			}else{
				$("#art_img_preview").html('');
			}
		});
	</script>
</body>

</html>



