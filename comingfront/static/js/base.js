//slider start
    function Slider(o) {
        this.con = o.con;
        this.tab = o.tab;
        this.numAll = this.con.length;
        this.numNow = 0;
        this.lock = false;
        this.auto = true;
        this.init();
    }
    Slider.prototype = {
        init:function() {
            var _self = this;
            this.con.mouseover(function(){_self.auto = false;});
            this.con.mouseout(function(){_self.auto = true;});
            this.tab.mouseover(function(){_self.auto = false;});
            this.tab.mouseout(function(){_self.auto = true;});
            this.tab.each(function(i){
                $(this).click(function(){
                    if(!_self.lock) {
                        _self.movie(i);
                    }
                });
            });
            setInterval(function(){
                if(!_self.lock && _self.auto) {
                    _self.movie(_self.numNow+1);
                }
            },3000);
        },
        movie:function(numNew) {
            if(this.numNow != numNew && !this.lock) {
                this.lock = true;
                numNew = numNew > (this.numAll-1) ? 0 : numNew;
                numNew = numNew < 0 ? (this.numAll-1) : numNew;
                var _self = this;
                this.con.eq(numNew).fadeIn(500,function(){_self.lock = false;});
                this.con.eq(this.numNow).fadeOut(500);
                this.tab.eq(this.numNow).removeClass("cur");
                this.tab.eq(numNew).addClass("cur");
                this.numNow = numNew;
            }
        }
    };
//slider end

//slider start
    function SliderA(o) {
        this.conBox = o.conBox;
        this.con = o.con;
        this.numAll = this.con.length;
        this.numView = o.numView;
        this.numNow = o.numNow > (this.numAll - o.numView) ? (this.numAll - o.numView) : o.numNow < 0 ? 0 : o.numNow;
        this.numWidth = o.numWidth;
        this.bLeft = o.bLeft;
        this.bRight = o.bRight;
        this.lock = false;
        this.init();
    }
    SliderA.prototype = {
        init:function() {
            var _self = this;

            if(_self.numNow > 0) {
                _self.conBox.css('left', - _self.numNow * _self.numWidth)
            }

            _self.check();

            this.bLeft.click(function() {
                _self.movie(_self.numNow - 1);
            });
            this.bRight.click(function() {
                _self.movie(_self.numNow + 1);
            });
        },
        check:function() {
            var _self = this;

            if(_self.numNow == 0) {
                _self.bLeft.hide();
            }else{
                _self.bLeft.css("display", "block");
            }

            if(_self.numNow == (_self.numAll - _self.numView) ) {
                _self.bRight.hide();
            }else{
                _self.bRight.css("display", "block");
            }
        },
        movie:function(numNew) {
            var _self = this;

            if(_self.numNow != numNew && !_self.lock) {
                _self.lock = true;
                numNew = numNew > (_self.numAll - _self.numView) ? (_self.numAll - _self.numView) : numNew < 0 ? 0 : numNew;
                var nLeft = - numNew * this.numWidth;
                _self.conBox.animate({'left':nLeft}, 300, function(){_self.lock = false;});
                _self.numNow = numNew;
                _self.check();
            }
        }
    };
//slider end








