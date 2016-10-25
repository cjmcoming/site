

KindEditor.plugin('iframe', function(K) {
	var self = this, name = 'iframe';
	self.plugin.iframe = {
		edit : function() {
			var lang = self.lang(name + '.');
			var html = '<div style="padding:20px;">' +
					//url
					'<div class="ke-dialog-row">' +
					'<label for="keUrl" style="width:60px;">url：</label>' +
					'<input class="ke-input-text" type="text" id="keUrl" name="url" value="" style="width:260px;" /></div>' +
					'</div>',
				dialog = self.createDialog({
					name : name,
					width : 450,
					title : self.lang(name),
					body : html,
					yesBtn : {
						name : self.lang('yes'),
						click : function(e) {
							var url = K.trim(urlBox.val());
							if (url == 'http://' || K.invalidUrl(url)) {
								alert(self.lang('invalidUrl'));
								urlBox[0].focus();
								return;
							}
							// self.exec('createlink', url, typeBox.val()).hideDialog().focus();
							self.insertHtml('<iframe src="'+url+'" frameborder="0"></iframe>');
							self.hideDialog().focus();
						}
					}
				}),
				div = dialog.div,
				urlBox = K('input[name="url"]', div);
			urlBox.val('http://');
			urlBox[0].focus();
			urlBox[0].select();
		}
	};
	self.clickToolbar(name, self.plugin.iframe.edit);
});

