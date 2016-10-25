!function() {
    function addEvent(ele, eType, func) {
        if(ele.addEventListener){
            ele.addEventListener(eType, func, false);
        }else if(ele.attachEvent){
            ele.attachEvent("on"+eType, func);
        }
    }

    function setIframe(o) {
        var frameContent = o.contentWindow.document, bodyHeight;
        if(frameContent.body !== null && frameContent.documentElement !== null) {
            bodyHeight = Math.max(frameContent.body.scrollHeight, frameContent.documentElement.scrollHeight);
            if (bodyHeight != o.height) o.height = bodyHeight;
        }
    }

    var oIframe = document.getElementById("sArtCon").getElementsByTagName("iframe");

    if(oIframe.length) {
        for (var i = 0; i < oIframe.length; i++) {
            (function(i) {
                addEvent(oIframe[i], 'load', function() {
                    setIframe(oIframe[i]);
                });
                setIframe(oIframe[i]);
            }) (i);
        }

        addEvent(window, 'resize', function() {
            for (var i = 0; i < oIframe.length; i++) {
                oIframe[i].height = 100;
                setIframe(oIframe[i]);
            }
        });
    }
} ();








