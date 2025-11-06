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
    .img-cell 
    {
        padding: 0;
        text-align: center;      /* Căn giữa theo chiều ngang */
        vertical-align: middle;  /* Căn giữa theo chiều dọc */
    }

    .img-wrapper 
    {
        display: flex;
        justify-content: center; /* Căn giữa ngang trong wrapper */
        align-items: center;     /* Căn giữa dọc trong wrapper */
        height: 24px;            /* Chiều cao của ô */
    }
    .signal-icon 
    {
        width: 60px;  /* tăng kích thước ảnh */
        height: auto;
        display: block;
        max-height: 50px;  /* không vượt quá header */
        object-fit: contain;
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
                    <input type="hidden" id="data_output_pin_no" value="<?php echo $data['seq_data']['output_pin_no']; ?>">
                    <input type="hidden" id="data_wave" value="<?php echo $data['seq_data']['wave']; ?>">
                    <input type="hidden" id="data_wave_on" value="<?php echo $data['seq_data']['wave_on']; ?>">

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
                    <div class="mx-auto" style="max-width: 700px; width: 100%; margin-bottom: 20px;">
                        
                        <!-- Sequence Name -->
                        <div class="row align-items-center mb-3" style="padding-left: 5%; padding-top: 3%;">
                            <div class="col-4 text-end t1" style="white-space: nowrap;"><?php echo $text['sequence_name'];?> :</div>
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
                                            <th class="img-cell">
                                                <div class="img-wrapper">
                                                    <img src="../public/img/signal02.png" class="signal-icon">
                                                </div>
                                            </th>
                                            <th class="img-cell">
                                                <div class="img-wrapper">
                                                    <img src="../public/img/signal01.png" class="signal-icon">
                                                </div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        <?php
                                            $check = false;
                                            for ($i=1; $i <= 11; $i++) { 
                                                echo '<tr>';
                                                foreach ($data['job_outputs'] as $key => $value) {
                                                    if($value['output_pin_no'] == $i){
                                                        echo '<td>'.$i.'</td>';
                                                        echo '<td>'.$value['event_id'].'</td>';
                                                        if($value['wave'] == 1){
                                                            echo '<td><input id="output_'.$i.'_wave" type="checkbox" class="zoom form-check-input" value="'.$i.'" disabled></td>';
                                                            echo '<td><input id="output_'.$i.'_waveHi" type="checkbox" class="zoom form-check-input" value="'.$i.'" disabled checked></td>';
                                                        }else{
                                                            echo '<td><input id="output_'.$i.'_wave" type="checkbox" class="zoom form-check-input" value="'.$i.'" disabled checked></td>';
                                                            echo '<td><input id="output_'.$i.'_waveHi" type="checkbox" class="zoom form-check-input" value="'.$i.'" disabled></td>';
                                                        }
                                                        $check = true;
                                                    }
                                                }

                                                if(!$check){
                                                    echo '<td>'.$i.'</td>';
                                                    echo '<td>Logical out</td>';
                                                    echo '<td><input id="output_'.$i.'_wave" name="output_check" type="radio" class="form-check-input" value="'.$i.'_2"></td>';//wave 2 直流 1
                                                    echo '<td><input id="output_'.$i.'_waveHi" name="output_check" type="radio" class="form-check-input" value="'.$i.'_1"></td>';
                                                }
                                                
                                                echo '</tr>';
                                                $check = false;
                                            }
                                        ?>

                                    </tbody>
                                </table>
                                <!-- Input Setting -->
                                <div class="row align-items-center" style="padding-left: 5%;">
                                    <label class="col-8 mb-3"><?php echo $text['Impulse_Setting'];?> :<img style="max-width: 80px;" src="../public/img/signal02.png"><?php echo $text['Impulse_Signal'];?> :</label>
                                    <div class="col mb-3">
                                        <div class="form-check form-check-inline">
                                            <input id="pulse" class="form-control" size="7" value="100">
                                            <div class="invalid-feedback" style="text-align:center;"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Mũi tên thông báo dữ liệu vẫn chưa được cuộn hết -->
                                <!-- <div id="scroll-indicator">
                                    ↓
                                </div> -->

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

        if(mode == 'new'){
            //帶入預設值
        }

        if(mode == 'edit'){
            //帶入資料
            document.getElementById("seq_name").value = document.getElementById("data_seq_name").value

            let output_pin_no = document.getElementById("data_output_pin_no").value;
            let data_wave = document.getElementById("data_wave").value;
            let data_wave_on = document.getElementById("data_wave_on").value;

            // document.getElementById("output_"+input_pin_no).checked = true;
            if(data_wave == 2){
                document.getElementById("output_"+output_pin_no+"_wave").checked = true;
            }else{
                document.getElementById("output_"+output_pin_no+"_waveHi").checked = true;
            }
            // output_1_waveHi
            // output_1_wave

            document.getElementById("pulse").value = data_wave_on;

        }

        // DataTable('#InputNew_Modal');
        // let table = new DataTable('#InputNew_Modal');


	});


    function input_check(argument) {

        let conditions = [
            { id: 'seq_name', pattern: /^[a-zA-Z0-9\u4E00-\u9FA5\-]+$/, min: null, max: null },
            { id: 'pulse', pattern: /^\d{0,4}$/, min: 100, max: 10000 },
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

        return isFormValid;

    }

</script>

<!-- xử lý ẩn mũi tên khi được cuộn hết -->
<script>
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