<?php

class Step extends Controller
{
   
    // 在建構子中將 Post 物件（Model）實例化

        private $MiscellaneousModel;
        private $stepModel;
        private $sequenceModel;
        private $SettingModel;
        private $ToolModel;
    public function __construct()
    {
        //$this->ToolModel = $this->model('Tool');
        $this->MiscellaneousModel = $this->model('Miscellaneous');
        $this->SettingModel = $this->model('Setting');
        $this->sequenceModel = $this->model('Sequence');
        //$this->stepModel = $this->model('Step');
        //$this->sequenceModel = $this->model('Sequence');
        //$this->SettingModel = $this->model('Setting');
        //$this->ToolModel = $this->model('Tool');
        
    }
    public function index($job_id,$seq_id){
   
        
        echo "eeee!!!";
    }
    
  
}