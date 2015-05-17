// JavaScript Document
function $ (id) {
    return Y.DOM.byId(id);
}

function load_tree(idtreecode) {
    if ($(idtreecode)) {
        var control_container = $(idtreecode);
        var json_string = control_container.value;
        
        var container_tree = document.createElement('div');
        control_container.parentNode.appendChild(container_tree);

        try {
            var json_object = JSON.parse(json_string);

            Y.use('yui2-treeview', function(Y) {
                var tree = new Y.YUI2.widget.TreeView(container_tree);
            

                _GLOBAL_VARS['id_menu_tree'] = tree.id;
    
                var sheet;
    
                 if (json_object.topics && json_object.topics.length > 0) {
                    for (var i = 0; i < json_object.topics.length; i++) {
                        create_sheet(tree.getRoot(), json_object.topics[i]);
                    }
                }
    
                tree.render();
                tree.subscribe('dblClickEvent',tree.onEventEditNode); 
                tree.subscribe("labelClick", function(node) {
                    sheet_click (node);
                });
            });
    
        }
        catch (e) {
            alert(e);
            return false;
        }
        
        return true;
    }
}

function create_sheet (node_root, obj) {
    var node = new Y.YUI2.widget.TextNode({label: obj.name}, node_root, true);
    _SHEETS[node.index] = obj;
    
    if (obj.subtopics) {
        for (var i = 0; i < obj.subtopics.length; i++) {
            create_sheet(node, obj.subtopics[i]);
        }
    }
}

function update_sheet (oldnode, obj) {
    var idtree = _GLOBAL_VARS['id_menu_tree'];

    var node = Y.YUI2.widget.TreeView.getNode(idtree, oldnode.index);
    node.editable = true;

    if (node.label != obj.name) {
        node.label = obj.name;
        node.parent.refresh();
    }
    node.editable = false;

}

function sheet_click (node) {
    YUI.tree_admin.panel_edit_sheet.show();

    $('name_text').value = _SHEETS[node.index].name;
    $('select_topic').value = _SHEETS[node.index].topicnumber;
    $('url_text').value = _SHEETS[node.index].url;
    $('select_target').value = _SHEETS[node.index].target;
    _GLOBAL_VARS['active_node'] = node;
    
    config_menu_actions (node);
}

function change_sheet() {
    var node = _GLOBAL_VARS['active_node'];
    var obj = new Object();
    obj.name = $('name_text').value;
    obj.topicnumber = $('select_topic').value;
    obj.url = $('url_text').value;
    obj.target = $('select_target').value;
    _SHEETS[node.index] = obj;
    update_sheet (node, obj);
    YUI.tree_admin.panel_edit_sheet.hide();
}

function add_sheet_daughter() {
    var node = _GLOBAL_VARS['active_node'];
    var obj = new Object();
    obj.name = $('name_text').value;
    obj.topicnumber = $('select_topic').value;
    obj.url = $('url_text').value;
    obj.target = $('select_target').value;

    create_sheet (node, obj);
    node.expanded = true;
    YUI.tree_admin.panel_edit_sheet.hide();
    node.parent.refresh();
}

function add_sheet_sister() {
    var node = _GLOBAL_VARS['active_node'];
    var obj = new Object();
    obj.name = $('name_text').value;
    obj.topicnumber = $('select_topic').value;
    obj.url = $('url_text').value;
    obj.target = $('select_target').value;

    create_sheet (node.parent, obj);
    node.expanded = true;
    YUI.tree_admin.panel_edit_sheet.hide();
    node.parent.refresh();
}

function move_sheet_left() {
    var idtree = _GLOBAL_VARS['id_menu_tree'];
    var tree = Y.YUI2.widget.TreeView.getTree(idtree);
    var node = _GLOBAL_VARS['active_node'];    
    var targetNode = node.parent;
    tree.popNode(node);

    node.insertAfter(targetNode);
    targetNode.expanded = true;

    targetNode.parent.refresh();
    
    config_menu_actions (node);
}

function move_sheet_right() {
    var idtree = _GLOBAL_VARS['id_menu_tree'];
    var tree = Y.YUI2.widget.TreeView.getTree(idtree);
    var node = _GLOBAL_VARS['active_node'];    
    var targetNode = node.previousSibling;
    tree.popNode(node);

    node.appendTo(targetNode);

    targetNode.expanded = true;

    targetNode.parent.refresh();
    
    config_menu_actions (node);
}

function move_sheet_up() {
    var idtree = _GLOBAL_VARS['id_menu_tree'];
    var tree = Y.YUI2.widget.TreeView.getTree(idtree);
    var node = _GLOBAL_VARS['active_node'];    
    var targetNode = node.previousSibling;
    tree.popNode(node);

    node.insertBefore(targetNode);

    targetNode.parent.refresh();
    
    config_menu_actions (node);
}

function move_sheet_down() {
    var idtree = _GLOBAL_VARS['id_menu_tree'];
    var tree = Y.YUI2.widget.TreeView.getTree(idtree);
    var node = _GLOBAL_VARS['active_node'];    
    var targetNode = node.nextSibling;
    tree.popNode(node);

    node.insertAfter(targetNode);

    targetNode.parent.refresh();
    
    config_menu_actions (node);
}

function delete_sheet() {
    var idtree = _GLOBAL_VARS['id_menu_tree'];
    var tree = Y.YUI2.widget.TreeView.getTree(idtree);
    var node = _GLOBAL_VARS['active_node'];    
    var root = tree.getRoot();

    tree.removeNode(node);
    root.refresh();
    
    YUI.tree_admin.panel_edit_sheet.hide();
}

function config_menu_actions (node) {
    var xy = Y.YUI2.util.Dom.getXY(node.contentElId);
    xy[0] = xy[0] + 50;
    xy[1] = xy[1] + 5;
    Y.YUI2.util.Dom.setXY('panel_container_editsheet', xy);

    if (node.parent == 'RootNode') {
        $('btn_move_left_sheet').style.display = 'none';
    }
    else {
        $('btn_move_left_sheet').style.display = '';
    }
    
    if (node.previousSibling == null) {
        $('btn_move_right_sheet').style.display = 'none';
        $('btn_move_up_sheet').style.display = 'none';
    }
    else {
        $('btn_move_right_sheet').style.display = '';
        $('btn_move_up_sheet').style.display = '';
    }

    if (node.nextSibling == null) {
        $('btn_move_down_sheet').style.display = 'none';
    }
    else {
        $('btn_move_down_sheet').style.display = '';
    }

    if (node.tree.getNodeCount() > 1) {
        $('btn_delete_sheet').style.display = '';
    }
    else {
        $('btn_delete_sheet').style.display = 'none';
    }
}

function save_tree_config () {
    var treecode = '';
    var idtree = _GLOBAL_VARS['id_menu_tree'];
    var tree = Y.YUI2.widget.TreeView.getTree(idtree);
    var root = tree.getRoot();
    var obj;

    treecode = '{"topics": [';
    treecode += tree_code_from_node(root);
    treecode += ']}';
    $('id_treecode').value = treecode;
}

function tree_code_from_node(rootNode) {
    var treecode = '';
    var obj;
    
    if (rootNode.children) {
        var node = rootNode.children[0];
        while (node != null) {
            if (treecode != '') {
                treecode += ",\n";
            }

            obj = _SHEETS[node.index];
            treecode += '{"name" : "' + obj.name + '",';
            treecode += '         "subtopics": [' + tree_code_from_node(node) + '],';
            treecode += '         "topicnumber": "' + obj.topicnumber + '",';
            treecode += '         "url": "' + obj.url + '",';
            treecode += '         "target": "' + obj.target + '"';
            treecode += '}';
            
            node = node.nextSibling;
        }
    }
    return treecode;
}

function change_topic(list) {
    if(list.value !== "") {
        $('url_text').disabled = true;
    }
    else {
        $('url_text').disabled = false;
    }
}