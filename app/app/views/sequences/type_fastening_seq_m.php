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

     <div style="display:none;">
        <input id="tool_max_torque" value="<?php echo $data['tools_info']['max_torque']; ?>">
        <input id="tool_min_torque" value="<?php echo $data['tools_info']['min_torque']; ?>">
        <input id="tool_max_rpm" value="<?php echo $data['tools_info']['max_rpm']; ?>">
        <input id="tool_min_rpm" value="<?php echo $data['tools_info']['min_rpm']; ?>"> 
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
                <input type="hidden" id="seq_type_id" name="seq_type_id" value="<?php echo $data['seq_type_id']; ?>">
                <input type="text" id="seq_type" name="seq_type" size="10" maxlength="20" value="<?php echo $text['sequence_type'][$data['seq_type_id']]; ?>" readonly
                style="height:28px; font-size:18px;text-align: center; background-color: #DDDDDD; border:0; margin: 3px;">
      
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
                        <input type="hidden" id="data_unscrew_force" value="<?php echo isset($data['seq_data']['unscrew_force']) ? $data['seq_data']['unscrew_force'] : '0'; ?>">
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
                                <div class="col-4 t1"><?php echo $text['tighten_repeat'];?>:</div>
                                <div class="col t2">
                                    <input id="tightening_repeat"  name="tightening_repeat" class="form-control" value="1">
                                    <div class="invalid-feedback"></div>(1-99)
                                </div>
                            </div>
                            <div class="row t1">
                                <div class="col-4 t1"><?php echo $text['Timeout'];?> (<?php echo $text['Second'];?>):</div>
                                <div class="col t2">
                                    <input id="timeout" name="timeout" class="form-control" value="20">
                                    <div class="invalid-feedback"></div>(0-60)
                                </div>
                            </div>

                            <div class="row t1">
                                <div class="col-4 t1"><?php echo $text['DT_Time'];?> (<?php echo $text['Second'];?>):</div>
                                <div class="col t2">
                                    <input id="dt_time" name="dt_time" class="form-control" value ="0">
                                    <div class="invalid-feedback"></div>(0-99)
                                </div>
                            </div>
                            <div class="row t1">
                                <div class="col-4 t1"><?php echo $text['TT_Time'];?> (<?php echo $text['Second'];?>):</div>
                                <div class="col t2">
                                    <input id="tt_time" name="tt_time" class="form-control" value ="0">
                                    <div class="invalid-feedback"></div> (0-6000)
                                </div>
                            </div>
                                
                            <div class="row t1">
                                <div class="col-4 t1"><?php echo $text['OK_Seq'];?>:</div>
                                <div class="col t2">
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input" type="radio" name="ok_seq_option" id="ok_seq_off" value="0">
                                        <label class="form-check-label" for="ok_seq_off"><?php echo $text['switch_off']; ?></label>
                                    </div>
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input" type="radio" name="ok_seq_option" id="ok_seq_on" value="1">
                                        <label class="form-check-label" for="ok_seq_on"><?php echo $text['switch_on']; ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row t1">
                                <div class="col-4 t1"><?php echo $text['Seq_Stop'];?>:</div>
                                <div class="col t2">
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input" type="radio" name="seq_stop_option" id="seq_stop_off" value="0">
                                        <label class="form-check-label" for="seq_stop_off"><?php echo $text['switch_off']; ?></label>
                                    </div>
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input" type="radio" name="seq_stop_option" id="seq_stop_on" value="1">
                                        <label class="form-check-label" for="seq_stop_on"><?php echo $text['switch_on']; ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row t1">
                                <div class="col-4 t1"><?php echo $text['NG_Stop'];?> :</div>
                                <div class="col t2 d-flex align-items-center">
                                    <select id="ng_stop" class="form-select" style="font-size: 14px; width: 163px;">
                                        <?php 
                                            for ($i = 0; $i <= 9; $i++) {
                                                echo '<option value="' . $i . '" ' . (($data['seq_data']['ng_stop'] == $i) ? 'selected' : '') . '>' . $i . '</option>'; 
                                            }
                                        ?> 
                                    </select>
                                    <span class="ms-2">(0-9)</span> <!-- ms-2 thêm khoảng cách trái -->
                                </div>
                            </div>

                            <div class="row t1">
                                <div class="col-4 t1"><?php echo $text['Accumulate_Angle'];?>:</div>
                                <div class="col t2">
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input" type="radio" name="accumulate_angle_option" id="accumulate_angle_off" value="0">
                                        <label class="form-check-label" for="accumulate_angle_off"><?php echo $text['switch_off']; ?></label>
                                    </div>
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input" type="radio" name="accumulate_angle_option" id="accumulate_angle_on" value="1">
                                        <label class="form-check-label" for="accumulate_angle_on"><?php echo $text['switch_on']; ?></label>
                                    </div>
                                </div>
                            </div>
 
                            <div class="row t1">
                                <div class="col-4 t1"><?php echo $text['Angle_Calculation'];?> (<?php echo $text['step'];?>):</div>
                                <div class="col t2">
                                    <?php for ($i = 1; $i <= 5; $i++) { ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input thread-checkbox" type="checkbox" id="Thread_Calcu_<?php echo $i; ?>" 
                                            value="<?php echo $i; ?>" 
                                            onchange="getCheckboxValue_seq()" >
                                        <label class="form-check-label" for="Thread_Calcu_<?php echo $i; ?>"><?php echo $i; ?></label>
                                        </div>
                                    <?php } ?>
                                </div>

                                <!-- thêm input ẩn để lưu tổng giá trị checkbox -->
                                <input type="hidden" id="Thread_Calcu" name="Thread_Calcu" value="">
                            </div>

                            <hr>

                            <div class="row t1">
                                <div class="col fw-bolder"><?php echo $text['Reverse'];?></div>
                            </div>

                            <div class="row t1">
                                <div class="col-4 t1"><?php echo $text['rev_count'];?>:</div>
                                <div class="col t2">
                                    <div class="form-check form-check-inline col-3">
                                        <input class="form-check-input" type="radio" name="reverse_count_option" id="reverse_count_off" value="0">
                                        <label class="form-check-label" for="reverse_count_off"><?php echo $text['switch_off']; ?></label>
                                    </div>
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input" type="radio" name="reverse_count_option" id="reverse_count_on" value="1">
                                        <label class="form-check-label" for="reverse_count_on"><?php echo $text['switch_on']; ?></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row t1">
                                <div class="col-4 t1"><?php echo $text['NG_Reverse'];?>:</div>
                                <div class="col t2">
                                    <div class="form-check form-check-inline col-3">
                                        <input class="form-check-input" type="radio" name="ng_reverse_option" id="ng_reverse_off" value="0">
                                        <label class="form-check-label" for="ng_reverse_off"><?php echo $text['switch_off']; ?></label>
                                    </div>
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input" type="radio" name="ng_reverse_option" id="ng_reverse_on" value="1">
                                        <label class="form-check-label" for="ng_reverse_on"><?php echo $text['switch_on']; ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row t1">
                                <div class="col-4 t1"><?php echo $text['Reverse_mode'];?>:</div>
                                <div class="col t2">
                                    <div class="form-check form-check-inline col-3">
                                        <input class="form-check-input" type="radio" name="reverse_mode_option" id="reverse_mode_off" value="0">
                                        <label class="form-check-label" for="reverse_mode_off"><?php echo $text['Auto_text']; ?></label>
                                    </div>
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input" type="radio" name="reverse_mode_option" id="reverse_mode_on" value="1">
                                        <label class="form-check-label" for="reverse_mode_on"><?php echo $text['Custom_text']; ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row t1" id="div_speed">
                                <div class="col-4 t1"><?php echo $text['speed_RPM'];?> (rpm):</div>
                                <div class="col t2">
                                    <input id="speed" class="form-control" value="150">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="row t1" id="div_torque_threshold">
                                <div class="col-4 t1"><?php echo $text['Threshold_Torque'];?> (<?php echo $text[$data['torque_unit']]; ?>):</div>
                                <div class="col t2">
                                    <input id="torque_threshold" class="form-control" value="0">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="row t1" id="div_direction">
                                <div class="col-4 t1"><?php echo $text['direction'];?>:</div>
                                <div class="col t2">
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input" type="radio" name="direction_option" id="direction_cw" value="1">
                                        <label class="form-check-label" for="direction_cw"><?php echo $text['CW']; ?></label>
                                    </div>
                                    <div class="form-check form-check-inline ">
                                        <input class="form-check-input" type="radio" name="direction_option" id="direction_ccw" value="0">
                                        <label class="form-check-label" for="direction_ccw"><?php echo $text['CCW']; ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row t1" id="div_force">
                                <div class="col-4 t1"><?php echo $text['Force'];?> (%):</div>
                                <div class="col t2">
                                    <div class="form-check form-check-inline col-xs-3">
                                        <input class="form-check-input" type="radio" name="force_option" id="force_on" value="1">
                                        <label class="form-check-label" for="force_on"><?php echo $text['switch_on']; ?></label>&nbsp;&nbsp;
                                        <input class="form-control" size="5" id="force_number" name="force_number" style="height:27px;">
                                        <div class="invalid-feedback"></div>
                                    </div>
                                    <div class="form-check form-check-inline col-xs-3">
                                        <input class="form-check-input" type="radio" name="force_option" id="force_unlimit" value="2">
                                        <label class="form-check-label" for="force_unlimit"><?php echo $text['Unlimited_text']; ?></label>
                                    </div>
                                    <div class="form-check form-check-inline col-xs-3">
                                        <input class="form-check-input" type="radio" name="force_option" id="force_off" value="0">
                                        <label class="form-check-label" for="force_off"><?php echo $text['switch_off']; ?></label>
                                    </div>
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

        if(mode == 'new'){
            //帶入預設值
            $('input[name=direction_option]:checked').val()
            document.getElementById("ok_seq_off").checked = true;
            document.getElementById("seq_stop_off").checked = true;
            document.getElementById("reverse_count_off").checked = true;
            document.getElementById("ng_stop").value = 0;
            document.getElementById("ng_reverse_on").checked = true;
            document.getElementById("accumulate_angle_on").checked = true;
            document.getElementById("reverse_mode_off").checked = true;
            document.getElementById("speed").value = 100;
            document.getElementById("torque_threshold").value = 0;
            document.getElementById("direction_ccw").checked = true;
            document.getElementById("dt_time").value = 0;
            document.getElementById("tt_time").value = 0;
            
            // Mặc định khi new: force_off
            document.getElementById('force_off').checked = true;
            document.getElementById('force_number').value = 50;
            document.getElementById('force_number').disabled = true;

            // Gọi change thủ công bằng cách tạo Event
            document.getElementById('force_off').dispatchEvent(new Event('change'));

            document.getElementById("tightening_repeat").value = 1;
            document.getElementById("timeout").value = 0;

            for (let i = 1; i <= 5; i++) {
                document.getElementById("Thread_Calcu_" + i).checked = true;
            }
            getCheckboxValue_seq(); // ← thêm dòng này
        }

        if(mode == 'edit'){
            //帶入資料
            document.getElementById("seq_name").value = document.getElementById("data_seq_name").value
            document.getElementById("tightening_repeat").value = document.getElementById("data_seq_repeat").value
            document.getElementById("timeout").value = document.getElementById("data_delay").value
            document.getElementById("ng_stop").value = document.getElementById("data_ng_stop").value
            document.getElementById("speed").value = document.getElementById("data_unscrew_rpm").value
            document.getElementById("torque_threshold").value = document.getElementById("data_unscrew_torque_threshold").value
            // Thêm 2 dòng này 👇
            document.getElementById("dt_time").value = document.getElementById("data_dt_time").value;
            document.getElementById("tt_time").value = document.getElementById("data_tt_time").value;
           
            let ok_seq = document.getElementById("data_ok_seq").value;
            let seq_stop = document.getElementById("data_ok_stop").value;
            let reverse_count = document.getElementById("data_unscrew_count").value;
            let ng_reverse = document.getElementById("data_ng_unscrew").value;
            let accu_angle = document.getElementById("data_accu_angle").value;
            let reverse_mode = document.getElementById("data_unscrew_mode").value;
            let revers_direction = document.getElementById("data_unscrew_dir").value;
            
            let displayedValue = '<?php echo isset($data['seq_data']['Thread_Calcu']) ? $data['seq_data']['Thread_Calcu'] : ''; ?>';
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            setCheckboxesByValue(displayedValue);

            if(ok_seq == 0){
                document.getElementById("ok_seq_off").checked = true;
            }else{
                document.getElementById("ok_seq_on").checked = true;
            }
            if(seq_stop == 0){
                document.getElementById("seq_stop_off").checked = true;
            }else{
                document.getElementById("seq_stop_on").checked = true;
            }
            if(reverse_count == 0){
                document.getElementById("reverse_count_off").checked = true;
            }else{
                document.getElementById("reverse_count_on").checked = true;
            }
            if(ng_reverse == 0){
                document.getElementById("ng_reverse_off").checked = true;
            }else{
                document.getElementById("ng_reverse_on").checked = true;
            }
            if(accu_angle == 0){
                document.getElementById("accumulate_angle_off").checked = true;
            }else{
                document.getElementById("accumulate_angle_on").checked = true;
            }
            if(reverse_mode == 0){
                document.getElementById("reverse_mode_off").checked = true;
            }else{
                document.getElementById("reverse_mode_on").checked = true;
            }
            if(revers_direction == 0){
                document.getElementById("direction_ccw").checked = true;
            }else{
                document.getElementById("direction_cw").checked = true;
            }
 
            // Lấy giá trị force từ hidden input hoặc data (bạn đặt đúng ở server)
            let forceRaw = document.getElementById('data_unscrew_force').value;

            let force_option = '0';  // OFF mặc định
            let force_number = '';

            if (forceRaw === null || forceRaw === undefined || forceRaw === '') {
                // giữ mặc định OFF
                force_option = '0';
                force_number = '';
            } else {
                let unscrew_force = parseInt(forceRaw);

                if (unscrew_force > 0) {
                    force_option = '1';  // ON
                    force_number = unscrew_force;
                } else if (unscrew_force === -1) {
                    force_option = '2';  // Unlimited
                    force_number = '';
                } else {
                    force_option = '0';  // OFF
                    force_number = '';
                }
            }
              
            document.getElementById('force_number').value = force_number;

            document.getElementById('force_on').checked = false;
            document.getElementById('force_unlimit').checked = false;
            document.getElementById('force_off').checked = false;

            if (force_option === '1') {
                document.getElementById('force_on').checked = true;
                document.getElementById('force_number').disabled = false;
            } else if (force_option === '2') {
                document.getElementById('force_unlimit').checked = true;
                document.getElementById('force_number').disabled = true;
            } else {
                document.getElementById('force_off').checked = true;
                document.getElementById('force_number').disabled = true;
            }

            //getCheckboxValue_seq(); // ← thêm dòng này
            
        }

        //主動觸發change事件 - Zhǔdòng chùfā change shìjiàn
        let checkedReverseMode = document.querySelector('input[name="reverse_mode_option"]:checked');
        if(checkedReverseMode){
            checkedReverseMode.dispatchEvent(new Event("change"));
        }

        // Auto check reverse mode on edit
        if ($('#mode').val() === 'edit') {
            const val = $('#data_unscrew_mode').val();
            if (val === '1') {
                $('#reverse_mode_on').prop('checked', true);
            } else {
                $('#reverse_mode_off').prop('checked', true);
            }
        }

        function updateForceNumberStatus() {
            const isForceOn = document.getElementById('force_on').checked;
            const isReverseModeOn = document.getElementById('reverse_mode_on').checked;

            if (isForceOn && isReverseModeOn) {
                document.getElementById('force_number').disabled = false;
            } else {
                document.getElementById('force_number').disabled = true;
            }
        }

        // Gán sự kiện cho radio force_option
        $('input[name="force_option"]').change(function () {
            updateForceNumberStatus();
        });

        // Gán sự kiện cho reverse_mode_option
        $('input[name="reverse_mode_option"]').change(function () {
            updateForceNumberStatus();
        });

	});


    function getCheckboxValue_seq() {
        const checkboxes = document.querySelectorAll('.thread-checkbox');
        let total = 0;

        checkboxes.forEach((checkbox, index) => {
            if (checkbox.checked) {
                switch (index) {
                    case 0: total += 16; break; // 第1個
                    case 1: total += 8; break;  // 第2個
                    case 2: total += 4; break;  // 第3個
                    case 3: total += 2; break;  // 第4個
                    case 4: total += 1; break;  // 第5個
                }
            }
        });

        // Gán tổng vào input ẩn để gửi khi lưu
        const hiddenInput = document.getElementById('Thread_Calcu');
        if (hiddenInput) hiddenInput.value = total;

        return total;
    }

    // --- Hàm set lại checkbox khi Edit ---
    function setCheckboxesByValue(value) {
        const checkboxes = document.querySelectorAll('.thread-checkbox');
        const num = parseInt(value, 10) || 0;

        checkboxes.forEach((checkbox, index) => {
            let bitValue = 0;
            switch (index) {
                case 0: bitValue = 16; break;
                case 1: bitValue = 8; break;
                case 2: bitValue = 4; break;
                case 3: bitValue = 2; break;
                case 4: bitValue = 1; break;
            }
            checkbox.checked = (num & bitValue) !== 0;
        });

        // Cập nhật lại input ẩn
        getCheckboxValue_seq();
    }


    //監控option 變化
    document.querySelectorAll('input[name="reverse_mode_option"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
        // 获取选中的radio的值
        var selectedValue = document.querySelector('input[name="reverse_mode_option"]:checked').value;
        // 根据值来显示或隐藏特定的div
        if (selectedValue === '1') {
            $("#div_speed *").removeAttr("disabled")
            $("#div_torque_threshold *").removeAttr("disabled")
            $("#div_direction *").removeAttr("disabled")
            $("#div_force *").removeAttr("disabled")
        } else {
            $("#div_speed *").attr("disabled", "disabled").off('click');
            $("#div_torque_threshold *").attr("disabled", "disabled").off('click');
            $("#div_direction *").attr("disabled", "disabled").off('click');
            $("#div_force *").attr("disabled", "disabled").off('click');
        }
      });
    });


    function input_check(argument) {
        let Tool_Max_Torque = document.getElementById('tool_max_torque').value;
        let Tool_Min_Torque = document.getElementById('tool_min_torque').value;
        let Tool_Max_RPM = document.getElementById('tool_max_rpm').value;
        let Tool_Min_RPM = document.getElementById('tool_min_rpm').value;

        var selectedValue = document.querySelector('input[name="force_option"]:checked')?.value;
        var isAutoMode = document.getElementById('reverse_mode_off')?.checked;

        let conditions = [
            { id: 'seq_name', pattern: /^[a-zA-Z0-9\u4E00-\u9FA5\-]+$/, min: null, max: null },
            { id: 'tightening_repeat', pattern: /^\d{0,4}$/, min: 1, max: 99 },
            { id: 'timeout', pattern: /^\d{0,5}?$/, min: 0, max: 60 },
            { id: 'ng_stop', pattern: /^\d{0,5}?$/, min: 0, max: 9 },
            { id: 'speed', pattern: /^\d{1,3}$/, min: Tool_Min_RPM, max: Tool_Max_RPM },
            { id: 'torque_threshold', pattern: /^\d{1,3}(\.\d{1,3})?$/, min: 0, max: Tool_Max_Torque },
            { id: 'force_number', pattern: /^\d{1,3}$/, min: 1, max: 100 },
            { id: 'dt_time', pattern: /^\d{0,5}?$/, min: 0, max: 99 },
            { id: 'tt_time', pattern: /^\d{0,5}?$/, min: 0, max: 6000 },

        ];

        let isFormValid = true;

        conditions.forEach(function(input) {
            var element = document.getElementById(input.id);
            var value = element.value.trim();

            if (isAutoMode && (input.id === 'torque_threshold' || input.id === 'force_number' || input.id === 'speed')) {
                // Bỏ qua validate khi chế độ tự động bật
                return;
            }

            if(input.id != 'seq_name'){
                var nextSibling = element.nextElementSibling;
                if (nextSibling) {
                    nextSibling.innerHTML = input.min + ' ~ ' + input.max;
                }
            }

            // Xử lý riêng cho force_number dựa trên force_option
            if(input.id === 'force_number'){
                if(selectedValue === '1'){
                    // bật validate
                    element.disabled = false;
                } else {
                    // tắt validate và disable input
                    element.disabled = true;
                    element.classList.remove("is-invalid");
                    return; // bỏ qua validate force_number
                }
            }

            if (value === "") {
                element.classList.add("is-invalid");
                isFormValid = false;
            } else if (!input.pattern.test(value)) {
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

    function toggleDisableAndError(elementId, disable) {
        const element = document.getElementById(elementId);
        if (!element) {
            console.warn(`Element with ID '${elementId}' not found.`);
            return;
        }

        element.disabled = disable;
        if (disable) {
            element.classList.remove('is-invalid'); 
        }
    }

    function toggleForceNumber() {
        let forceOption = $('input[name="force_option"]:checked').val();
        let reverseMode = $('input[name="reverse_mode_option"]:checked').val();

        if (forceOption === '1' && reverseMode === '1') {
            $('#force_number').prop('disabled', false);
        } else {
            $('#force_number').prop('disabled', true);
        }
    }

    // Gọi lần đầu khi load trang
    toggleForceNumber();

    // Khi thay đổi force_option
    $('input[name="force_option"]').change(function() {
        toggleForceNumber();
    });

    // Khi thay đổi reverse_mode
    $('input[name="reverse_mode_option"]').change(function() {
        toggleForceNumber();
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