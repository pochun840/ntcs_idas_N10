<?php

class Sequence{
    private $db_iDas;

    public function __construct()
    {

        $db_instance = new Database;
        $this->db_iDas = $db_instance->getDb_das();
    }

    #取得所有sequences
    public function getSequences_by_job_id($job_id){

        $sql ="SELECT seq.*,count(ns.SEQID) as total_step FROM SEQ_lst as seq LEFT JOIN STEP_lst as ns ON seq.SEQID = ns.SEQID AND seq.JOBID = ns.JOBID WHERE seq.JOBID = '".$job_id."' group by seq.JOBID,seq.SEQID ";
        $statement = $this->db_iDas->prepare($sql);
        $statement->execute();
        return $statement->fetchall();

    }

    #透過 job_id  取得當前有幾個seq
    public function countseq($jobid ){
        $sql = "SELECT COUNT(*) as count FROM SEQ_lst WHERE JOBID = ? ";
        $statement = $this->db_iDas->prepare($sql);
        $statement->execute([$jobid]);
        $result = $statement->fetch();
        return $result['count'];
    }


    //取得job id，依job_type判斷 - Qǔdé job id, yī job_type pànduàn
    public function get_head_seq_id($job_id){

        $query = "SELECT count(*)+1 AS missing_id FROM SEQ_lst where JOBID = '".$job_id."' ";

        $statement = $this->db_iDas->prepare($query);
        $statement->execute();

        return $statement->fetch();
    }

    //get seq by job_id and seq_id
    public function get_seq_by_id($job_id,$sequence_id){

        $sql= "SELECT * FROM SEQ_lst WHERE JOBID = ? AND SEQID = ?";
        $statement = $this->db_iDas->prepare($sql);
        $results = $statement->execute([$job_id,$sequence_id]);
        $rows = $statement->fetch();

        return $rows;
    }


    public function update_sequence($job_id, $seq_id, $seq_type_id, $data)
    {
        //file_put_contents("log.txt", "seq_type_id: $seq_type_id\n", FILE_APPEND);
        $sql = '';
        $statement = null;

        switch ((int)$seq_type_id) {
            case 1: // Fastening
                $sql = "UPDATE SEQ_lst SET  
                            SEQname = :SEQname,
                            seq_repeat = :seq_repeat,
                            delay = :timeout,
                            ok_seq = :ok_seq,
                            ok_stop = :seq_stop,
                            unscrew_count = :reverse_count_option,
                            ng_stop = :ng_stop,
                            ng_unscrew = :ng_reverse_option,
                            accu_angle = :accumulate_angle_option,
                            unscrew_mode = :reverse_mode,
                            unscrew_rpm = :speed,
                            unscrew_torque_threshold = :torque_threshold,
                            unscrew_dir = :direction,
                            unscrew_force = :force_number,
                            Thread_Calcu = :Thread_Calcu,
                            dt_time = :dt_time,
                            tt_time = :tt_time
                        WHERE JOBID = :job_id AND SEQID = :seq_id";

                $statement = $this->db_iDas->prepare($sql);
                $statement->bindValue(':SEQname', $data['seq_name']);
                $statement->bindValue(':seq_repeat', $data['tightening_repeat']);
                $statement->bindValue(':timeout', $data['timeout']);
                $statement->bindValue(':ok_seq', $data['ok_seq']);
                $statement->bindValue(':seq_stop', $data['seq_stop']);
                $statement->bindValue(':reverse_count_option', $data['reverse_count_option']);
                $statement->bindValue(':ng_stop', $data['ng_stop']);
                $statement->bindValue(':ng_reverse_option', $data['ng_reverse_option']);
                $statement->bindValue(':accumulate_angle_option', $data['accumulate_angle_option']);
                $statement->bindValue(':reverse_mode', $data['reverse_mode']);
                $statement->bindValue(':speed', $data['speed']);
                $statement->bindValue(':torque_threshold', $data['torque_threshold']);
                $statement->bindValue(':direction', $data['direction']);
                $statement->bindValue(':force_number', $data['force_number']);
                $statement->bindValue(':Thread_Calcu', $data['Thread_Calcu']);
                $statement->bindValue(':dt_time', $data['dt_time']);
                $statement->bindValue(':tt_time', $data['tt_time']);
    
                break;

            case 2: // Message
                $sql = "UPDATE SEQ_lst 
                        SET SEQname = :SEQname,
                            image = :image,
                            message = :message 
                        WHERE JOBID = :job_id AND SEQID = :seq_id";

                $statement = $this->db_iDas->prepare($sql);
                $statement->bindValue(':SEQname', $data['seq_name']);
                $statement->bindValue(':image', $data['image']);
                $statement->bindValue(':message', $data['message']);
                break;

            case 3: // Delay
                $sql = "UPDATE SEQ_lst 
                        SET SEQname = :SEQname,
                            delay = :delay
                        WHERE JOBID = :job_id AND SEQID = :seq_id";

                $statement = $this->db_iDas->prepare($sql);
                $statement->bindValue(':SEQname', $data['seq_name']);
                $statement->bindValue(':delay', $data['timeout']);
                break;

            case 4: // Input
                $sql = "UPDATE SEQ_lst 
                        SET SEQname = :SEQname,
                            input_pin_no = :input_pin_no,
                            wave = :wave
                        WHERE JOBID = :job_id AND SEQID = :seq_id";

                $statement = $this->db_iDas->prepare($sql);
                $statement->bindValue(':SEQname', $data['seq_name']);
                $statement->bindValue(':input_pin_no', $data['input_pin_no']);
                $statement->bindValue(':wave', $data['wave']);
                break;

            case 5: // Output
                $sql = "UPDATE SEQ_lst 
                        SET SEQname = :SEQname,
                            output_pin_no = :output_pin_no,
                            wave = :wave,
                            wave_on = :wave_on
                        WHERE JOBID = :job_id AND SEQID = :seq_id";

                $statement = $this->db_iDas->prepare($sql);
                $statement->bindValue(':SEQname', $data['seq_name']);
                $statement->bindValue(':output_pin_no', $data['output_pin_no']);
                $statement->bindValue(':wave', $data['wave']);
                $statement->bindValue(':wave_on', $data['pulse']); // pulse == wave_on
                break;

            default:
            
                return false;
        }

        $statement->bindValue(':job_id', $job_id);
        $statement->bindValue(':seq_id', $seq_id);

        return $statement->execute();
    }


    //create and update fastening seq
    public function create_Seq($seq_data){

        if ($this->seqExists($seq_data['job_id'], $seq_data['seq_id'])) {

        }
        //check job_id exist 
        $sql = "SELECT COUNT(*) as count FROM  SEQ_lst WHERE JOBID = ? and SEQID = ? ";
        $statement = $this->db_iDas->prepare($sql);
        $results = $statement->execute([$seq_data['job_id'],$seq_data['seq_id']]);
        $results = $statement->fetch();

        // $data['sequence_enable'] = 1;//預設開啟
        // $data['sequence_mintime'] = 0;//預設是0
        

        if($results['count'] > 0){//表示job_id已存在，用update
            $sql = "UPDATE SEQ_lst SET  SEQname = :SEQname,
                                        type = :type,
                                        seq_repeat = :seq_repeat,
                                        ok_seq = :ok_seq,
                                        ok_stop = :ok_stop,
                                        unscrew_count = :unscrew_count,
                                        ng_stop = :ng_stop,
                                        ng_unscrew = :ng_unscrew,
                                        accu_angle = :accu_angle,
                                        Thread_Calcu = :Thread_Calcu,
                                        unscrew_mode = :unscrew_mode,
                                        unscrew_force = :unscrew_force,
                                        unscrew_rpm = :unscrew_rpm,
                                        unscrew_dir = :unscrew_dir,
                                        unscrew_torque_threshold = :unscrew_torque_threshold,
                                        dt_time = :dt_time,
                                        tt_time = :tt_time,
                                        delay = :delay 
                                WHERE JOBID = :job_id AND SEQID = :seq_id";
            $statement = $this->db_iDas->prepare($sql);
            $statement->bindValue(':job_id', $seq_data['job_id']);
            $statement->bindValue(':seq_id', $seq_data['seq_id']);
            $statement->bindValue(':SEQname', $seq_data['seq_name']);
            $statement->bindValue(':type', $seq_data['seq_type']);
            $statement->bindValue(':seq_repeat', $seq_data['tightening_repeat']);
            $statement->bindValue(':ok_seq', $seq_data['ok_seq']);
            $statement->bindValue(':ok_stop', $seq_data['seq_stop']);
            $statement->bindValue(':unscrew_count', $seq_data['reverse_count_option']);
            $statement->bindValue(':ng_stop', $seq_data['ng_stop']);
            $statement->bindValue(':ng_unscrew', $seq_data['ng_reverse_option']);
            $statement->bindValue(':accu_angle', $seq_data['accumulate_angle_option']);
            $statement->bindValue(':Thread_Calcu', $seq_data['Thread_Calcu']);
            $statement->bindValue(':unscrew_mode', $seq_data['reverse_mode']);
            $statement->bindValue(':unscrew_force', $seq_data['force_number']);
            $statement->bindValue(':unscrew_rpm', $seq_data['speed']);
            $statement->bindValue(':unscrew_dir', $seq_data['direction']);
            $statement->bindValue(':unscrew_torque_threshold', $seq_data['torque_threshold']);
            $statement->bindValue(':dt_time', $seq_data['dt_time']);
            $statement->bindValue(':tt_time', $seq_data['tt_time']);
            $statement->bindValue(':delay', $seq_data['timeout']);

            $results = $statement->execute();

        }else{//表示job_id & seq_id不存在，用insert
            $sql = "INSERT INTO SEQ_lst 
            ('JOBID','SEQID','SEQname','type','seq_repeat','ok_seq','ok_stop','ok_screw','unscrew_count','ng_stop',
            'ng_unscrew','accu_angle','unscrew_mode','unscrew_force','unscrew_rpm','unscrew_dir','unscrew_torque_threshold','dt_time','tt_time','delay') 
            VALUES 
            (:JOBID,:SEQID,:SEQname,:type,:seq_repeat,:ok_seq,:ok_stop,:ok_screw,:unscrew_count,
            :ng_stop,:ng_unscrew,:accu_angle,:unscrew_mode,:unscrew_force,:unscrew_rpm,:unscrew_dir,:unscrew_torque_threshold,:dt_time,:tt_time,:delay);";

            $statement = $this->db_iDas->prepare($sql);
            $statement->bindValue(':JOBID', $seq_data['job_id']);
            $statement->bindValue(':SEQID', $seq_data['seq_id']);
            $statement->bindValue(':SEQname', $seq_data['seq_name']);
            $statement->bindValue(':type', $seq_data['seq_type']);
            $statement->bindValue(':seq_repeat', $seq_data['tightening_repeat']);
            $statement->bindValue(':ok_seq', $seq_data['ok_seq']);
            $statement->bindValue(':ok_stop', $seq_data['seq_stop']);
            $statement->bindValue(':ok_screw', 1);
            $statement->bindValue(':unscrew_count', $seq_data['reverse_count_option']);
            $statement->bindValue(':ng_stop', $seq_data['ng_stop']);
            $statement->bindValue(':ng_unscrew', $seq_data['ng_reverse_option']);
            $statement->bindValue(':accu_angle', $seq_data['accumulate_angle_option']);
            $statement->bindValue(':unscrew_mode', $seq_data['reverse_mode']);
            $statement->bindValue(':unscrew_force', $seq_data['force_number']);
            $statement->bindValue(':unscrew_rpm', $seq_data['speed']);
            $statement->bindValue(':unscrew_dir', $seq_data['direction']);
            $statement->bindValue(':unscrew_torque_threshold', $seq_data['torque_threshold']);
            $statement->bindValue(':dt_time', $seq_data['dt_time']);
            $statement->bindValue(':tt_time', $seq_data['tt_time']);
            $statement->bindValue(':delay', $seq_data['timeout']);
 
            $results = $statement->execute();
            
        }

        return $results;
    }

    public function create_img_Seq($seq_data)
    {
        // Kiểm tra JOBID + SEQID đã tồn tại chưa
        $sql = "SELECT COUNT(*) as count FROM  'SEQ_lst' WHERE JOBID = ? and SEQID = ? ";
        $statement = $this->db_iDas->prepare($sql);
        $statement->execute([$seq_data['job_id'], $seq_data['seq_id']]);
        $results = $statement->fetch();

        // $data['sequence_enable'] = 1;      // mặc định mở
        // $data['sequence_mintime'] = 0;     // mặc định là 0

        if ($results['count'] > 0) {
            // ✅ Nếu đã có thì UPDATE (dành cho EDIT)
            $sql = "UPDATE SEQ_lst SET  
                        SEQname = :SEQname,
                        type = :type,
                        delay = :delay,
                        image = :image, 
                        message = :message
                    WHERE JOBID = :JOBID AND SEQID = :SEQID";

            $statement = $this->db_iDas->prepare($sql);
            $statement->bindValue(':SEQname', $seq_data['seq_name']);
            $statement->bindValue(':type', $seq_data['seq_type']);
            $statement->bindValue(':delay', $seq_data['timeout']);  // ✅ Cập nhật delay
            $statement->bindValue(':image', $seq_data['image']);
            $statement->bindValue(':message', $seq_data['message']);
            $statement->bindValue(':JOBID', $seq_data['job_id']);
            $statement->bindValue(':SEQID', $seq_data['seq_id']);

            return $statement->execute();

        } else {
            // ✅ Nếu chưa có thì INSERT (dành cho NEW)
            $sql = "INSERT INTO 'SEQ_lst' 
                    ('JOBID', 'SEQID', 'SEQname', 'type', 'seq_repeat', 'ok_seq', 'ok_stop', 'ok_screw',
                    'unscrew_count', 'ng_stop', 'ng_unscrew', 'accu_angle', 'unscrew_mode', 'unscrew_force',
                    'unscrew_rpm', 'unscrew_dir', 'unscrew_torque_threshold', 'delay', 'image', 'message', 'dt_time', 'tt_time')
                    VALUES 
                    (:JOBID, :SEQID, :SEQname, :type, :seq_repeat, :ok_seq, :ok_stop, :ok_screw,
                    :unscrew_count, :ng_stop, :ng_unscrew, :accu_angle, :unscrew_mode, :unscrew_force,
                    :unscrew_rpm, :unscrew_dir, :unscrew_torque_threshold, :delay, :image, :message, :dt_time, :tt_time)";

            $statement = $this->db_iDas->prepare($sql);
            $statement->bindValue(':JOBID', $seq_data['job_id']);
            $statement->bindValue(':SEQID', $seq_data['seq_id']);
            $statement->bindValue(':SEQname', $seq_data['seq_name']);
            $statement->bindValue(':type', $seq_data['seq_type']);
            $statement->bindValue(':seq_repeat', 1);
            $statement->bindValue(':ok_seq', 1);
            $statement->bindValue(':ok_stop', 0);
            $statement->bindValue(':ok_screw', 1);
            $statement->bindValue(':unscrew_count', 1);
            $statement->bindValue(':ng_stop', 0);
            $statement->bindValue(':ng_unscrew', 1);
            $statement->bindValue(':accu_angle', 1);
            $statement->bindValue(':unscrew_mode', 1);
            $statement->bindValue(':unscrew_force', 50);
            $statement->bindValue(':unscrew_rpm', 300);
            $statement->bindValue(':unscrew_dir', 0);
            $statement->bindValue(':unscrew_torque_threshold', 0);
            $statement->bindValue(':delay', $seq_data['timeout']);
            $statement->bindValue(':image', $seq_data['image']);
            $statement->bindValue(':message', $seq_data['message']);
            $statement->bindValue(':dt_time', 0);
            $statement->bindValue(':tt_time', 0);
 
            return $statement->execute();
        }
    }

    public function create_delay_Seq($seq_data)
    {
        //check job_id exist 
        $sql = "SELECT COUNT(*) as count FROM  'SEQ_lst' WHERE JOBID = ? and SEQID = ? ";
        $statement = $this->db_iDas->prepare($sql);
        $results = $statement->execute([$seq_data['job_id'],$seq_data['seq_id']]);
        $results = $statement->fetch();

        // $data['sequence_enable'] = 1;//預設開啟
        // $data['sequence_mintime'] = 0;//預設是0

        if($results['count'] > 0){//表示job_id已存在，用update
            $sql = "UPDATE SEQ_lst SET  SEQname = :SEQname,
                                        delay = :delay 
                                WHERE JOBID = :job_id AND SEQID = :seq_id";
            $statement = $this->db_iDas->prepare($sql);
            $statement->bindValue(':job_id', $seq_data['job_id']);
            $statement->bindValue(':seq_id', $seq_data['seq_id']);
            $statement->bindValue(':SEQname', $seq_data['seq_name']);
            $statement->bindValue(':delay', $seq_data['timeout']);
            $results = $statement->execute();

        }else{//表示job_id & seq_id不存在，用insert

            $sql = "INSERT INTO 'SEQ_lst' (
                        'JOBID','SEQID','SEQname','type','seq_repeat',
                        'ok_seq','ok_stop','ok_screw','unscrew_count',
                        'ng_stop','ng_unscrew','accu_angle','unscrew_mode',
                        'unscrew_force','unscrew_rpm','unscrew_dir',
                        'unscrew_torque_threshold','delay', 'dt_time', 'tt_time'
                    ) VALUES (
                        :JOBID,:SEQID,:SEQname,:type,:seq_repeat,
                        :ok_seq,:ok_stop,:ok_screw,:unscrew_count,
                        :ng_stop,:ng_unscrew,:accu_angle,:unscrew_mode,
                        :unscrew_force,:unscrew_rpm,:unscrew_dir,
                        :unscrew_torque_threshold,:delay, :dt_time, :tt_time
                    ); ";

            $statement = $this->db_iDas->prepare($sql);
            $statement->bindValue(':JOBID', $seq_data['job_id']);
            $statement->bindValue(':SEQID', $seq_data['seq_id']);
            $statement->bindValue(':SEQname', $seq_data['seq_name']);
            $statement->bindValue(':type', $seq_data['seq_type']);
            $statement->bindValue(':seq_repeat', 1);
            $statement->bindValue(':ok_seq', 1);
            $statement->bindValue(':ok_stop', 0);
            $statement->bindValue(':ok_screw', 1);//???
            $statement->bindValue(':unscrew_count', 1);
            $statement->bindValue(':ng_stop', 0);
            $statement->bindValue(':ng_unscrew', 1);
            $statement->bindValue(':accu_angle', 1);
            $statement->bindValue(':unscrew_mode', 1);
            $statement->bindValue(':unscrew_force', 50);
            $statement->bindValue(':unscrew_rpm', 300);
            $statement->bindValue(':unscrew_dir', 0);
            $statement->bindValue(':unscrew_torque_threshold', 0);
            $statement->bindValue(':delay', $seq_data['timeout']);
            $statement->bindValue(':dt_time', 0);
            $statement->bindValue(':tt_time', 0);

            $results = $statement->execute();
            
        }


        return $results;
    }

    public function create_input_Seq($seq_data)
    {
        //check job_id exist 
        $sql = "SELECT COUNT(*) as count FROM SEQ_lst WHERE JOBID = ? and SEQID = ? ";
        $statement = $this->db_iDas->prepare($sql);
        $results = $statement->execute([$seq_data['job_id'],$seq_data['seq_id']]);
        $results = $statement->fetch();

        // $data['sequence_enable'] = 1;//預設開啟
        // $data['sequence_mintime'] = 0;//預設是0

        if($results['count'] > 0){//表示job_id已存在，用update
            $sql = "UPDATE SEQ_lst SET  SEQname = :SEQname,
                                        input_pin_no = :input_pin_no,
                                        wave = :wave  
                                WHERE JOBID = :job_id AND SEQID = :seq_id";
            $statement = $this->db_iDas->prepare($sql);
            $statement->bindValue(':job_id', $seq_data['job_id']);
            $statement->bindValue(':seq_id', $seq_data['seq_id']);
            $statement->bindValue(':SEQname', $seq_data['seq_name']);
            $statement->bindValue(':input_pin_no', $seq_data['input_pin_no']);
            $statement->bindValue(':wave', $seq_data['wave']);
            $results = $statement->execute();

        }else{//表示job_id & seq_id不存在，用insert

            $sql = "INSERT INTO 'SEQ_lst' (
                        'JOBID','SEQID','SEQname','type','seq_repeat',
                        'ok_seq','ok_stop','ok_screw','unscrew_count',
                        'ng_stop','ng_unscrew','accu_angle','unscrew_mode',
                        'unscrew_force','unscrew_rpm','unscrew_dir',
                        'unscrew_torque_threshold','delay','input_pin_no',
                        'wave','event_id', 'dt_time', 'tt_time'
                    ) VALUES (
                        :JOBID,:SEQID,:SEQname,:type,:seq_repeat,:ok_seq,
                        :ok_stop,:ok_screw,:unscrew_count,:ng_stop,
                        :ng_unscrew,:accu_angle,:unscrew_mode,:unscrew_force,
                        :unscrew_rpm,:unscrew_dir,:unscrew_torque_threshold,
                        :delay,:input_pin_no,:wave,:event_id, :dt_time, :tt_time 
                    ); ";

            $statement = $this->db_iDas->prepare($sql);
            $statement->bindValue(':JOBID', $seq_data['job_id']);
            $statement->bindValue(':SEQID', $seq_data['seq_id']);
            $statement->bindValue(':SEQname', $seq_data['seq_name']);
            $statement->bindValue(':type', $seq_data['seq_type']);
            $statement->bindValue(':seq_repeat', 1);
            $statement->bindValue(':ok_seq', 1);
            $statement->bindValue(':ok_stop', 0);
            $statement->bindValue(':ok_screw', 1);//???
            $statement->bindValue(':unscrew_count', 1);
            $statement->bindValue(':ng_stop', 0);
            $statement->bindValue(':ng_unscrew', 1);
            $statement->bindValue(':accu_angle', 1);
            $statement->bindValue(':unscrew_mode', 1);
            $statement->bindValue(':unscrew_force', 50);
            $statement->bindValue(':unscrew_rpm', 300);
            $statement->bindValue(':unscrew_dir', 0);
            $statement->bindValue(':unscrew_torque_threshold', 0);
            $statement->bindValue(':delay', 3);
            $statement->bindValue(':input_pin_no', $seq_data['input_pin_no']);
            $statement->bindValue(':wave', $seq_data['wave']);
            $statement->bindValue(':event_id', 1000);
            $statement->bindValue(':dt_time', 0);
            $statement->bindValue(':tt_time', 0);

            $results = $statement->execute();
            
        }

        return $results;
    }

    public function create_output_Seq($seq_data)
    {
        //check job_id exist 
        $sql = "SELECT COUNT(*) as count FROM  SEQ_lst WHERE JOBID = ? and SEQID = ? ";
        $statement = $this->db_iDas->prepare($sql);
        $results = $statement->execute([$seq_data['job_id'],$seq_data['seq_id']]);
        $results = $statement->fetch();

        // $data['sequence_enable'] = 1;//預設開啟
        // $data['sequence_mintime'] = 0;//預設是0

        if($results['count'] > 0){//表示job_id已存在，用update
            $sql = "UPDATE SEQ_lst SET  SEQname = :SEQname,
                                        output_pin_no = :output_pin_no,
                                        wave = :wave,
                                        wave_on = :wave_on 
                                WHERE JOBID = :job_id AND SEQID = :seq_id";
            $statement = $this->db_iDas->prepare($sql);
            $statement->bindValue(':job_id', $seq_data['job_id']);
            $statement->bindValue(':seq_id', $seq_data['seq_id']);
            $statement->bindValue(':SEQname', $seq_data['seq_name']);
            $statement->bindValue(':output_pin_no', $seq_data['output_pin_no']);
            $statement->bindValue(':wave', $seq_data['wave']);
            $statement->bindValue(':wave_on', $seq_data['pulse']);
            $results = $statement->execute();

        }else{//表示job_id & seq_id不存在，用insert

            $sql = "INSERT INTO SEQ_lst (
                        'JOBID','SEQID','SEQname','type','seq_repeat',
                        'ok_seq','ok_stop','ok_screw','unscrew_count',
                        'ng_stop','ng_unscrew','accu_angle','unscrew_mode',
                        'unscrew_force','unscrew_rpm','unscrew_dir',
                        'unscrew_torque_threshold','delay','output_pin_no',
                        'wave','wave_on','event_id', 'dt_time', 'tt_time'
                    ) VALUES (
                        :JOBID,:SEQID,:SEQname,:type,:seq_repeat,:ok_seq,
                        :ok_stop,:ok_screw,:unscrew_count,:ng_stop,
                        :ng_unscrew,:accu_angle,:unscrew_mode,:unscrew_force,
                        :unscrew_rpm,:unscrew_dir,:unscrew_torque_threshold,
                        :delay,:output_pin_no,:wave,:wave_on,:event_id, :dt_time, :tt_time
                    ); ";

            $statement = $this->db_iDas->prepare($sql);
            $statement->bindValue(':JOBID', $seq_data['job_id']);
            $statement->bindValue(':SEQID', $seq_data['seq_id']);
            $statement->bindValue(':SEQname', $seq_data['seq_name']);
            $statement->bindValue(':type', $seq_data['seq_type']);
            $statement->bindValue(':seq_repeat', 1);
            $statement->bindValue(':ok_seq', 1);
            $statement->bindValue(':ok_stop', 0);
            $statement->bindValue(':ok_screw', 1);//???
            $statement->bindValue(':unscrew_count', 1);
            $statement->bindValue(':ng_stop', 0);
            $statement->bindValue(':ng_unscrew', 1);
            $statement->bindValue(':accu_angle', 1);
            $statement->bindValue(':unscrew_mode', 1);
            $statement->bindValue(':unscrew_force', 50);
            $statement->bindValue(':unscrew_rpm', 300);
            $statement->bindValue(':unscrew_dir', 0);
            $statement->bindValue(':unscrew_torque_threshold', 0);
            $statement->bindValue(':delay', 3);
            $statement->bindValue(':output_pin_no', $seq_data['output_pin_no']);
            $statement->bindValue(':wave', $seq_data['wave']);
            $statement->bindValue(':wave_on', $seq_data['pulse']);
            $statement->bindValue(':event_id', 2000);
            $statement->bindValue(':dt_time', 0);
            $statement->bindValue(':tt_time', 0);

            $results = $statement->execute();
            
        }

        return $results;
    }


    #修改單筆的sequence的狀態 - Xiūgǎi dān bǐ de sequence de zhuàngtài
    public function check_seq_type($job_id, $seq_id, $type_value) {
        $sql = "UPDATE SEQ_lst SET act = :act WHERE JOBID = :JOBID AND SEQID = :SEQID ";
        $statement = $this->db_iDas->prepare($sql);
    
        $statement->bindValue(':act', $type_value);
        $statement->bindValue(':JOBID', $job_id);
        $statement->bindValue(':SEQID', $seq_id);
        
        $success = $statement->execute();    
        return $success;
    }

    public function update_seq_type($seq_data) {

        $sql = "UPDATE `SEQ_lst` SET skip = :skip  WHERE JOBID = :JOBID AND SEQID = :SEQID ";
        $statement = $this->db_iDas->prepare($sql);
    
        $statement->bindValue(':skip', $seq_data['skip']);
        $statement->bindValue(':JOBID', $seq_data['jobid']);
        $statement->bindValue(':SEQID', $seq_data['seqid']);
        
        $success = $statement->execute();    
        return $success;
    }

    #用jobid seqid oldseqname 查詢該筆的所有資料 - Yòng jobid seqid oldseqname cháxún gāi bǐ de suǒyǒu zīliào
    public function search_old_data($job_id,$seq_id,$oldseqname){

        $sql= " SELECT * FROM SEQ_lst WHERE JOBID = ? AND SEQID = ? AND SEQname = ? ";
        $statement = $this->db_iDas->prepare($sql);
        $statement->execute([$job_id,$seq_id,$oldseqname]);
        $rows = $statement->fetch();

        return $rows;
    }
    

    public function copy_seq_by_seq_id($new_temp_seq) {

        $sql = "INSERT INTO 'SEQ_lst' 
                (JOBID, SEQID, SEQname, type, time, act, skip, seq_repeat,
                ok_seq, ok_stop, countType, ok_screw, unscrew_count, ng_stop, ng_unscrew, interrupt_alarm, 
                accu_angle, Thread_Calcu, unscrew_mode, unscrew_force, unscrew_rpm, unscrew_dir, unscrew_torque_threshold, 
                image, message, delay, event_id, input_pin_no, output_pin_no, wave, wave_on, addtion, dt_time, tt_time) 
                VALUES 
                (:JOBID, :SEQID, :SEQname, :type, :time, :act, :skip, :seq_repeat,
                :ok_seq, :ok_stop, :countType, :ok_screw, :unscrew_count, :ng_stop, :ng_unscrew, :interrupt_alarm, 
                :accu_angle, :Thread_Calcu, :unscrew_mode, :unscrew_force, :unscrew_rpm, :unscrew_dir, :unscrew_torque_threshold, 
                :image, :message, :delay, :event_id, :input_pin_no, :output_pin_no, :wave, :wave_on, :addtion, :dt_time, :tt_time);";

        $statement = $this->db_iDas->prepare($sql);
        $insertedrecords = 0;

        foreach ($new_temp_seq as $seq) {
            try {
                // 過濾掉數字索引
                $seq = array_filter($seq, 'is_string', ARRAY_FILTER_USE_KEY);

                // 將空字串轉為 NULL，或給預設值（例如 0）
                $integerFields = [
                    'event_id',
                    'addtion',
                    'delay',
                    'input_pin_no',
                    'output_pin_no',
                    'unscrew_torque_threshold',
                    'wave',
                    'wave_on',
                    'ng_stop',
                    'unscrew_count',
                    'ok_seq',
                    'ok_stop',
                    'countType',
                    'ok_screw',
                    'ng_unscrew',
                    'interrupt_alarm',
                    'accu_angle',
                    'unscrew_mode',
                    'unscrew_force',
                    'unscrew_rpm',
                    'unscrew_dir',
                    'seq_repeat',
                    'act',
                    'skip',
                    'type',
                    'Thread_Calcu',
                    'JOBID',
                    'SEQID'
                ];

                foreach ($integerFields as $field) {
                    if (array_key_exists($field, $seq)) {
                        if ($seq[$field] === '') {
                            $seq[$field] = null;
                        }
                    }
                }

                // 同樣處理 image 和 message，如空字串就存 NULL
                foreach (['image', 'message'] as $field) {
                    if (array_key_exists($field, $seq)) {
                        if ($seq[$field] === '') {
                            $seq[$field] = null;
                        }
                    }
                }

                // 不要用 isset()，改用 array_key_exists()
                $requiredKeys = [
                    'JOBID', 'SEQID', 'SEQname', 'type', 'time',
                    'act', 'skip', 'seq_repeat', 'ok_seq',
                    'ok_stop', 'countType', 'ok_screw', 'unscrew_count', 'ng_stop',
                    'ng_unscrew', 'interrupt_alarm', 'accu_angle', 'Thread_Calcu',
                    'unscrew_mode', 'unscrew_force', 'unscrew_rpm', 'unscrew_dir',
                    'image', 'message', 'delay', 'event_id', 'input_pin_no',
                    'output_pin_no', 'addtion', 'wave', 'unscrew_torque_threshold', 'wave_on'
                ];

                $missing = [];
                foreach ($requiredKeys as $key) {
                    if (!array_key_exists($key, $seq)) {
                        $missing[] = $key;
                    }
                }

                if (empty($missing)) {
                    if ($statement->execute($seq)) {
                        $insertedrecords++;
                    } else {
                        $errorInfo = $statement->errorInfo();
                        echo "SQL Error: " . $errorInfo[2] . "\n";
                    }
                } else {
                    echo "缺少必要字段: " . implode(', ', $missing) . "\n";
                    echo "資料: " . print_r($seq, true);
                }

            } catch (PDOException $e) {
                echo "PDOException: " . $e->getMessage();
            }
        }

        return $insertedrecords;
    }

    
    public function copy_step_by_seq_id($new_temp_step){

        $sql = "INSERT INTO STEP_lst (
            JOBID, SEQID, StepSelect, STEPname, type, time, act, 
            StepSwitch, StepRPM, StepOption, StepTime, StepAngle, StepTorque, 
            StepDirection, StepDelay, StepMoniByWin, StepLimiHi, StepLimiLo, 
            StepHiAngle, StepLoAngle, StepHiTorque, StepLoTorque, StepAccelerateOffset, 
            StepAccelerateOffsetSign, StepEnableTorqueOffset, StepTorqueOffset, 
            StepTorqueOffsetSign, StepEnableDownShift, StepTorqueDownShift, 
            StepRPMDownShift, StepEnbaleThreshold, StepTorqueTS, StepReTry, 
            StepUnScrew, StepReTryTorq, StepReTryAngl, StepAngleRecord, 
            StepAutoDetectAngle, InterruptAlarm, OverAngleStop, KValue, step_unit
        ) VALUES (
            :JOBID, :SEQID, :StepSelect, :STEPname, :type, :time, :act, 
            :StepSwitch, :StepRPM, :StepOption, :StepTime, :StepAngle, :StepTorque, 
            :StepDirection, :StepDelay, :StepMoniByWin, :StepLimiHi, :StepLimiLo, 
            :StepHiAngle, :StepLoAngle, :StepHiTorque, :StepLoTorque, :StepAccelerateOffset, 
            :StepAccelerateOffsetSign, :StepEnableTorqueOffset, :StepTorqueOffset, 
            :StepTorqueOffsetSign, :StepEnableDownShift, :StepTorqueDownShift, 
            :StepRPMDownShift, :StepEnbaleThreshold, :StepTorqueTS, :StepReTry, 
            :StepUnScrew, :StepReTryTorq, :StepReTryAngl, :StepAngleRecord, 
            :StepAutoDetectAngle, :InterruptAlarm, :OverAngleStop, :KValue, :step_unit
        )";


        $statement = $this->db_iDas->prepare($sql);
        $insertedrecords = 0;

        foreach ($new_temp_step as $step) {
            try {

                if ($statement->execute([
                    ':JOBID' => $step['JOBID'],
                    ':SEQID' => $step['SEQID'],
                    ':StepSelect' => $step['StepSelect'],
                    ':STEPname' => $step['STEPname'],
                    ':type' => $step['type'],
                    ':time' => $step['time'],
                    ':act' => $step['act'],
                    ':StepSwitch' => $step['StepSwitch'],
                    ':StepRPM' => $step['StepRPM'],
                    ':StepOption' => $step['StepOption'],
                    ':StepTime' => $step['StepTime'],
                    ':StepAngle' => $step['StepAngle'],
                    ':StepTorque' => $step['StepTorque'],
                    ':StepDirection' => $step['StepDirection'],
                    ':StepDelay' => $step['StepDelay'],
                    ':StepMoniByWin' => $step['StepMoniByWin'],
                    ':StepLimiHi' => $step['StepLimiHi'],
                    ':StepLimiLo' => $step['StepLimiLo'],
                    ':StepHiAngle' => $step['StepHiAngle'],
                    ':StepLoAngle' => $step['StepLoAngle'],
                    ':StepHiTorque' => $step['StepHiTorque'],
                    ':StepLoTorque' => $step['StepLoTorque'],
                    ':StepAccelerateOffset' => $step['StepAccelerateOffset'],
                    ':StepAccelerateOffsetSign' => $step['StepAccelerateOffsetSign'],
                    ':StepEnableTorqueOffset' => $step['StepEnableTorqueOffset'],
                    ':StepTorqueOffset' => $step['StepTorqueOffset'],
                    ':StepTorqueOffsetSign' => $step['StepTorqueOffsetSign'],
                    ':StepEnableDownShift' => $step['StepEnableDownShift'],
                    ':StepTorqueDownShift' => $step['StepTorqueDownShift'],
                    ':StepRPMDownShift' => $step['StepRPMDownShift'],
                    ':StepEnbaleThreshold' => $step['StepEnbaleThreshold'],
                    ':StepTorqueTS' => $step['StepTorqueTS'],
                    ':StepReTry' => $step['StepReTry'],
                    ':StepUnScrew' => $step['StepUnScrew'],
                    ':StepReTryTorq' => $step['StepReTryTorq'],
                    ':StepReTryAngl' => $step['StepReTryAngl'],
                    ':StepAngleRecord' => $step['StepAngleRecord'],
                    ':StepAutoDetectAngle' => $step['StepAutoDetectAngle'],
                    ':InterruptAlarm' => $step['InterruptAlarm'],
                    ':OverAngleStop' => $step['OverAngleStop'],
                    ':KValue' => $step['KValue'],
                    ':step_unit' => isset($step['step_unit']) ? $step['step_unit'] : 0,
                ])) {

                    $insertedrecords++;
                } else {

                    error_log("Failed to execute query for JOBID: " . $step['JOBID'] . " SEQID: " . $step['SEQID']);
                }
            } catch (Exception $e) {
                error_log("Error inserting record: " . $e->getMessage());
            }
        }

        return $insertedrecords;
    }    

    //delete sequence by id
    public function delete_sequence_by_job_seq_id($job_id, $seq_id) {

        try {
            // 開始交易
            $this->db_iDas->beginTransaction();

            // 1) 先刪該序列的所有步驟
            $sqlStep = "DELETE FROM STEP_lst WHERE JOBID = ? AND SEQID = ?";
            $stmtStep = $this->db_iDas->prepare($sqlStep);
            $stmtStep->execute([$job_id, $seq_id]);

            // 2) 再刪該序列本身
            $sqlSeq = "DELETE FROM SEQ_lst WHERE JOBID = ? AND SEQID = ?";
            $stmtSeq = $this->db_iDas->prepare($sqlSeq);
            $stmtSeq->execute([$job_id, $seq_id]);

            // 3) 若不是保留的 100，就把後面的 SEQID 整體往前補位
            if ((int)$seq_id !== 100) {
                // 3a) 重新編號序列
                $sqlUpdateSeq = "UPDATE SEQ_lst SET SEQID = SEQID - 1 WHERE JOBID = ? AND SEQID > ?";
                $stmtUpdateSeq = $this->db_iDas->prepare($sqlUpdateSeq);
                $stmtUpdateSeq->execute([$job_id, $seq_id]);

                // 3b) 重新編號步驟（若未使用外鍵 ON UPDATE CASCADE，這步很重要）
                $sqlUpdateStep = "UPDATE STEP_lst SET SEQID = SEQID - 1 WHERE JOBID = ? AND SEQID > ?";
                $stmtUpdateStep = $this->db_iDas->prepare($sqlUpdateStep);
                $stmtUpdateStep->execute([$job_id, $seq_id]);
            }

            // 送交
            $this->db_iDas->commit();
            return true;

        } catch (Exception $e) {
            // 回滾
            if ($this->db_iDas->inTransaction()) {
                $this->db_iDas->rollBack();
            }
            // 你可視需求記錄 log
            // error_log("delete_sequence failed: " . $e->getMessage());
            return false;
        }
    }

    //get inputs by job
    public function GetInputsByJob($job_id)
    {
        $sql= "SELECT * FROM ntcs_io_input WHERE job_id = ?";
        $statement = $this->db_iDas->prepare($sql);
        $results = $statement->execute([$job_id]);

        return $statement->fetchall(PDO::FETCH_ASSOC);
    }

    //get inputs by job
    public function GetOutputsByJob($job_id)
    {
        $sql= "SELECT * FROM ntcs_io_output WHERE job_id = ?";
        $statement = $this->db_iDas->prepare($sql);
        $results = $statement->execute([$job_id]);

        return $statement->fetchall(PDO::FETCH_ASSOC);
    }

    
    
    public function swapupdate($jobid, $rowInfoArray, $new_info) {
        $this->db_iDas->beginTransaction();

        try {
            $temp_ids = [];
            $temp_start = 9999;

            // Step 1: 原始 SEQID → 暫存 SEQID（避免衝突）
            foreach ($rowInfoArray as $v_s) {
                $old_seq_id = (int)$v_s['sequence_id'];

                // 已經是 999x 的暫存值就跳過
                if ($old_seq_id >= 9990) {
                    $temp_ids[$old_seq_id] = $old_seq_id;
                    continue;
                }

                $temp_seq_id = $temp_start--;
                $temp_ids[$old_seq_id] = $temp_seq_id;

                // SEQ_lst → 暫存
                $this->db_iDas->prepare("UPDATE SEQ_lst SET SEQID = ? WHERE JOBID = ? AND SEQID = ?")
                    ->execute([$temp_seq_id, $jobid, $old_seq_id]);

                // STEP_lst → 暫存
                $this->db_iDas->prepare("UPDATE STEP_lst SET SEQID = ? WHERE JOBID = ? AND SEQID = ?")
                    ->execute([$temp_seq_id, $jobid, $old_seq_id]);
            }

            // Step 2: 暫存 SEQID → 最終順序 SEQID（從 1 開始）
            $final_seqid = 1;
            foreach ($new_info as $v_s) {
                $original_seq_id = (int)$v_s['sequence_id'];
                $temp_seq_id = $temp_ids[$original_seq_id] ?? $original_seq_id;

                // SEQ_lst
                $this->db_iDas->prepare("UPDATE SEQ_lst SET SEQID = ? WHERE JOBID = ? AND SEQID = ?")
                    ->execute([$final_seqid, $jobid, $temp_seq_id]);

                // STEP_lst
                $this->db_iDas->prepare("UPDATE STEP_lst SET SEQID = ? WHERE JOBID = ? AND SEQID = ?")
                    ->execute([$final_seqid, $jobid, $temp_seq_id]);

                $final_seqid++;
            }

            // Step 3: 自動修復殘留 999x SEQID
            $fix_seqid = $final_seqid;

            $sql = "SELECT SEQID FROM SEQ_lst WHERE JOBID = ? AND SEQID >= 9990";
            $statement = $this->db_iDas->prepare($sql);
            $statement->execute([$jobid]);
            $leftovers = $statement->fetchAll(PDO::FETCH_COLUMN);

            foreach ($leftovers as $left_seqid) {
                // SEQ_lst
                $this->db_iDas->prepare("UPDATE SEQ_lst SET SEQID = ? WHERE JOBID = ? AND SEQID = ?")
                    ->execute([$fix_seqid, $jobid, $left_seqid]);

                // STEP_lst
                $this->db_iDas->prepare("UPDATE STEP_lst SET SEQID = ? WHERE JOBID = ? AND SEQID = ?")
                    ->execute([$fix_seqid, $jobid, $left_seqid]);

                $fix_seqid++;
            }

            $this->db_iDas->commit();
            return true;

        } catch (Exception $e) {
            $this->db_iDas->rollBack();
            error_log("swapupdate error: " . $e->getMessage());
            return false;
        }
    }






    #驗證seq id是否重複
    public function sequence_id_repeat($job_id, $seq_id)
    {
        $sql = "SELECT count(*) as count FROM SEQ_lst WHERE JOBID = ? AND SEQID = ?";
        $statement = $this->db_iDas->prepare($sql);
        $results = $statement->execute([$job_id, $seq_id]);
        $rows = $statement->fetch();

        if ($rows && $rows['count'] > 0) {
            // Nếu có thì xóa step
            $sql_d = "DELETE FROM step WHERE JOBID = ? AND SEQID = ?";
            $statement = $this->db_iDas->prepare($sql_d);
            $results_d = $statement->execute([$job_id, $seq_id]);

            return "True"; // sequence_id đã tồn tại
        } else {
            return "False"; // sequence_id không tồn tại
        }
    }


    #用 $job_id,$new_seqid 尋找有沒有對應的資料 - Yòng $job_id,$new_seqid xúnzhǎo yǒu méiyǒu duìyìng de zīliào
    #有的話就刪除唷 - Yǒu de huà jiù shānchú yō
    public function del_seq_type($job_id, $seq_id) {

        $sql= " DELETE FROM SEQ_lst WHERE JOBID = ? AND SEQID = ? ";
        $statement = $this->db_iDas->prepare($sql);
        $results = $statement->execute([$job_id, $seq_id]);

        if ($seq_id != 100 ) {
            $sql_update = "UPDATE SEQ_lst  SET SEQID = SEQID - 1 WHERE JOBID = ? AND SEQID > ?";
            $statement_update = $this->db_iDas->prepare($sql_update);
            $statement_update->execute([$job_id, $seq_id]);
        }   
        return $results;

        // #查詢資料是否存在
        // $sql = "SELECT COUNT(*) FROM SEQ_lst WHERE JOBID = ? AND SEQID = ?";
        // $statement = $this->db_iDas->prepare($sql);
        // $statement->execute([$job_id, $seq_id]);
        // $count = $statement->fetchColumn();
        // $count = intval($count);
       
        // //var_dump($count);
        // //die();
        // if ($count > 0) {
        //     #如果資料存在，則刪除
        //     $deleteSql = "DELETE FROM SEQ_lst  WHERE JOBID = ? AND SEQID = ?";
        //     $deleteStatement = $this->db_iDas->prepare($deleteSql);
        //     $deleteStatement->execute([$job_id, $seq_id]);
    
        //     return true;
        // } else {
        //     return false;
        // }
    }

    public function del_step_type($job_id, $seq_id){

        $sql= "DELETE FROM STEP_lst WHERE  JOBID = ? AND SEQID = ? ";
        $statement = $this->db_iDas->prepare($sql);
        $results = $statement->execute([$job_id, $seq_id]);

        return $results;
    }

    #查詢 單筆的sequences
    public function search_seqinfo($job_id,$seq_id){

        $sql= " SELECT *  FROM SEQ_lst WHERE JOBID = ? AND SEQID = ? ";
        $statement = $this->db_iDas->prepare($sql);
        $statement->execute([$job_id, $seq_id]);
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);

    }


    public function search_stepinfo($job_id,$seq_id){

        $sql= " SELECT *  FROM STEP_lst WHERE JOBID = ? AND SEQID = ? ";
        $statement = $this->db_iDas->prepare($sql);
        $statement->execute([$job_id,$seq_id]);
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);

    }


    // ✅ 共用查詢 Function
    private function seqExists($job_id, $seq_id) {
        $sql = "SELECT COUNT(*) as count FROM SEQ_lst WHERE JOBID = ? AND SEQID = ?";
        $statement = $this->db_iDas->prepare($sql);
        $statement->execute([$job_id, $seq_id]);
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

}
