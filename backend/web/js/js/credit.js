var tolM = 0,
	tolNum = 0,
	credit_now = 0;
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
	$("#credit-choose").find(".submit").click(function(){
		var _credit_val = $("#credit-choose-con ul li:eq(" + (credit_now + 2) + ")").attr("data-val");
		$("#credit-choose").hide();
		$("#bg-cover").hide();
        $.ajax({
            type: 'GET',
            url: '/default/credit/asynclist_list.html',
            data:{'range':_credit_val},
            dataType: 'html',
            success: function (data) {            
                $('#GOODS_LIST_DIV').html(data);
            },
            error:function(){
            }
        });
	});
	$("#credit-choose").find(".cancel").click(function(){
		$("#credit-choose").hide();
		$("#bg-cover").hide();
	});
	$("#show-credit-choose").click(function(){
		$("#credit-choose").show();
		$("#bg-cover").show();
		credit_now = 0;
		creditScroll = new iScroll("credit-choose-con", {
            snap: "li",
            vScrollbar: false,
            onScrollEnd: function() {
                credit_now = (this.y / 30) * (-1) ;
            }
        });
	});
});
$(window).load(function() {
	$(".container").show();
    TouchSlide({
        slideCell : "#credit-banner",
        titCell : ".hd ul", // 开启自动分页 autoPage:true ，此时设置 titCell 为导航元素包裹层
        mainCell : ".bd ul",
        effect : "leftLoop",
        autoPlay : true, // 自动播放
        autoPage : true, // 自动分页
        delayTime: 1000, // 毫秒；切换效果持续时间（执行一次效果用多少毫秒）
        interTime: 2500, // 毫秒；自动运行间隔（隔多少毫秒后执行下一个效果）
        switchLoad : "_src" // 切换加载，真实图片路径为"_src"
    });
});