<?php $this->load->view(config_item('admin_directory').'/header');?>
<!-- BEGIN PAGE LEVEL STYLES -->
<link rel="stylesheet" href="<?php echo config_item('admin_assets_url');?>plugins/data-tables/DT_bootstrap.css" />
<link rel="stylesheet" type="text/css" href="<?php echo config_item('admin_assets_url');?>plugins/bootstrap-datepicker/css/datepicker.css"/>
<!-- END PAGE LEVEL STYLES -->
<div class="page-container">
    <!-- BEGIN sidebar -->
<?php $this->load->view(config_item('admin_directory').'/sidebar');?>
    <!-- END sidebar -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <!-- BEGIN PAGE header-->
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN PAGE TITLE & BREADCRUMB-->
                    <h3 class="page-title">
                    Track User Event <small>(for user: <?php echo $userDetail->first_name." ".$userDetail->last_name." - ".$userDetail->email;?>)</small>
                    </h3>
                    <ul class="page-breadcrumb breadcrumb">
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?php echo config_item('admin_base_url');?>">
                            Home </a>
                            <i class="fa fa-angle-right"></i>
                        </li>
                        <li>
                            Track User Event
                        </li>
                    </ul>
                    <!-- END PAGE TITLE & BREADCRUMB-->
                </div>
            </div>            
            <!-- END PAGE header-->            
            <div class="row">
                <div class="col-md-12">
                    <!-- BEGIN EXAMPLE TABLE PORTLET-->
                    <div class="portlet box red">
                        <div class="portlet-title">
                            <div class="caption">Screen tracking of User <?php echo $userDetail->first_name." ".$userDetail->last_name." - ".$userDetail->email;?></div>
                        </div>
                        <div class="portlet-body">
                            <div class="table-container">
                                <table class="table table-striped table-bordered table-hover" id="datatable_ajax">
                                        <thead>
                                        <tr role="row" class="heading">
                                            <th class="table-checkbox">#</th>
                                            <th>Screen Name</th>
                                            <th>Tracking Date</th>
                                            <th>Event Date</th>
                                            <th>Action</th>
                                        </tr>
                                        <tr role="row" class="filter">
                                            <td></td>                                       
                                            <td>
                                                <select name="screen_types_search" class="form-control form-filter input-sm">
                                                    <option value="">Select Screen</option>
                                                    <?php
                                                    foreach (getScreenName() as $not_key => $not_val) 
                                                    {?>
                                                        <option value="<?php echo $not_key?>"><?php echo $not_val;?></option>
                                                    <?php } ?>
                                                </select>                                                
                                            </td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="event_date_search" id="tracking_date_search" readonly></td>
                                            <td><input type="text" class="form-control form-filter input-sm" name="event_date_search" id="event_date_search" readonly></td>
                                            <td>
                                                <div class="margin-bottom-5">
                                                    <button class="btn btn-sm  danger-btn filter-submit margin-bottom"><i class="fa fa-search"></i> Search</button>
                                                </div>
                                                <button class="btn btn-sm danger-btn filter-cancel"><i class="fa fa-times"></i> Reset</button>
                                            </td>
                                        </tr>
                                        </thead>                                        
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                    <!-- END EXAMPLE TABLE PORTLET-->
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
</div>
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/data-tables/jquery.dataTables.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/data-tables/DT_bootstrap.js"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="<?php echo config_item('admin_assets_url');?>scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>scripts/layout.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>scripts/datatable.js"></script>
<script type="text/javascript" src="<?php echo config_item('admin_assets_url');?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script>
var grid;
jQuery(document).ready(function() {           
    Layout.init(); // init current layout    
    grid = new Datatable();
    grid.init({
        src: $("#datatable_ajax"),
        onSuccess: function(grid) {
            // execute some code after table records loaded
        },
        onError: function(grid) {
            // execute some code on network or other general error  
        },
        dataTable: {  // here you can define a typical datatable settings from http://datatables.net/usage/options 
            /* 
                By default the ajax datatable's layout is horizontally scrollable and this can cause an issue of dropdown menu is used in the table rows which.
                Use below "sDom" value for the datatable layout if you want to have a dropdown menu for each row in the datatable. But this disables the horizontal scroll. 
            */
            "sDom" : "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", 
           "aoColumns": [
                { "bSortable": false },                
                null,
                null,
                null,
                { "bSortable": false }
              ],
            "sPaginationType": "bootstrap_full_number",
            "oLanguage": {  // language settings
                "sProcessing": '<img src="<?php echo base_url();?>assets/backend/img/loading-spinner-grey.gif"/><span>&nbsp;&nbsp;Loading...</span>',
                "sLengthMenu": "_MENU_ records",
                "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                "sInfoEmpty": "No records found to show",
                "sGroupActions": "_TOTAL_ records selected:  ",
                "sAjaxRequestGeneralError": "Could not complete request. Please check your internet connection",
                "sEmptyTable":  "No data available in table",
                "sZeroRecords": "No matching records found",
                "oPaginate": {
                    "sPrevious": "Prev",
                    "sNext": "Next",
                    "sPage": "Page",
                    "sPageOf": "of"
                }
            },
            "bServerSide": true, // server side processing
            "sAjaxSource": BASEURL+"user_event/ajaxview/"+'<?php echo $userDetail->user_id;?>', // ajax source
            "aaSorting": [[ 3, "desc" ]] // set first column as a default sort by asc
        }
    });            
    $('#datatable_ajax_filter').addClass('hide');
    $('input.form-filter, select.form-filter').keydown(function(e) 
    {
        if (e.keyCode == 13) 
        {
            grid.addAjaxParam($(this).attr("name"), $(this).val());
            grid.getDataTable().fnDraw(); 
        }
    });
    $('#event_date_search').datepicker({               
            autoclose:true,
            format: 'yyyy-mm-dd'
    });    

});
</script>
<?php $this->load->view(config_item('admin_directory').'/footer');?>