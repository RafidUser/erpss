<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class transaksi_queue_task extends MX_Controller {
    private $sess_auth;
    private $dbset;
    function __construct() {
        parent::__construct();
        $this->sess_auth = new Zend_Session_Namespace('auth'); 
		$this->load->library('auth');
		$this->load->library('lib_utilitas');
		$this->user = $this->auth->user();
        $this->dbset = $this->load->database('hrd', true);
        $this->url = 'transaksi_queue_task'; 
		$this->_table = 'ss.raw_problems';
		$this->_table2 = 'ss.support_type';
		$this->_table3 = 'ss.task_scheduling';
		$this->_table4 = 'ss.task_scheduling_detail';
    }
    
    function index($action = '') {
    	$action = $this->input->get('action');
		
		
    	//Bikin Object Baru Nama nya $grid		
        $grid = new Grid;		
        $grid->setTitle('Support Request');		
        $grid->setTable($this->_table);		
        $grid->setUrl('transaksi_queue_task');
        $grid->addList('Priority','problem_subject','pic','project','deadline','role','size','start','finish','schedule','status','action');//'lPersen', 'yPersen',
	//	$grid->addFields('Priority','problem_subject','project','deadline','role','size','start','finish','schedule','status','action');//'lPersen', 'yPersen',
        
		$grid->setJoinTable($this->_table2, $this->_table2.'.typeId = '.$this->_table.'.typeId', 'inner');
		$grid->setJoinTable($this->_table3, $this->_table3.'.iSSID = '.$this->_table.'.id', 'inner');
		$grid->setJoinTable($this->_table4, $this->_table4.'.iSSID = '.$this->_table.'.id', 'inner');
	
		
		$grid->setQuery('ss.raw_problems.pic like "%'.$this->user->gNIP.'%"', null );
		
		$grid->setSortBy('id');
		$grid->setSortOrder('desc'); //sort ordernya
		$grid->setSearch('pic');
		$grid->setSearch('problem_subject');
		//$grid->setSearch('problem_subject');
		
		$grid->setAlign('Priority', 'left');
		$grid->setWidth('Priority', '30');
		$grid->setLabel('Priority', 'Priority');
		
		$grid->setAlign('problem_subject', 'left');
		$grid->setWidth('problem_subject', '300');
		$grid->setLabel('problem_subject', 'Problem Subject');
		
		$grid->setAlign('cPIC', 'left');
		$grid->setWidth('cPIC', '200');
		$grid->setLabel('cPIC', 'PIC');
        $grid->setGridView('grid');

        switch ($action) {
                case 'json':
                        $grid->getJsonData();
                        break;
                case 'view':
                        $grid->render_form($this->input->get('id'), true);
                        break;
                case 'create':
                        $grid->render_form();
                        break;
                case 'createproses':
					echo $grid->saved_form();
					break;
                case 'update':
                        $grid->render_form($this->input->get('id'));
                        break;
                case 'updateproses':								
					echo $grid->updated_form();
					break;
                case 'delete':
                        echo $grid->delete_row();
                        break;
                default:
                        $grid->render_grid();
                        break;
        }
    }   
	function searchbox_transaksi_queue_task_pic($field, $id) {
	
			$cNip = $this->user->gNIP;
	
			$o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value=''>Pilih</option>";
			$this->db->select('cNip, vName');
			$this->db->from('hrd.employee');
			$this->db->where('iDeptId',6);
			$this->db->where('dresign <',date('Y-m-d', strtotime('-90 days', strtotime(date('Y-m-d')))));
			
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as  $row) {     
					if ($cNip == $row['cNip']) $selected = " selected";
					else $selected = '';
                       $o .= "<option {$selected} value='".$row['cNip']."'>".$row['vName']."</option>";
                }
            }
            $o .= "</select>";
			
			return $o;
	}
	function searchbox_transaksi_queue_task_problem_subject($field, $id) {
		$o='';
			
			return $o;
	}
	public function listBox_transaksi_queue_task_pic($value, $pk, $name, $rowData) {
		$return = $this->getEmployeeName($value);
		return $return;
	}
	
	public function listBox_transaksi_queue_task_role($value, $pk, $name, $rowData) {
	//	$sql ="select * from "
		return $value." (".$pk.")";
	}
	
	public function listBox_transaksi_queue_task_problem_subject($value, $pk, $name, $rowData) {
		return $value." (".$pk.")";
		
	}
	public function listBox_transaksi_queue_task_deadline($value, $pk, $name, $rowData) {
		return date('d-M-Y', strtotime($value));
		
	}
	
	public function listBox_transaksi_queue_task_action($value, $pk, $name, $rowData) {
		//echo 'test : '.$rowData->isCanceled;
			$url = base_url().'processor/ss/queue/task/sizing/'; 
			$btn ='<a href="#" onclick="javascript:browse(\''.$url.'?action=view&id='.$pk.'&company_id='.$this->input->get('company_id').'&modul_id=0&group_id=0\',\'PEMBATALAN PROJECT\')">Sizing</a>';
			return $btn;
			return $btn;
		
	}
	
	function getEmployeeName($id) {
		$sql = "Select vName from hrd.employee where cNip = '{$id}'";
		$query = $this->dbset->query($sql);
		$nm_comp = '';
		if ($query->num_rows() > 0) {
			$r = $query->row();
			$nm_comp = $r->vName;
		}
		
		return $nm_comp;
	}
	public function listBox_action($row, $button) {
		//unset($actions['view']);
		//unset($actions['delete']);
		//unset($actions['update']);
		unset($button['create']);
		return $button;
	}
	
	
	
    public function output(){
            $this->index($this->input->get('action'));
    }
}