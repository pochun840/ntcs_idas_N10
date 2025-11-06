<link rel="stylesheet" type="text/css" href="<?php echo URLROOT; ?>css/add_seq_m.css">

<style type="text/css">
    .form-control
    {
        width: auto!important;
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
                <input type="text" id="job_id" name="job_id" size="5" maxlength="20" value="<?php echo $data['job_id'];?>" readonly
                style="height:28px; font-size:18px;text-align: center; background-color: #DDDDDD; border:0; margin: 3px;">

                <label style="font-size:20px;color: #000; padding-left: 2%"><?php echo $text['seq_id'];?> :</label>&nbsp;
                <input type="text" id="seq_id" name="seq_id" size="5" maxlength="20" value="<?php echo $data['seq_id'];?>" readonly
                style="height:28px; font-size:18px;text-align: center; background-color: #DDDDDD; border:0; margin: 3px;">

                <label style="font-size:20px;color: #000; padding-left: 2%"><?php echo $text['seq_type'];?> :</label>&nbsp;
                <input type="hidden" name="seq_type_id" value="<?php echo $data['seq_type_id']; ?>">
                <input type="text" id="seq_type" name="seq_type" size="10" maxlength="20" value="<?php echo $text['sequence_type'][$data['seq_type_id']]; ?>" readonly
                style="height:28px; font-size:18px;text-align: center; background-color: #DDDDDD; border:0; margin: 3px;">
      
                <button id="back_btn" type="button" onclick="history.go(-1);"><?php echo $text['return']; ?></button>

                <?php if ($data['mode'] == 'edit') { ?>
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
                        <input type="hidden" id="data_image" value="<?php echo $data['seq_data']['image']; ?>">

                        <!-- Thêm hidden mới cho text_message -->
                        <input type="hidden" id="data_text_message" value="<?php echo htmlspecialchars($data['seq_data']['message']); ?>">

                        <!-- Thêm 2 field mới -->
                        <input type="hidden" id="data_dt_time" value="<?php echo isset($data['seq_data']['dt_time']) ? $data['seq_data']['dt_time'] : '0'; ?>">
                        <input type="hidden" id="data_tt_time" value="<?php echo isset($data['seq_data']['tt_time']) ? $data['seq_data']['tt_time'] : '0'; ?>">
                    </div>
                <?php } else { ?>
                    <div style="display: none;">
                        <input type="hidden" id="mode" value="<?php echo $data['mode']; ?>">
                    </div>
                <?php } ?>

            </div>

            <div class="new-container">
                <div class="scrollbar" id="style-SeqType">
                    <div class="force-overflow">
                        <div style="background-color: #F2F1F1;">
                            
                            <div class="row t1">
                                    <div class="col-4 t1"><?php echo $text['seq_name'];?>:</div>
                                    <div class="col t2">
                                        <input id="seq_name" class="form-control"
                                            value="<?php echo isset($data['seq_data']['SEQname']) 
                                                ? $data['seq_data']['SEQname'] 
                                                : 'SEQ-' . $data['next_seq_id']; ?>">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                </div>
                                
                                <div class="row t1">
                                    <div class="col-4 t1"><?php echo $text['Timeout'];?> (<?php echo $text['Second'];?>):</div>
                                    <div class="col t2">
                                        <input id="timeout" value="0" class="form-control">
                                        <div class="invalid-feedback"></div> (0-60)
                                    </div>
                                </div>
                                <div class="row t1">
                                    <div class="col-4 t1"><?php echo $text['text_message'];?>:</div>
                                    <div class="col t2 form-group">
                                        <textarea id="text_message" name="text_message" rows="1" maxlength="30" placeholder=""><?php echo $seq_data['message'] ?? ''; ?></textarea>
                                        <div class="note"></div>
                                    </div>
                                </div>
                                <div class="row t1">
                                    <div class="col-4 t1"><?php echo $text['imgText_message'];?>:</div>
                                    <div class="col t2">
                                        <select id="img_list" class="form-select" style="font-size: 17px;width: 190px;">
                                            <option value="-">-</option>
                                            <?php foreach ($data['img_list'] as $key => $value) {
                                                if (!in_array($value,array(".",".."))){ //排除.跟..
                                                    echo '<option value="'.$value.'" >'.$value.'</option>';
                                                }
                                            } ?>
                                        </select>
                                    </div>
                                </div>
                                
                            
                            <div class="row t1">
                                <div class="col t2" style="border: solid;border-color: #d3d3d3;display: flex;align-items: center;border-radius: 10px;justify-content: center;">
                                    <!-- <div class="col-4 fw-bolder">Reverse</div> -->
                                    <img id="select_img" src="" style="height: auto;width:auto;border: none;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer">
                <div class="w3-center" style="margin: 10px 0px; margin-bottom: 10px;">
                    <button id="button1" class="button-save" onclick="save_sequence();"><?php echo $text['save']; ?></button>
                </div>
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

        if (mode == 'new') {
            // Đặt giá trị mặc định nếu cần
        }

        if (mode == 'edit') {
            // Gán giá trị từ hidden field vào form
            document.getElementById("seq_name").value = document.getElementById("data_seq_name").value;
            document.getElementById("img_list").value = document.getElementById("data_image").value;
            document.getElementById("timeout").value = document.getElementById("data_delay").value;

            // Gán text message đầy đủ (nhiều dòng)
            document.getElementById("text_message").value = document.getElementById("data_text_message").value;
        }

        // Kích hoạt sự kiện change để update ảnh xem trước (nếu có)
        const change_event = new Event("change");
        document.querySelector('select[id="img_list"]').dispatchEvent(change_event);

        // Kích hoạt select2 cho dropdown hình ảnh
        $('#img_list').select2();
    });

    document.addEventListener('DOMContentLoaded', function() {
        let msg = document.getElementById('data_text_message')?.value || '';
        document.getElementById('text_message').value = msg;
    });


    //監控img select 變化 - Jiānkòng img select biànhuà
    $('#img_list').on('change', function() {
        console.log( this.value );
        document.getElementById('select_img').src = '../public/img/' + this.value
    });

    function input_check(argument) {

        let conditions = [
            { id: 'seq_name', pattern: /^[a-zA-Z0-9\u4E00-\u9FA5\-]+$/, min: null, max: null },
            { id: 'timeout', pattern: /^\d{0,4}$/, min: 0, max: 60 },
        ];

        let isFormValid = true;
        conditions.forEach(function(input) {
            var element = document.getElementById(input.id);
            var value = element.value.trim();

            if(input.id != 'seq_name'){
                element.nextElementSibling.innerHTML = input.min+' ~ '+input.max;
            }

            if (value === "") {
                element.classList.add("is-invalid");
                isFormValid = false;
            } else if (!input.pattern.test(value)) {
                // element.value = "";
                element.classList.add("is-invalid");
                isFormValid = false;
            } else if (input.min !== null && parseFloat(value) < input.min) {
                element.classList.add("is-invalid");
                isFormValid = false;
            } else if (input.max !== null && parseFloat(value) > input.max) {
                element.classList.add("is-invalid");
                isFormValid = false;
            } else {
                element.classList.remove("is-invalid");
            }

        });

        console.log(conditions)

        return isFormValid;

    }


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