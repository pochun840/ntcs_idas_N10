<?php

class Sequences extends Controller
{
    private $MiscellaneousModel;
    private $sequenceModel;
    private $SettingModel;
    private $ToolModel;
    // 在建構子中將 Post 物件（Model）實例化
    public function __construct(){
        $this->sequenceModel = $this->model('Sequence');
        $this->MiscellaneousModel = $this->model('Miscellaneous');
        $this->SettingModel = $this->model('Setting');
        $this->ToolModel = $this->model('Tool');
    }

    // 取得所有Sequences
    public function index($job_id){

        // session_start();
        // $this->language_auto(); // ✅ gọi trước bất kỳ view nào

        if( isset($job_id) && !empty($job_id) ){

        }else{
            $job_id = 1;
        }

        $sequences = $this->sequenceModel->getSequences_by_job_id($job_id);  
        $unit_arr   = $this->MiscellaneousModel->details('torque_unit');

        $total_seq = (int)$this->sequenceModel->countseq($job_id);      

        // var_dump($sequences);

        if(empty($sequences)){
            $seq_id = 1;
            $next_seq_id = $seq_id;
        }else{
            $seq_id = count($sequences) + 1 ;
            $next_seq_id = $seq_id;
        }


        //$job_type = 'normal';

        $isMobile = $this->isMobileCheck();
        $device_info = $this->Device_Info();

        $data = array(
            'sequences' => $sequences,
            'job_id' => $job_id,
            'unit_arr' => $unit_arr,
            'seq_id' => $seq_id,
            'old_seqid' => '',
            'total_seq' => $total_seq,
            'next_seq_id' => $next_seq_id

        );

        if($isMobile){
            $this->view('sequences/index_m', $data);
        }else{
            $this->view('sequences/index', $data);
        }
        
    }

    //-----------------NTCS iDAS--------------------

    public function Add_Sequence($job_id, $type_id = '1')
    {
        // $file = $this->MiscellaneousModel->lang_load();
        // if(!empty($file)){
        //     include $file;
        // }

        // Lấy SEQID tiếp theo
        $head_seq_id = $this->sequenceModel->get_head_seq_id($job_id);
        $seq_id = $head_seq_id[0];
        $next_seq_id = $seq_id; 

        // Lấy thông tin tool
        $torque_unit_code = $this->SettingModel->Get_System_Toq_Unit();
        $unit_arr = $this->MiscellaneousModel->details('torque_unit');
        $torque_unit = $unit_arr[$torque_unit_code] ?? 'N.m';
        $decimals_arr = $this->MiscellaneousModel->details("decimals");

        $res_device = $this->SettingModel->GetControllerInfo();
        $device_torque_unit = (int)$res_device['torque_unit'];

        $tools_info = $this->ToolModel->GetToolInfo();
        if (!empty($tools_info)){
            $tools_info = $tools_info[0]; // lấy dòng đầu tiên
        }

        if (!empty($tools_info)) {
            foreach (['max_rpm', 'min_rpm'] as $key) {
                if (isset($tools_info[$key])) {
                    $val = (float) $tools_info[$key];

                    // 檢查是否為整數
                    if (floor($val) == $val) {
                        // 若為整數 → 輸出為沒有小數的字串
                        $tools_info[$key] = (string) intval($val);
                    } else {
                        // 若有小數 → 保留原數值
                        $tools_info[$key] = (string) $val;
                    }
                }
            }

  
            $from_unit = 1;
            $tools_temp = $this->MiscellaneousModel->prepareToolTorqueValues($tools_info,$from_unit,$device_torque_unit,$decimals_arr);
            if(!empty($tools_temp)){
                $tools_info['max_torque'] = $tools_temp['max_torque'];
                $tools_info['min_torque'] = $tools_temp['min_torque'];
            }
        }

        $isMobile = $this->isMobileCheck();

        // Bản đồ kiểu ID => Tên
        $type_names = [
            '1' => 'Fastening',
            '2' => 'Message',
            '3' => 'Delay',
            '4' => 'Input',
            '5' => 'Output'
        ];

        $type_name = $type_names[$type_id] ?? 'Unknown';

        // Dữ liệu chung
        $data = [
            //'sequences'        => $sequences,
            'job_id'           => $job_id,
            'seq_id'           => $seq_id,
            'next_seq_id'      => $next_seq_id,
            'mode'             => 'new',
            'seq_type_id'      => $type_id,       // để gửi về controller xử lý
            'seq_type'         => $type_name,     // chỉ để hiển thị
            'tools_info'       => $tools_info,
            'torque_unit'      => $torque_unit,
            'torque_unit_code' => $torque_unit_code
        ];
        
        // Hiển thị view tùy theo loại
        switch ($type_id) {
            case '1': // Fastening
                $view_file = $isMobile ? 'sequences/type_fastening_seq_m' : 'sequences/type_fastening_seq';
                $this->view($view_file, $data);
                break;

            case '2': // Message

                $img_dir = '../public/img';
                $data['img_list'] = $this->GetImageList($img_dir);
                $view_file = $isMobile ? 'sequences/type_message_seq_m' : 'sequences/type_message_seq';
                break;

            case '3': // Delay
                $view_file = $isMobile ? 'sequences/type_delay_seq_m' : 'sequences/type_delay_seq';
                break;

            case '4': // Input
                $data['job_inputs'] = $this->sequenceModel->GetInputsByJob($job_id);
                $view_file = $isMobile ? 'sequences/type_input_seq_m' : 'sequences/type_input_seq';
                break;

            case '5': // Output
                $data['job_outputs'] = $this->sequenceModel->GetOutputsByJob($job_id);
                $view_file = $isMobile ? 'sequences/type_output_seq_m' : 'sequences/type_output_seq';
                break;

            default:
                echo "Unknown sequence type";
                exit();
        }

                     
        // ✅ Cuối cùng gọi view:
        $this->view($view_file, $data);
        
    }

    public function update_all_seq()
    {
        $job_id = $_POST['job_id'] ?? '';
        $seq_id = $_POST['seq_id'] ?? '';
        $seq_type_id = $_POST['seq_type_id'] ?? '0';

        // Chuẩn bị data theo từng loại
        switch ($seq_type_id) {
            case '2': // Message
                $data = [
                    'seq_name' => $_POST['seq_name'] ?? '',
                    'image' => $_POST['image'] ?? '',
                    'message' => $_POST['text_message'] ?? '',
                ];
                break;

            case '3': // Delay
                $data = [
                    'seq_name' => $_POST['seq_name'] ?? '',
                    'timeout' => $_POST['timeout'] ?? 0,
                ];
                break;

            case '4': // Input
                $data = [
                    'seq_name' => $_POST['seq_name'] ?? '',
                    'input_pin_no' => $_POST['input_pin_no'] ?? '',
                    'wave' => $_POST['wave'] ?? '',
                ];
                break;

            case '5': // Output
                $data = [
                    'seq_name' => $_POST['seq_name'] ?? '',
                    'output_pin_no' => $_POST['output_pin_no'] ?? '',
                    'wave' => $_POST['wave'] ?? '',
                    'pulse' => $_POST['pulse'] ?? 0,
                ];
                break;

            case '1': // Fastening
            default:
                $data = [
                    'seq_name' => $_POST['seq_name'] ?? '',
                    'tightening_repeat' => $_POST['tightening_repeat'] ?? 0,
                    'timeout' => $_POST['timeout'] ?? 0,
                    'ok_seq' => $_POST['ok_seq'] ?? 0,
                    'seq_stop' => $_POST['seq_stop'] ?? 0,
                    'reverse_count_option' => $_POST['reverse_count_option'] ?? 0,
                    'ng_stop' => $_POST['ng_stop'] ?? 0,
                    'ng_reverse_option' => $_POST['ng_reverse_option'] ?? 0,
                    'accumulate_angle_option' => $_POST['accumulate_angle_option'] ?? 0,
                    'reverse_mode' => $_POST['reverse_mode'] ?? 0,
                    'speed' => $_POST['speed'] ?? 0,
                    'torque_threshold' => $_POST['torque_threshold'] ?? 0,
                    'direction' => $_POST['direction'] ?? 0,
                    'force_option' => $_POST['force_option'] ?? 0,
                    'force_number' => $_POST['force_number'] ?? 0,
                    'Thread_Calcu' => $_POST['Thread_Calcu'] ?? '',
                    'dt_time' => $_POST['dt_time'] ?? 0,
                    'tt_time' => $_POST['tt_time'] ?? 0,
                ];
                break;
        }

        $result = $this->sequenceModel->update_sequence($job_id, $seq_id, $seq_type_id, $data);

        echo json_encode([
            'res_type' => $result ? 'Success' : 'Error',
            'res_msg'  => $result ? 'Update success' : 'Update fail'
        ]);
        exit();
    }

    public function Edit_Sequence($job_id, $seq_id)
    {

        // Lấy thông tin sequence hiện tại
        $seq_data = $this->sequenceModel->get_seq_by_id($job_id, $seq_id);
        $type = $seq_data['type'] ?? 1;

        $type_names = [
            1 => 'Fastening',
            2 => 'Message',
            3 => 'Delay',
            4 => 'Input',
            5 => 'Output'
        ];

        $type_name = $type_names[$type] ?? 'Unknown';
        $isMobile = $this->isMobileCheck();

        // Các thông tin cần thiết chung
        $torque_unit_code = $this->SettingModel->Get_System_Toq_Unit();
        $unit_arr = $this->MiscellaneousModel->details('torque_unit');
        $torque_unit = $unit_arr[$torque_unit_code] ?? 'N.m';
        $decimals_arr = $this->MiscellaneousModel->details("decimals");

        $res_device = $this->SettingModel->GetControllerInfo();
        $device_torque_unit = (int)$res_device['torque_unit'];

        $tools_info = $this->ToolModel->GetToolInfo()[0] ?? [];

        if (!empty($tools_info)) {
            foreach (['max_rpm', 'min_rpm'] as $key) {
                if (isset($tools_info[$key])) {
                    $val = (float) $tools_info[$key];

                    // 檢查是否為整數
                    if (floor($val) == $val) {
                        // 若為整數 → 輸出為沒有小數的字串
                        $tools_info[$key] = (string) intval($val);
                    } else {
                        // 若有小數 → 保留原數值
                        $tools_info[$key] = (string) $val;
                    }
                }
            }

            $from_unit = 1;
            $tools_temp = $this->MiscellaneousModel->prepareToolTorqueValues($tools_info,$from_unit,$device_torque_unit,$decimals_arr);
            if(!empty($tools_temp)){
                $tools_info['max_torque'] = $tools_temp['max_torque'];
                $tools_info['min_torque'] = $tools_temp['min_torque'];
            }
        }

        // Dữ liệu chung
        $data = [
            'job_id'           => $job_id,
            'seq_id'           => $seq_id,
            'mode'             => 'edit',
            'seq_type_id'      => $type,
            'seq_type'         => $type_name,
            'seq_data'         => $seq_data,
            'tools_info'       => $tools_info,
            'torque_unit'      => $torque_unit,
            'torque_unit_code' => $torque_unit_code
        ];

        // Hiển thị view tương ứng
        switch ($type) {
            case 1: // Fastening
                $view_file = $isMobile ? 'sequences/type_fastening_seq_m' : 'sequences/type_fastening_seq';
                break;

            case 2: // Message
                $img_dir = '../public/img';
                $data['img_list'] = $this->GetImageList($img_dir);
                $view_file = $isMobile ? 'sequences/type_message_seq_m' : 'sequences/type_message_seq';
                break;

            case 3: // Delay
                $view_file = $isMobile ? 'sequences/type_delay_seq_m' : 'sequences/type_delay_seq';
                break;

            case 4: // Input
                $data['job_inputs'] = $this->sequenceModel->GetInputsByJob($job_id);
                $view_file = $isMobile ? 'sequences/type_input_seq_m' : 'sequences/type_input_seq';
                break;

            case 5: // Output
                $data['job_outputs'] = $this->sequenceModel->GetOutputsByJob($job_id);
                $view_file = $isMobile ? 'sequences/type_output_seq_m' : 'sequences/type_output_seq';
                break;

            default:
                echo "Unknown sequence type";
                exit();
        }

        $this->view($view_file, $data);
    }


    public function GetImageList($img_dir)
    {
        $files_list = scandir($img_dir);
        $filter_list = array();
        foreach ($files_list as $key => $value) {
            if (!in_array($value,array(".","..")) && strstr($value,".png") ){ //排除.跟..
                $filter_list[] = $value;
            }
        }
        return $filter_list;
    }


    //-----------------End NTCS iDAS--------------------

    public function create_seq() {
        $file = $this->MiscellaneousModel->lang_load();
        if (!empty($file)) include $file;

        file_put_contents('log_create_seq.txt', json_encode($_POST) . PHP_EOL, FILE_APPEND);

        $input_check = true;
        $error_message = '';
        $seq_data = [];

        // 共用必填欄位
        $required_common = ['job_id', 'seq_id', 'seq_type_id'];
        foreach ($required_common as $field) {
            if (!isset($_POST[$field])) {
                $input_check = false;
                $error_message .= $field . ',';
            } else {
                $key = ($field === 'seq_type_id') ? 'seq_type' : $field;
                $seq_data[$key] = $_POST[$field];
            }
        }

        if (!$input_check) {
            echo json_encode(['result' => 'fail', 'error_message' => rtrim($error_message, ',')]);
            return;
        }

        // 各類型 seq 對應欄位設定 - Gè lèixíng seq duìyìng lán wèi shèdìng
        $seq_type_fields = [
            1 => ['seq_name', 'tightening_repeat', 'timeout', 'ok_seq', 'seq_stop', 'reverse_count_option',
                'ng_stop', 'ng_reverse_option', 'accumulate_angle_option', 'reverse_mode', 'speed',
                'torque_threshold', 'direction', 'force_option', 'force_number', 'Thread_Calcu', 'dt_time', 'tt_time'],
            2 => ['seq_name', 'timeout', 'text_message', 'image'],
            3 => ['seq_name', 'timeout'],
            4 => ['seq_name', 'input_pin_no', 'wave'],
            5 => ['seq_name', 'output_pin_no', 'wave', 'pulse']
        ];

        $seq_type = (int)$seq_data['seq_type'];

        if (!isset($seq_type_fields[$seq_type])) {
            echo json_encode(['result' => 'fail', 'error_message' => 'Invalid seq_type']);
            return;
        }

        foreach ($seq_type_fields[$seq_type] as $field) {
            if (!isset($_POST[$field])) {
                $input_check = false;
                $error_message .= $field . ',';
            } else {
                if ($field === 'text_message') {
                    $seq_data['message'] = str_replace("\r\n", PHP_EOL, $_POST[$field]);
                } else {
                    $seq_data[$field] = $_POST[$field];
                }
            }
        }

        if (!$input_check) {
            echo json_encode(['result' => 'fail', 'error_message' => rtrim($error_message, ',')]);
            return;
        }

        // Model 方法對應 -Model fāngfǎ duìyìng
        $model_map = [
            1 => 'create_Seq',
            2 => 'create_img_Seq',
            3 => 'create_delay_Seq',
            4 => 'create_input_Seq',
            5 => 'create_output_Seq'
        ];

        $method = $model_map[$seq_type] ?? null;


        //var_dump($method);die();

        $res = ($method && method_exists($this->sequenceModel, $method))
            ? $this->sequenceModel->{$method}($seq_data)
            : false;

        // 回傳結果
        echo json_encode([
            'res_type' => $res ? 'Success' : 'Error',
            'res_msg'  => $res ? ($text['success'] ?? 'Success') : ($text['fail'] ?? 'Fail')
        ]);
    }



    //get job by id
    public function get_seq_by_id(){
        $input_check = true;
        //因job id是由系統指派，在create job時，抓取最前面的job id 帶入
        if( !empty($_POST['job_id']) && isset($_POST['job_id'])  ){
            $job_id = $_POST['job_id'];
        }else{ 
            $input_check = false; 
        }
        if( !empty($_POST['seq_id']) && isset($_POST['seq_id'])  ){
            $seq_id = $_POST['seq_id'];
        }else{ 
            $input_check = false; 
        }

        if($input_check){
            $sequence_data = $this->sequenceModel->get_seq_by_id($job_id,$seq_id);    
        }

        echo json_encode($sequence_data);
    }


    public function check_seq_type(){
        
        $job_id = $_POST['job_id'] ?? null;
        $seq_id = $_POST['newseqid'] ?? null;
        

        if(!empty($seq_id)){
            $res  = $this->sequenceModel->sequence_id_repeat($job_id,$seq_id);
            if($res == "True"){

            }
            echo  $res;
        }
    }

    //check_seq_enable
    public function check_seq_enable(){

        $input_check = true;

        if(!empty($_POST)){
            $seq_data = array();
            $seq_data = $_POST;
        }else{
            $input_check = false; 
        }

        if($input_check){
            
            $this->sequenceModel->update_seq_type($seq_data) ;
        }
    }

    public function copy_seq_data() {
        $file = $this->MiscellaneousModel->lang_load();
        if (!empty($file)) {
            include $file;
        }

        $job_id = $_POST['job_id'] ?? null;
        $seq_id = $_POST['seq_id'] ?? null;
        $newseqid = $_POST['newseqid'] ?? null;
        $oldseqname = $_POST['oldseqname'] ?? null;
        $newseqname = $_POST['newseqname'] ?? null;

        //用jobid 及 seqid 去找出 對應的資料
        $old_res = $this->sequenceModel->search_seqinfo($job_id,$seq_id);
        
        $this->sequenceModel->del_seq_type($job_id,$newseqid);
        $this->sequenceModel->del_step_type($job_id,$newseqid);

        $select_step = $this->sequenceModel->search_stepinfo($job_id,$seq_id);
        
        $rows = false; 

        if(!empty($old_res)){
            $new_temp_seq = array();
            foreach($old_res as $kk_seq =>$val){
                $new_temp_seq[$kk_seq]['JOBID'] = $val['JOBID'];
                $new_temp_seq[$kk_seq]['SEQID'] = $newseqid;
                $new_temp_seq[$kk_seq]['SEQname'] = $newseqname;
                $new_temp_seq[$kk_seq]['type'] = $val['type'];
                $new_temp_seq[$kk_seq]['time'] = $val['time'];
                $new_temp_seq[$kk_seq]['act'] = $val['act'];
                $new_temp_seq[$kk_seq]['skip'] = $val['skip']; 
                $new_temp_seq[$kk_seq]['seq_repeat'] = $val['seq_repeat']; 
                $new_temp_seq[$kk_seq]['ok_seq'] = $val['ok_seq']; 
                $new_temp_seq[$kk_seq]['ok_stop'] = $val['ok_stop']; 
                $new_temp_seq[$kk_seq]['countType'] = $val['countType'];
                $new_temp_seq[$kk_seq]['ok_screw'] = $val['ok_screw'];
                $new_temp_seq[$kk_seq]['unscrew_count'] = $val['unscrew_count'];
                $new_temp_seq[$kk_seq]['ng_stop'] = $val['ng_stop'];
                $new_temp_seq[$kk_seq]['ng_unscrew'] = $val['ng_unscrew'];
                $new_temp_seq[$kk_seq]['interrupt_alarm'] = $val['interrupt_alarm'];
                $new_temp_seq[$kk_seq]['accu_angle'] = $val['accu_angle'];
                $new_temp_seq[$kk_seq]['Thread_Calcu'] = $val['Thread_Calcu'];
                $new_temp_seq[$kk_seq]['unscrew_mode'] = $val['unscrew_mode'];
                $new_temp_seq[$kk_seq]['unscrew_force'] = $val['unscrew_force'];
                $new_temp_seq[$kk_seq]['unscrew_rpm'] = $val['unscrew_rpm'];
                $new_temp_seq[$kk_seq]['unscrew_dir'] = $val['unscrew_dir'];
                $new_temp_seq[$kk_seq]['unscrew_torque_threshold'] = $val['unscrew_torque_threshold'];
                $new_temp_seq[$kk_seq]['image'] = '';
                $new_temp_seq[$kk_seq]['message'] = '';
                $new_temp_seq[$kk_seq]['delay'] = $val['delay'];
                $new_temp_seq[$kk_seq]['event_id'] = $val['event_id'];
                $new_temp_seq[$kk_seq]['input_pin_no'] = $val['input_pin_no'];
                $new_temp_seq[$kk_seq]['output_pin_no'] = $val['output_pin_no'];
                $new_temp_seq[$kk_seq]['wave'] = $val['wave'];
                $new_temp_seq[$kk_seq]['wave_on'] = $val['wave_on'];
                $new_temp_seq[$kk_seq]['addtion'] = null;
                $new_temp_seq[$kk_seq]['dt_time'] = $val['dt_time'];
                $new_temp_seq[$kk_seq]['tt_time'] = $val['tt_time'];


            }  

            $rows = $this->sequenceModel->copy_seq_by_seq_id($new_temp_seq);
        } else {
            $rows = false;
            
        }
        if(!empty($select_step)){
            $new_temp_step = array();
            foreach($select_step as $k_step =>$v_step){
                $new_temp_step[$k_step]['JOBID'] = $v_step['JOBID'];
                $new_temp_step[$k_step]['SEQID'] = $newseqid;
                $new_temp_step[$k_step]['StepSelect'] = $v_step['StepSelect'];
                $new_temp_step[$k_step]['STEPname'] = $v_step['STEPname'];
                $new_temp_step[$k_step]['type'] = $v_step['type'];
                $new_temp_step[$k_step]['time'] = $v_step['time'];
                $new_temp_step[$k_step]['act'] = $v_step['act'];
                $new_temp_step[$k_step]['StepSwitch'] = $v_step['StepSwitch'];
                $new_temp_step[$k_step]['StepRPM'] = $v_step['StepRPM'];
                $new_temp_step[$k_step]['StepOption'] = $v_step['StepOption'];
                $new_temp_step[$k_step]['StepTime'] = $v_step['StepTime'];
                $new_temp_step[$k_step]['StepAngle'] = $v_step['StepAngle'];
                $new_temp_step[$k_step]['StepTorque'] = $v_step['StepTorque'];
                $new_temp_step[$k_step]['StepDirection'] = $v_step['StepDirection'];
                $new_temp_step[$k_step]['StepDelay'] = $v_step['StepDelay'];
                $new_temp_step[$k_step]['StepMoniByWin'] = $v_step['StepMoniByWin'];
                $new_temp_step[$k_step]['StepLimiHi'] = $v_step['StepLimiHi'];
                $new_temp_step[$k_step]['StepLimiLo'] = $v_step['StepLimiLo'];
                $new_temp_step[$k_step]['StepHiAngle'] = $v_step['StepHiAngle'];
                $new_temp_step[$k_step]['StepLoAngle'] = $v_step['StepLoAngle'];
                $new_temp_step[$k_step]['StepLoAngle'] = $v_step['StepLoAngle'];
                $new_temp_step[$k_step]['StepHiTorque'] = $v_step['StepHiTorque'];
                $new_temp_step[$k_step]['StepLoTorque'] = $v_step['StepLoTorque'];
                $new_temp_step[$k_step]['StepAccelerateOffset'] = $v_step['StepAccelerateOffset'];
                $new_temp_step[$k_step]['StepAccelerateOffsetSign'] = $v_step['StepAccelerateOffsetSign'];
                $new_temp_step[$k_step]['StepEnableTorqueOffset'] = $v_step['StepEnableTorqueOffset'];
                $new_temp_step[$k_step]['StepTorqueOffset'] = $v_step['StepTorqueOffset'];
                $new_temp_step[$k_step]['StepTorqueOffsetSign'] = $v_step['StepTorqueOffsetSign'];
                $new_temp_step[$k_step]['StepEnableDownShift']  = $v_step['StepEnableDownShift'];
                $new_temp_step[$k_step]['StepTorqueDownShift'] = $v_step['StepTorqueDownShift'];
                $new_temp_step[$k_step]['StepRPMDownShift'] = $v_step['StepRPMDownShift'];
                $new_temp_step[$k_step]['StepEnbaleThreshold'] = $v_step['StepEnbaleThreshold'];
                $new_temp_step[$k_step]['StepTorqueTS'] = $v_step['StepTorqueTS'];
                $new_temp_step[$k_step]['StepReTry'] = $v_step['StepReTry'];
                $new_temp_step[$k_step]['StepUnScrew'] = $v_step['StepUnScrew'];
                $new_temp_step[$k_step]['StepReTryTorq'] = $v_step['StepReTryTorq'];
                $new_temp_step[$k_step]['StepReTryAngl'] = $v_step['StepReTryAngl'];
                $new_temp_step[$k_step]['StepAngleRecord'] = $v_step['StepAngleRecord'];
                $new_temp_step[$k_step]['StepAutoDetectAngle'] = $v_step['StepAutoDetectAngle'];
                $new_temp_step[$k_step]['InterruptAlarm'] = $v_step['InterruptAlarm'];
                $new_temp_step[$k_step]['OverAngleStop'] = $v_step['OverAngleStop'];
                $new_temp_step[$k_step]['KValue'] = $v_step['KValue'];
                $new_temp_step[$k_step]['step_unit'] = $v_step['step_unit'];

            }

            $rows_temp = $this->sequenceModel->copy_step_by_seq_id($new_temp_step);
        
        }

        if($rows !== false){
            $res_type = 'Success';
            $res_msg  = $text['Copy_Sequence'].':'.$newseqid."  ".$text['success'];
        }else{
            $res_type = 'Error';
            $res_msg  = $text['Copy_Sequence'].':'.$newseqid."  ".$text['fail'];
        }

        $result = array(
            'res_type' => $res_type,
            'res_msg'  => $res_msg 
        );

        echo json_encode($result);


    }


    //delete seq
    public function delete_seq_by_id(){
        $file = $this->MiscellaneousModel->lang_load();
        if (!empty($file)) {
            include $file;
        }

        // Vẫn giữ tên biến POST là job_id và seq_id
        $job_id = $_POST['jobid'] ?? null;
        $seq_id = $_POST['seqid'] ?? null;

        if(!empty($job_id)){

            $result = array();
            
            $res = $this->sequenceModel->delete_sequence_by_job_seq_id($job_id,$seq_id);            
            if($res){
                $res_type = 'Success';
                $res_msg  = $text['del_seq'].':'. $seq_id."  ".$text['success'];
                $this->MiscellaneousModel->generateErrorResponse($res_type, $res_msg );
            }else{
                $res_type = 'Error';
                $res_msg  = $text['del_seq'].':'. $seq_id."  ".$text['fail'];
                $this->MiscellaneousModel->generateErrorResponse($res_type, $res_msg );
            }
        }
    }

    #查詢seq data - Cháxún seq data
    public function search_seqinfo(){

        $jobid = $_POST['jobid'] ?? null;
        $seqid = $_POST['seqid'] ?? null;

        if(!empty($jobid)){
            $res  = $this->sequenceModel->search_seqinfo($jobid,$seqid);
        }

        $data = array(
            'seq_info' => $res,
            'job_id' => $jobid,
            'seq_id' => $seqid
        );
    }


    #seq 排序
    public function adjustment_order(){


        if(isset($_POST['jobid'])){

            $jobid = $_POST['jobid'];
            $rowInfoArray = $_POST['rowInfoArray'];
            if(!empty($rowInfoArray)){
                $new_info = array();
                $index = 1;
                foreach ($rowInfoArray as $v_s) {
                    $new_info[$index] = $v_s;
                    $index++;
                }


                $res = $this->sequenceModel->swapupdate($jobid,$rowInfoArray,$new_info);
                
                if($res){
                    $res_msg = 'success';
                }else{
                    $res_msg = 'fail';
                }
            }
            
        }
    }

}