<?php
//
// You can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// It is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @since 2.3
 * @package contribution
 * @copyright 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


require_once($CFG->libdir.'/formslib.php');

class tree_menutopic_form extends moodleform {

    function definition() {
        global $USER, $CFG, $course, $PAGE;

        $PAGE->requires->js_module(array(
	        'name' => 'format_menutopic',
	        'fullpath' => '/course/format/menutopic/module.js',
	        'requires' => array('yui2-treeview', 'panel', 'dd-plugin'),//'base', 'dom', 'event-delegate', 'event-key',
	                //'json-parse', 'yui2-treeview', 'container', 'dragdrop', 'panel'),
	        'strings' => array(
	            array('error_jsontree', 'format_menutopic'),
	            array('title_panel_sheetedit', 'format_menutopic')
	        ),
    	));
        $PAGE->requires->js_init_call('M.format_menutopic.init_tree');
    	
		$treecode = '';

		if (is_object($this->_customdata['format_data']) && property_exists($this->_customdata['format_data'], 'tree') && !empty($this->_customdata['format_data']->tree)) {
			$treecode = stripslashes($this->_customdata['format_data']->tree);
		}
		else {
			$treecode = '{"topics": [';
			for ($i = 0; $i < ($course->numsections + 1); $i++){
				$treecode .= '{"name" : "' . get_string('template_namemenutopic', 'format_menutopic', $i) . '",';
				$treecode .= '         "subtopics": [],';
				$treecode .= '         "topicnumber": ' . $i . ',';
				$treecode .= '         "url": "",';
				$treecode .= '         "target": ""';
				$treecode .= "},\n";
			}
			$treecode = rtrim($treecode, ",\n");
			$treecode .= ']}';
		}

        $mform =& $this->_form;

        $mform->addElement('header','general', get_string('tree_editmenu_title', 'format_menutopic'));
        $mform->addHelpButton('general', 'tree_struct', 'format_menutopic');

        $mform->addElement('textarea','treecode', get_string('tree_struct', 'format_menutopic'), array('style'=>'display:none'));
        $mform->setType('treecode', PARAM_RAW);
		$mform->setDefault('treecode', $treecode);

        $mform->addElement('text', 'sections', '', array('style'=>'display:none'));
        $mform->setType('sections', PARAM_INT);
		$mform->setDefault('sections', ($course->numsections + 1));

        $mform->addElement('hidden', 'id', $course->id);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'section', $this->_customdata['displaysection']);
        $mform->setType('section', PARAM_INT);

        $mform->addElement('hidden', 'editmenumode', 'true');
        $mform->setType('editmenumode', PARAM_BOOL);
        
        $mform->addElement('hidden', 'menuaction', 'tree');
        $mform->setType('menuaction', PARAM_ALPHA);

		$this->add_action_buttons(false);
    }
}

//$displaysection, $format_data and $course_cancel_link are loaded in the render function
$display_form = new tree_menutopic_form('view.php', array('format_data' =>$format_data, 'displaysection'=>$displaysection));

if ($display_form->is_cancelled()){
	redirect($course_cancel_link);
}
else if ($data = $display_form->get_data()) {
	$format_data->tree = $data->treecode;

	if (!$DB->update_record('format_menutopic', $format_data)){
	        notify (get_string('notsaved', 'format_menutopic'));
	}
	else {
		//ToDo: Delete html cache if exists
        notify (get_string('savecorrect', 'format_menutopic'));
	}
}

$display_form->display();

?>
<style>
	.ygtvlabel {
		cursor: pointer;
	}

	.input_edit_control {
		border: 1px solid #CCC;
		background-color: #FFF;
	}
	
	.select_topics{
		border: solid 1px #FFF;
	}
	
	.img_action {
		cursor: pointer;
		margin-right: 10px;
	}
	
	.yui3-widget-hd {
		font-weight: bold;
		cursor: move;
	}
	
</style>
<div id="tree_container"><!--El div debe estar antes del script para que pueda ser referenciado como contenedor del árbol --></div>
<script type="text/javascript" src="<?php echo $CFG->wwwroot; ?>/course/format/menutopic/lib.js"></script>
<script language="javascript" type="text/javascript">
	var _SHEETS = new Array();
	var _GLOBAL_VARS = new Array();
	
	YUI.namespace("tree_admin");
	
</script>
<div id="panel_container_editsheet" class="yui3-skin-sam">
    <div id="panel_edit_sheet">
        <div class="bd">
            <table cellpadding="0" cellspacing="0">
            	<tr>
                    <th><?php print_string('actions_sheet_sheetedit', 'format_menutopic'); ?></th>
                    <td>
                    	&nbsp;&nbsp;
                    	<img onclick="move_sheet_left()" id="btn_move_left_sheet" class="img_action" src="<?php echo $OUTPUT->pix_url('t/left');?>" alt="<?php print_string('actionleft_sheet_sheetedit', 'format_menutopic'); ?>" title="<?php print_string('actionleft_sheet_sheetedit', 'format_menutopic'); ?>" />
                    	<img onclick="move_sheet_right()" id="btn_move_right_sheet" class="img_action" src="<?php echo $OUTPUT->pix_url('t/right');?>" alt="<?php print_string('actionright_sheet_sheetedit', 'format_menutopic'); ?>" title="<?php print_string('actionright_sheet_sheetedit', 'format_menutopic'); ?>" />
                    	<img onclick="move_sheet_up()" id="btn_move_up_sheet" class="img_action" src="<?php echo $OUTPUT->pix_url('t/up');?>" alt="<?php print_string('actionup_sheet_sheetedit', 'format_menutopic'); ?>" title="<?php print_string('actionup_sheet_sheetedit', 'format_menutopic'); ?>" />
                    	<img onclick="move_sheet_down()" id="btn_move_down_sheet" class="img_action" src="<?php echo $OUTPUT->pix_url('t/down');?>" alt="<?php print_string('actiondown_sheet_sheetedit', 'format_menutopic'); ?>" title="<?php print_string('actiondown_sheet_sheetedit', 'format_menutopic'); ?>" />
                    	<img class="img_action" id="btn_delete_sheet" src="<?php echo $OUTPUT->pix_url('t/delete');?>" alt="<?php print_string('actiondelete_sheet_sheetedit', 'format_menutopic'); ?>" title="<?php print_string('actiondelete_sheet_sheetedit', 'format_menutopic'); ?>" onclick="if(confirm('<?php print_string('actiondeleteconfirm_sheet_sheetedit', 'format_menutopic'); ?>')){ delete_sheet();}" />
                    </td>
                </tr>
                <tr>
                    <th><?php print_string('name_sheet_sheetedit', 'format_menutopic'); ?></th>
                    <td><input id="name_text" size="20" class="input_edit_control" /></td>
                </tr>
                <tr>
                    <th><?php print_string('topic_sheet_sheetedit', 'format_menutopic'); ?></th>
                    <td>
                        <select id="select_topic" onchange="change_topic(this)">
                            <option></option>
                        <?php
                            for ($i = 0; $i < ($course->numsections + 1); $i++) {
                                echo '<option>' . $i . '</option>';
                            }
                        ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><?php print_string('url_sheet_sheetedit', 'format_menutopic'); ?></th>
                    <td><input id="url_text" size="40" class="input_edit_control" /></td>
                </tr>
                <tr>
                    <th><?php print_string('target_sheet_sheetedit', 'format_menutopic'); ?></th>
                    <td>
                        <select id="select_target">
                            <option></option>
                            <option value="_blank"><?php print_string('targetblank_sheet_sheetedit', 'format_menutopic'); ?></option>
                            <option value="_self"><?php print_string('targetself_sheet_sheetedit', 'format_menutopic'); ?></option>
                        </select>
                    </td>
                </tr>
	            <tr>
                	<td colspan="2">
                    	<input type="button" onclick="change_sheet()" value="<?php print_string('actionsave_sheet_sheetedit', 'format_menutopic'); ?>" />
                    	<input type="button" onclick="add_sheet_daughter()" value="<?php print_string('actionadd_sheet_daughter_sheetedit', 'format_menutopic'); ?>" />
                    	<input type="button" onclick="add_sheet_sister()" value="<?php print_string('actionadd_sheet_sister_sheetedit', 'format_menutopic'); ?>" />
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>