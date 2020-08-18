<?php
/**
* @package OS CCK
* @copyright 2019 OrdaSoft.
* @author Andrey Kvasnevskiy(akbet@mail.ru),Roman Akoev (akoevroman@gmail.com)
* @link http://ordasoft.com/cck-content-construction-kit-for-joomla.html
* @description OrdaSoft Content Construction Kit
* @license GNU General Public license version 2 or later; 
*/

defined('_JEXEC') or die;
//getting all params

$layout_params = unserialize($layout->params);
$layout_params['unique_fields'] = ($layout->custom_fields)?unserialize($layout->custom_fields):array();

$rowsNames = array();
if(isset($layout_params['row_ids']) && !empty($layout_params['row_ids'])){
  $rowsIds= explode('|',$layout_params['row_ids']);
  foreach ($rowsIds as $value) {
    if(!empty($value)){
      $rowName = new stdClass();
      $rowName->field_name = 'row_'.$value;
      $rowsNames[] = $rowName;
    }
  }
}else{
  $layout_params['row_ids'] = '';
}

$columnNames = array();
if(isset($layout_params['column_ids']) && !empty($layout_params['column_ids'])){
  $columnIds = explode('|',$layout_params['column_ids']);
  foreach ($columnIds as $value) {
    if(!empty($value)){
      $columnName = new stdClass();
      $columnName->field_name = 'col_'.$value;
      $columnNames[] = $columnName;
    }
  }
}else{
  $layout_params['column_ids'] = '';
}
$original_select = '<div class="original_select">
            <br>
            SELECT ei.eiid FROM  #__os_cck_entity_instance AS ei <br>
            LEFT JOIN #__os_cck_categories_connect AS ccc ON ccc.fk_eiid=ei.eiid <br>
            LEFT JOIN #__os_cck_layout AS lay ON lay.lid=ei.fk_lid <br>
            LEFT JOIN #__os_cck_content_instances_price AS price ON price.fk_eiid=ei.eiid <br>
            LEFT JOIN `#__os_cck_content_entity_$entity_name` as instance ON instance.`fk_eiid` = ei.eiid <br>
            WHERE ei.published="1" <br>
            AND ei.approved="1" <br>
            AND lay.type="add_instance" <br>
            AND ccc.fk_cid={$catid} <br>
            AND ei.fk_eid="{$entity_id}"<br>
            <span class="your_sql_where">{Your "Sql Where" expression}</span>
        </div>';
//get unique max array key
$counter = (count($layout_params['unique_fields']))? max(array_keys($layout_params['unique_fields']))+1 : 1 ;
$fields_from_params = (isset($layout_params['fields']))?$layout_params['fields']:array();

$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root() . "components/com_os_cck/assets/css/editLayout.css");
//custom category field
$category_title = new stdClass();
$category_title->label = JText::_("COM_OS_CCK_CATEGORY_UNIQUE_FIELD_TITLE");
$category_title->db_field_name = 'category_title';
$category_title->field_type = 'category_title';
$category_title->description = 'Title for category';

$category_description = new stdClass();
$category_description->label = JText::_("COM_OS_CCK_CATEGORIES_DESCRIPTION");
$category_description->db_field_name = 'category_description';
$category_description->field_type = 'category_description';
$category_description->description = 'Description for category';

$category_map = new stdClass();
$category_map->label = JText::_("COM_OS_CCK_CATEGORY_UNIQUE_FIELD_MAP");
$category_map->db_field_name = 'category_map';
$category_map->field_type = 'instances_map';
$category_map->description = 'Map for category';

$category_image = new stdClass();
$category_image->label = JText::_("COM_OS_CCK_CATEGORY_UNIQUE_FIELD_CAT_IMAGE");
$category_image->db_field_name = 'category_image';
$category_image->field_type = 'category_image';
$category_image->description = 'Image for category';

$category_order_by = new stdClass();
$category_order_by->label = JText::_("COM_OS_CCK_CATEGORY_UNIQUE_FIELD_CAT_ORDERING");
$category_order_by->db_field_name = 'category_order_by';
$category_order_by->field_type = 'order_by';
$category_order_by->description = 'Category Order by block for category';

$category_pagination = new stdClass();
$category_pagination->label = JText::_("COM_OS_CCK_CATEGORY_UNIQUE_FIELD_PAGINATION");
$category_pagination->db_field_name = 'joom_pagination';
$category_pagination->field_type = 'pagination';
$category_pagination->description = 'Pagination block for this layout';

$category_alphabetical = new stdClass();
$category_alphabetical->label = JText::_("COM_OS_CCK_CATEGORY_UNIQUE_FIELD_ALPHABETICAL");
$category_alphabetical->db_field_name = 'joom_alphabetical';
$category_alphabetical->field_type = 'alphabetical';
$category_alphabetical->description = 'Alphabetical pagination block for this layout';

$category_instance_count = new stdClass();
$category_instance_count->label = JText::_("COM_OS_CCK_CATEGORY_INSTANCE_COUNT");
$category_instance_count->db_field_name = 'joom_instance_count';
$category_instance_count->field_type = 'instance_count';
$category_instance_count->description = 'Instance count in category';

$unique_fields = array($category_title,$category_description, $category_map,$category_image, $category_order_by,$category_pagination, $category_alphabetical, $category_instance_count);
//end

//custom field
$custom_code_field = new stdClass();
$custom_code_field->label = JText::_("COM_OS_CCK_CATEGORY_UNIQUE_CODE_FIELD");
$custom_code_field->db_field_name = 'custom_code_field';
$custom_code_field->description = '';
//end

?>
<div id="messages" class="cck-spiner">
<div class="spiner-bg"></div>
  <div class="sk-cube sk-cube1"></div>
  <div class="sk-cube sk-cube2"></div>
  <div class="sk-cube sk-cube3"></div>
  <div class="sk-cube sk-cube4"></div>
  <div class="sk-cube sk-cube5"></div>
  <div class="sk-cube sk-cube6"></div>
  <div class="sk-cube sk-cube7"></div>
  <div class="sk-cube sk-cube8"></div>
  <div class="sk-cube sk-cube9"></div>
<span id="result-message"></span></div>

<!-- START main drag&drop aria -->
<div class="container-fluid">

<!-- layout notice -->
<?php 
  noticeLinkToDocLayout('category_layout', 'Category layout', 'http://ordasoft.com/News/OS-CCK-Documentation/cck-term-explanation.html#Show category');
?>
<!-- /layout notice -->

  <!-- block for main body -->
  <div id="main-block" class="row">
      <!-- all fields block -->
      <div id="fields-block" class="col-lg-2 col-md-2 col-sm-4 col-xs-12">
        <div id="fields-block-title">
          <div class="fields-search"><input class="cck-main-field-search" type="text" placeholder="Search"></div>
          <div class="fields-title">Fields</div>
        </div>
        <!-- custom block for field -->
        <div class="field-block">
          <div>
            <!-- popover back/front title -->
            <span class="field-name <?php echo $custom_code_field->db_field_name.'-'.$counter.'-label-hidden';
                        echo  (isset($fields_from_params['showName_'.$custom_code_field->db_field_name.'_'.$counter]) )?
                               '':
                               ' hide-field-name-'.$custom_code_field->db_field_name.'_'.$counter;?>"
                data-field-name="<?php echo $custom_code_field->db_field_name.'_'.$counter; ?>">
                <?php echo $custom_code_field->label; ?>
            </span>
            <span class="col_box admin_aria_hidden">
              <?php echo '{|f-'.$custom_code_field->db_field_name.'_'.$counter.'|}'; ?>
            </span>
          </div>
        </div>
        <!--END custom block for field -->
        <?php
        foreach($unique_fields as $field) {?>
          <!-- block for field -->
          <div class="field-block">
            <div>
              <!-- popover back/front title -->
              <span class="field-name <?php echo $field->db_field_name.'-label-hidden';
                          echo  (isset($fields_from_params['showName_'.$field->db_field_name])
                                  || !$layout->lid)?
                                 '':
                                 ' hide-field-name-'.$field->db_field_name;?>"
                  data-field-name="<?php echo $field->db_field_name; ?>">
                  <?php if($field->db_field_name == "joom_alphabetical"){
                      echo $field->label; ?><i title="<?php echo JText::_("COM_OS_CCK_USER_INSTANCES_INFO_ALPHABETICAL")?>" class="glyphicon glyphicon-info-sign date_tooltip"></i>
                 <?php } else {
                     echo $field->label;
                 } ?>
              </span>
              <span class="col_box admin_aria_hidden">
                <?php echo '{|f-'.$field->db_field_name.'|}'; ?>
              </span>
              <input class="f-params" name="<?php echo 'fi_Params_'.$field->db_field_name ?>" type="hidden" value="{}">
            </div>
          </div>
          <!--END block for field -->
          <?php
        } ?>
        <!-- attached block -->
        <div id="attached-block">
          <div class="layout-title">Attach Layouts</div>
          <input id="add-layout" class="new-layout" type="button" aria-invalid="false" value="Add New">

          <div class="layout-title">Attach Modules</div>
          <input id="add-module" class="new-layout" type="button" aria-invalid="false" value="Add New">
        </div>
        <!-- End attached block -->
      </div>
      <!-- END all fields block -->

      <!-- editor main aria -->
      <div id="editor-block" class="col-lg-7 col-md-6 col-sm-8 col-xs-12">
        <div id="content-block">
          <?php echo $layout->html; ?>
        </div>
        <input class="form-params" name="form-params" type="hidden" value='<?php echo (isset($layout_params['form_params']))?$layout_params['form_params']:'{}';?>'>
        <input id="add-row" class="new-row" type="button" aria-invalid="false" value="New Row">
      </div>
      <!--END editor main aria -->

      <!-- block for options -->
      <div id="field_options" class="col-lg-3 col-md-4 col-sm-8 col-xs-12">
        <ul>
          <li><a href="#options-tab-1"><?php echo JText::_("COM_OS_CCK_EDIT_LAYOUT_FIELD_OPTION")?></a></li>
          <li><a href="#options-tab-2"><?php echo JText::_("COM_OS_CCK_EDIT_LAYOUT_BLOCK_OPTION")?></a></li>
          <li><a href="#options-tab-3"><?php echo JText::_("COM_OS_CCK_EDIT_LAYOUT_FORM_OPTION")?></a></li>
        </ul>
        <!-- START OPTIONS TABS -->
        <div id="options-tab-1">
          <div id="fields-options-accordion">
            <h3><?php echo JText::_("COM_OS_CCK_STYLING_LABEL_ACCORDION_FIELDS_MAIN_OPTIONS") ?></h3>
            <div class="main-fields-content main-fields-options">
              <?php
              foreach ($unique_fields as $field) {
                  //require getLayoutPathCCK::getAdminLayoutViewPath('com_os_cck', 'category_layout', 'fieldUniq');
                  require getAdminFiledSettingsViewPath('com_os_cck', 'unique', $field->field_type);
              }
              if(isset($layout_params['views']['show_request_layout'])){
                foreach ($layout_params['views']['show_request_layout'] as $key => $value) {
                  require getLayoutPathCCK::getAdminLayoutViewPath('com_os_cck', 'category_layout', 'layoutOptions');
                }
              }
              if(count($layout_params['unique_fields'])){
                foreach ($layout_params['unique_fields'] as $key => $custom_options){
                  //require getLayoutPathCCK::getAdminLayoutViewPath('com_os_cck', 'category_layout', 'customField');
                    require getAdminFiledSettingsViewPath('com_os_cck', 'unique', 'custom_code');
                }
              } ?>
            </div>
            <h3><?php echo JText::_("COM_OS_CCK_STYLING_LABEL_ACCORDION_FIELDS_CSS_OPTIONS") ?></h3>
            <div class="styling-field-content">
              <?php
              
                styling_options($layout,'f','block');
              ?>
            </div>
          </div>
        </div>
        <div id="options-tab-2">
          <div id="block-options-accordion">
            <h3 id="row-styling"><?php echo JText::_("COM_OS_CCK_STYLING_LABEL_ACCORDION_ROW_CSS_OPTIONS")?></h3>
            <div class="styling-row-content">
              <?php styling_options($layout,'row');?>
            </div>
            <h3 id="column-styling"><?php echo JText::_("COM_OS_CCK_STYLING_LABEL_ACCORDION_COLUMN_CSS_OPTIONS")?></h3>
            <div class="styling-column-content">
              <?php styling_options($layout,'col');?>
            </div>
          </div>
        </div>
        <div id="options-tab-3">
          <div id="form-options-accordion">

<!--             <h3><?php echo JText::_("COM_OS_CCK_LAYOUT_COUNT_INST_GRID") ?></h3>
            <div class="main-fields-content">

                main one 
              <div class="category-options g_instance_grid">
                <label><?php echo JText::_('COM_OS_CCK_LAYOUT_COUNT_INST_GRID'); ?></label>
                <?php  echo $layout->instance_grid;?>
              </div>
               main one 

                main two 
              <div class="category-options g_auto_custom">
               <label><?php echo JText::_('COM_OS_CCK_LAYOUT_CALCULATION_TYPE'); ?></label>
                <?php  echo $layout->auto_custom;?>
              </div>
               main two 
              <div class="g_auto">
                <div class="category-options">
                  <label><?php echo JText::_('COM_OS_CCK_LAYOUT_COUNT_INST_COLUNMS'); ?></label>
                  <?php  echo $layout->count_inst_columns;?>
                </div>
                
                <div class="category-options">
                  <label><?php echo JText::_('COM_OS_CCK_LAYOUT_MIN_WIDTH'); ?></label>
                  <?php  echo $layout->lay_min_width;?>
                </div>

                <div class="category-options">
                  <label><?php echo JText::_('COM_OS_CCK_LAYOUT_SPACE_BETWEEN'); ?></label>
                  <?php  echo $layout->space_between;?>
                </div>
              </div>

              <div class="category-options g_custom">
                <div class="category-options">
                  <label><?php echo JText::_('COM_OS_CCK_LAYOUT_RESOLUTION_ONE'); ?></label>
                  <span style="text-valign;middle;" ><?php  echo $layout->resolition_one;?></span>
                </div>

                <div class="category-options">
                  <label><?php echo JText::_('COM_OS_CCK_LAYOUT_RESOLUTION_TWO'); ?></label>
                  <?php  echo $layout->resolition_two;?>
                </div>

                <div class="category-options">
                  <label><?php echo JText::_('COM_OS_CCK_LAYOUT_RESOLUTION_THREE'); ?></label>
                  <?php  echo $layout->resolition_three;?>
                </div>

                 <div class="category-options">
                  <label><?php echo JText::_('COM_OS_CCK_LAYOUT_RESOLUTION_FOUR'); ?></label>
                  <?php  echo $layout->resolition_four;?>
                </div>
              </div>

            </div>-->

            <h3><?php echo JText::_("COM_OS_CCK_STYLING_LABEL_ACCORDION_FIELDS_MAIN_OPTIONS") ?></h3>
            <div class="main-fields-content">
              <div class="category-options">
                <label><?php echo JText::_('COM_OS_CCK_LAYOUT_TITLE'); ?></label>
                <?php  print_r($layout->layout_title);?>
              </div>
           
               <div class="category-options">
                <label><?php echo JText::_('COM_OS_CCK_SHOW_LAYOUT_TITLE'); ?></label>
                <?php  print_r($layout->show_layout_title);?>
              </div>
              
              
              <div class="category-options">
                <label><?php echo JText::_('COM_OS_CCK_LAYOUT_FEATURED'); ?> <i title="<?php echo JText::_("COM_OS_CCK_LAYOUT_FEATURED_TOOLTIP")?>" class="glyphicon glyphicon-info-sign date_tooltip"></i></label>
                <?php echo $layout->featured; ?>
              </div>

           <!--    <div class="category-options">
                <label><?php echo JText::_('COM_OS_CCK_LAYOUT_DESC'); ?></label>
                <?php echo $layout->cat_desc; ?>
              </div> -->
      <!--         <div class="category-options">
                <label><?php echo JText::_('COM_OS_CCK_LAYOUT_SORT_BY'); ?></label>
                <?php echo $layout->indexed; ?>
              </div>
              <div class="category-options">
                <label><?php echo JText::_('COM_OS_CCK_LAYOUT_ORDER_BY'); ?></label>
                <?php echo $layout->sortType; ?>
              </div>
              <div class="category-options">
                <label><?php echo JText::_('COM_OS_CCK_LAYOUT_ORDER_BY_FIELDS'); ?></label>
                <?php echo $layout->orderByFields; ?>
              </div> -->
              <div class="category-options">
                <label><?php echo JText::_("COM_OS_CCK_LAYOUT_INSTANCE_LIMIT"); ?> <i title="<?php echo JText::_("COM_OS_CCK_LAYOUT_INSTANCE_LIMIT_TOOLTIP")?>" class="glyphicon glyphicon-info-sign date_tooltip"></i></label>
                <input type="number" name="vi_limit" value="<?php echo $layout->limit ?>" />
              </div>
              <div class="category-options">
                <label><?php echo JText::_("COM_OS_CCK_LAYOUT_PAGE"); ?></label>
                <?php
                $pagenator = isset($layout_params['views'])?$layout_params['views']['pagenator_limit']:'';
                ?>
                <input type="text" name="vi_pagenator_limit"
                       value="<?php echo ($pagenator) ? $pagenator : "10"; ?>">
              </div>
<!--              <div class="category-options">
                <label><?php echo JText::_("COM_OS_CCK_LABEL_LINK_FIELD"); ?> <i title="<?php echo JText::_("COM_OS_CCK_LABEL_LINK_FIELD_TOOLTIP")?>" class="glyphicon glyphicon-info-sign date_tooltip"></i></label>
                <?php echo $layout->link_field; ?>
              </div>
              <div class="category-options">
                <label><?php echo JText::_('COM_OS_CCK_HEADER_ITEM_LAYOUT'); ?>:</label>
                <?php echo $layout->instanceLayout; ?>
              </div>-->
      
              <div class="category-options subcat" id="subcat">
                <label><?php echo JText::_('COM_OS_CCK_LAYOUT_SUB_CAT'); ?></label>
                <?php echo $layout->sub_category_level; ?>
              </div>
              <div class="category-options" id="subcat_lay">
                <label><?php echo JText::_('COM_OS_CCK_HEADER_SUBCATEGORIES_LAYOUT'); ?>:</label>
                <?php echo $layout->subcategoriesLayout; ?>
              </div>
              <div class="category-options" id="subcat_fild_link">
                <label><?php echo JText::_("COM_OS_CCK_LABEL_LINK_FIELD_CATEGORY"); ?></label>
                <?php echo $layout->subcat_link_field; ?>
              </div>
            </div>
            <h3><?php echo JText::_("COM_OS_CCK_STYLING_LABEL_ACCORDION_FIELDS_CSS_OPTIONS") ?></h3>
            <div class="styling-field-content">
              <?php styling_options($layout,'form');?>
            </div>
          </div>
        <!-- END options tabs -->
      </div>
  </div>
</div>
<!-- END main drag-drop aria -->


<!-- ADD MODALS -->
<?php
  require getLayoutPathCCK::getAdminLayoutViewPath('com_os_cck', 'modal_snippets', 'editor-modal');
  require getLayoutPathCCK::getAdminLayoutViewPath('com_os_cck', 'modal_snippets', 'layout-modal');
  require getLayoutPathCCK::getAdminLayoutViewPath('com_os_cck', 'modal_snippets', 'attached-layout-modal');
  require getLayoutPathCCK::getAdminLayoutViewPath('com_os_cck', 'modal_snippets', 'attached-module-modal');
  require getLayoutPathCCK::getAdminLayoutViewPath('com_os_cck', 'modal_snippets', 'field-php-show-modal');
  require getLayoutPathCCK::getAdminLayoutViewPath('com_os_cck', 'modal_snippets', 'field-sql-show-modal');
  require getLayoutPathCCK::getAdminLayoutViewPath('com_os_cck', 'modal_snippets', 'font-awesom-modal');
?>
<!-- ADD MODALS -->


<!-- hidden option block -->
<input type="hidden" name="vi_instance_type" value="add"/>
<input id="lid" type="hidden" name="lid" value="<?php echo $layout->lid; ?>"/>
<input id="attached_module_ids" type="hidden" name="attached_module_ids"
      value="<?php echo (isset($layout_params['attachedModuleIds']))?$layout_params['attachedModuleIds'] : '' ;?>">
<input id="row_ids" type="hidden" name="row_ids" value="<?php echo $layout_params['row_ids']; ?>">
<input id="column_ids" type="hidden" name="column_ids" value="<?php echo $layout_params['column_ids']; ?>">
<!-- end hidden option -->
<?php
require_once(JPATH_SITE . "/administrator/components/com_os_cck/views/editLayoutFunctions.php");
?>
<!-- script for drop joomla menu and buttons sidebar -->
<script type="text/javascript">
  //counters block
  var cust_count = <?php echo $counter?>;
//end
  //fn drop for rows
  function make_droppable(){
    jQuerOs(".drop-area").droppable({
      activeClass: "activeDroppable",
      accept: ".field-block, .attached-layout-block, .attached-module-block",
      drop: function( event, ui ) {
        var draggable = ui.draggable;
        draggable.find("div:first-child").addClass("drop-item");
        if(draggable.hasClass("field-block")){
          //field block
          delete_button = '<span class="delete-field"></span>';
          inform_button = '<span class="f-inform-button"></span>';
        }else if(draggable.hasClass("attached-module-block")){
          inform_button = '<span class="m-inform-button"></span>';
          delete_button = '<span class="delete-module"></span>';
        }else{
          //attached layout block
          inform_button = '<span class="l-inform-button"></span>';
          delete_button = '<span class="delete-layout"></span>';
        }
        draggable.find(".drop-item").prepend(inform_button);
        draggable.find(".drop-item").append(delete_button);
        jQuerOs(this).append(draggable.html());
        if(draggable.hasClass("field-block")){
          //field block
          
          del_field();
          hide_options();
          makeOptions();
          jQuerOs("#field_options div[id^='options-field-']").hide();
          jQuerOs("#field_options #options-field-"+draggable.find(".field-name").data("field-name")).show();
        }else if(draggable.hasClass("attached-module-block")){
          
          hide_options();
          makeOptions();
          del_module();
          addHiddenModuleIds(draggable.find(".module-name").data("field-name"));//(modId)
        }else{
          //attached layout block
          
          addOptionForLayout(draggable.find(".layout-name").data("field-name"));
          del_layout();
          hide_options();
          makeOptions();
          make_showHideTitle();
          jQuerOs("#field_options div[id^='options-field-']").hide();
          jQuerOs("#field_options #options-field-"+draggable.find(".layout-name").data("field-name")).show();
        }
        if(draggable.find(".field-name").length
            && draggable.find(".field-name").data("field-name").indexOf("custom_code_field_") >= 0){
          //if we drag custom field
          //start block with cutom_field_counter//we change field data-name,class and inner hidden text
          cust_count++;
          //delete current class
          draggable.find(".field-name").removeClass("hide-field-name-"+jQuerOs(this).find(".field-name").data("field-name"));
          //delete previos class
          draggable.find(".field-name").removeClass("hide-field-name-custom_code_field_"+(cust_count-1));
          draggable.find(".field-name").removeClass("custom_code_field-"+(cust_count-1)+"-label-hidden");
          //add new class
          draggable.find(".field-name").addClass("hide-field-name-custom_code_field_"+cust_count);
          draggable.find(".field-name").addClass("custom_code_field-"+(cust_count)+"-label-hidden");
          //change curent data-field-name
          draggable.find(".field-name").attr("data-field-name","custom_code_field_"+(cust_count));
          //change current hfield hidden
          draggable.find(".admin_aria_hidden").text("{|f-custom_code_field_"+cust_count+"|}");
          //remove x buttons
          draggable.find(".f-inform-button").remove();
          draggable.find(".delete-field").remove();
          //end
          //start dinamic create unique option
          add_unique_option(cust_count);
          make_showHideTitle();
          hide_options();
          makeOptions();
          jQuerOs("#field_options #options-field-custom_code_field_"+(cust_count-1)).show();
          //end
        }else if(draggable.find(".field-name").length
            && draggable.find(".field-name").data("field-name") == "joom_pagination"
            || draggable.find(".field-name").data("field-name") == "joom_alphabetical"){

           //field block

          del_field();
          hide_options();
          makeOptions();
          jQuerOs("#field_options div[id^='options-field-']").hide();
          jQuerOs("#field_options #options-field-"+draggable.find(".field-name").data("field-name")).show();
          //remove x buttons
          draggable.find(".f-inform-button").remove();
          draggable.find(".delete-field").remove();
          draggable.find("div:first-child").removeClass("drop-item")
        }else{
          //remove dragable field from field block
          draggable.remove();
        }
      }
    });
  }
//end

// --------------------------------------------------READY BLOCK START-----------------------------\\
  jQuerOs( document ).ready(function() {
    //make fun-s
    return_span_shell();
    make_add_row();
    make_attached_layout();
    make_attached_module();
    make_popover();
    make_remove_joomla_bars();
    make_synchronize_fields();
    make_synchronize_layout();
    make_field_mask_php_show();
    make_field_mask_sql_show();
    make_font_awesom_modal();
    //resizable
    jQuerOs('[id^=cck_col-]').addClass('resizable');
    jQuerOs('.ui-resizable-handle').remove();
    make_resize_grid();
    //resizable
    make_sortable();
    make_droppable();
    makeTabs();
    make_draggable();
    make_showHideTitle();
    del_field();
    del_module();
    makeOptions();
    makeDeleteRow();
    del_layout();
    make_accordion();
    make_colorpicker();
    make_editor();
    makeSearchInFields();
    jQuerOs("#messages").removeClass('cck-spiner');

//    function grid_panel(){
//      jQuerOs('.g_auto, .g_custom, .g_auto_custom').hide();
//      if(jQuerOs('.g_instance_grid select').val() == 1){
//        jQuerOs('.g_auto, .g_custom, .g_auto_custom').show();
//
//        if(jQuerOs('.g_auto_custom select').val() == 'auto'){
//          jQuerOs('.g_auto').show()
//          jQuerOs('.g_custom').hide()
//        }else{
//          jQuerOs('.g_custom').show()
//          jQuerOs('.g_auto').hide()
//        }
//      }  
//      return;
//    }
//
//    grid_panel();
//      
//    jQuerOs('.g_instance_grid select, .g_auto_custom select').change(function(event) {
//      grid_panel();
//    });
    
    function subcat_panel(){
      jQuerOs('#subcat_lay, #subcat_fild_link').hide();
      if(jQuerOs('#vi_sub_category_level').val() == 1){
        jQuerOs('#subcat_lay, #subcat_fild_link').show();

      }  
      return;
    }

    subcat_panel();
      
    jQuerOs('#vi_sub_category_level').change(function(event) {
      subcat_panel();
    });
//end
  });
// --------------------------------------------------READY BLOCK END-----------------------------\\
</script>
