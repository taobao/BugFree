/*
 * JQuery zTree 2.6.01
 * http://code.google.com/p/jquerytree/
 *
 * Copyright (c) 2010 Hunter.z (baby666.cn)
 *
 * Licensed same as jquery - under the terms of either the MIT License or the GPL Version 2 License
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * email: hunter.z@263.net
 * Date: 2011-06-29
 */

(function($) {

	var ZTREE_NODECREATED = "ZTREE_NODECREATED";
	var ZTREE_CLICK = "ZTREE_CLICK";
	var ZTREE_CHANGE = "ZTREE_CHANGE";
	var ZTREE_RENAME = "ZTREE_RENAME";
	var ZTREE_REMOVE = "ZTREE_REMOVE";
	var ZTREE_DRAG = "ZTREE_DRAG";
	var ZTREE_DROP = "ZTREE_DROP";
	var ZTREE_EXPAND = "ZTREE_EXPAND";
	var ZTREE_COLLAPSE = "ZTREE_COLLAPSE";
	var ZTREE_ASYNC_SUCCESS = "ZTREE_ASYNC_SUCCESS";
	var ZTREE_ASYNC_ERROR = "ZTREE_ASYNC_ERROR";

	var IDMark_Switch = "_switch";
	var IDMark_Icon = "_ico";
	var IDMark_Span = "_span";
	var IDMark_Input = "_input";
	var IDMark_Check = "_check";
	var IDMark_Edit = "_edit";
	var IDMark_Remove = "_remove";
	var IDMark_Ul = "_ul";
	var IDMark_A = "_a";

	var LineMark_Root = "root";
	var LineMark_Roots = "roots";
	var LineMark_Center = "center";
	var LineMark_Bottom = "bottom";
	var LineMark_NoLine = "noLine";
	var LineMark_Line = "line";

	var FolderMark_Open = "open";
	var FolderMark_Close = "close";
	var FolderMark_Docu = "docu";

	var Class_CurSelectedNode = "curSelectedNode";
	var Class_CurSelectedNode_Edit = "curSelectedNode_Edit";
	var Class_TmpTargetTree = "tmpTargetTree";
	var Class_TmpTargetNode = "tmpTargetNode";

	var Check_Style_Box = "checkbox";
	var Check_Style_Radio = "radio";
	var CheckBox_Default = "chk";
	var CheckBox_False = "false";
	var CheckBox_True = "true";
	var CheckBox_Full = "full";
	var CheckBox_Part = "part";
	var CheckBox_Focus = "focus";
	var Radio_Type_All = "all";
	var Radio_Type_Level = "level";

	var MoveType_Inner = "inner";
	var MoveType_Before = "before";
	var MoveType_After = "after";
	var MinMoveSize = "5";

	var settings = new Array();
	var zTreeId = 0;
	var zTreeNodeCache = [];

	//zTree构造函数
	$.fn.zTree = function(zTreeSetting, zTreeNodes) {

		var setting = {
			//Tree 唯一标识，主UL的ID
			treeObjId: "",
			treeObj: null,
			//是否显示CheckBox
			checkable: false,
			//是否在编辑状态
			editable: false,
			//编辑状态是否显示修改按钮
			edit_renameBtn:true,
			//编辑状态是否显示删除节点按钮
			edit_removeBtn:true,
			//是否显示树的线
			showLine: true,
			//是否显示图标
			showIcon: true,
			//是否锁定父节点状态
			keepParent: false,
			//是否锁定叶子节点状态
			keepLeaf: false,
			//当前被选择的TreeNode
			curTreeNode: null,
			//当前正被编辑的TreeNode
			curEditTreeNode: null,
			//是否处于拖拽期间 0: not Drag; 1: doing Drag
			dragStatus: 0,
			dragNodeShowBefore: false,
			//拖拽操作控制 move or copy
			dragCopy: false,
			dragMove: true,
			//选择CheckBox 或 Radio
			checkStyle: Check_Style_Box,
			//checkBox点击后影响父子节点设置（checkStyle=Check_Style_Radio时无效）
			checkType: {
				"Y": "ps",
				"N": "ps"
			},
			//radio 最大个数限制类型，每一级节点限制 或 整棵Tree的全部节点限制（checkStyle=Check_Style_Box时无效）
			checkRadioType:Radio_Type_Level,
			//checkRadioType = Radio_Type_All 时，保存被选择节点的堆栈
			checkRadioCheckedList:[],
			//是否异步获取节点数据
			async: false,
			//获取节点数据的URL地址
			asyncUrl: "",
			//获取节点数据时，必须的数据名称，例如：id、name
			asyncParam: [],
			//其它参数
			asyncParamOther: [],
			//异步加载获取数据，针对数据进行预处理的函数
			asyncDataFilter: null,
			//简单Array数组转换为JSON嵌套数据参数
			isSimpleData: false,
			treeNodeKey: "",
			treeNodeParentKey: "",
			rootPID: null,
			//用户自定义名称列
			nameCol: "name",
			//用户自定义子节点列
			nodesCol: "nodes",
			//用户自定义checked列
			checkedCol: "checked",
			//折叠、展开特效速度
			expandSpeed: "fast",
			//折叠、展开Trigger开关
			expandTriggerFlag:false,
			//hover 增加按钮接口
			addHoverDom:null,
			//hover 删除按钮接口
			removeHoverDom:null,
			//永久自定义显示控件方法
			addDiyDom:null,
			//字体个性化样式接口
			fontCss:{},

			root: {
				isRoot: true,
				nodes: []
			},
			//event Function
			callback: {
				beforeAsync:null,
				beforeClick:null,
				beforeRightClick:null,
				beforeMouseDown:null,
				beforeMouseUp:null,
				beforeChange:null,
				beforeDrag:null,
				beforeDrop:null,
				beforeRename:null,
				beforeRemove:null,
				beforeExpand:null,
				beforeCollapse:null,
				confirmDragOpen:null,
				confirmRename:null,

				nodeCreated:null,
				click:null,
				rightClick:null,
				mouseDown:null,
				mouseUp:null,
				change:null,
				drag:null,
				drop:null,
				rename:null,
				remove:null,
				expand:null,
				collapse:null,
				asyncConfirmData:null,
				asyncSuccess:null,
				asyncError:null
			}
		};

		if (zTreeSetting) {
			var tmp_checkType = zTreeSetting.checkType;
			zTreeSetting.checkType = undefined;
			var tmp_callback = zTreeSetting.callback;
			zTreeSetting.callback = undefined;
			var tmp_root = zTreeSetting.root;
			zTreeSetting.root = undefined;

			$.extend(setting, zTreeSetting);

			zTreeSetting.checkType = tmp_checkType;
			$.extend(true, setting.checkType, tmp_checkType);
			zTreeSetting.callback = tmp_callback;
			$.extend(setting.callback, tmp_callback);
			zTreeSetting.root = tmp_root;
			$.extend(setting.root, tmp_root);
		}

		setting.treeObjId = this.attr("id");
		setting.treeObj = this;
		setting.root.tId = -1;
		setting.root.name = "ZTREE ROOT";
		setting.root.isRoot = true;
		setting.checkRadioCheckedList = [];
		setting.curTreeNode = null;
		setting.curEditTreeNode = null;
		setting.dragNodeShowBefore = false;
		setting.dragStatus = 0;
		setting.expandTriggerFlag = false;
		if (!setting.root[setting.nodesCol]) setting.root[setting.nodesCol]= [];

		if (zTreeNodes) {
			setting.root[setting.nodesCol] = zTreeNodes;
		}
		if (setting.isSimpleData) {
			setting.root[setting.nodesCol] = transformTozTreeFormat(setting, setting.root[setting.nodesCol]);
		}
		settings[setting.treeObjId] = setting;

		setting.treeObj.empty();
		zTreeNodeCache[setting.treeObjId] = [];
		bindTreeNodes(setting, this);
		if (setting.root[setting.nodesCol] && setting.root[setting.nodesCol].length > 0) {
			initTreeNodes(setting, 0, setting.root[setting.nodesCol]);
		} else if (setting.async && setting.asyncUrl && setting.asyncUrl.length > 0) {
			asyncGetNode(setting);
		}

		return new zTreePlugin().init(this);
	};

	//绑定事件
	function bindTreeNodes(setting, treeObj) {
		var eventParam = {treeObjId: setting.treeObjId};
		setting.treeObj.unbind('click', eventProxy);
		setting.treeObj.bind('click', eventParam, eventProxy);
		setting.treeObj.unbind('dblclick', eventProxy);
		setting.treeObj.bind('dblclick', eventParam, eventProxy);
		setting.treeObj.unbind('mouseover', eventProxy);
		setting.treeObj.bind('mouseover', eventParam, eventProxy);
		setting.treeObj.unbind('mouseout', eventProxy);
		setting.treeObj.bind('mouseout', eventParam, eventProxy);
		setting.treeObj.unbind('mousedown', eventProxy);
		setting.treeObj.bind('mousedown', eventParam, eventProxy);
		setting.treeObj.unbind('mouseup', eventProxy);
		setting.treeObj.bind('mouseup', eventParam, eventProxy);
		setting.treeObj.unbind('contextmenu', eventProxy);
		setting.treeObj.bind('contextmenu', eventParam, eventProxy);

		treeObj.unbind(ZTREE_NODECREATED);
		treeObj.bind(ZTREE_NODECREATED, function (event, treeId, treeNode) {
			tools.apply(setting.callback.nodeCreated, [event, treeId, treeNode]);
		});

		treeObj.unbind(ZTREE_CLICK);
		treeObj.bind(ZTREE_CLICK, function (event, treeId, treeNode) {
			tools.apply(setting.callback.click, [event, treeId, treeNode]);
		});

		treeObj.unbind(ZTREE_CHANGE);
		treeObj.bind(ZTREE_CHANGE, function (event, treeId, treeNode) {
			tools.apply(setting.callback.change, [event, treeId, treeNode]);
		});

		treeObj.unbind(ZTREE_RENAME);
		treeObj.bind(ZTREE_RENAME, function (event, treeId, treeNode) {
			tools.apply(setting.callback.rename, [event, treeId, treeNode]);
		});

		treeObj.unbind(ZTREE_REMOVE);
		treeObj.bind(ZTREE_REMOVE, function (event, treeId, treeNode) {
			tools.apply(setting.callback.remove, [event, treeId, treeNode]);
		});

		treeObj.unbind(ZTREE_DRAG);
		treeObj.bind(ZTREE_DRAG, function (event, treeId, treeNode) {
			tools.apply(setting.callback.drag, [event, treeId, treeNode]);
		});

		treeObj.unbind(ZTREE_DROP);
		treeObj.bind(ZTREE_DROP, function (event, treeId, treeNode, targetNode, moveType) {
			tools.apply(setting.callback.drop, [event, treeId, treeNode, targetNode, moveType]);
		});

		treeObj.unbind(ZTREE_EXPAND);
		treeObj.bind(ZTREE_EXPAND, function (event, treeId, treeNode) {
			tools.apply(setting.callback.expand, [event, treeId, treeNode]);
		});

		treeObj.unbind(ZTREE_COLLAPSE);
		treeObj.bind(ZTREE_COLLAPSE, function (event, treeId, treeNode) {
			tools.apply(setting.callback.collapse, [event, treeId, treeNode]);
		});

		treeObj.unbind(ZTREE_ASYNC_SUCCESS);
		treeObj.bind(ZTREE_ASYNC_SUCCESS, function (event, treeId, treeNode, msg) {
			tools.apply(setting.callback.asyncSuccess, [event, treeId, treeNode, msg]);
		});

		treeObj.unbind(ZTREE_ASYNC_ERROR);
		treeObj.bind(ZTREE_ASYNC_ERROR, function (event, treeId, treeNode, XMLHttpRequest, textStatus, errorThrown) {
			tools.apply(setting.callback.asyncError, [event, treeId, treeNode, XMLHttpRequest, textStatus, errorThrown]);
		});
	}

	//初始化并显示节点Json对象
	function initTreeNodes(setting, level, treeNodes, parentNode) {
		if (!treeNodes) return;

		var zTreeHtml = appendTreeNodes(setting, level, treeNodes, parentNode);
		if (!parentNode) {
			setting.treeObj.append(zTreeHtml.join(''));
		} else {
			$("#" + parentNode.tId + IDMark_Ul).append(zTreeHtml.join(''));
		}
		repairParentChkClassWithSelf(setting, parentNode);
		createCallback(setting, treeNodes);
	}

	function createCallback(setting, treeNodes) {
		for (var i = 0, l = treeNodes.length; i < l; i++) {
			var node = treeNodes[i];
			tools.apply(setting.addDiyDom, [setting.treeObjId, node]);
			//触发nodeCreated事件
			setting.treeObj.trigger(ZTREE_NODECREATED, [setting.treeObjId, node]);
			if (node[setting.nodesCol] && node[setting.nodesCol].length > 0) {
				createCallback(setting, node[setting.nodesCol], node);
			}
		}
	}

	function appendTreeNodes(setting, level, treeNodes, parentNode) {
		if (!treeNodes) return [];
		var html = [];
		for (var i = 0, l = treeNodes.length; i < l; i++) {
			var node = treeNodes[i];
			node.level = level;
			node.tId = setting.treeObjId + "_" + (++zTreeId);
			node.parentNode = parentNode;
			node[setting.checkedCol] = !!node[setting.checkedCol];
			node.checkedOld = node[setting.checkedCol];
			node.check_Focus = false;
			node.check_True_Full = true;
			node.check_False_Full = true;
			node.editNameStatus = false;
			node.isAjaxing = null;
			addCache(setting, node);
			fixParentKeyValue(setting, node);

			var tmpParentNode = (parentNode) ? parentNode: setting.root;
			//允许在非空节点上增加节点
			node.isFirstNode = (tmpParentNode[setting.nodesCol].length == treeNodes.length) && (i == 0);
			node.isLastNode = (i == (treeNodes.length - 1));

			if (node[setting.nodesCol] && node[setting.nodesCol].length > 0) {
				node.open = (node.open) ? true: false;
				node.isParent = true;
			} else {
				node.isParent = (node.isParent) ? true: false;
			}

			var url = makeNodeUrl(setting, node);
			var fontcss = makeNodeFontCss(setting, node);
			var fontStyle = [];
			for (var f in fontcss) {
				fontStyle.push(f, ":", fontcss[f], ";");
			}

			var childHtml = [];
			if (node[setting.nodesCol] && node[setting.nodesCol].length > 0) {
				childHtml = appendTreeNodes(setting, level + 1, node[setting.nodesCol], node);
			}
			html.push("<li id='", node.tId, "' treenode>",
				"<button type='button' id='", node.tId, IDMark_Switch,
				"' title='' class='", makeNodeLineClass(setting, node), "' treeNode", IDMark_Switch," onfocus='this.blur();'></button>");
			if (setting.checkable) {
				makeChkFlag(setting, node);
				if (setting.checkStyle == Check_Style_Radio && setting.checkRadioType == Radio_Type_All && node[setting.checkedCol] ) {
					setting.checkRadioCheckedList = setting.checkRadioCheckedList.concat([node]);
				}
				html.push("<button type='button' ID='", node.tId, IDMark_Check, "' class='", makeChkClass(setting, node), "' treeNode", IDMark_Check," onfocus='this.blur();' ",(node.nocheck === true?"style='display:none;'":""),"></button>");
			}
                        var nodeName = node[setting.nameCol].replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
			html.push("<a id='", node.tId, IDMark_A, "' treeNode", IDMark_A," onclick=\"", (node.click || ''),
				"\" ", ((url != null && url.length > 0) ? "href='" + url + "'" : ""), " target='",makeNodeTarget(node),"' style='", fontStyle.join(''),
                                "' title='",nodeName+'#'+node.id,
				"'><button type='button' id='", node.tId, IDMark_Icon,
				"' title='' treeNode", IDMark_Icon," onfocus='this.blur();' class='", makeNodeIcoClass(setting, node), "' style='", makeNodeIcoStyle(setting, node), "'></button><span id='", node.tId, IDMark_Span,
				"'>",nodeName,"</span></a><ul id='", node.tId, IDMark_Ul, "' class='", makeUlLineClass(setting, node), "' style='display:", (node.open ? "block": "none"),"'>");
			html.push(childHtml.join(''));
			html.push("</ul></li>");
		}
		return html;
	}

	function eventProxy(event) {
		var target = event.target;
		var relatedTarget = event.relatedTarget;
		var setting = settings[event.data.treeObjId];
		var tId = "";
		var childEventType = "", mainEventType = "";
		var tmp = null;

		if (tools.eqs(event.type, "mouseover")) {
			if (setting.checkable && tools.eqs(target.tagName, "button") && target.getAttribute("treeNode"+IDMark_Check) !== null) {
				tId = target.parentNode.id;
				childEventType = "mouseoverCheck";
			} else {
				tmp = tools.getMDom(setting, target, [{tagName:"a", attrName:"treeNode"+IDMark_A}]);
				if (tmp) {
					tId = tmp.parentNode.id;
					childEventType = "hoverOverNode";
				}
			}
		} else if (tools.eqs(event.type, "mouseout")) {
			if (setting.checkable && tools.eqs(target.tagName, "button") && target.getAttribute("treeNode"+IDMark_Check) !== null) {
				tId = target.parentNode.id;
				childEventType = "mouseoutCheck";
			} else {
				tmp = tools.getMDom(setting, relatedTarget, [{tagName:"a", attrName:"treeNode"+IDMark_A}]);
				if (!tmp) {
					tId = "remove";
					childEventType = "hoverOutNode";
				}
			}
		} else if (tools.eqs(event.type, "mousedown")) {
			mainEventType = "mousedown";
			tmp = tools.getMDom(setting, target, [{tagName:"a", attrName:"treeNode"+IDMark_A}]);
			if (tmp) {
				tId = tmp.parentNode.id;
				childEventType = "mousedownNode";
			}
		} else if (tools.eqs(event.type, "mouseup")) {
			mainEventType = "mouseup";
			tmp = tools.getMDom(setting, target, [{tagName:"a", attrName:"treeNode"+IDMark_A}]);
			if (tmp) {tId = tmp.parentNode.id;}
		} else if (tools.eqs(event.type, "contextmenu")) {
			mainEventType = "contextmenu";
			tmp = tools.getMDom(setting, target, [{tagName:"a", attrName:"treeNode"+IDMark_A}]);
			if (tmp) {tId = tmp.parentNode.id;}
		} else if (tools.eqs(event.type, "click")) {
			if (tools.eqs(target.tagName, "button") && target.getAttribute("treeNode"+IDMark_Switch) !== null) {
				tId = target.parentNode.id;
				childEventType = "switchNode";
			} else if (setting.checkable && tools.eqs(target.tagName, "button") && target.getAttribute("treeNode"+IDMark_Check) !== null) {
				tId = target.parentNode.id;
				childEventType = "checkNode";
			} else {
				tmp = tools.getMDom(setting, target, [{tagName:"a", attrName:"treeNode"+IDMark_A}]);
				if (tmp) {
					tId = tmp.parentNode.id;
					childEventType = "clickNode";
				}
			}
		} else if (tools.eqs(event.type, "dblclick")) {
			mainEventType = "dblclick";
			tmp = tools.getMDom(setting, target, [{tagName:"a", attrName:"treeNode"+IDMark_A}]);
			if (tmp) {
				tId = tmp.parentNode.id;
				childEventType = "switchNode";
			}
		}

		if (tId.length>0 || mainEventType.length>0) {
			if (childEventType!="hoverOverNode" && childEventType != "hoverOutNode"
				&& childEventType!="mouseoverCheck" && childEventType != "mouseoutCheck"
				&& target.getAttribute("treeNode"+IDMark_Input) === null
				&& !st.checkEvent(setting)) return false;
		}
		if (tId.length>0) {
			//	编辑框Text状态下 允许选择文本
			if (!(setting.curTreeNode && setting.curTreeNode.editNameStatus)) {
				tools.noSel();
			}
			event.data.treeNode = getTreeNodeByTId(setting, tId);
			switch (childEventType) {
				case "switchNode" :
					handler.onSwitchNode(event);
					break;
				case "clickNode" :
					handler.onClickNode(event);
					break;
				case "checkNode" :
					handler.onCheckNode(event);
					break;
				case "mouseoverCheck" :
					handler.onMouseoverCheck(event);
					break;
				case "mouseoutCheck" :
					handler.onMouseoutCheck(event);
					break;
				case "mousedownNode" :
					handler.onMousedownNode(event);
					break;
				case "hoverOverNode" :
					handler.onHoverOverNode(event);
					break;
				case "hoverOutNode" :
					handler.onHoverOutNode(event);
					break;
			}
		} else {
			event.data.treeNode = null;
		}
		switch (mainEventType) {
			case "mousedown" :
				return handler.onZTreeMousedown(event);
				break;
			case "mouseup" :
				return handler.onZTreeMouseup(event);
				break;
			case "dblclick" :
				return handler.onZTreeDblclick(event);
				break;
			case "contextmenu" :
				return handler.onZTreeContextmenu(event);
				break;
		}
	}

	var tools = {
		eqs: function(str1, str2) {
			return str1.toLowerCase() === str2.toLowerCase();
		},
		isArray: function(arr) {
			return Object.prototype.toString.apply(arr) === "[object Array]";
		},
		noSel: function() {
			//除掉默认事件，防止文本被选择
			try {
				window.getSelection ? window.getSelection().removeAllRanges() : document.selection.empty();
			} catch(e){}
		},
		inputFocus: function(inputObj) {
			if (inputObj.get(0)) {
				inputObj.focus();
				setCursorPosition(inputObj.get(0), inputObj.val().length);
			}
		},
		apply: function(fun, param, defaultValue) {
			if ((typeof fun) == "function") {
				return fun.apply(tools, param);
			}
			return defaultValue;
		},
		getAbs: function (obj) {
			//获取对象的绝对坐标
			oRect = obj.getBoundingClientRect();
			return [oRect.left,oRect.top]
		},
		getMDom: function (setting, curDom, targetExpr) {
			if (!curDom) return null;
			while (curDom && curDom.id !== setting.treeObjId) {
				for (var i=0, l=targetExpr.length; curDom.tagName && i<l; i++) {
					if (tools.eqs(curDom.tagName, targetExpr[i].tagName) && curDom.getAttribute(targetExpr[i].attrName) !== null) {
						return curDom;
					}
				}
				curDom = curDom.parentNode;
			}
			return null;
		},
		clone: function (jsonObj) {
			var buf;
			if (jsonObj instanceof Array) {
				buf = [];
				var i = jsonObj.length;
				while (i--) {
					buf[i] = arguments.callee(jsonObj[i]);
				}
				return buf;
			}else if (typeof jsonObj == "function"){
				return jsonObj;
			}else if (jsonObj instanceof Object){
				buf = {};
				for (var k in jsonObj) {
					if (k!="parentNode") {
						buf[k] = arguments.callee(jsonObj[k]);
					}
				}
				return buf;
			}else{
				return jsonObj;
			}
		}
	};

	var st = {
		checkEvent: function(setting) {
			return st.checkCancelPreEditNode(setting);
		},
		//取消之前选中节点状态
		cancelPreSelectedNode: function (setting) {
			if (setting.curTreeNode) {
				$("#" + setting.curTreeNode.tId + IDMark_A).removeClass(Class_CurSelectedNode);
				setNodeName(setting, setting.curTreeNode);
				removeTreeDom(setting, setting.curTreeNode);
				setting.curTreeNode = null;
			}
		},
		//校验取消之前编辑节点状态
		checkCancelPreEditNode: function (setting) {
			if (setting.curEditTreeNode) {
				var inputObj = setting.curEditInput;
				if ( tools.apply(setting.callback.confirmRename, [setting.treeObjId, setting.curEditTreeNode, inputObj.val()], true) === false) {
					setting.curEditTreeNode.editNameStatus = true;
					tools.inputFocus(inputObj);
					return false;
				}
			}
			return true;
		},
		//取消之前编辑节点状态
		cancelPreEditNode: function (setting, newName) {
			if (setting.curEditTreeNode) {
				var inputObj = $("#" + setting.curEditTreeNode.tId + IDMark_Input);
				setting.curEditTreeNode[setting.nameCol] = newName ? newName:inputObj.val();
				//触发rename事件
				setting.treeObj.trigger(ZTREE_RENAME, [setting.treeObjId, setting.curEditTreeNode]);

				$("#" + setting.curEditTreeNode.tId + IDMark_A).removeClass(Class_CurSelectedNode_Edit);
				inputObj.unbind();
				setNodeName(setting, setting.curEditTreeNode);
				setting.curEditTreeNode.editNameStatus = false;
				setting.curEditTreeNode = null;
				setting.curEditInput = null;
			}
			return true;
		}
	}

	var handler = {
		//点击展开、折叠节点
		onSwitchNode: function (event) {
			var setting = settings[event.data.treeObjId];
			var treeNode = event.data.treeNode;

			if (treeNode.open) {
				if (tools.apply(setting.callback.beforeCollapse, [setting.treeObjId, treeNode], true) == false) return;
				setting.expandTriggerFlag = true;
				switchNode(setting, treeNode);
			} else {
				if (tools.apply(setting.callback.beforeExpand, [setting.treeObjId, treeNode], true) == false) return;
				setting.expandTriggerFlag = true;
				switchNode(setting, treeNode);
			}
		},
		onClickNode: function (event) {
			var setting = settings[event.data.treeObjId];
			var treeNode = event.data.treeNode;
			if (tools.apply(setting.callback.beforeClick, [setting.treeObjId, treeNode], true) == false) return;
			//设置节点为选中状态
			selectNode(setting, treeNode);
			//触发click事件
			setting.treeObj.trigger(ZTREE_CLICK, [setting.treeObjId, treeNode]);
		},
		onCheckNode: function (event) {
			var setting = settings[event.data.treeObjId];
			var treeNode = event.data.treeNode;
			if (tools.apply(setting.callback.beforeChange, [setting.treeObjId, treeNode], true) == false) return;

			treeNode[setting.checkedCol] = !treeNode[setting.checkedCol];
			checkNodeRelation(setting, treeNode);

			var checkObj = $("#" + treeNode.tId + IDMark_Check);
			setChkClass(setting, checkObj, treeNode);
			repairParentChkClassWithSelf(setting, treeNode);

			//触发 CheckBox 点击事件
			setting.treeObj.trigger(ZTREE_CHANGE, [setting.treeObjId, treeNode]);
		},
		onMouseoverCheck: function(event) {
			var setting = settings[event.data.treeObjId];
			var treeNode = event.data.treeNode;
			var checkObj = $("#" + treeNode.tId + IDMark_Check);
			treeNode.checkboxFocus = true;
			setChkClass(setting, checkObj, treeNode);
		},
		onMouseoutCheck: function(event) {
			var setting = settings[event.data.treeObjId];
			var treeNode = event.data.treeNode;
			var checkObj = $("#" + treeNode.tId + IDMark_Check);
			treeNode.checkboxFocus = false;
			setChkClass(setting, checkObj, treeNode);
		},
		onMousedownNode: function(eventMouseDown) {
			var setting = settings[eventMouseDown.data.treeObjId];
			var treeNode = eventMouseDown.data.treeNode;
			//右键、禁用拖拽功能 不能拖拽
			if (eventMouseDown.button == 2 || !setting.editable || (!setting.dragCopy && !setting.dragMove)) return;
			//编辑输入框内不能拖拽节点
			var target = eventMouseDown.target;
			if (treeNode.editNameStatus && tools.eqs(target.tagName, "input") && target.getAttribute("treeNode"+IDMark_Input) !== null) {
				return;
			}

			var doc = document;
			var curNode;
			var tmpArrow;
			var tmpTarget;
			var isOtherTree = false;
			var targetSetting = setting;
			var preTmpTargetNodeId = null;
			var preTmpMoveType = null;
			var tmpTargetNodeId = null;
			var moveType = MoveType_Inner;
			var mouseDownX = eventMouseDown.clientX;
			var mouseDownY = eventMouseDown.clientY;
			var startTime = (new Date()).getTime();

			$(doc).mousemove(function(event) {
				tools.noSel();

				//避免鼠标误操作，对于第一次移动小于MinMoveSize时，不开启拖拽功能
				if (setting.dragStatus == 0 && Math.abs(mouseDownX - event.clientX) < MinMoveSize
					&& Math.abs(mouseDownY - event.clientY) < MinMoveSize) {
					return true;
				}

				$("body").css("cursor", "pointer");

				if (setting.dragStatus == 0 && treeNode.isParent && treeNode.open) {
					expandAndCollapseNode(setting, treeNode, !treeNode.open);
					setting.dragNodeShowBefore = true;
				}

				if (setting.dragStatus == 0) {
					//避免beforeDrag alert时，得到返回值之前仍能拖拽的Bug
					setting.dragStatus = -1;
					if (tools.apply(setting.callback.beforeDrag, [setting.treeObjId, treeNode], true) == false) return true;

					setting.dragStatus = 1;
					showIfameMask(true);

					//设置节点为选中状态
					treeNode.editNameStatus = false;
					selectNode(setting, treeNode);
					removeTreeDom(setting, treeNode);

					var tmpNode = $("#" + treeNode.tId).clone();
					tmpNode.attr("id", treeNode.tId + "_tmp");
					tmpNode.css("padding", "0");
					tmpNode.children("#" + treeNode.tId + IDMark_A).removeClass(Class_CurSelectedNode);
					tmpNode.children("#" + treeNode.tId + IDMark_Ul).css("display", "none");

					curNode = $("<ul class='zTreeDragUL'></ul>").append(tmpNode);
					curNode.attr("id", treeNode.tId + IDMark_Ul + "_tmp");
					curNode.addClass(setting.treeObj.attr("class"));
					curNode.appendTo("body");

					tmpArrow = $("<button class='tmpzTreeMove_arrow'></button>");
					tmpArrow.attr("id", "zTreeMove_arrow_tmp");
					tmpArrow.appendTo("body");

					//触发 DRAG 拖拽事件，返回正在拖拽的源数据对象
					setting.treeObj.trigger(ZTREE_DRAG, [setting.treeObjId, treeNode]);
				}

				if (setting.dragStatus == 1 && tmpArrow.attr("id") != event.target.id) {
					if (tmpTarget) {
						tmpTarget.removeClass(Class_TmpTargetTree);
						if (tmpTargetNodeId) $("#" + tmpTargetNodeId + IDMark_A, tmpTarget).removeClass(Class_TmpTargetNode);
					}
					tmpTarget = null;
					tmpTargetNodeId = null;

					//判断是否不同的树
					isOtherTree = false;
					targetSetting = setting;
					for (var s in settings) {
						if (settings[s].editable && settings[s].treeObjId != setting.treeObjId
							&& (event.target.id == settings[s].treeObjId || $(event.target).parents("#" + settings[s].treeObjId).length>0)) {
							isOtherTree = true;
							targetSetting = settings[s];
						}
					}

					var docScrollTop = $(doc).scrollTop();
					var docScrollLeft = $(doc).scrollLeft();
					var treeOffset = targetSetting.treeObj.offset();
					var scrollHeight = targetSetting.treeObj.get(0).scrollHeight;
					var scrollWidth = targetSetting.treeObj.get(0).scrollWidth;
					var dTop = (event.clientY + docScrollTop - treeOffset.top);
					var dBottom = (targetSetting.treeObj.height() + treeOffset.top - event.clientY - docScrollTop);
					var dLeft = (event.clientX + docScrollLeft - treeOffset.left);
					var dRight = (targetSetting.treeObj.width() + treeOffset.left - event.clientX - docScrollLeft);
					var isTop = (dTop < 10 && dTop > -5);
					var isBottom = (dBottom < 10 && dBottom > -5);
					var isLeft = (dLeft < 10 && dLeft > -5);
					var isRight = (dRight < 10 && dRight > -5);
					var isTreeTop = (isTop && targetSetting.treeObj.scrollTop() <= 0);
					var isTreeBottom = (isBottom && (targetSetting.treeObj.scrollTop() + targetSetting.treeObj.height()+10) >= scrollHeight);
					var isTreeLeft = (isLeft && targetSetting.treeObj.scrollLeft() <= 0);
					var isTreeRight = (isRight && (targetSetting.treeObj.scrollLeft() + targetSetting.treeObj.width()+10) >= scrollWidth);

					if (event.target.id && targetSetting.treeObj.find("#" + event.target.id).length > 0) {
						//任意节点 移到 其他节点
						var targetObj = event.target;
						while (targetObj && targetObj.tagName && !tools.eqs(targetObj.tagName, "li") && targetObj.id != targetSetting.treeObjId) {
							targetObj = targetObj.parentNode;
						}

						var canMove = false;
						//如果移到自己 或者自己的子集，则不能当做临时目标
						if (treeNode.parentNode && targetObj.id != treeNode.tId && $("#" + treeNode.tId).find("#" + targetObj.id).length == 0) {
							//非根节点移动
							canMove = true;
						} else if (treeNode.parentNode == null && targetObj.id != treeNode.tId && $("#" + treeNode.tId).find("#" + targetObj.id).length == 0) {
							//根节点移动
							canMove = true;
						}
						if (canMove) {
							if (event.target.id &&
								(event.target.id == (targetObj.id + IDMark_A) || $(event.target).parents("#" + targetObj.id + IDMark_A).length > 0)) {
								tmpTarget = $(targetObj);
								tmpTargetNodeId = targetObj.id;
							}
						}
					}

					//确保鼠标在zTree内部
					if (event.target.id == targetSetting.treeObjId || $(event.target).parents("#" + targetSetting.treeObjId).length>0) {
						//只有移动到zTree容器的边缘才算移到 根（排除根节点在本棵树内的移动）
						if (!tmpTarget && (isTreeTop || isTreeBottom || isTreeLeft || isTreeRight) && (isOtherTree || (!isOtherTree && treeNode.parentNode != null))) {
							tmpTarget = targetSetting.treeObj;
							tmpTarget.addClass(Class_TmpTargetTree);
						}
						//滚动条自动滚动
						if (isTop) {
							targetSetting.treeObj.scrollTop(targetSetting.treeObj.scrollTop()-10);
						} else if (isBottom)  {
							targetSetting.treeObj.scrollTop(targetSetting.treeObj.scrollTop()+10);
						}
						if (isLeft) {
							targetSetting.treeObj.scrollLeft(targetSetting.treeObj.scrollLeft()-10);
						} else if (isRight) {
							targetSetting.treeObj.scrollLeft(targetSetting.treeObj.scrollLeft()+10);
						}
						//目标节点在可视区域左侧，自动移动横向滚动条
						if (tmpTarget && tmpTarget != targetSetting.treeObj && tmpTarget.offset().left < targetSetting.treeObj.offset().left) {
							targetSetting.treeObj.scrollLeft(targetSetting.treeObj.scrollLeft()+ tmpTarget.offset().left - targetSetting.treeObj.offset().left);
						}
					}

					curNode.css({
						"top": (event.clientY + docScrollTop + 3) + "px",
						"left": (event.clientX + docScrollLeft + 3) + "px"
					});

					var dX = 0;
					var dY = 0;
					if (tmpTarget && tmpTarget.attr("id")!=targetSetting.treeObjId) {
						var tmpTargetNode = tmpTargetNodeId == null ? null: getTreeNodeByTId(targetSetting, tmpTargetNodeId);
						var tmpNodeObj = $("#" + treeNode.tId);
						var isPrev = (tmpNodeObj.prev().attr("id") == tmpTargetNodeId) ;
						var isNext = (tmpNodeObj.next().attr("id") == tmpTargetNodeId) ;
						var isInner = (treeNode.parentNode && treeNode.parentNode.tId == tmpTargetNodeId) ;

						var canPrev = !isNext;
						var canNext = !isPrev;
						var canInner = !isInner && !(targetSetting.keepLeaf && !tmpTargetNode.isParent);
						if (!canPrev && !canNext && !canInner) {
							tmpTarget = null;
							tmpTargetNodeId = "";
							moveType = MoveType_Inner;
							tmpArrow.css({
								"display":"none"
							});
							if (window.zTreeMoveTimer) {
								clearTimeout(window.zTreeMoveTimer);
							}
						} else {
							var tmpTargetA = $("#" + tmpTargetNodeId + IDMark_A, tmpTarget);
							tmpTargetA.addClass(Class_TmpTargetNode);

							var prevPercent = canPrev ? (canInner ? 0.25 : (canNext ? 0.5 : 1) ) : -1;
							var nextPercent = canNext ? (canInner ? 0.75 : (canPrev ? 0.5 : 0) ) : -1;
							var dY_percent = (event.clientY + docScrollTop - tmpTargetA.offset().top)/tmpTargetA.height();
							if ((prevPercent==1 ||dY_percent<=prevPercent && dY_percent>=-.2) && canPrev) {
								dX = 1 - tmpArrow.width();
								dY = 0 - tmpArrow.height()/2;
								moveType = MoveType_Before;
							} else if ((nextPercent==0 || dY_percent>=nextPercent && dY_percent<=1.2) && canNext) {
								dX = 1 - tmpArrow.width();
								dY = tmpTargetA.height() - tmpArrow.height()/2;
								moveType = MoveType_After;
							}else {
								dX = 5 - tmpArrow.width();
								dY = 0;
								moveType = MoveType_Inner;
							}
							tmpArrow.css({
								"display":"block",
								"top": (tmpTargetA.offset().top + dY) + "px",
								"left": (tmpTargetA.offset().left + dX) + "px"
							});

							if (preTmpTargetNodeId != tmpTargetNodeId || preTmpMoveType != moveType) {
								startTime = (new Date()).getTime();
							}
							if (moveType == MoveType_Inner) {
								window.zTreeMoveTimer = setTimeout(function() {
									if (moveType != MoveType_Inner) return;
									var targetNode = getTreeNodeByTId(targetSetting, tmpTargetNodeId);
									if (targetNode && targetNode.isParent && !targetNode.open && (new Date()).getTime() - startTime > 500
										&& tools.apply(targetSetting.callback.confirmDragOpen, [targetSetting.treeObjId, targetNode], true)) {
										switchNode(targetSetting, targetNode);
									}
								}, 600);
							}
						}
					} else {
						moveType = MoveType_Inner;
						tmpArrow.css({
							"display":"none"
						});
						if (window.zTreeMoveTimer) {
							clearTimeout(window.zTreeMoveTimer);
						}
					}
					preTmpTargetNodeId = tmpTargetNodeId;
					preTmpMoveType = moveType;
				}
				return false;
			});

			$(doc).mouseup(function(event) {
				if (window.zTreeMoveTimer) {
					clearTimeout(window.zTreeMoveTimer);
				}
				preTmpTargetNodeId = null;
				preTmpMoveType = null;
				$(doc).unbind("mousemove");
				$(doc).unbind("mouseup");
				$("body").css("cursor", "auto");
				if (tmpTarget) {
					tmpTarget.removeClass(Class_TmpTargetTree);
					if (tmpTargetNodeId) $("#" + tmpTargetNodeId + IDMark_A, tmpTarget).removeClass(Class_TmpTargetNode);
				}
				showIfameMask(false);

				if (setting.dragStatus == 0) return;
				setting.dragStatus = 0;

				if (treeNode.isParent && setting.dragNodeShowBefore && !treeNode.open) {
					expandAndCollapseNode(setting, treeNode, !treeNode.open);
					setting.dragNodeShowBefore = false;
				}

				if (curNode) curNode.remove();
				if (tmpArrow) tmpArrow.remove();

				//显示树上 移动后的节点
				if (tmpTarget && tmpTargetNodeId && treeNode.parentNode && tmpTargetNodeId==treeNode.parentNode.tId && moveType == MoveType_Inner) {
					tmpTarget = null;
				}
				if (tmpTarget) {
					var dragTargetNode = tmpTargetNodeId == null ? null: getTreeNodeByTId(targetSetting, tmpTargetNodeId);
					if (tools.apply(setting.callback.beforeDrop, [targetSetting.treeObjId, treeNode, dragTargetNode, moveType], true) == false) return;
					var isCopy = (event.ctrlKey && setting.dragMove && setting.dragCopy) || (!setting.dragMove && setting.dragCopy);

					var newNode = isCopy ? tools.clone(treeNode) : treeNode;
					if (isOtherTree) {
						if (!isCopy) {removeTreeNode(setting, treeNode);}
						if (moveType == MoveType_Inner) {
							addTreeNodes(targetSetting, dragTargetNode, [newNode]);
						} else {
							addTreeNodes(targetSetting, dragTargetNode.parentNode, [newNode]);
							moveTreeNode(targetSetting, dragTargetNode, newNode, moveType, false);
						}
					}else {
						if (isCopy) {
							if (moveType == MoveType_Inner) {
								addTreeNodes(targetSetting, dragTargetNode, [newNode]);
							} else {
								addTreeNodes(targetSetting, dragTargetNode.parentNode, [newNode]);
								moveTreeNode(targetSetting, dragTargetNode, newNode, moveType, false);
							}
						} else {
							moveTreeNode(targetSetting, dragTargetNode, newNode, moveType);
						}
					}
					selectNode(targetSetting, newNode);
					$("#" + newNode.tId + IDMark_Icon).focus().blur();

					//触发 DROP 拖拽事件，返回拖拽的目标数据对象
					setting.treeObj.trigger(ZTREE_DROP, [targetSetting.treeObjId, newNode, dragTargetNode, moveType]);

				} else {
					//触发 DROP 拖拽事件，返回null
					setting.treeObj.trigger(ZTREE_DROP, [setting.treeObjId, null, null, null]);
				}
			});

			//阻止默认事件专门用于处理 FireFox 的Bug，
			//该 Bug 导致如果 zTree Div CSS 中存在 overflow 设置，则拖拽节点移出 zTree 时，无法得到正确的event.target
			if(eventMouseDown.preventDefault) {
				eventMouseDown.preventDefault();
			}
		},
		onHoverOverNode: function(event) {
			var setting = settings[event.data.treeObjId];
			var treeNode = event.data.treeNode;
			if (setting.curHoverTreeNode != treeNode) {
				event.data.treeNode = setting.curHoverTreeNode;
				handler.onHoverOutNode(event);
			}
			setting.curHoverTreeNode = treeNode;
			addTreeDom(setting, treeNode);
		},
		onHoverOutNode: function(event) {
			var setting = settings[event.data.treeObjId];
			if (setting.curHoverTreeNode && setting.curTreeNode != setting.curHoverTreeNode) {
				removeTreeDom(setting, setting.curHoverTreeNode);
				setting.curHoverTreeNode = null;
			}
		},
		onZTreeMousedown: function(event) {
			var setting = settings[event.data.treeObjId];
			var treeNode = event.data.treeNode;
			//触发mouseDown事件
			if (tools.apply(setting.callback.beforeMouseDown, [setting.treeObjId, treeNode], true)) {
				tools.apply(setting.callback.mouseDown, [event, setting.treeObjId, treeNode]);
			}
			return true;
		},
		onZTreeMouseup: function(event) {
			var setting = settings[event.data.treeObjId];
			var treeNode = event.data.treeNode;
			//触发mouseUp事件
			if (tools.apply(setting.callback.beforeMouseUp, [setting.treeObjId, treeNode], true)) {
				tools.apply(setting.callback.mouseUp, [event, setting.treeObjId, treeNode]);
			}
			return true;
		},
		onZTreeDblclick: function(event) {
			var setting = settings[event.data.treeObjId];
			var treeNode = event.data.treeNode;
			//触发mouseUp事件
			if (tools.apply(setting.callback.beforeDblclick, [setting.treeObjId, treeNode], true)) {
				tools.apply(setting.callback.dblclick, [event, setting.treeObjId, treeNode]);
			}
			return true;
		},
		onZTreeContextmenu: function(event) {
			var setting = settings[event.data.treeObjId];
			var treeNode = event.data.treeNode;
			//触发rightClick事件
			if (tools.apply(setting.callback.beforeRightClick, [setting.treeObjId, treeNode], true)) {
				tools.apply(setting.callback.rightClick, [event, setting.treeObjId, treeNode]);
			}
			return (typeof setting.callback.rightClick) != "function";
		}
	};

	//设置光标位置函数
	function setCursorPosition(obj, pos){
		if(obj.setSelectionRange) {
			obj.focus();
			obj.setSelectionRange(pos,pos);
		} else if (obj.createTextRange) {
			var range = obj.createTextRange();
			range.collapse(true);
			range.moveEnd('character', pos);
			range.moveStart('character', pos);
			range.select();
		}
	}

	var dragMaskList = new Array();
	//显示、隐藏 Iframe的遮罩层（主要用于避免拖拽不流畅）
	function showIfameMask(showSign) {
		//清空所有遮罩
		while (dragMaskList.length > 0) {
			dragMaskList[0].remove();
			dragMaskList.shift();
		}
		if (showSign) {
			//显示遮罩
			var iframeList = $("iframe");
			for (var i = 0, l = iframeList.length; i < l; i++) {
				var obj = iframeList.get(i);
				var r = tools.getAbs(obj);
				var dragMask = $("<div id='zTreeMask_" + i + "' class='zTreeMask' style='top:" + r[1] + "px; left:" + r[0] + "px; width:" + obj.offsetWidth + "px; height:" + obj.offsetHeight + "px;'></div>");
				dragMask.appendTo("body");
				dragMaskList.push(dragMask);
			}
		}
	}

	//设置Name
	function setNodeName(setting, treeNode) {
		var nObj = $("#" + treeNode.tId + IDMark_Span);
		nObj.text(treeNode[setting.nameCol]);
	}
	//设置Target
	function setNodeTarget(treeNode) {
		var aObj = $("#" + treeNode.tId + IDMark_A);
		aObj.attr("target", makeNodeTarget(treeNode));
	}
	function makeNodeTarget(treeNode) {
		return (treeNode.target || "_blank");
	}
	//设置URL
	function setNodeUrl(setting, treeNode) {
		var aObj = $("#" + treeNode.tId + IDMark_A);
		var url = makeNodeUrl(setting, treeNode);
		if (url == null || url.length == 0) {
			aObj.removeAttr("href");
		} else {
			aObj.attr("href", url);
		}
	}
	function makeNodeUrl(setting, treeNode) {
		return (treeNode.url && !setting.editable) ? treeNode.url : null;
	}
	//设置Line、Ico等css属性
	function setNodeLineIcos(setting, treeNode) {
		if (!treeNode) return;
		var switchObj = $("#" + treeNode.tId + IDMark_Switch);
		var ulObj = $("#" + treeNode.tId + IDMark_Ul);
		var icoObj = $("#" + treeNode.tId + IDMark_Icon);

		var ulLine = makeUlLineClass(setting, treeNode);
		if (ulLine.length==0) {
			ulObj.removeClass(LineMark_Line);
		} else {
			ulObj.addClass(ulLine);
		}

		switchObj.attr("class", makeNodeLineClass(setting, treeNode));
		icoObj.removeAttr("style");
		icoObj.attr("style", makeNodeIcoStyle(setting, treeNode));
		icoObj.attr("class", makeNodeIcoClass(setting, treeNode));
	}
	function makeNodeLineClass(setting, treeNode) {
		var lineClass = ["switch"];
		if (setting.showLine) {
			if (treeNode.level == 0 && treeNode.isFirstNode && treeNode.isLastNode) {
				lineClass.push(LineMark_Root);
			} else if (treeNode.level == 0 && treeNode.isFirstNode) {
				lineClass.push(LineMark_Roots);
			} else if (treeNode.isLastNode) {
				lineClass.push(LineMark_Bottom);
			} else {
				lineClass.push(LineMark_Center);
			}

		} else {
			lineClass.push(LineMark_NoLine);
		}
		if (treeNode.isParent) {
			lineClass.push(treeNode.open ? FolderMark_Open : FolderMark_Close);
		} else {
			lineClass.push(FolderMark_Docu);
		}
		return lineClass.join('_');
	}
	function makeUlLineClass(setting, treeNode) {
		return (setting.showLine && !treeNode.isLastNode) ? LineMark_Line : "";
	}
	function makeNodeIcoClass(setting, treeNode) {
		var icoCss = ["ico"];
		if (!treeNode.isAjaxing) {
			icoCss[0] = (treeNode.iconSkin ? treeNode.iconSkin : "") + " " + icoCss[0];
			if (treeNode.isParent) {
				icoCss.push(treeNode.open ? FolderMark_Open : FolderMark_Close);
			} else {
				icoCss.push(FolderMark_Docu);
			}
		}
		return icoCss.join('_');
	}
	function makeNodeIcoStyle(setting, treeNode) {
		var icoStyle = [];
		if (!treeNode.isAjaxing) {
			var icon = (treeNode.isParent && treeNode.iconOpen && treeNode.iconClose) ? (treeNode.open ? treeNode.iconOpen : treeNode.iconClose) : treeNode.icon;
			if (icon) icoStyle.push("background:url(", icon, ") 0 0 no-repeat;");
			if (setting.showIcon == false || !tools.apply(setting.showIcon, [setting.treeObjId, treeNode], true)) {
				icoStyle.push("width:0px;height:0px;");
			}
		}
		return icoStyle.join('');
	}

	//设置自定义字体样式
	function setNodeFontCss(setting, treeNode) {
		var aObj = $("#" + treeNode.tId + IDMark_A);
		var fontCss = makeNodeFontCss(setting, treeNode);
		if (fontCss) {
			aObj.css(fontCss);
		}
	}
	function makeNodeFontCss(setting, treeNode) {
		var fontCss = tools.apply(setting.fontCss, [setting.treeObjId, treeNode]);
		if (fontCss == null) {
			fontCss = setting.fontCss;
		}
		if (fontCss) {
			return fontCss;
		} else {
			return {};
		}
	}

	//对于button替换class 拼接字符串
	function replaceSwitchClass(obj, newName) {
		if (!obj) return;

		var tmpName = obj.attr("class");
		if (tmpName == undefined) return;
		var tmpList = tmpName.split("_");
		switch (newName) {
			case LineMark_Root:
			case LineMark_Roots:
			case LineMark_Center:
			case LineMark_Bottom:
			case LineMark_NoLine:
				tmpList[1] = newName;
				break;
			case FolderMark_Open:
			case FolderMark_Close:
			case FolderMark_Docu:
				tmpList[2] = newName;
				break;
		}

		obj.attr("class", tmpList.join("_"));
	}
	function replaceIcoClass(treeNode, obj, newName) {
		if (!obj || treeNode.isAjaxing) return;

		var tmpName = obj.attr("class");
		if (tmpName == undefined) return;
		var tmpList = tmpName.split("_");
		switch (newName) {
			case FolderMark_Open:
			case FolderMark_Close:
			case FolderMark_Docu:
				tmpList[1] = newName;
				break;
		}

		obj.attr("class", tmpList.join("_"));
	}

	//添加zTree的按钮控件
	function addTreeDom(setting, treeNode) {
		if (setting.dragStatus == 0) {
			treeNode.isHover = true;
			if (setting.editable) {
				addEditBtn(setting, treeNode);
				addRemoveBtn(setting, treeNode);
			}
			tools.apply(setting.addHoverDom, [setting.treeObjId, treeNode]);
		}
	}
	//删除zTree的按钮控件
	function removeTreeDom(setting, treeNode) {
		treeNode.isHover = false;
		removeEditBtn(treeNode);
		removeRemoveBtn(treeNode);
		tools.apply(setting.removeHoverDom, [setting.treeObjId, treeNode]);
	}
	//删除 编辑、删除按钮
	function removeEditBtn(treeNode) {
		$("#" + treeNode.tId + IDMark_Edit).unbind().remove();
	}
	function removeRemoveBtn(treeNode) {
		$("#" + treeNode.tId + IDMark_Remove).unbind().remove();
	}
	function addEditBtn(setting, treeNode) {
		if (treeNode.editNameStatus || $("#" + treeNode.tId + IDMark_Edit).length > 0) {
			return;
		}
		if (!tools.apply(setting.edit_renameBtn, [treeNode], setting.edit_renameBtn)) {
			return;
		}
		var nObj = $("#" + treeNode.tId + IDMark_Span);
		var editStr = "<button type='button' class='edit' id='" + treeNode.tId + IDMark_Edit + "' title='' treeNode"+IDMark_Edit+" onfocus='this.blur();' style='display:none;'></button>";
		nObj.after(editStr);

		$("#" + treeNode.tId + IDMark_Edit).bind('click',
			function() {
				if (tools.apply(setting.callback.beforeRename, [setting.treeObjId, treeNode], true) == false) return true;
				editTreeNode(setting, treeNode);
				return false;
			}
			).show();
	}
	function addRemoveBtn(setting, treeNode) {
		if (!setting.edit_removeBtn || $("#" + treeNode.tId + IDMark_Remove).length > 0) {
			return;
		}
		if (!tools.apply(setting.edit_removeBtn, [treeNode], setting.edit_removeBtn)) {
			return;
		}
		var aObj = $("#" + treeNode.tId + IDMark_A);
		var removeStr = "<button type='button' class='remove' id='" + treeNode.tId + IDMark_Remove + "' title='' treeNode"+IDMark_Remove+" onfocus='this.blur();' style='display:none;'></button>";
		aObj.append(removeStr);

		$("#" + treeNode.tId + IDMark_Remove).bind('click',
			function() {
				if (tools.apply(setting.callback.beforeRemove, [setting.treeObjId, treeNode], true) == false) return true;
				removeTreeNode(setting, treeNode);
				//触发remove事件
				setting.treeObj.trigger(ZTREE_REMOVE, [setting.treeObjId, treeNode]);
				return false;
			}
			).bind('mousedown',
			function(eventMouseDown) {
				return true;
			}
			).show();
	}

	//设置check后，父子节点联动关系
	function checkNodeRelation(setting, treeNode) {
		var pNode, i, l;
		if (setting.checkStyle == Check_Style_Radio) {
			if (treeNode[setting.checkedCol]) {
				if (setting.checkRadioType == Radio_Type_All) {
					for (i = setting.checkRadioCheckedList.length-1; i >= 0; i--) {
						pNode = setting.checkRadioCheckedList[i];
						pNode[setting.checkedCol] = false;
						setting.checkRadioCheckedList.splice(i, 1);

						setChkClass(setting, $("#" + pNode.tId + IDMark_Check), pNode);
						if (pNode.parentNode != treeNode.parentNode) {
							repairParentChkClassWithSelf(setting, pNode);
						}
					}
					setting.checkRadioCheckedList = setting.checkRadioCheckedList.concat([treeNode]);
				} else {
					var parentNode = (treeNode.parentNode) ? treeNode.parentNode : setting.root;
					for (i = 0, l = parentNode[setting.nodesCol].length; i < l; i++) {
						pNode = parentNode[setting.nodesCol][i];
						if (pNode[setting.checkedCol] && pNode != treeNode) {
							pNode[setting.checkedCol] = false;
							setChkClass(setting, $("#" + pNode.tId + IDMark_Check), pNode);
						}
					}
				}
			} else if (setting.checkRadioType == Radio_Type_All) {
				for (i = 0, l = setting.checkRadioCheckedList.length; i < l; i++) {
					if (treeNode == setting.checkRadioCheckedList[i]) {
						setting.checkRadioCheckedList.splice(i, 1);
						break;
					}
				}
			}

		} else {
			if (treeNode[setting.checkedCol] && setting.checkType.Y.indexOf("s") > -1) {
				setSonNodeCheckBox(setting, treeNode, true);
			}
			if (treeNode[setting.checkedCol] && setting.checkType.Y.indexOf("p") > -1) {
				setParentNodeCheckBox(setting, treeNode, true);
			}
			if (!treeNode[setting.checkedCol] && setting.checkType.N.indexOf("s") > -1) {
				setSonNodeCheckBox(setting, treeNode, false);
			}
			if (!treeNode[setting.checkedCol] && setting.checkType.N.indexOf("p") > -1) {
				setParentNodeCheckBox(setting, treeNode, false);
			}
		}
	}

	//遍历父节点设置checkbox
	function setParentNodeCheckBoxOld(setting, treeNode, value) {
		var checkObj = $("#" + treeNode.tId + IDMark_Check);
		treeNode[setting.checkedCol] = value;
		setChkClass(setting, checkObj, treeNode);
		if (treeNode.parentNode) {
			var pSign = true;
			if (!value) {
				for (var i = 0, l = treeNode.parentNode[setting.nodesCol].length; i < l; i++) {
					if (treeNode.parentNode[setting.nodesCol][i][setting.checkedCol]) {
						pSign = false;
						break;
					}
				}
			}
			if (pSign) {
				setParentNodeCheckBox(setting, treeNode.parentNode, value);
			}
		}
	}

        //遍历父节点设置checkbox(子节点全部check，则check父节点)
	function setParentNodeCheckBox(setting, treeNode, value) {
		var checkObj = $("#" + treeNode.tId + IDMark_Check);
		treeNode[setting.checkedCol] = value;
		setChkClass(setting, checkObj, treeNode);
		if (treeNode.parentNode) {
			var pSign = true;
			for (var son = 0; son < treeNode.parentNode[setting.nodesCol].length; son++) {
				if (!treeNode.parentNode[setting.nodesCol][son][setting.checkedCol]) {
					pSign = false;
					break;
				}
			}
			setParentNodeCheckBox(setting, treeNode.parentNode, pSign);
		}
	}


	//遍历子节点设置checkbox
	function setSonNodeCheckBox(setting, treeNode, value) {
		if (!treeNode) return;
		var checkObj = $("#" + treeNode.tId + IDMark_Check);

		if (treeNode != setting.root) {
			treeNode[setting.checkedCol] = value;
			treeNode.check_True_Full = true;
			treeNode.check_False_Full = true;
			setChkClass(setting, checkObj, treeNode);
		}

		if (!treeNode[setting.nodesCol]) return;
		for (var i = 0, l = treeNode[setting.nodesCol].length; i < l; i++) {
			if (treeNode[setting.nodesCol][i]) setSonNodeCheckBox(setting, treeNode[setting.nodesCol][i], value);
		}
	}

	//设置CheckBox的Class类型，主要用于显示子节点是否全部被选择的样式
	function setChkClass(setting, obj, treeNode) {
		if (!obj) return;
		if (treeNode.nocheck === true) {
			obj.hide();
		} else {
			obj.show();
		}
		obj.removeClass();
		obj.addClass(makeChkClass(setting, treeNode));
	}
	function makeChkClass(setting, treeNode) {
		var chkName = setting.checkStyle + "_" + (treeNode[setting.checkedCol] ? CheckBox_True : CheckBox_False)
		+ "_" + ((treeNode[setting.checkedCol] || setting.checkStyle == Check_Style_Radio) ? (treeNode.check_True_Full? CheckBox_Full:CheckBox_Part) : (treeNode.check_False_Full? CheckBox_Full:CheckBox_Part) );
		chkName = treeNode.checkboxFocus ? chkName + "_" + CheckBox_Focus : chkName;
		return CheckBox_Default + " " + chkName;
	}

	function repairAllChk(setting, checked) {
		if (setting.checkable) {
			for (var son = 0; son < setting.root[setting.nodesCol].length; son++) {
				var treeNode = setting.root[setting.nodesCol][son];
				treeNode[setting.checkedCol] = checked;
				setSonNodeCheckBox(setting, treeNode, checked);
			}
		}
	}
	//修正父节点选择的样式
	function repairParentChkClass(setting, treeNode) {
		if (!treeNode || !treeNode.parentNode) return;
		repairChkClass(setting, treeNode.parentNode);
		repairParentChkClass(setting, treeNode.parentNode);
	}
	function repairParentChkClassWithSelf(setting, treeNode) {
		if (!treeNode) return;
		if (treeNode[setting.nodesCol] && treeNode[setting.nodesCol].length > 0) {
			repairParentChkClass(setting, treeNode[setting.nodesCol][0]);
		} else {
			repairParentChkClass(setting, treeNode);
		}
	}

	function repairChkClass(setting, treeNode) {
		if (!treeNode) return;
		makeChkFlag(setting, treeNode);
		var checkObj = $("#" + treeNode.tId + IDMark_Check);
		setChkClass(setting, checkObj, treeNode);
	}
	function makeChkFlag(setting, treeNode) {
		if (!treeNode) return;
		var chkFlag = {"trueFlag": true, "falseFlag": true};
		if (treeNode[setting.nodesCol]) {
			for (var i = 0, l = treeNode[setting.nodesCol].length; i < l; i++) {
				if (setting.checkStyle == Check_Style_Radio && (treeNode[setting.nodesCol][i][setting.checkedCol] || !treeNode[setting.nodesCol][i].check_True_Full)) {
					chkFlag.trueFlag = false;
				} else if (setting.checkStyle != Check_Style_Radio && treeNode[setting.checkedCol] && (!treeNode[setting.nodesCol][i][setting.checkedCol] || !treeNode[setting.nodesCol][i].check_True_Full)) {
					chkFlag.trueFlag = false;
				} else if (setting.checkStyle != Check_Style_Radio && !treeNode[setting.checkedCol] && (treeNode[setting.nodesCol][i][setting.checkedCol] || !treeNode[setting.nodesCol][i].check_False_Full)) {
					chkFlag.falseFlag = false;
				}
				if (!chkFlag.trueFlag || !chkFlag.falseFlag) break;
			}
		}
		treeNode.check_True_Full = chkFlag.trueFlag;
		treeNode.check_False_Full = chkFlag.falseFlag;
	}

	function switchNode(setting, treeNode) {
		if (treeNode.open || (treeNode && treeNode[setting.nodesCol] && treeNode[setting.nodesCol].length > 0)) {
			expandAndCollapseNode(setting, treeNode, !treeNode.open);
		} else if (setting.async) {
			if (tools.apply(setting.callback.beforeAsync, [setting.treeObjId, treeNode], true) == false) {
				expandAndCollapseNode(setting, treeNode, !treeNode.open);
				return;
			}
			asyncGetNode(setting, treeNode);
		} else if (treeNode) {
			expandAndCollapseNode(setting, treeNode, !treeNode.open);
		}
	}

	function asyncGetNode(setting, treeNode) {
		var i, l;
		if (treeNode && (treeNode.isAjaxing || !treeNode.isParent)) {
			return;
		}
		if (treeNode) {
			treeNode.isAjaxing = true;
			var icoObj = $("#" + treeNode.tId + IDMark_Icon);
			icoObj.attr("class", "ico_loading");
		}

		var tmpParam = "";
		for (i = 0, l = setting.asyncParam.length; treeNode && i < l; i++) {
			tmpParam += (tmpParam.length > 0 ? "&": "") + setting.asyncParam[i] + "=" + treeNode[setting.asyncParam[i]];
		}
		if (tools.isArray(setting.asyncParamOther)) {
			for (i = 0, l = setting.asyncParamOther.length; i < l; i += 2) {
				tmpParam += (tmpParam.length > 0 ? "&": "") + setting.asyncParamOther[i] + "=" + setting.asyncParamOther[i + 1];
			}
		} else {
			for (var p in setting.asyncParamOther) {
				tmpParam += (tmpParam.length > 0 ? "&" : "") + p + "=" + setting.asyncParamOther[p];
			}
		}

		$.ajax({
			type: "POST",
			url: tools.apply(setting.asyncUrl, [treeNode], setting.asyncUrl),
			data: tmpParam,
			dataType: "text",
			success: function(msg) {
				var newNodes = [];
				try {
					if (!msg || msg.length == 0) {
						newNodes = [];
					} else if (typeof msg == "string") {
						newNodes = eval("(" + msg + ")");
					} else {
						newNodes = msg;
					}
				} catch(err) {}

				if (treeNode) treeNode.isAjaxing = null;
				setNodeLineIcos(setting, treeNode);
				if (newNodes && newNodes != "") {
					newNodes = tools.apply(setting.asyncDataFilter, [setting.treeObjId, treeNode, newNodes], newNodes);
					addTreeNodes(setting, treeNode, newNodes, false);
				} else {
					addTreeNodes(setting, treeNode, [], false);
				}
				setting.treeObj.trigger(ZTREE_ASYNC_SUCCESS, [setting.treeObjId, treeNode, msg]);

			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				setting.expandTriggerFlag = false;
				setNodeLineIcos(setting, treeNode);
				if (treeNode) treeNode.isAjaxing = null;
				setting.treeObj.trigger(ZTREE_ASYNC_ERROR, [setting.treeObjId, treeNode, XMLHttpRequest, textStatus, errorThrown]);
			}
		});
	}

	// 展开 或者 折叠 节点下级
	function expandAndCollapseNode(setting, treeNode, expandSign, animateSign, callback) {
		if (!treeNode || treeNode.open == expandSign) {
			tools.apply(callback, []);
			return;
		}
		if (setting.expandTriggerFlag) {
			callback = function(){
				if (treeNode.open) {
					//触发expand事件
					setting.treeObj.trigger(ZTREE_EXPAND, [setting.treeObjId, treeNode]);
				} else {
					//触发collapse事件
					setting.treeObj.trigger(ZTREE_COLLAPSE, [setting.treeObjId, treeNode]);
				}
			};
			setting.expandTriggerFlag = false;
		}

		var switchObj = $("#" + treeNode.tId + IDMark_Switch);
		var icoObj = $("#" + treeNode.tId + IDMark_Icon);
		var ulObj = $("#" + treeNode.tId + IDMark_Ul);

		if (treeNode.isParent) {
			treeNode.open = !treeNode.open;
			if (treeNode.iconOpen && treeNode.iconClose) {
				icoObj.attr("style", makeNodeIcoStyle(setting, treeNode));
			}

			if (treeNode.open) {
				replaceSwitchClass(switchObj, FolderMark_Open);
				replaceIcoClass(treeNode, icoObj, FolderMark_Open);
				if (animateSign == false || setting.expandSpeed == "") {
					ulObj.show();
					tools.apply(callback, []);
				} else {
					if (treeNode[setting.nodesCol] && treeNode[setting.nodesCol].length > 0) {
						ulObj.show(setting.expandSpeed, callback);
					} else {
						ulObj.show();
						tools.apply(callback, []);
					}
				}
			} else {
				replaceSwitchClass(switchObj, FolderMark_Close);
				replaceIcoClass(treeNode, icoObj, FolderMark_Close);
				if (animateSign == false || setting.expandSpeed == "") {
					ulObj.hide();
					tools.apply(callback, []);
				} else {
					ulObj.hide(setting.expandSpeed, callback);
				}
			}
		} else {
			tools.apply(callback, []);
		}
	}

	//遍历子节点展开 或 折叠
	function expandCollapseSonNode(setting, treeNode, expandSign, animateSign, callback) {
		var treeNodes = (treeNode) ? treeNode[setting.nodesCol]: setting.root[setting.nodesCol];

		//针对动画进行优化,一般来说只有在第一层的时候，才进行动画效果
		var selfAnimateSign = (treeNode) ? false : animateSign;
		if (treeNodes) {
			for (var i = 0, l = treeNodes.length; i < l; i++) {
				if (treeNodes[i]) expandCollapseSonNode(setting, treeNodes[i], expandSign, selfAnimateSign);
			}
		}
		//保证callback只执行一次
		expandAndCollapseNode(setting, treeNode, expandSign, animateSign, callback );

	}

	//遍历父节点展开 或 折叠
	function expandCollapseParentNode(setting, treeNode, expandSign, animateSign, callback) {
		//针对动画进行优化,一般来说只有在第一层的时候，才进行动画效果
		if (!treeNode) return;
		if (!treeNode.parentNode) {
			//保证callback只执行一次
			expandAndCollapseNode(setting, treeNode, expandSign, animateSign, callback);
			return ;
		} else {
			expandAndCollapseNode(setting, treeNode, expandSign, animateSign);
		}

		if (treeNode.parentNode) {
			expandCollapseParentNode(setting, treeNode.parentNode, expandSign, animateSign, callback);
		}
	}

	//遍历子节点设置level,主要用于移动节点后的处理
	function setSonNodeLevel(setting, parentNode, treeNode) {
		if (!treeNode) return;
		treeNode.level = (parentNode)? parentNode.level + 1 : 0;
		if (!treeNode[setting.nodesCol]) return;
		for (var i = 0, l = treeNode[setting.nodesCol].length; i < l; i++) {
			if (treeNode[setting.nodesCol][i]) setSonNodeLevel(setting, treeNode, treeNode[setting.nodesCol][i]);
		}
	}

	//增加子节点
	function addTreeNodes(setting, parentNode, newNodes, isSilent) {
		if (setting.keepLeaf && parentNode && !parentNode.isParent) {
			return;
		}
		if (setting.isSimpleData) {
			newNodes = transformTozTreeFormat(setting, newNodes);
		}
		if (parentNode) {
			//目标节点必须在当前树内
			if (setting.treeObj.find("#" + parentNode.tId).length == 0) return;

			var target_switchObj = $("#" + parentNode.tId + IDMark_Switch);
			var target_icoObj = $("#" + parentNode.tId + IDMark_Icon);
			var target_ulObj = $("#" + parentNode.tId + IDMark_Ul);

			//处理节点在目标节点的图片、线
			if (!parentNode.open) {
				replaceSwitchClass(target_switchObj, FolderMark_Close);
				replaceIcoClass(parentNode, target_icoObj, FolderMark_Close);
				parentNode.open = false;
				target_ulObj.css({"display": "none"});
			}

			addTreeNodesData(setting, parentNode, newNodes);
			initTreeNodes(setting, parentNode.level + 1, newNodes, parentNode);
			//如果选择某节点，则必须展开其全部父节点
			if (!isSilent) {
				expandCollapseParentNode(setting, parentNode, true);
			}
		} else {
			addTreeNodesData(setting, setting.root, newNodes);
			initTreeNodes(setting, 0, newNodes, null);
		}
	}

	//增加节点数据
	function addTreeNodesData(setting, parentNode, treenodes) {
		if (!parentNode[setting.nodesCol]) parentNode[setting.nodesCol] = [];
		if (parentNode[setting.nodesCol].length > 0) {
			parentNode[setting.nodesCol][parentNode[setting.nodesCol].length - 1].isLastNode = false;
			setNodeLineIcos(setting, parentNode[setting.nodesCol][parentNode[setting.nodesCol].length - 1]);
		}
		parentNode.isParent = true;
		parentNode[setting.nodesCol] = parentNode[setting.nodesCol].concat(treenodes);
	}

	//移动子节点
	function moveTreeNode(setting, targetNode, treeNode, moveType, animateSign) {
		if (targetNode == treeNode) return;
		if (setting.keepLeaf && targetNode && !targetNode.isParent && moveType == MoveType_Inner) return;
		var oldParentNode = treeNode.parentNode == null ? setting.root: treeNode.parentNode;

		var targetNodeIsRoot = (targetNode === null || targetNode == setting.root);
		if (targetNodeIsRoot && targetNode === null) targetNode = setting.root;
		if (targetNodeIsRoot) moveType = MoveType_Inner;
		var targetParentNode = (targetNode.parentNode ? targetNode.parentNode : setting.root);

		if (moveType != MoveType_Before && moveType != MoveType_After) {
			moveType = MoveType_Inner;
		}

		//进行数据结构修正
		var i,l;
		var tmpSrcIndex = -1;
		var tmpTargetIndex = 0;
		var oldNeighbor = null;
		var newNeighbor = null;
		if (treeNode.isFirstNode) {
			tmpSrcIndex = 0;
			if (oldParentNode[setting.nodesCol].length > 1 ) {
				oldNeighbor = oldParentNode[setting.nodesCol][1];
				oldNeighbor.isFirstNode = true;
			}
		} else if (treeNode.isLastNode) {
			tmpSrcIndex = oldParentNode[setting.nodesCol].length -1;
			oldNeighbor = oldParentNode[setting.nodesCol][tmpSrcIndex - 1];
			oldNeighbor.isLastNode = true;
		} else {
			for (i = 0, l = oldParentNode[setting.nodesCol].length; i < l; i++) {
				if (oldParentNode[setting.nodesCol][i].tId == treeNode.tId) tmpSrcIndex = i;
			}
		}
		if (tmpSrcIndex >= 0) {
			oldParentNode[setting.nodesCol].splice(tmpSrcIndex, 1);
		}
		if (moveType != MoveType_Inner) {
			for (i = 0, l = targetParentNode[setting.nodesCol].length; i < l; i++) {
				if (targetParentNode[setting.nodesCol][i].tId == targetNode.tId) tmpTargetIndex = i;
			}
		}
		if (moveType == MoveType_Inner) {
			if (targetNodeIsRoot) {
				//成为根节点，则不操作目标节点数据
				treeNode.parentNode = null;
			} else {
				targetNode.isParent = true;
				treeNode.parentNode = targetNode;
			}

			if (!targetNode[setting.nodesCol]) targetNode[setting.nodesCol] = new Array();
			if (targetNode[setting.nodesCol].length > 0) {
				newNeighbor = targetNode[setting.nodesCol][targetNode[setting.nodesCol].length - 1];
				newNeighbor.isLastNode = false;
			}
			targetNode[setting.nodesCol].splice(targetNode[setting.nodesCol].length, 0, treeNode);
			treeNode.isLastNode = true;
			treeNode.isFirstNode = (targetNode[setting.nodesCol].length == 1);
		} else if (targetNode.isFirstNode && moveType == MoveType_Before) {
			targetParentNode[setting.nodesCol].splice(tmpTargetIndex, 0, treeNode);
			newNeighbor = targetNode;
			newNeighbor.isFirstNode = false;
			treeNode.parentNode = targetNode.parentNode;
			treeNode.isFirstNode = true;
			treeNode.isLastNode = false;

		} else if (targetNode.isLastNode && moveType == MoveType_After) {
			targetParentNode[setting.nodesCol].splice(tmpTargetIndex + 1, 0, treeNode);
			newNeighbor = targetNode;
			newNeighbor.isLastNode = false;
			treeNode.parentNode = targetNode.parentNode;
			treeNode.isFirstNode = false;
			treeNode.isLastNode = true;

		} else {
			if (moveType == MoveType_Before) {
				targetParentNode[setting.nodesCol].splice(tmpTargetIndex, 0, treeNode);
			} else {
				targetParentNode[setting.nodesCol].splice(tmpTargetIndex + 1, 0, treeNode);
			}
			treeNode.parentNode = targetNode.parentNode;
			treeNode.isFirstNode = false;
			treeNode.isLastNode = false;
		}
		fixParentKeyValue(setting, treeNode);

		setSonNodeLevel(setting, treeNode.parentNode, treeNode);

		//进行HTML结构修正
		var targetObj;
		var target_switchObj;
		var target_icoObj;
		var target_ulObj;

		if (targetNodeIsRoot) {
			//转移到根节点
			targetObj = setting.treeObj;
			target_ulObj = targetObj;
		} else {
			//转移到子节点
			targetObj = $("#" + targetNode.tId);
			target_switchObj = $("#" + targetNode.tId + IDMark_Switch);
			target_icoObj = $("#" + targetNode.tId + IDMark_Icon);
			target_ulObj = $("#" + targetNode.tId + IDMark_Ul);
		}

		//处理目标节点
		if (moveType == MoveType_Inner) {
			replaceSwitchClass(target_switchObj, FolderMark_Open);
			replaceIcoClass(targetNode, target_icoObj, FolderMark_Open);
			targetNode.open = true;
			target_ulObj.css({
				"display":"block"
			});
			target_ulObj.append($("#" + treeNode.tId).remove(null, true));
		} else if (moveType == MoveType_Before) {
			targetObj.before($("#" + treeNode.tId).remove(null, true));

		} else if (moveType == MoveType_After) {
			targetObj.after($("#" + treeNode.tId).remove(null, true));
		}

		//处理被移动的节点
		setNodeLineIcos(setting, treeNode);

		//处理原节点的父节点
		if (!setting.keepParent && oldParentNode[setting.nodesCol].length < 1) {
			//原所在父节点无子节点
			oldParentNode.isParent = false;
			var tmp_ulObj = $("#" + oldParentNode.tId + IDMark_Ul);
			var tmp_switchObj = $("#" + oldParentNode.tId + IDMark_Switch);
			var tmp_icoObj = $("#" + oldParentNode.tId + IDMark_Icon);
			replaceSwitchClass(tmp_switchObj, FolderMark_Docu);
			replaceIcoClass(oldParentNode, tmp_icoObj, FolderMark_Docu);
			tmp_ulObj.css("display", "none");

		} else if (oldNeighbor) {
			//原所在位置需要处理的相邻节点
			setNodeLineIcos(setting, oldNeighbor);
		}

		//处理目标节点的相邻节点
		if (newNeighbor) {
			setNodeLineIcos(setting, newNeighbor);
		}

		//修正父节点Check状态
		if (setting.checkable) {
			repairChkClass(setting, oldParentNode);
			repairParentChkClassWithSelf(setting, oldParentNode);
			if (oldParentNode != treeNode.parent)
				repairParentChkClassWithSelf(setting, treeNode);
		}

		//移动后，则必须展开新位置的全部父节点
		expandCollapseParentNode(setting, treeNode.parentNode, true, animateSign);
	}

	//修正pId
	function fixParentKeyValue(setting, treeNode) {
		if (setting.isSimpleData) {
			treeNode[setting.treeNodeParentKey] = treeNode.parentNode ? treeNode.parentNode[setting.treeNodeKey] : setting.rootPID;
		}
	}

	//编辑子节点名称
	function editTreeNode(setting, treeNode) {
		treeNode.editNameStatus = true;
		removeTreeDom(setting, treeNode);
		selectNode(setting, treeNode);
	}

	//删除子节点
	function removeTreeNode(setting, treeNode) {
		var parentNode = treeNode.parentNode == null ? setting.root: treeNode.parentNode;
		if (setting.curTreeNode === treeNode) setting.curTreeNode = null;
		if (setting.curEditTreeNode === treeNode) setting.curEditTreeNode = null;

		$("#" + treeNode.tId).remove();
		removeCache(setting, treeNode);

		//进行数据结构修正
		var tmpSrcIndex = -1;
		for (var i = 0, l = parentNode[setting.nodesCol].length; i < l; i++) {
			if (parentNode[setting.nodesCol][i].tId == treeNode.tId) tmpSrcIndex = i;
		}
		if (tmpSrcIndex >= 0) {
			parentNode[setting.nodesCol].splice(tmpSrcIndex, 1);
		}
		var tmp_ulObj,tmp_switchObj,tmp_icoObj;

		//处理原节点的父节点的图标、线
		if (!setting.keepParent && parentNode[setting.nodesCol].length < 1) {
			//原所在父节点无子节点
			parentNode.isParent = false;
			parentNode.open = false;
			tmp_ulObj = $("#" + parentNode.tId + IDMark_Ul);
			tmp_switchObj = $("#" + parentNode.tId + IDMark_Switch);
			tmp_icoObj = $("#" + parentNode.tId + IDMark_Icon);
			replaceSwitchClass(tmp_switchObj, FolderMark_Docu);
			replaceIcoClass(parentNode, tmp_icoObj, FolderMark_Docu);
			tmp_ulObj.css("display", "none");

		} else if (setting.showLine && parentNode[setting.nodesCol].length > 0) {
			//原所在父节点有子节点
			parentNode[setting.nodesCol][parentNode[setting.nodesCol].length - 1].isLastNode = true;
			parentNode[setting.nodesCol][parentNode[setting.nodesCol].length - 1].isFirstNode = (parentNode[setting.nodesCol].length == 1);
			tmp_ulObj = $("#" + parentNode[setting.nodesCol][parentNode[setting.nodesCol].length - 1].tId + IDMark_Ul);
			tmp_switchObj = $("#" + parentNode[setting.nodesCol][parentNode[setting.nodesCol].length - 1].tId + IDMark_Switch);
			tmp_icoObj = $("#" + parentNode[setting.nodesCol][parentNode[setting.nodesCol].length - 1].tId + IDMark_Icon);
			if (parentNode == setting.root) {
				if (parentNode[setting.nodesCol].length == 1) {
					//原为根节点 ，且移动后只有一个根节点
					replaceSwitchClass(tmp_switchObj, LineMark_Root);
				} else {
					var tmp_first_switchObj = $("#" + parentNode[setting.nodesCol][0].tId + IDMark_Switch);
					replaceSwitchClass(tmp_first_switchObj, LineMark_Roots);
					replaceSwitchClass(tmp_switchObj, LineMark_Bottom);
				}
			} else {
				replaceSwitchClass(tmp_switchObj, LineMark_Bottom);
			}

			tmp_ulObj.removeClass(LineMark_Line);
		}
	}

	//根据 tId 获取 节点的数据对象
	function getTreeNodeByTId(setting, treeId) {
		return zTreeNodeCache[setting.treeObjId][treeId];
	}
	function addCache(setting, treeNode) {
		zTreeNodeCache[setting.treeObjId][treeNode.tId] = treeNode;
	}
	function removeCache(setting, treeNode) {
		delete zTreeNodeCache[setting.treeObjId][treeNode.tId];
	}
	//根据唯一属性 获取 节点的数据对象
	function getTreeNodeByParam(setting, treeNodes, key, value) {
		if (!treeNodes || !key) return null;
		for (var i = 0, l = treeNodes.length; i < l; i++) {
			if (treeNodes[i][key] == value) {
				return treeNodes[i];
			}
			var tmp = getTreeNodeByParam(setting, treeNodes[i][setting.nodesCol], key, value);
			if (tmp) return tmp;
		}
		return null;
	}
	//根据属性 获取 节点的数据对象集合
	function getTreeNodesByParam(setting, treeNodes, key, value) {
		if (!treeNodes || !key) return [];
		var result = [];
		for (var i = 0, l = treeNodes.length; i < l; i++) {
			if (treeNodes[i][key] == value) {
				result.push(treeNodes[i]);
			}
			result = result.concat(getTreeNodesByParam(setting, treeNodes[i][setting.nodesCol], key, value));
		}
		return result;
	}
	//根据属性 模糊搜索获取 节点的数据对象集合（仅限String）
	function getTreeNodesByParamFuzzy(setting, treeNodes, key, value) {
		if (!treeNodes || !key) return [];
		var result = [];
		for (var i = 0, l = treeNodes.length; i < l; i++) {
			if (typeof treeNodes[i][key] == "string" && treeNodes[i][key].indexOf(value)>-1) {
				result.push(treeNodes[i]);
			}
			result = result.concat(getTreeNodesByParamFuzzy(setting, treeNodes[i][setting.nodesCol], key, value));
		}
		return result;
	}

	//设置节点为当前选中节点
	function selectNode(setting, treeNode) {
		if (setting.curTreeNode == treeNode && ((setting.curEditTreeNode == treeNode && treeNode.editNameStatus))) {return;}
		st.cancelPreEditNode(setting);
		st.cancelPreSelectedNode(setting);

		if (setting.editable && treeNode.editNameStatus) {
			$("#" + treeNode.tId + IDMark_Span).html("<input type=text class='rename' id='" + treeNode.tId + IDMark_Input + "' treeNode" + IDMark_Input + " >");

			var inputObj = $("#" + treeNode.tId + IDMark_Input);
			setting.curEditInput = inputObj;
			inputObj.attr("value", treeNode[setting.nameCol]);
			tools.inputFocus(inputObj);

			//拦截A的click dblclick监听
			inputObj.bind('blur', function(event) {
				if (st.checkEvent(setting)) {
					treeNode.editNameStatus = false;
					selectNode(setting, treeNode);
				}
			}).bind('keyup', function(event) {
				if (event.keyCode=="13") {
					if (st.checkEvent(setting)) {
						treeNode.editNameStatus = false;
						selectNode(setting, treeNode);
					}
				} else if (event.keyCode=="27") {
					inputObj.attr("value", treeNode[setting.nameCol]);
					treeNode.editNameStatus = false;
					selectNode(setting, treeNode);
				}
			}).bind('click', function(event) {
				return false;
			}).bind('dblclick', function(event) {
				return false;
			});

			$("#" + treeNode.tId + IDMark_A).addClass(Class_CurSelectedNode_Edit);
			setting.curEditTreeNode = treeNode;
		} else {
			$("#" + treeNode.tId + IDMark_A).addClass(Class_CurSelectedNode);
		}
		addTreeDom(setting, treeNode);
		setting.curTreeNode = treeNode;
	}

	//获取全部 checked = true or false 的节点集合
	function getTreeCheckedNodesOld(setting, treeNodes, checked) {
		if (!treeNodes) return [];
		var results = [];
		for (var i = 0, l = treeNodes.length; i < l; i++) {
			if (treeNodes[i].nocheck !== true && treeNodes[i][setting.checkedCol] == checked) {
				results = results.concat([treeNodes[i]]);
			}
			var tmp = getTreeCheckedNodes(setting, treeNodes[i][setting.nodesCol], checked);
			if (tmp.length > 0) results = results.concat(tmp);
		}
		return results;
	}
        //获取全部 checked = true or false 的节点集合(父节点check则不需要返回子节点信息)
	function getTreeCheckedNodes(setting, treeNodes, checked) {
		if (!treeNodes) return [];
		var results = [];
		for (var i = 0, l = treeNodes.length; i < l; i++) {
			if (treeNodes[i].nocheck !== true && treeNodes[i][setting.checkedCol] == checked) {
				results = results.concat([treeNodes[i]]);
			}
                        else
                        {
                                var tmp = getTreeCheckedNodes(setting, treeNodes[i][setting.nodesCol], checked);
                                if (tmp.length > 0) results = results.concat(tmp);
                        }

		}
		return results;
	}


	//获取全部 被修改Check状态 的节点集合
	function getTreeChangeCheckedNodes(setting, treeNodes) {
		if (!treeNodes) return [];
		var results = [];
		for (var i = 0, l = treeNodes.length; i < l; i++) {
			if (treeNodes[i].nocheck !== true && treeNodes[i][setting.checkedCol] != treeNodes[i].checkedOld) {
				results = results.concat([treeNodes[i]]);
			}
			var tmp = getTreeChangeCheckedNodes(setting, treeNodes[i][setting.nodesCol]);
			if (tmp.length > 0) results = results.concat(tmp);
		}
		return results;
	}

	//简要数据转换为标准JSON数组
	function transformTozTreeFormat(setting, simpleTreeNodes) {
		var i,l;
		var key = setting.treeNodeKey;
		var parentKey = setting.treeNodeParentKey;
		if (!key || key=="" || !simpleTreeNodes) return [];

		if (tools.isArray(simpleTreeNodes)) {
			var r = [];
			var tmpMap = [];
			for (i=0, l=simpleTreeNodes.length; i<l; i++) {
				tmpMap[simpleTreeNodes[i][key]] = simpleTreeNodes[i];
			}
			for (i=0, l=simpleTreeNodes.length; i<l; i++) {
				if (tmpMap[simpleTreeNodes[i][parentKey]]) {
					if (!tmpMap[simpleTreeNodes[i][parentKey]][setting.nodesCol])
						tmpMap[simpleTreeNodes[i][parentKey]][setting.nodesCol] = [];
					tmpMap[simpleTreeNodes[i][parentKey]][setting.nodesCol].push(simpleTreeNodes[i]);
				} else {
					r.push(simpleTreeNodes[i]);
				}
			}
			return r;
		}else {
			return [simpleTreeNodes];
		}
	}

	//标准JSON zTreeNode 数组转换为普通Array简要数据
	function transformToArrayFormat(setting, treeNodes) {
		if (!treeNodes) return [];
		var r = [];
		if (tools.isArray(treeNodes)) {
			for (var i=0, l=treeNodes.length; i<l; i++) {
				r.push(treeNodes[i]);
				if (treeNodes[i][setting.nodesCol])
					r = r.concat(transformToArrayFormat(setting, treeNodes[i][setting.nodesCol]));
			}
		} else {
			r.push(treeNodes);
			if (treeNodes[setting.nodesCol])
				r = r.concat(transformToArrayFormat(setting, treeNodes[setting.nodesCol]));
		}
		return r;
	}

	function zTreePlugin(){
		return {
			container:null,
			setting:null,

			init: function(obj) {
				this.container = obj;
				this.setting = settings[obj.attr("id")];
				return this;
			},

			refresh : function() {
				this.setting.treeObj.empty();
				zTreeNodeCache[this.setting.treeObjId] = [];
				this.setting.curTreeNode = null;
				this.setting.curEditTreeNode = null;
				this.setting.dragStatus = 0;
				this.setting.dragNodeShowBefore = false;
				this.setting.checkRadioCheckedList = [];
				initTreeNodes(this.setting, 0, this.setting.root[this.setting.nodesCol]);
			},

			setEditable : function(editable) {
				this.setting.editable = editable;
				return this.refresh();
			},

			transformTozTreeNodes : function(simpleTreeNodes) {
				return transformTozTreeFormat(this.setting, simpleTreeNodes);
			},

			transformToArray : function(treeNodes) {
				return transformToArrayFormat(this.setting, treeNodes);
			},

			getNodes : function() {
				return this.setting.root[this.setting.nodesCol];
			},

			getSelectedNode : function() {
				return this.setting.curTreeNode;
			},

			getCheckedNodes : function(checked) {
				checked = (checked != false);
				return getTreeCheckedNodes(this.setting, this.setting.root[this.setting.nodesCol], checked);
			},

			getChangeCheckedNodes : function() {
				return getTreeChangeCheckedNodes(this.setting, this.setting.root[this.setting.nodesCol]);
			},

			getNodeByTId : function(treeId) {
				if (!treeId) return null;
				return getTreeNodeByTId(this.setting, treeId);
			},
			getNodeByParam : function(key, value) {
				if (!key) return null;
				return getTreeNodeByParam(this.setting, this.setting.root[this.setting.nodesCol], key, value);
			},
			getNodesByParam : function(key, value, parentNode) {
				if (!key) return null;
				return getTreeNodesByParam(this.setting, parentNode?parentNode[this.setting.nodesCol]:this.setting.root[this.setting.nodesCol], key, value);
			},
			getNodesByParamFuzzy : function(key, value, parentNode) {
				if (!key) return null;
				return getTreeNodesByParamFuzzy(this.setting, parentNode?parentNode[this.setting.nodesCol]:this.setting.root[this.setting.nodesCol], key, value);
			},

			getNodeIndex : function(treeNode) {
				if (!treeNode) return null;
				var parentNode = (treeNode.parentNode == null) ? this.setting.root : treeNode.parentNode;
				for (var i=0, l = parentNode[this.setting.nodesCol].length; i < l; i++) {
					if (parentNode[this.setting.nodesCol][i] == treeNode) return i;
				}
				return -1;
			},

			getSetting : function() {
				var zTreeSetting = this.setting;
				var setting = {
					checkType:{},
					callback:{}
				};

				var tmp_checkType = zTreeSetting.checkType;
				zTreeSetting.checkType = undefined;
				var tmp_callback = zTreeSetting.callback;
				zTreeSetting.callback = undefined;
				var tmp_root = zTreeSetting.root;
				zTreeSetting.root = undefined;

				$.extend(setting, zTreeSetting);

				zTreeSetting.checkType = tmp_checkType;
				zTreeSetting.callback = tmp_callback;
				zTreeSetting.root = tmp_root;

				//不能获取root信息
				$.extend(true, setting.checkType, tmp_checkType);
				$.extend(setting.callback, tmp_callback);

				return setting;
			},

			updateSetting : function(zTreeSetting) {
				if (!zTreeSetting) return;
				var setting = this.setting;
				var treeObjId = setting.treeObjId;

				var tmp_checkType = zTreeSetting.checkType;
				zTreeSetting.checkType = undefined;
				var tmp_callback = zTreeSetting.callback;
				zTreeSetting.callback = undefined;
				var tmp_root = zTreeSetting.root;
				zTreeSetting.root = undefined;

				$.extend(setting, zTreeSetting);

				zTreeSetting.checkType = tmp_checkType;
				zTreeSetting.callback = tmp_callback;
				zTreeSetting.root = tmp_root;

				//不提供root信息update
				$.extend(true, setting.checkType, tmp_checkType);
				$.extend(setting.callback, tmp_callback);
				setting.treeObjId = treeObjId;
				setting.treeObj = this.container;

			},

			expandAll : function(expandSign) {
				expandCollapseSonNode(this.setting, null, expandSign, true);
			},

			expandNode : function(treeNode, expandSign, sonSign, focus) {
				if (!treeNode) return;

				if (expandSign) {
					//如果展开某节点，则必须展开其全部父节点
					//为了保证效率,展开父节点时不使用动画
					if (treeNode.parentNode) expandCollapseParentNode(this.setting, treeNode.parentNode, expandSign, false);
				}
				if (sonSign) {
					//多个图层同时进行动画，导致产生的延迟很难用代码准确捕获动画最终结束时间
					//因此为了保证准确将节点focus进行定位，则对于js操作节点时，不进行动画
					expandCollapseSonNode(this.setting, treeNode, expandSign, false, function() {
						if (focus !== false) {$("#" + treeNode.tId + IDMark_Icon).focus().blur();}
					});
				} else if (treeNode.open != expandSign) {
					switchNode(this.setting, treeNode);
					if (focus !== false) {$("#" + treeNode.tId + IDMark_Icon).focus().blur();}
				}
			},

			selectNode : function(treeNode) {
				if (!treeNode) return;

				if (st.checkEvent(this.setting)) {
					selectNode(this.setting, treeNode);
					//如果选择某节点，则必须展开其全部父节点
					//多个图层同时进行动画，导致产生的延迟很难用代码准确捕获动画最终结束时间
					//因此为了保证准确将节点focus进行定位，则对于js操作节点时，不进行动画
					if (treeNode.parentNode) {
						expandCollapseParentNode(this.setting, treeNode.parentNode, true, false, function() {
							$("#" + treeNode.tId + IDMark_Icon).focus().blur();
						});
					} else {
						$("#" + treeNode.tId + IDMark_Icon).focus().blur();
					}
				}
			},

			cancleSelectedNode : function() {
				this.cancelSelectedNode();
			},
			cancelSelectedNode : function() {
				st.cancelPreSelectedNode(this.setting);
			},

			checkAllNodes : function(checked) {
				repairAllChk(this.setting, checked);
			},

			reAsyncChildNodes : function(parentNode, reloadType) {
				if (!this.setting.async) return;
				var isRoot = !parentNode;
				if (isRoot) {
					parentNode = this.setting.root;
				}
				if (reloadType=="refresh") {
					parentNode[this.setting.nodesCol] = [];
					if (isRoot) {
						this.setting.treeObj.empty();
					} else {
						var ulObj = $("#" + parentNode.tId + IDMark_Ul);
						ulObj.empty();
					}
				}
				asyncGetNode(this.setting, isRoot? null:parentNode);
			},

			addNodes : function(parentNode, newNodes, isSilent) {
				if (!newNodes) return;
				if (!parentNode) parentNode = null;
				var xNewNodes = tools.isArray(newNodes)? newNodes: [newNodes];
				addTreeNodes(this.setting, parentNode, xNewNodes, (isSilent==true));
			},
			inputNodeName: function(treeNode) {
				if (!treeNode) return;
				if (st.checkEvent(this.setting)) {
					editTreeNode(this.setting, treeNode)
				}
			},
			cancelInput: function(newName) {
				if (!this.setting.curEditTreeNode) return;
				var treeNode = this.setting.curEditTreeNode;
				st.cancelPreEditNode(this.setting, newName?newName:treeNode[this.setting.nameCol]);
				this.selectNode(treeNode);
			},
			updateNode : function(treeNode, checkTypeFlag) {
				if (!treeNode) return;
				if (st.checkEvent(this.setting)) {
					var checkObj = $("#" + treeNode.tId + IDMark_Check);
					if (this.setting.checkable) {
						if (checkTypeFlag == true) checkNodeRelation(this.setting, treeNode);
						setChkClass(this.setting, checkObj, treeNode);
						repairParentChkClassWithSelf(this.setting, treeNode);
					}
					setNodeName(this.setting, treeNode);
					setNodeTarget(treeNode);
					setNodeUrl(this.setting, treeNode);
					setNodeLineIcos(this.setting, treeNode);
					setNodeFontCss(this.setting, treeNode);
				}
			},

			moveNode : function(targetNode, treeNode, moveType) {
				if (!treeNode) return;

				if (targetNode && ((treeNode.parentNode == targetNode && moveType == MoveType_Inner) || $("#" + treeNode.tId).find("#" + targetNode.tId).length > 0)) {
					return;
				} else if (!targetNode) {
					targetNode = null;
				}
				moveTreeNode(this.setting, targetNode, treeNode, moveType, false);
			},

			copyNode : function(targetNode, treeNode, moveType) {
				if (!treeNode) return null;
				var newNode = tools.clone(treeNode);
				if (!targetNode) {
					targetNode = null;
					moveType = MoveType_Inner;
				}
				if (moveType == MoveType_Inner) {
					addTreeNodes(this.setting, targetNode, [newNode]);
				} else {
					addTreeNodes(this.setting, targetNode.parentNode, [newNode]);
					moveTreeNode(this.setting, targetNode, newNode, moveType, false);
				}
				return newNode;
			},

			removeNode : function(treeNode) {
				if (!treeNode) return;
				removeTreeNode(this.setting, treeNode);
			}
		};
	}
})(jQuery);