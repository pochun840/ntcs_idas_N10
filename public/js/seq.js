    function save_sequence() {
        let job_id = document.getElementById("job_id").value;
        let seq_id = document.getElementById("seq_id").value;
        let mode = document.getElementById("mode")?.value || 'new';
        let seq_type_id = document.querySelector("[name='seq_type_id']").value;
        var seq_unit = "<?php echo $data['torque_unit_code'];?>";

        //add seq fastening check
        let check_seq = seq_fastening_check();

        let data = new FormData();
        data.append('mode', 'create');
        data.append("job_id", job_id);
        data.append("seq_id", seq_id);
        data.append("seq_type_id", seq_type_id);
        data.append("seq_name", document.getElementById("seq_name")?.value || '');
        data.append("tool_id", document.getElementById("tool_id")?.value || 0);



        // Message
        if (document.getElementById("text_message")) {
            data.append("text_message", document.getElementById("text_message").value);
        }

        if (document.getElementById("img_list")) {
            data.append("image", document.getElementById("img_list").value);
        }

        // Delay
        if (document.getElementById("timeout")) {
            data.append("timeout", document.getElementById("timeout").value);
        }

        // Input
        if (seq_type_id === "4") {
            const inputPinValue = get_checked_input(); // name="input_check"
            if (inputPinValue) {
                data.append("input_pin_no", inputPinValue);
            }
            if (document.getElementById("wave")) {
                data.append("wave", document.getElementById("wave").value);
            }
        }

        // Output
        if (seq_type_id === "5") {
            const output = get_checked_output(); // name="output_check", value="pin_wave"
            if (output.length === 2) {
                data.append("output_pin_no", output[0]);
                data.append("wave", output[1]);
            }
            if (document.getElementById("pulse")) {
                data.append("pulse", document.getElementById("pulse").value);
            }
        }

        // Fastening
        if (seq_type_id === "1") {

            data.append("tightening_repeat", document.getElementById("tightening_repeat")?.value || '');
            data.append("timeout", document.getElementById("timeout")?.value || '');
            data.append("ng_stop", document.getElementById("ng_stop")?.value || '');
            data.append("speed", document.getElementById("speed")?.value || '');
            data.append("torque_threshold", document.getElementById("torque_threshold")?.value || '');
            data.append("unscrew_force", document.getElementById("unscrew_force")?.value || '');
            data.append("dt_time", document.getElementById("dt_time")?.value || '');
            data.append("tt_time", document.getElementById("tt_time")?.value || '');
            data.append("total_angle_limit", document.getElementById("total_angle_limit")?.value || '');
            data.append("total_angle_lower", document.getElementById("total_angle_lower")?.value || '');

            // Radio buttons
            data.append("ok_seq", document.querySelector("input[name='ok_seq_option']:checked")?.value || '0');
            data.append("seq_stop", document.querySelector("input[name='seq_stop_option']:checked")?.value || '0');
            data.append("reverse_count_option", document.querySelector("input[name='reverse_count_option']:checked")?.value || '0');
            data.append("ng_reverse_option", document.querySelector("input[name='ng_reverse_option']:checked")?.value || '0');
            data.append("accumulate_angle_option", document.querySelector("input[name='accumulate_angle_option']:checked")?.value || '0');
            data.append("reverse_mode", document.querySelector("input[name='reverse_mode_option']:checked")?.value || '0');
            data.append("direction", document.querySelector("input[name='direction_option']:checked")?.value || '1');
            data.append("force_option", document.querySelector("input[name='force_option']:checked")?.value || '0');

            

            if(seq_unit ==0 ){
                document.getElementById('torque_threshold').value = "0.00";
            }
            if(seq_unit == 1){
                document.getElementById('torque_threshold').value = "0.000";
            }
            if(seq_unit == 2){
                document.getElementById('torque_threshold').value = "0.000";
            }

            if(seq_unit == 3){
                document.getElementById('torque_threshold').value = "0.0000";
            }

             if(seq_unit == 4){
                document.getElementById('torque_threshold').value = "0.0";
            }

            // Checkbox Angle_Calculation (Thread_Calcu_1~5)
            const totalAngle = getCheckboxValue_seq();  // tính tổng bit theo 16,8,4,2,1
            data.append("Thread_Calcu", totalAngle);
         
        }

        // Gửi dữ liệu
        const url = (mode === "edit")
            ? "?url=Sequences/update_all_seq"
            : "?url=Sequences/create_seq";

        let check = seq_fastening_check(); // Nếu có kiểm tra trước
        
        if (check) {
            document.querySelector(".main-content").classList.add("overlay-active");
            document.getElementById("spinner").style.display = 'block';

            $.ajax({
                type: "POST",
                url: url,
                data: data,
                //dataType: "json",
                contentType: false,
                processData: false,

                success: function (response) {
                    const job_id = document.getElementById("job_id").value;
                    success_response_seq(response, 'spinner', `../public/?url=Sequences/index/${job_id}`);
                },

                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }
    }

   
    function get_checked_input() {
        let checked_box = '';
        document.querySelectorAll('input[name="input_check"]').forEach(function(checkbox) {
            if (checkbox.checked && !checkbox.disabled) {
                checked_box += checkbox.value + ',';
            }
        });
        checked_box = checked_box.slice(0, -1); // bỏ dấu phẩy cuối
        return checked_box;
    }

    function get_checked_output() {
        let checked_box = '';
        document.querySelectorAll('input[name="output_check"]').forEach(function(checkbox) {
            if (checkbox.checked && !checkbox.disabled) {
                checked_box = checkbox.value.split("_"); // [pin, wave]
            }
        });
        return checked_box;
    }

    
    function edit_sequence(seq_id, seq_type) {
        const job_id = document.getElementById('job_id')?.value || '';
        console.log("Redirect to edit:", seq_type);

        // Gọi controller chung Edit_Sequence thay vì các hàm riêng
        location.href = `?url=Sequences/Edit_Sequence/${job_id}/${seq_id}`;

        document.querySelector(".main-content").classList.add("overlay-active");
        document.getElementById("spinner").style.display = 'block';
            
        $.ajax({
            url: '?url=Sequences/Edit_Sequence',
            type: 'POST',
            data: data,
            processData: false,
            contentType: false,
            success: function (response) {
                const responseData = JSON.parse(response);
                success_response_seq(response, 'spinner', `../public/?url=Sequences/index/${job_id}/${seq_id}`);

            },
                error: function (xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }


