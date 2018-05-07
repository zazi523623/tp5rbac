
$(function(){
    "use strict";

    //DATATABLE
    // =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
    $('.data-table').DataTable({
    	paging: false,//不显示本地分页
    	info:false,//不显示左下角数据信息
    	ordering:false,//不允许自动排序
		language: {
			"search": "本地关键词搜索:" //设置搜索框提示
		}
    });
});


