var tolM = 0,
	tolNum = 0;
$(function() {
	$(window).scroll(function() {
		if ($(document).scrollTop() > 200) {
			$('#to-top').fadeIn();
		} else {
			$('#to-top').fadeOut();
		}
	});
	$('#to-top').click(function() {
		$('body,html').animate({
			scrollTop: 0
		}, 500);
	});
	$(".good-choose-con").children("div").click(function(){
		var _this = $(this);
		_this.siblings("div").removeClass("good-chosen");
		if(!_this.attr("class")){
			_this.addClass("good-chosen");
			var _p = _this.children("p").clone();
			$("#good-footer").children("p").html(_p);
		}		
	});
	$("#to-exchange").click(function(){
		var _this = $(this);
		if(!_this.attr("data-num")){
			_this.attr("data-num","true");
			$("#good-num").show();
		}else{
			location.href="credit_order.html";
		}		
	});
	$(".add-good").click(function(){
		var _num = parseInt($(this).siblings(".num-good").html())+1;
		$(this).siblings(".num-good").html(_num);
	});
	$(".minus-good").click(function(){
		var _num = parseInt($(this).siblings(".num-good").html())-1;
		if(_num <= 0){
			alert("兑换数量不能小于1");
		}else{
			$(this).siblings(".num-good").html(_num);
		}    
    });
})
$(window).load(function() {
	$(".container").show();
	//大图banner初始化
	$("#good-banner").bsBanner({
		'slider_bom': 'img', //slider-con内的方块元素，可为BOM元素
		'banner_width': 0, //banner宽度，单位为px，设置为0则默认100%
		'banner_height': 0, //banner高度，单位为px，设置为0则默认自适应第一张banner方块高度
		'touch': true, //开启触屏滑动
		'mouse_hover': false, //开启鼠标hover时可左右翻页效果
		'nav': true //开启小选项圈，可选择查看第几张图片
	});
    show_goods_desc();
});

(function($, window, document, undefined) {
	$.fn.bsBanner = function(options){
		//创建banner实体
		var bsBanner = new BsBanner(this,options);
		//调用其方法
		bsBanner.ready();
		if(options.nav) bsBanner.addNav();
		if(options.touch) bsBanner.touchMove();
		if(options.mouse_hover) bsBanner.mouseHover();
		if(options.window_resize){
			$(window).resize(function() {
				bsBanner.windowResize();
			});
		} 
	}
	
	var BsBanner = function(ele,opt){
		this.$ele = ele,
		this.defaults = {
			'slider_con':null,
			'slider_con_id':null,
			'slider':null,
			'nav_con':null,
			'banner':null,
			'banner_width':0,
			'banner_height':0,
			'banner_now':1,
			'banner_int':null,
			'num':0
		},
		this.options = $.extend({}, this.defaults, opt);
	}
	BsBanner.prototype = {
		//banner初始化
		ready: function(){	
			
			var that = this,
				_ele = $(that.$ele),
				_options = that.options,
				_slider_con = _options.slider_con = _ele.find(".slider-con"),
				_banner = _options.banner = _ele,							
				_slider = _options.slider = _ele.find(".slider"),
				_slider_con_id = _options.slider_con_id = "slider-con-"+_ele.attr("id"),
				_boms = _slider_con.children(_options.slider_bom),
				_width = _options.banner_width || $(_banner).width();
				
			_slider_con.attr("id",_slider_con_id)
			if(_options.banner_width) $(_banner).css({"width":_options.banner_width});
			
			//初始化
						
			_slider_con.append(_boms.first().clone(true));	
			_boms = _slider_con.children(_options.slider_bom);
			_options.num = _boms.length;
					
			_boms.each(function() {
				$(this).css({
					"width": _width + "px",
					"height": _height?_height+"px" : "auto",
					"float":"left"
				});				
			});		
			
			var _height = _options.banner_height?_options.banner_height+"px" : _boms.height();
			_slider_con.css({"height": _height});
			_options.slider.css({"height": _height});		
					
			_options.banner_int = window.setInterval(function(){
				that.bannerMove('right');
			}, 3000);
						
			return this;
		},
		windowResize: function(){
			var that = this,
				_options = that.options,
				_slider_con = _options.slider_con,
				_boms = _slider_con.children(_options.slider_bom),
				_width = _options.banner_width || _options.banner.width();		
			if(_options.banner_width) _options.banner.css({"width":_options.banner_width});
						
			_boms = _slider_con.children(_options.slider_bom);
			_options.num = _boms.length;
					
			_boms.each(function() {
				$(this).css({
					"width": _width + "px",
					"height": _height?_height+"px" : "auto",
					"float":"left"
				});				
			});		
			
			var _height = _options.banner_height?_options.banner_height+"px" : _boms.height();
			_slider_con.css({"height": _height});
			$(_options.slider).css({"height": _height});		
					
			window.clearInterval(_options.banner_int);
			_options.banner_int = window.setInterval(function(){
				that.bannerMove('right');
			}, 3000);
						
			return this;
		},
		//增加小选项圈
		addNav: function(){
			var that = this,
				_options = that.options,
				_nav_con = _options.nav_con = $('<nav class="nav-con"></nav>'),
				_boms = _options.slider_con.children(_options.slider_bom),
				_num = _boms.length;
				
			_boms.each(function(index, value) {
				if(index == _num-1) return;
				var _a = $("<a></a>");
				$(_a).click(function() {
					that.bannerChange(index);
				});
				_nav_con.append(_a);
			});	
			
			_nav_con.find("a").eq(0).addClass("nav-chose");	
			$(_options.slider).append(_nav_con);
		},
		//banner触屏判断
		touchMove: function(){
			var that = this,
				_options = that.options,
				sliderIMG = document.getElementById(_options.slider_con_id);
				
			sliderIMG.addEventListener("touchstart", touchStart, false);
			
			function touchStart(event) {
				if (!event.touches.length) return;
				//event.preventDefault();
				var touch = event.touches[0];
				touchmoveX = touchmoveY = 0;
				touchstartX = touch.pageX;
				touchstartY = touch.pageY;
				sliderIMG.addEventListener("touchend", touchEnd, false);
				sliderIMG.addEventListener("touchmove", touchMove, false);
			}
			function touchMove(event) {
				var touch = event.touches[0];
				touchmoveX = touch.pageX - touchstartX;
				touchmoveY = touch.pageY - touchstartY;
				if (-10 < touchmoveY && touchmoveY < 10) {
					event.preventDefault();
				}
			}		
			function touchEnd(event) {
				if (touchmoveX > 30) {
					that.bannerMove('left');
				} else if (touchmoveX <= -30) {
					that.bannerMove('right');
				}
				sliderIMG.removeEventListener('touchend', touchEnd, false);
				sliderIMG.removeEventListener("touchmove", touchMove, false);
			}
			
			return this;
		},
		//鼠标放置效果
		mouseHover: function(){
			var that = this,
				_options = that.options;
			var _direction_con = $('<div class="direction-con"></div>'),
				_left = $('<div class="left"><img src="./img/left.png" /></div>'),
				_right = $('<div class="right"><img src="./img/right.png" /></div>');
			_left.click(function(){
				that.bannerMove('left');
			});
			_right.click(function(){
				that.bannerMove('right');
			});
			_direction_con.append(_left,_right);
			$(_options.slider).append(_direction_con);
		},
		//banner左右滑动
		bannerMove: function(direction){
			var _now, _move, _totalmove,
				that = this,
				_options = that.options,
				_slider_con = $(_options.slider_con),
				_width = $(_options.banner).width(),
				
				
			_totalmove = -(_options.num - 1) * _width;
			_now = _options.banner_now;
			
			
			if (direction == "left") {
				if (_now == 1) {
					_now = _options.num - 1;
					_slider_con.css("left", _totalmove);
				} else _now--;
			}else if (direction == "right") {
				if (_now == _options.num) {
					_now = 2;
					_slider_con.css("left", 0);
				} else _now++;
			}
			
			_move = -(_now - 1) * _width;
			_options.banner_now = _now;
			
			_slider_con.animate({
				left: _move
			}, 200);			
			
			if(_options.nav_con){
				_a = $(_options.nav_con).find("a");
				_a.removeClass("nav-chose");
				if (_now == _options.num) {
					_a.eq(0).addClass("nav-chose");
				} else {
					_a.eq(_now - 1).addClass("nav-chose");
				}
			}			
			
			window.clearInterval(_options.banner_int);
			_options.banner_int = window.setInterval(function(){that.bannerMove('right')}, 3000);
		},
		//banner选择
		bannerChange: function(idnum){
			var _move,
				that = this,
				_options = that.options,
				_slider_con = _options.slider_con,
				_width = _options.banner.width(),
				_a = _options.nav_con.find("a");
			_a.removeClass("nav-chose");
			_move = -idnum * _width;
			_slider_con.animate({
				left: _move
			}, 200);
			_a.eq(idnum).addClass("nav-chose");
			_options.banner_now = idnum + 1;
			window.clearInterval(_options.banner_int);
			_options.banner_int = window.setInterval(function(){that.bannerMove('right')}, 3000);
		},
	}
})(jQuery,window,document);
