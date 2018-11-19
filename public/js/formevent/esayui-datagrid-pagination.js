/**
 * Created by PhpStorm.
 * User: JMX
 * Date: 2018/3/6
 * Time: 12:09
 */

var subPuid = {};
var tabPanlSearch = {};
$(function () {
	
	/*Form*/
	var formId, formToken, tableId, extraParam;
	
	/*dataGrid*/
	var dg, title, reqUrl, method, rownumbers, singleSelect, showFooter,
		columns, idField, toolbar, queryParams, pageNumber, pageSize, rowStyler;
	
	/*pagination*/
	var buttons;
	
	/*other*/
	var mergeHeader, footerMsg;
	
	$.WidgetPage = function (arg) {
		reqUrl              = arg.reqUrl
		formToken           = arg.formToken;
		tableId             = arg.tableId;
		method              = arg.method;
		columns             = arg.columns;
		toolbar             = arg.toolbar;
		singleSelect        = arg.singleSelect;
		buttons             = arg.buttons;
		showFooter          = arg.showFooter;
		rownumbers          = arg.rownumbers;
		queryParams         = arg.queryParams;
		pageNumber          = (arg.pageNumber) ? arg.pageNumber : 1;
		pageSize            = (arg.pageSize) ? arg.pageSize : 20;
		rowStyler           = (arg.rowStyler) ? arg.rowStyler : false;
		dg                  = $("#" + arg.tableId);
		formId              = $("#" + arg.formId);
		extraParam          = arg.extraParam;
		title               = arg.title;
		mergeHeader         = arg.mergeHeader;
		footerMsg			= (arg.footerMsg) ? arg.footerMsg : "";
		
		//初始化页面
		this.GridInit = function () {
			//构建表格所有属性
			dg.datagrid({
				url: reqUrl,
				method: method,
				loadMsg:'正在努力加载...', // 提示消息
				emptyMsg: '<span style="color: red; font-size: 15px; font-weight: 600; line-height: 36px;">没有找到数据</span>',
				fitColumns:true, // 网格宽度自适应
			//	resizeHandle:'right', // 调整列位置
				toolbar: toolbar, // 表格工具栏
				striped: true, // 数据表格条纹化
				nowrap: true, // 一行里显示
				showFooter: showFooter,
				rownumbers: rownumbers,
				pageSize: pageSize,
				pageNumber: pageNumber,
				pageList: [5, 10, pageSize, pageSize * 2, pageSize * 4],
				idField: idField,
				queryParams: getQueryParam(), //json,object,function getQueryParams
				columns: columns,
				onSelect: function (rowIndex, rowData) { //用户选择一行的时候触发
					//custOnSelect(rowIndex, rowData);
				},
				onUnselect: function (rowIndex, rowData) { //用户取消选择一行的时候触发
					if (arg.onUnselect != undefined) {
						onUnselect(rowIndex, rowData);
					}
				},
				onSelectAll: function (rows) { //在用户选择所有行的时候触发
					if (arg.onSelectAll != undefined) {
						onSelectAll(rows);
					}
				},
				onUnselectAll: function (rows) {//在用户取消选择所有行的时候触发
					if (arg.onUnselectAll != undefined) {
						onUnselectAll(rows);
					}
				},
				onCheck: function (rowIndex, rowData) { //用户选择一行的时候触发
					if (arg.onCheck != undefined) {
						onCheck(rowIndex, rowData);
					}
				},
				onUncheck: function (rowIndex, rowData) {
					if (arg.onUncheck != undefined) {
						onUncheck(rowIndex, rowData);
					}
				},
				//onClickRow: function (rowIndex, rowData) { },
				onDblClickRow: function (rowIndex, rowData) {
					DbClickEditAccountInfo(rowIndex, rowData);
				},
				onLoadSuccess: function (rowData) {
					//mergesCellByField(rowData);
					//$(this).datagrid("fixRownumber"); 扩展行number
					//alert(rowData.rows.length);
					if (rowData.rows.length == 0) {
						if (mergeHeader) {$(".datagrid-view").css('height', '85px');}
						dg.closest('div.datagrid-wrap').find('div.pagination').hide();
						dg.closest('div.datagrid-wrap').find('div.datagrid-footer').hide();
					} else if (showFooter && rowData.rows.length > 0) {
						if (dg.closest('div.datagrid-wrap').find('div.datagrid-footer').css('display') == "none") {
							dg.closest('div.datagrid-wrap').find('div.pagination').css('display', 'block');
							dg.closest('div.datagrid-wrap').find('div.datagrid-footer').css('display', 'block');
							var h = Number(dg.closest('div.datagrid-wrap').find('div.datagrid-view').height()) + 25;
							dg.closest('div.datagrid-wrap').find('div.datagrid-view').css("height", h + "px");
						} else {
							dg.closest('div.datagrid-wrap').find('div.pagination').css('display', 'block');
							dg.closest('div.datagrid-wrap').find('div.datagrid-footer').css('display', 'block');
						}
					}
				},
				rowStyler:function(index,row) {
					if (rowStyler) {
						if (row.ticket) {
							var comment = row.orderComment;
							if (comment.indexOf("so") >= 0) {
								return 'background-color:#FFD9EC;color:#000;';
							}
						}
					}
				},
				rowTooltip: function (index, row) {
					/*console.info(row);
					if(row.returnMark == '1'){
						var text = "此档案为退回件（需修正）" ;
						return $("<span></span>").css("color", "Red").text(text);
					}*/
				}
			});
			
			/*动态更改title*/
			//dg.datagrid({title: title});
			//dg.datagrid("getPanel").panel("setTitle","new Title");
			
			dg.datagrid('getPager').pagination({
				buttons: buttons,
				beforePageText: '第',//页数文本框前显示的汉字
				afterPageText: '页   共 {pages} 页',
				displayMsg: '显示 {from} - {to} 条记录   共 {total} 条记录.  ' + footerMsg,
				layout: ['list', 'sep', 'first', 'prev', 'links', 'next', 'last', 'sep', 'refresh', 'manual'],
			});
		};
	}
	
	/*function notFoundData(rowData) {
		if (rowData.rows.length == 0) {
			$('#' + tableId).closest('div.datagrid-wrap').find('div.pagination').hide();
		}
	}*/
	
	function getQueryParam() {
		var c = {}, p = {};
		$.each(formId.find("input,select,textarea"), function (e, i) {
			if (i.name = (i.name || "").replace(/^\s*|\s*&/, ""), i.name) {
				if (/^.*\[\]$/.test(i.name)) {
					var t = i.name.match(/^(.*)\[\]$/g)[0];
					p[t] = 0 | p[t], i.name = i.name.replace(/^(.*)\[\]$/, "$1[" + p[t]+++"]");
				}
				(/^checkbox|radio$/.test(i.type)) && !i.checked || (c[i.name] = i.value);
			}
		});
		
		//向对象 C 额外追加 参数和值
		c._token = formToken;
		//console.log($.extend({}, c, extraParam));
		return $.extend({}, c, extraParam);
	}
});

/*$.extend($.fn.datagrid.methods, {
	fixRownumber : function (jq) {
		return jq.each(function () {
			var panel = $(this).datagrid("getPanel");
			//获取最后一行的number容器,并拷贝一份
			var clone = $(".datagrid-cell-rownumber", panel).last().clone();
			//由于在某些浏览器里面,是不支持获取隐藏元素的宽度,所以取巧一下
			clone.css({
				"position" : "absolute",
				left : -1000
			}).appendTo("body");
			var width = clone.width("auto").width();
			//默认宽度是25,所以只有大于25的时候才进行fix
			if (width > 25) {
				//多加5个像素,保持一点边距
				$(".datagrid-header-rownumber,.datagrid-cell-rownumber", panel).width(width + 5);
				//修改了宽度之后,需要对容器进行重新计算,所以调用resize
				$(this).datagrid("resize");
				//一些清理工作
				clone.remove();
				clone = null;
			} else {
				//还原成默认状态
				$(".datagrid-header-rownumber,.datagrid-cell-rownumber", panel).removeAttr("style");
			}
		});
	}
});*/

