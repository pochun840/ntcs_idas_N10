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
    .form-check-inline {
        align-items: center;
    }

    .form-check-inline img {
        height: 24px; /* tùy chỉnh kích thước hình ảnh nếu cần */
        margin-left: 5px; /* tạo khoảng cách giữa radio và ảnh */
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
                    <input type="hidden" id="data_input_pin_no" value="<?php echo $data['seq_data']['input_pin_no']; ?>">
                    <input type="hidden" id="data_wave" value="<?php echo $data['seq_data']['wave']; ?>">

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
                <div style="background-color: #F2F1F1; height: calc(100vh - 200px);">
                    <!-- Gói cả SEQ NAME + TABLE trong một div có max-width -->
                    <div class="mx-auto" style="max-width: 600px; width: 100%; margin-bottom: 20px;">
                        <!-- Sequence Name -->
                        <div class="row align-items-center mb-1" style="padding-left: 5%; padding-top: 2%;">
                            <div class="col-4 text-end t1"><?php echo $text['sequence_name'];?> :</div>
                            <div class="col-8">
                                    <input id="seq_name" class="form-control"
                                        value="<?php echo isset($data['seq_data']['SEQname']) 
                                            ? $data['seq_data']['SEQname'] 
                                            : 'SEQ-' . $data['next_seq_id']; ?>">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>

                        <hr class="hr">

                        <!-- Table -->
                        <div class="scrollbar-input-pin" id="style-input-pin">
                            <div class="force-overflow-input-pin">
                                <table id="InputNew_Modal" class="table w3-table text-center mb-4" style="background-color: #FFFFFF">
                                    <thead id="header-table" style="background-color:rgb(218, 235, 248)">
                                        <tr>
                                            <th><?php echo $text['column_no'];?></th>
                                            <th><?php echo $text['event'];?></th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <?php
                                            for ($i = 2; $i <= 12; $i++) {
                                                echo '<tr>';
                                                $found = false;

                                                foreach ($data['job_inputs'] as $value) {
                                                    if ($value['input_pin_no'] == $i) {
                                                        echo '<td><span style="width: 35px; display: inline-block;">' . $i . '</span>
                                                            <input id="input_' . $i . '" name="input_check" type="checkbox" class="form-check-input" value="' . $i . '" checked>
                                                            </td>';
                                                        echo '<td>' . $value['event_id'] . '</td>';
                                                        $found = true;
                                                        break;
                                                    }
                                                }

                                                if (!$found) {
                                                    echo '<td><span style="width: 35px; display: inline-block;">' . $i . '</span>
                                                        <input id="input_' . $i . '" name="input_check" type="checkbox" class="form-check-input" value="' . $i . '">
                                                        </td>';
                                                    echo '<td>Logical in</td>';
                                                }

                                                echo '</tr>';
                                            }
                                        ?>
                                    </tbody>
                                </table>
                                                            
                                <!-- Input Setting -->
                                <div class="row align-items-center" style="padding-left: 5%;">
                                    <label for="ok_job" class="col-3 mb-3"><?php echo $text['Input_Setting'];?> :</label>
                                    <div class="col mb-3">
                                        <div class="form-check form-check-inline">
                                            <input type="hidden" id="wave" name="wave" value="">
                                            <input id="wave_high" class="zoom form-check-input" type="radio" name="wave" value="1" checked>
                                            <label for="wave_high"><img src="../public/img/signal01.png" style="max-width: 80px;"></label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input id="wave_low" class="zoom form-check-input" type="radio" name="wave" value="2">
                                            <label for="wave_low"><img src="../public/img/low.png" style="max-width: 80px;"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- kết thúc khung giữa -->
                </div>
            </div>
            <div class="footer">
                <div class="w3-center" style="margin: 10px 0px; margin-bottom: 10px;">
                    <button id="button1" class="button-save" onclick="save_sequence();"><?php echo $text['save']; ?></button>
                </div>
            </div>
            
            <!-- Mũi tên thông báo dữ liệu vẫn chưa được cuộn hết -->
            <!-- <div id="scroll-indicator">
                ↓
            </div> -->

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

            let input_pin_no = document.getElementById("data_input_pin_no").value;
            document.getElementById("input_"+input_pin_no).checked = true;

            let data_wave = document.getElementById("data_wave").value;

            if(data_wave == 1){
                document.getElementById("wave_high").checked = true;
            }else{
                document.getElementById("wave_low").checked = true;
            }


        }

        // DataTable('#InputNew_Modal');
        // let table = new DataTable('#InputNew_Modal');


	});


</script>

<!-- xử lý ẩn Mũi tên khi được cuộn hết -->
<!-- <script>
    document.addEventListener("DOMContentLoaded", function () {
    const scrollArea = document.querySelector('.scrollbar-input-pin');
    const arrow = document.getElementById('scroll-indicator');

    function checkScroll() {
        const isScrolledToBottom = scrollArea.scrollHeight - scrollArea.scrollTop <= scrollArea.clientHeight + 1;
        arrow.style.display = isScrolledToBottom ? "none" : "block";
    }

    // Initial check
    checkScroll();

    // Check every time scrolls
    scrollArea.addEventListener('scroll', checkScroll);

    // Recheck on window resize (in case layout changes)
    window.addEventListener('resize', checkScroll);
});
</script> -->



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