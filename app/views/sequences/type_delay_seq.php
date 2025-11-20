<?php require APPROOT . 'views/inc/header.php'; ?>
<link rel="stylesheet" type="text/css" href="<?php echo URLROOT; ?>css/add_seq.css">

<style type="text/css">
    .form-control
    {
        width: auto;
        display: initial!important;
    }
    .form-control.is-invalid
    {
        padding-right:inherit!important;
    }
    .is-invalid~.invalid-feedback
    {
        display: inline!important;
    }
</style>

<div class="container-ms">
    <div class="w3-text-white w3-center">
        <table class="no-border">
            <tr id="header">
                <td>
                    <h3>
                        <?php 
                            if ($data['mode'] == 'edit') {
                                echo $text['edit_seq'];
                            } else {
                                echo $text['new_seq']; 
                            }
                        ?>
                    </h3>
                </td>
            </tr>
        </table>
    </div>
    <div class="main-content">
        <div class="center-content">
            <div class="topnav">
                <label style="font-size:20px;color: #000; padding-left: 2%"><?php echo $text['job_id'];?> :</label>&nbsp;
                <input type="text" id="job_id" name="job_id" size="8" maxlength="20" value="<?php echo $data['job_id'];?>" disabled
                style="height:28px; font-size:20px;text-align: center; background-color: #DDDDDD; border:0; margin: 3px;">

                <label style="font-size:20px;color: #000; padding-left: 2%"><?php echo $text['seq_id'];?> :</label>&nbsp;
                <input type="text" id="seq_id" name="seq_id" size="8" maxlength="20" value="<?php echo $data['seq_id'];?>" disabled
                style="height:28px; font-size:20px;text-align: center; background-color: #DDDDDD; border:0; margin: 3px;">

                <label style="font-size:20px;color: #000; padding-left: 2%"><?php echo $text['seqtype'];?> :</label>&nbsp;
                <input type="hidden" name="seq_type_id" value="<?php echo $data['seq_type_id']; ?>">
                <input type="text" id="seq_type" name="seq_type" size="10" maxlength="20" value="<?php echo $text['sequence_type'][$data['seq_type_id']]; ?>" readonly
                style="height:28px; font-size:20px;text-align: center; background-color: #DDDDDD; border:0; margin: 3px;">
      
                <button id="back_btn" type="button" onclick="history.go(-1);"><?php echo $text['return']; ?></button>

                <?php if($data['mode'] == 'edit'){ ?>
                <div style="display: none;">
                    <input type="hidden" id="mode" value="<?php echo $data['mode']; ?>">
                    <input type="hidden" id="data_seq_name" value="<?php echo $data['seq_data']['SEQname']; ?>">
                    <input type="hidden" id="data_seq_repeat" value="<?php echo $data['seq_data']['seq_repeat']; ?>">
                    <input type="hidden" id="data_ok_seq" value="<?php echo $data['seq_data']['ok_seq']; ?>">
                    <input type="hidden" id="data_ok_stop" value="<?php echo $data['seq_data']['ok_stop']; ?>">
                    <input type="hidden" id="data_unscrew_count" value="<?php echo $data['seq_data']['unscrew_count']; ?>">
                    <input type="hidden" id="data_ng_stop" value="<?php echo $data['seq_data']['ng_stop']; ?>">
                    <input type="hidden" id="data_ng_unscrew" value="<?php echo $data['seq_data']['ng_unscrew']; ?>">
                    <input type="hidden" id="data_accu_angle" value="<?php echo $data['seq_data']['accu_angle']; ?>">
                    <input type="hidden" id="data_Thread_Calcu" value="<?php echo $data['seq_data']['Thread_Calcu']; ?>">
                    <input type="hidden" id="data_unscrew_mode" value="<?php echo $data['seq_data']['unscrew_mode']; ?>">
                    <input type="hidden" id="data_unscrew_force" value="<?php echo $data['seq_data']['unscrew_force']; ?>">
                    <input type="hidden" id="data_unscrew_rpm" value="<?php echo $data['seq_data']['unscrew_rpm']; ?>">
                    <input type="hidden" id="data_unscrew_dir" value="<?php echo $data['seq_data']['unscrew_dir']; ?>">
                    <input type="hidden" id="data_unscrew_torque_threshold" value="<?php echo $data['seq_data']['unscrew_torque_threshold']; ?>">
                    <input type="hidden" id="data_delay" value="<?php echo $data['seq_data']['delay']; ?>">

                    <!-- Thêm 2 field mới -->
                    <input type="hidden" id="data_dt_time" value="<?php echo isset($data['seq_data']['dt_time']) ? $data['seq_data']['dt_time'] : '0'; ?>">
                    <input type="hidden" id="data_tt_time" value="<?php echo isset($data['seq_data']['tt_time']) ? $data['seq_data']['tt_time'] : '0'; ?>">
                </div>
                <?php }else{ ?>
                <div style="display: none;">
                    <input type="" id="mode" value="<?php echo $data['mode']; ?>">
                </div>
                <?php } ?>
            </div>

            <div class="new-container">
                <div class="scrollbar" id="style-SeqType" style="margin-bottom: 20px;">
                    <div class="force-overflow">
                        <div style="background-color: #F2F1F1;">
                            <div class="row t2 mt-3" style=" align-items: center; ">
                                <div class="col-md-2 offset-md-4"><?php echo $text['sequence_name'];?> :</div>
                                <div class="col-md-3">
                                    <input id="seq_name" class="form-control"
                                        value="<?php echo isset($data['seq_data']['SEQname']) 
                                            ? $data['seq_data']['SEQname'] 
                                            : 'SEQ-' . $data['next_seq_id']; ?>">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>

                            <div class="row t2 mt-3" style=" align-items: center; ">
                                <div class="col-md-2 offset-md-4"><?php echo $text['Text_Delaytime'];?> (<?php echo $text['Second'];?>) :</div>
                                <input type="hidden" id="seq_type" value="<?php echo $type; ?>">
                                <div class="col-md-3">
                                    <input id="timeout" value="0" class="form-control" style="width: 20%;min-width: 50px;">
                                    <div class="invalid-feedback"></div> (0-60)
                                </div>
                            </div>
 
                        </div>
                    </div>
                </div>
            </div>
            <div class="w3-center" style="margin: 10px 0px; margin-bottom: 10px;">
                <button id="button1" class="button-save" onclick="save_sequence();"><?php echo $text['save']; ?></button>
            </div>
        </div>
    </div>

    <!-- hiển thị cửa sổ thông báo -->
	<div id="spinner" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
		<div class="spinner-border text-primary" role="status">
			<span class="sr-only"></span>
		</div>
	</div>	

</div>

<script>
	$(document).ready(function () {

        let mode = document.getElementById('mode').value;

        if(mode == 'new'){
            //帶入預設值
        }

        if(mode == 'edit'){
            //帶入資料
            document.getElementById("seq_name").value = document.getElementById("data_seq_name").value
            document.getElementById("timeout").value = document.getElementById("data_delay").value

        }

	});


    //監控img select 變化
    $('#img_list').on('change', function() {
        console.log( this.value );
        document.getElementById('select_img').src = '../public/img/' + this.value
    });


</script>

<?php if($_SESSION['privilege'] != 'admin'){ ?>
<script>
  $(document).ready(function () {
    disableAllButtonsAndInputs()
    document.getElementById("return").disabled = false;
  });
</script>
<?php } ?>


<?php require APPROOT . 'views/inc/footer.php'; ?>
<?php require APPROOT . 'views/sequences/seq_share.php'; ?>