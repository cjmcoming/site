(function(){
	window.oDevice = {};
	oDevice.width =  parseInt(window.screen.width);
    oDevice.scale = oDevice.width/640;
	var ua = navigator.userAgent.toLowerCase(),
	ipad = ua.match(/ipad/i) == "ipad",
    ipadmini = ua.match(/mi-pad/i) == "mi-pad",
    ipod = ua.match(/ipod/i) == "ipod",
    iphone = ua.match(/iphone/i) == "iphone",
    android = ua.match(/android/i) == "android",
    mobile = ua.match(/mobile/i) == "mobile",
    winPhone = ua.match(/windows phone/i) == "windows phone",
    symbian = ua.match(/symbian/i) == "symbian",
    nokia = ua.match(/nokia/i) == "nokia",
    winCe = ua.match(/windows ce/i) == "windows ce",
    rv = ua.match(/rv:1.2.3.4/i) == "rv:1.2.3.4",
    winNt = ua.match(/windows nt/i) == "windows nt",
    trident = ua.match(/trident/i) == "trident",
    bb = ua.match(/blackberry/i) == "blackberry";
    if(ipod || iphone || (android && mobile) || winPhone || symbian || nokia || winCe || rv || bb) {
    	window.oDevice.name = "phone";
    }else if(ipad || (android && (!mobile) ) ) {
    	window.oDevice.name = "pad";
    }else{
    	window.oDevice.name = "pc";
    }
    if(window.oDevice.name != "pc") {
		if(/Android (\d+\.\d+)/.test(ua) ) {
            var version = parseFloat(RegExp.$1);
            if(version>2.3){
                document.write('<meta name="viewport" content="width=640, minimum-scale = '+oDevice.scale+', maximum-scale = '+oDevice.scale+', target-densitydpi=device-dpi">');
            }else{
                document.write('<meta name="viewport" content="width=640, target-densitydpi=device-dpi">');
            }
        }else{
            document.write('<meta name="viewport" content="width=640, user-scalable=no, target-densitydpi=device-dpi">');
        }
    }
})()