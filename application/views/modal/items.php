
<!-- Main content -->

<div class="row">


<div class="col-12">
<div class="card-body">
    <input type="hidden" id="order_id" value="<?= $order_id; ?>">
    <input type="hidden" id="sj_id" value="<?= $id; ?>">

            <table id="table2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <td>Product Name</td>
                        <td>Qty Ordered</td>
                        <td>Qty SJ</td>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach($items as $t) {
                        $detail_id = $t->id;
                        $sj_detail_id = $t->sj_detail_id;
                        $sj_detail_item_id = $t->sj_detail_item_id;
                        if(empty($sj_detail_item_id)) {
                            $sj_detail_item_id = 0;
                        }
                        $readonly = "";
                        if($record->status_sj == 6 || $record->status_sj == 7) {
                            $readonly = "readonly";
                        }
                        echo "<tr>
                            <td>$t->product_name</td>
                            <td>$t->ordered_qty</td>
                            <td><input type='number' max='$t->ordered_qty' $readonly onchange='update_qty($order_id,$sj_detail_id,$detail_id,$sj_detail_item_id,this.value);' value='$t->delivered_qty' class='form-control'></td>
                        </tr>";
                    }
                    ?>               
                </tbody>
            </table>

            <div class="col-12 mb-2">
                <?php
                if($record->status_sj != 6 && $record->status_sj != 7) {
                    echo '<input type="button" value="Deliver Now" id="btn-submit" class="btn btn-success float-right">';
                    ?>
                <script>
                    
                $("#btn-submit").click(function() {
                    //location.reload();
                var order_id = $("#order_id").val();
                var sj_id = $("#sj_id").val();
                $.ajax({
                        type:'POST',
                        data:{order_id:order_id,sj_id:sj_id},
                        url:"<?= base_url('api/cms/update_status_sj'); ?>",
                        success:function(data) {
                            var message = data.message;
                            //console.log(order_id+" "+sj_id);
                            
                            if(data.status == 1) {
                                toastr.success('Berhasil di perbaharui');
                                location.reload();
                            
                            } else {
                                toastr.error(message);

                                $("#btn-submit").html('Deliver Now');
                                $('#btn-submit').removeAttr('disabled');
                            }
                            
                            
                            
                        }
                });
                });
                </script>
                <?php
                }
                if($record->status_sj == 6) {
                    echo '<input type="button" value="Delivered" id="btn-submit" class="btn btn-success float-right">';
                ?>
                <script>
                    $("#btn-submit").click(function() {
                        //location.reload();
                    var order_id = $("#order_id").val();
                    var sj_id = $("#sj_id").val();
                    $.ajax({
                            type:'POST',
                            data:{order_id:order_id,sj_id:sj_id,status_order:7},
                            url:"<?= base_url('api/cms/update_status_sj'); ?>",
                            success:function(data) {
                                var message = data.message;
                                //console.log(order_id+" "+sj_id);
                                
                                if(data.status == 1) {
                                    toastr.success('Berhasil di perbaharui');
                                    location.reload();
                                
                                } else {
                                    toastr.error(message);
                                    
                                    $("#btn-submit").html('Delivered');
                                    $('#btn-submit').removeAttr('disabled');
                                }
                                
                                
                                
                            }
                    });
                    });
                </script>

                <?php
                }
                ?>
            </div>

        </div>
</div>


</div>

<style>
.dropzone {
    border: 2px dashed #0087F7;
}
</style>


<script type="text/javascript">


$("#modal-close3").click(function() {
    location.reload();
});


function update_qty(order_id,sj_detail_id,detail_id,sj_detail_item_id,qty) {
  $.ajax({
        type:'POST',
        data:{order_id:order_id,sj_detail_id:sj_detail_id,detail_id:detail_id,sj_detail_item_id:sj_detail_item_id,qty:qty},
        url:"<?= base_url('api/cms/update_qty_sj'); ?>",
        success:function(data) {
            var message = data.message;
            //console.log("haii");
            
            if(data.status == 1) {
                toastr.success('Berhasil di perbaharui');
            } else {
                toastr.error(message);
                
            }
            
            
            
        }
  });
}


</script>