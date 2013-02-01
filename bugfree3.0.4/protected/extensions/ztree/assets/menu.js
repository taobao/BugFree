$.Menu = function(input, menu) {
    function hideMenu() {
        $(menu).hide();
    }

    function showMenu(treeObj,menuId,selectedId) {
        var inputOffset = $(input).offset();
        $("#"+menuId).bgiframe();
        $(menu).css({
            left:inputOffset.left + "px",
            top:(inputOffset.top + $(input).outerHeight()) + "px"
            }).show();
        if(treeObj.getSelectedNode() == null)
        {
            var node = treeObj.getNodeByParam('id',selectedId);
            treeObj.selectNode(node);
        }

    }

    function autoHide() {
        $("body").bind("mousedown",
            function(event){
                if (!($(event.target).attr("id") == $(menu).attr("id")  || $(event.target).parents("#" + $(menu).attr("id")).length>0)) {
                    hideMenu();
                }
            });
    }

    return {
        hideMenu: hideMenu,
        showMenu: showMenu,
        autoHide: autoHide
    }
}