(function($) {
	$.fn.map = function(options, Ycallback, Ncallback) {
		//插件默认选项
		var that = $(this);
		var docType = $(this).is('input');
		var indexP = 0,
			indexC = 0,
			indexA = 0;
		var initP = cityData3.length;
		var provinceScroll = null,
			cityScroll = null,
			areaScroll = null;
		$.fn.map.defaultOptions = {
				theme: "province", //控件样式
				mode: null, //操作模式（滑动模式）
				event: "click", //打开日期插件默认方式为点击后后弹出日期 
				show: true,
			}
			//用户选项覆盖插件默认选项   
		var opts = $.extend(true, {}, $.fn.map.defaultOptions, options);
		if (!opts.show) {
			that.unbind('click');
		} else {
			//绑定事件（默认事件为获取焦点）
			that.bind(opts.event, function() {
				createUL(); //动态生成控件显示的地址
				init_iScrll(); //初始化iscrll
				extendOptions(); //显示控件
				that.blur();
				refreshMap();
				bindButton();
			})
		};

		function refreshMap() {
			provinceScroll.refresh();
			cityScroll.refresh();
			areaScroll.refresh();

			resetInitMap();
			provinceScroll.scrollTo(0, indexP * 40, 100, true);
			cityScroll.scrollTo(0, indexC * 40, 100, true);
			areaScroll.scrollTo(0, indexA * 40, 100, true);
		}

		function resetIndex() {
			indexP = 0;
			indexC = 0;
			indexA = 0;
		}

		function resetInitMap() {
			if (that.val() === "") {
				return false;
			}
		}

		function bindButton() {
			resetIndex();
			$("#mapconfirm").unbind('click').click(function() {
				var mapstr = $("#provincewrapper ul li:eq(" + (indexP + 1) + ")").html();
				var mapval = $("#provincewrapper ul li:eq(" + (indexP + 1) + ")").attr("data-val");
				if (cityData3[indexP] && cityData3[indexP].children) {
					mapstr += " " + $("#citywrapper ul li:eq(" + (indexC + 1) + ")").html();
					mapval = $("#citywrapper ul li:eq(" + (indexC + 1) + ")").attr("data-val");
				}
				if (cityData3[indexP] && cityData3[indexP].children[indexC] && cityData3[indexP].children[indexC].children) {
					mapstr += " " + $("#areawrapper ul li:eq(" + (indexA + 1) + ")").html();
					mapval = $("#areawrapper ul li:eq(" + (indexA + 1) + ")").attr("data-val");
				}
				if (Ycallback === undefined) {
					if (docType) {
						that.val(mapstr);
					} else {
						that.html(mapstr);
					}
				} else {
					Ycallback(mapstr);
				}
				console.log(indexP, indexC, indexA);
				$("#mapPage").hide();
				$("#mapshadow").hide();
				$("#bs-form-add-number").val(mapval);
			});
			$("#mapcancle").click(function() {
				$("#mapPage").hide();
				$("#mapshadow").hide();
				//Ncallback(false);
			});
		}

		function extendOptions() {
			$("#mapPage").show();
			$("#mapshadow").show();
		}
		//日期滑动

		function init_iScrll() {
			var strY, strM;
			provinceScroll = new iScroll("provincewrapper", {
				snap: "li",
				vScrollbar: false,
				onScrollEnd: function() {
					indexP = (this.y / 40) * (-1);
					$("#citywrapper ul").html(createCITY_UL());
					$("#areawrapper ul").html(createAREA_UL());
					cityScroll.refresh();
					areaScroll.refresh();
				}
			});
			cityScroll = new iScroll("citywrapper", {
				snap: "li",
				vScrollbar: false,
				onScrollEnd: function() {
					indexC = (this.y / 40) * (-1);
					$("#areawrapper ul").html(createAREA_UL());
					areaScroll.refresh();
				}
			});
			areaScroll = new iScroll("areawrapper", {
				snap: "li",
				vScrollbar: false,
				onScrollEnd: function() {
					indexA = (this.y / 40) * (-1);
				}
			});
			console.log(indexP, indexC, indexA);
		}

		function createUL() {
			CreateMapUI();
			$("#provincewrapper ul").html(createPROVINCE_UL());
			$("#citywrapper ul").html(createCITY_UL());
			$("#areawrapper ul").html(createAREA_UL());
		}

		function CreateMapUI() {
			var str = '' +
				'<div id="mapshadow"></div>' +
				'<div id="mapPage" class="page">' +
				'<section>' +
				'<div id="maptitle"><h1>请选择地址</h1></div>' +
				'<div id="mapmark"><a id="markprovince"></a><a id="markcity"></a><a id="markarea"></a></div>' +
				'<div id="mapscroll">' +
				'<div id="provincewrapper">' +
				'<ul></ul>' +
				'</div>' +
				'<div id="citywrapper">' +
				'<ul></ul>' +
				'</div>' +
				'<div id="areawrapper">' +
				'<ul></ul>' +
				'</div>' +
				'</div>' +
				'</section>' +
				'<footer id="mapFooter">' +
				'<div id="mapsetcancle">' +
				'<ul>' +
				'<li id="mapconfirm">确定</li>' +
				'<li id="mapcancle">取消</li>' +
				'</ul>' +
				'</div>' +
				'</footer>' +
				'</div>'
			$("#mapPlugin").html(str);
		}

		function addTimeStyle() {
			$("#mapPage").css("height", "250px");
			$("#mapPage").css("top", "60px");
		}
		//创建 --省份-- 列表
		function createPROVINCE_UL() {
			var str = "<li>&nbsp;</li>";
			for (var i = 1; i <= cityData3.length; i++) {
				str += '<li data-val="' + cityData3[i - 1].value + '">' + cityData3[i - 1].text + '</li>'
			}
			return str + "<li>&nbsp;</li>";;
		}
		//创建 --城市-- 列表
		function createCITY_UL() {
			if (cityData3[indexP] && cityData3[indexP].children) {
				var str = "<li>&nbsp;</li>";
				for (var i = 1; i <= cityData3[indexP].children.length; i++) {
					str += '<li data-val="' + cityData3[indexP].children[i - 1].value + '">' + cityData3[indexP].children[i - 1].text + '</li>'
				}
				return str + "<li>&nbsp;</li>";
			} else {
				return "<li></li>";
			}
		}
		//创建 --地区-- 列表
		function createAREA_UL() {
			if (cityData3[indexP] && cityData3[indexP].children[indexC] && cityData3[indexP].children[indexC].children) {
				$("#daywrapper ul").html("");
				var str = "<li>&nbsp;</li>";
				for (var i = 1; i <= cityData3[indexP].children[indexC].children.length; i++) {
					str += '<li data-val="' + cityData3[indexP].children[indexC].children[i - 1].value + '">' + cityData3[indexP].children[indexC].children[i - 1].text + '</li>'
				}
				return str + "<li>&nbsp;</li>";;
			} else {
				return "<li></li>";
			}

		}
	}
})(jQuery);