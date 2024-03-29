<?php
    include("../../conf/conn.php");
    include("../../conf/function.php");
    include("session.php");
    include("../../conf/my_project.php");

    $my_project_header_title = "Stock Receive Report Today";

    $my_notification = @$_GET['note'];

    $date_now = date("Y-m-d");

    if ($my_notification == "delete") {
        $the_note_status = "visible";
        $color_note = "success";
        $message = "Re-Stock Report is removed";
    }else if ($my_notification == "error") {
        $the_note_status = "visible";
        $color_note = "danger";
        $message = "Theres something wrong here";
    }else if ($my_notification == "empty_search") {
        $the_note_status = "visible";
        $color_note = "danger";
        $message = "Empty Date Input";
    }else if ($my_notification == "pin_out") {
        $the_note_status = "visible";
        $color_note = "warning";
        $message = "Incorrect PIN";
    }else{
        $the_note_status = "hidden";
        $color_note = "default";
        $message = "";
    }

    $query_one = "Select * From `gy_restock` Where `gy_restock_status`='1' AND date(`gy_restock_date`)='$date_now' Order By `gy_restock_date` DESC";

    $query_two = "Select COUNT(`gy_restock_id`) From `gy_restock` Where `gy_restock_status`='1' AND date(`gy_restock_date`)='$date_now' Order By `gy_restock_date` DESC";

    $query_three = "Select * From `gy_restock` Where `gy_restock_status`='1' AND date(`gy_restock_date`)='$date_now' Order By `gy_restock_date` DESC ";

    $my_num_rows = 50;

    include 'my_pagination.php';

    $count_results=$link->query($query_one)->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
    <?php include 'head.php'; ?>
<body>

    <div id="wrapper">

        <?php include 'nav.php'; ?>

        <!-- Modals -->
        <?php include('modal.php');?>
        <?php include('modal_password.php');?> 

        <div id="page-wrapper">

            <div class="row">
                <div class="col-lg-8">
                    <h3 class="page-header"><i class="fa fa-plus"></i> <?php echo $my_project_header_title; ?></h3>
                </div>
                <div class="col-lg-4">
                    <!-- notification here -->
                    <div class="alert alert-<?php echo @$color_note; ?> alert-dismissable" id="my_note" style="margin-top: 12px; visibility: <?php echo @$the_note_status; ?>">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <?php echo @$message; ?>.
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3">
                            <!-- Search Engine -->
                            <div class="form-group">
                                <form method="post" enctype="multipart/form-data" action="redirect_manager">
                                    <input type="text" class="form-control" placeholder="Search Code/Supplier ..." name="restock_entry_search" style="border-radius: 0px;" autofocus required>
                                </form>
                            </div>
                        </div>

                        <form method="post" enctype="multipart/form-data" id="my_form" action="redirect_manager">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="date" class="form-control" name="restock_date_search_f" id="restock_date_search1" style="border-radius: 0px;" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="date" class="form-control" name="restock_date_search_t" id="restock_date_search2" style="border-radius: 0px;" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" name="restock_btn" class="btn btn-success" title="click to search"><i class="fa fa-search"></i> Search</button>
                            </div>
                        </form>                      
                    </div>
                    
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Stock Receive Summary Data Table <b><?php echo 0+$count_results; ?></b> result(s) <a href="print_rsummary?datef=<?php echo date('Y-m-d'); ?>&datet=<?php echo date('Y-m-d'); ?>&mode=date_search" onclick="window.open(this.href, 'mywin',
'left=20,top=20,width=1366,height=768,toolbar=1,resizable=0'); return false;"><button type="button" class="btn btn-success" title="click to print result ..."><i class="fa fa-print"></i> Print</button></a>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th><center>Date</center></th>
                                            <th style="color: blue;"><center>Code</center></th>
                                            <th><center>Product Code</center></th>
                                            <th><center>Description</center></th>
                                            <!-- <th><center>Supp. Code</center></th> -->
                                            <th style="color: #8d5c00;"><center>Supplier</center></th>
                                            <th><center>Quantity</center></th>
                                            <th style="color: green;" colspan="2"><center>Price Change</center></th>
                                            <th><center>Note</center></th>
                                            <th><center>User</center></th>
                                            <th><center>Branch</center></th>
                                            <th><center>Void</center></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    <?php  
                                        //get products
                                        //make pagination
                                        while ($res_row=$query->fetch_array()) {

                                            //get user info
                                            $cashier_identifier=$res_row['gy_restock_by'];
                                            $get_user_info=$link->query("Select * From `gy_user` Where `gy_user_id`='$cashier_identifier'");
                                            $user_info_row=$get_user_info->fetch_array();

                                            //get product info
                                            $my_product_id=$res_row['gy_product_id'];
                                            $get_product_info=$link->query("Select * From `gy_products` Where `gy_product_id`='$my_product_id'");
                                            $product_row=$get_product_info->fetch_array();

                                            //check supplier
                                            if ($res_row['gy_supplier_code'] == 0) {
                                                $my_supp_code = "NONE";
                                                $my_supp_name = "NONE";
                                            }else{
                                                $my_supp_code = $res_row['gy_supplier_code'];
                                                $my_supp_name = $res_row['gy_supplier_name'];
                                            }

                                            //capital price difference color
                                            if ($res_row['gy_product_price_cap'] <= $res_row['gy_product_old_price']) {
                                                $my_price_color = "green";
                                            }else{
                                                $my_price_color = "red";
                                            }

                                            //srp price difference color
                                            if ($res_row['gy_product_price_srp'] <= $res_row['gy_product_old_srp']) {
                                                $my_srp_color = "green";
                                            }else{
                                                $my_srp_color = "red";
                                            }
                                    ?>

                                        <tr class="success">
                                            <td style="font-weight: bold;"><center><?php echo date("M d, Y g:i A", strtotime($res_row['gy_restock_date'])); ?></center></td>
                                            <td style="font-weight: bold; color: blue;"><center><?php echo $res_row['gy_restock_code']; ?></center></td>
                                            <td style="font-weight: bold;"><center><?php echo $product_row['gy_product_code']; ?></center></td>
                                            <td style="font-weight: bold;"><center><?php echo $res_row['gy_product_name']; ?></center></td>
                                            <!-- <td style="font-weight: bold;"><center><?php #echo $my_supp_code; ?></center></td> -->
                                            <td style="font-weight: bold;"><center><?php echo "<span style='color: #8d5c00;'>".$my_supp_name."</span>"; ?></center></td>
                                            <td style="font-weight: bold;"><center><?php echo "<span style='color: blue;'>".$res_row['gy_restock_quantity']."</span> ".$product_row['gy_product_unit']; ?></center></td>
                                            <td><center><?php echo "(".date("M/d/Y",strtotime($res_row['gy_old_price_date'])).") <b>".number_format($res_row['gy_product_old_price'],2)." - <span style='color: ".$my_price_color.";'>".number_format($res_row['gy_product_price_cap'],2)."</span></b>"; ?></center></td>
                                            <td><center><?php echo "SRP <br><b>".number_format($res_row['gy_product_old_srp'],2)." - <span style='color: ".$my_srp_color.";'>".number_format($res_row['gy_product_price_srp'],2)."</span></b>"; ?></center></td>
                                            <td><center><button type="button" class="btn btn-success" title="click to see view the note ..." data-target="#details_<?php echo $res_row['gy_restock_id']; ?>" data-toggle="modal"><i class="fa fa-list fa-fw"></i></button></center></td>
                                            <td style="font-weight: bold;"><center><?php echo $user_info_row['gy_full_name']; ?></center></td>
                                            <td style="font-weight: bold;"><center><?php echo get_branch_name($res_row['gy_branch_id']); ?></center></td>
                                            <td><center><button type="button" class="btn btn-danger" title="click to void the restock summary ..." data-target="#void_<?php echo $res_row['gy_restock_id']; ?>" data-toggle="modal"><i class="fa fa-trash-o fa-fw"></i></button></center></td>
                                        </tr>

                                        <!-- Transaction Details -->
                                        
                                        <div class="modal fade" id="details_<?php echo $res_row['gy_restock_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        <center><h4 class="modal-title" id="myModalLabel">NOTE</h4></center>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="panel-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="panel panel-success" style="border-radius: 0px;">
                                                                        <div class="panel-heading" style="border-radius: 0px;">
                                                                            Supplier Name: <b><?php echo $my_supp_name; ?></b>
                                                                        </div>
                                                                        <div class="panel-body">
                                                                            <p style="text-align: justify;">
                                                                                <?php echo $res_row['gy_restock_note']; ?>
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete -->

                                        <div class="modal fade" id="void_<?php echo $res_row['gy_restock_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <!-- <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button> -->
                                                        <h4 class="modal-title" id="myModalLabel"><i class="fa fa-trash-o fa-fw"></i> Void Stock Receive Data <small style="color: #337ab7;">(press TAB to type/press ENTER to process)</small></h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form method="post" enctype="multipart/form-data" action="void_restock_summ?cd=<?php echo $res_row['gy_restock_id']; ?>&sd=restock_reports">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label><i class="fa fa-lock fa-fw"></i> Delete Secure PIN</label>
                                                                        <input type="password" name="my_secure_pin" class="form-control" autofocus required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="text-center"> 
                         <ul class="pagination">
                            <?php echo $paginationCtrls; ?>
                         </ul>
                    </div>
                 </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- <script type="text/javascript">
        $('#restock_date_search').change(function(){
            console.log('Submiting form');                
            $('#my_form').submit();
        });
    </script> -->

</body>

</html>
