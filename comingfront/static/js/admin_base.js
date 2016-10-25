//弹窗 start
    $(document).ready(function() {
        !function() {
            var $bPopMark = $("#bPopMark"),
                bPopMark = $bPopMark[0],
                $bPopBox = $("#bPopBox"),
                bPopBox = $bPopBox[0],
                $body = $("body");
            var fPop = {
                open: function(html) {
                    if($bPopBox.css("display") == "block") {
                        fPop.change(html);
                    }else{
                        bPopMark.style.height = (document.body.scrollHeight < document.documentElement.clientHeight) ? document.documentElement.clientHeight + "px" : document.body.scrollHeight + "px";
                        $bPopMark.show();
                        $bPopBox.html(html).show();
                        bPopBox.style.top = Math.max(document.documentElement.scrollTop,document.body.scrollTop) + document.documentElement.clientHeight/2 - bPopBox.clientHeight/2 + "px";
                        $body.css("overflow","hidden");
                    }
                },
                change: function(html) {
                    $bPopBox.html(html);
                    bPopBox.style.top = Math.max(document.documentElement.scrollTop,document.body.scrollTop) + document.documentElement.clientHeight/2 - bPopBox.clientHeight/2 + "px";
                },
                close: function() {
                    $bPopBox.html('').hide();
                    $bPopMark.hide();
                    $body.css("overflow","auto");
                },
                fade: function() {
                    $bPopBox.fadeOut(200);
                    $bPopMark.fadeOut(200);
                    $body.css("overflow","auto");
                }
            }
            window.fPop = fPop;
        }();
    });
//弹窗 end


//时间格式化 start
    function formatDate(num, mode) {
        if(num == 0) {
            return '';
        }

        var html,

        html = mode.replace(/y+/gi, function(con) {
            var len = con.length;
            var start;
            if(len < 5) {
                start = 4 - len;
                return num.substr(start, len);
            }else{
                return con;
            }
        });

        html = html.replace(/m+/gi, function(con) {
            var len = con.length;
            var numItem = num.substr(4, 2);
            if(len == 1) {
                return parseInt(numItem);
            }else if(len == 2) {
                return numItem;
            }else{
                return con;
            }
        });

        html = html.replace(/d+/gi, function(con) {
            var len = con.length;
            var numItem = num.substr(6, 2);
            if(len == 1) {
                return parseInt(numItem);
            }else if(len == 2) {
                return numItem;
            }else{
                return con;
            }
        });

        html = html.replace(/h+/gi, function(con) {
            var len = con.length;
            var numItem = num.substr(8, 2);
            if(len == 1) {
                return parseInt(numItem);
            }else if(len == 2) {
                return numItem;
            }else{
                return con;
            }
        });

        html = html.replace(/i+/gi, function(con) {
            var len = con.length;
            var numItem = num.substr(10, 2);
            if(len == 1) {
                return parseInt(numItem);
            }else if(len == 2) {
                return numItem;
            }else{
                return con;
            }
        });

        html = html.replace(/s+/gi, function(con) {
            var len = con.length;
            var numItem = num.substr(12, 2);
            if(len == 1) {
                return parseInt(numItem);
            }else if(len == 2) {
                return numItem;
            }else{
                return con;
            }
        });

        return html;
    }
//时间格式化 end


//tab start
    ;!function() {
        var fTab = function($tab, $con, nCur, sClass) {
            nCur = nCur || 0;
            sClass = sClass || 'cur';
            $tab.each(function(i) {
                if(i != nCur) {
                    $(this).removeClass(sClass);
                }else{
                    $(this).addClass(sClass);
                }
                $(this).click(function() {
                    if(i != nCur) {
                        $tab.eq(nCur).removeClass(sClass);
                        $con.eq(nCur).removeClass(sClass);
                        nCur = i;
                        $tab.eq(nCur).addClass(sClass);
                        $con.eq(nCur).addClass(sClass);
                    }
                });
            });
            $con.each(function(i) {
                if(i != nCur) {
                    $(this).removeClass(sClass);
                }else{
                    $(this).addClass(sClass);
                }
            });
        }

        window.fTab = fTab;
    }();
//tab end


