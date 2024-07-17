<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class master_group_activity_type extends MX_Controller {
    private $sess_auth;
    private $dbset;
    function __construct() {
        parent::__construct();
		$this->load->library('auth');
		$this->load->library('lib_utilitas');
		$this->user = $this->auth->user();
		$this->dbset = $this->load->database('hrd', true);
		$this->url = 'master_group_activity_type';
		$this->nipInferior = $this->lib_utilitas->get_all_inferior( $this->user->gNIP );
    }
	
    function index($action = '') {
    	$action = $this->input->get('action');
		
    	//Bikin Object Baru Nama nya $grid		
        $grid = new Grid;		
        $grid->setTitle('Group Activity Type');		
        $grid->setTable('ss.grp_activity_type');		
        $grid->setUrl('master_group_activity_type');		
        $grid->addList('vGrpName', 'iSmall','iMedium','iLarge','iRateperHrs','cUpdatedBy','tCreated','tUpdated','ldeleted');//'lPersen', 'yPersen',
		$grid->setLabel('vGrpName','Group Activity Name');
		$grid->setWidth('vGrpName','270');
		$grid->setLabel('iSmall','Small');
		$grid->setWidth('iSmall','50');
		$grid->setLabel('iMedium','Medium');
		$grid->setWidth('iMedium','50');
		$grid->setLabel('iLarge','Large');
		$grid->setWidth('iLarge','50');
		$grid->setLabel('iRateperHrs','Rate / Hour');
		$grid->setWidth('iRateperHrs','100');
		$grid->setLabel('cUpdatedBy','Last Updated By');
		$grid->setLabel('tCreated','Created Date');
		$grid->setLabel('ldeleted', 'Status Record');
		$grid->setLabel('tUpdated','Last Updated Date');
		$grid->setWidth('tCreated','90');
		$grid->setWidth('tUpdated','90');
		
		$grid->setLabel('ldeleted', 'Status Record');
		$grid->setWidth('ldeleted','90');
		
        $grid->addFields('vGrpName', 'iSmall','iMedium','iLarge','iRateperHrs','cUpdatedBy','tCreated','tUpdated','ldeleted');      
		
		$grid->changeFieldType('ldeleted','combobox', '', array(''=>'-- All --', 0=>'Active', 1=>'Deleted'));
		
		$grid->setQuery('ss.grp_activity_type.ldeleted', 0);
		
		
		//$grid->changeFieldType('cUpdatedBy','hidden');
		//$grid->changeFieldType('tUpdated','hidden');
		//$grid->changeFieldType('tCreated','hidden');	
		
		//$grid->setJoinTable('hrd.employee', 'employee.cNip = ss_grp_activity_type.cUpdatedBy', 'inner');
		
	    //set search
        $grid->setSearch('vGrpName');
		
        //set required
        $grid->setRequired('vGrpName', 'iSmall','iMedium','iLarge','iRateperHrs');	//Field yg mandatori

        $grid->setGridView('grid');
		
		$grid->setFormUpload(TRUE);
        switch ($action) {
                case 'json':
                        $grid->getJsonData();
                        break;
                case 'view':
                        $grid->render_form($this->input->get('id'), true);
                        break;
                case 'create':
                        $grid->render_form();
						$this->load->validator(1);
                        break;
                case 'createproses':
                        echo $grid->saved_form();
                        break;
                case 'update':
                        $grid->render_form($this->input->get('id'));
						$this->load->validator(1);
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
    
	function listBox_master_group_activity_type_tCreated($value) {
		return date('d M Y H:i:s', strtotime($value));
	}
	function listBox_master_group_activity_type_tUpdated($value) {
		return date('d M Y H:i:s', strtotime($value));
	}
	
	function insertBox_master_group_activity_type_vGrpName($field, $id) {
			$c 	= '<';
			$c .= 'input type="text"';
			$c .= 'name="'.$field.'" ';
			$c .= 'id="'.$id.'" value="" ';
			$c .= 'size="25" ';
			$c .= 'data-validation-length="5-30" ';
			$c .= 'data-validation="length" ';
			$c .= '/> ';
			return $c;
	}
	
	function updateBox_master_group_activity_type_vGrpName($field, $id, $value) {
		if ($this->input->get('action') == 'view')
			return $value;
		else {
			$c 	= '<';
			$c .= 'input type="text"';
			$c .= 'name="'.$field.'" ';
			$c .= 'id="'.$id.'" value="'.$value.'" ';
			$c .= 'size="25" ';
			$c .= 'data-validation-length="5-30" ';
			$c .= 'data-validation="length" ';
			$c .= '/> ';
			return $c;
		}
	}
	
	function insertBox_master_group_activity_type_iSmall($field, $id) {
		$c 	= '<';
		$c .= 'input type="text"';
		$c .= 'name="'.$field.'" ';
		$c .= 'id="'.$id.'" value="" ';
		$c .= 'style="text-align: right" ';
		$c .= 'size="5" ';
		$c .= 'data-validation="number" ';
		$c .= 'data-validation="required" ';
		$c .= '/> in minute. <br/>';
		return $c;
	}
	
	function updateBox_master_group_activity_type_iSmall($field, $id, $value) {
		if ($this->input->get('action') == 'view')
			return $value.' minute(s).';
		else {
			$c 	= '<';
			$c .= 'input type="text"';
			$c .= 'name="'.$field.'" ';
			$c .= 'id="'.$id.'" value="'.$value.'" ';
			$c .= 'style="text-align: right" ';
			$c .= 'size="5" ';
			$c .= 'data-validation="number" ';
			$c .= 'data-validation="required" ';
			$c .= '/> in minute. <br/>';
			return $c;
		}
	}
	
	function insertBox_master_group_activity_type_iMedium($field, $id) {
		$c 	= '<';
		$c .= 'input type="text"';
		$c .= 'name="'.$field.'" ';
		$c .= 'id="'.$id.'" value="" ';
		$c .= 'style="text-align: right" ';
		$c .= 'size="5" ';
		$c .= 'size="5" ';
		$c .= 'data-validation="number" ';
		$c .= 'data-validation="required" ';
		$c .= '/> in minute. <br/>';
		return $c;
	}
	
	function updateBox_master_group_activity_type_iMedium($field, $id, $value) {
		if ($this->input->get('action') == 'view')
			return $value.' minute(s).';
		else {
			$c 	= '<';
			$c .= 'input type="text"';
			$c .= 'name="'.$field.'" ';
			$c .= 'id="'.$id.'" value="'.$value.'" ';
			$c .= 'style="text-align: right" ';
			$c .= 'size="5" ';
			$c .= 'size="5" ';
			$c .= 'data-validation="number" ';
			$c .= 'data-validation="required" ';
			$c .= '/> in minute. <br/>';
			return $c;
		}
	}
	
	function insertBox_master_group_activity_type_iLarge($field, $id) {
		$c 	= '<';
		$c .= 'input type="text"';
		$c .= 'name="'.$field.'" ';
		$c .= 'id="'.$id.'" value="" ';
		$c .= 'style="text-align: right" ';
		$c .= 'size="5" ';
		$c .= 'size="5" ';
		$c .= 'data-validation="number" ';
		$c .= 'data-validation="required" ';
		$c .= '/> in minute. <br/>';
		RETURN $c;
	}
	
	function updateBox_master_group_activity_type_iLarge($field, $id, $value) {
		if ($this->input->get('action') == 'view')
			return $value.' minute(s).';
		else {
			$c 	= '<';
			$c .= 'input type="text"';
			$c .= 'name="'.$field.'" ';
			$c .= 'id="'.$id.'" value="'.$value.'" ';
			$c .= 'style="text-align: right" ';
			$c .= 'size="5" ';
			$c .= 'size="5" ';
			$c .= 'data-validation="number" ';
			$c .= 'data-validation="required" ';
			$c .= '/> in minute. <br/>';
			return $c;
		}
	}
	
	function insertBox_master_group_activity_type_iRateperHrs($field, $id) {
		$c 	= '<';
		$c .= 'input type="text"';
		$c .= 'name="'.$field.'" ';
		$c .= 'id="'.$id.'" value="" ';
		$c .= 'style="text-align: right" ';
		$c .= 'size="15" '; 
		$c .= 'data-validation="number" ';
		$c .= 'data-validation="required" ';
		$c .= '/>  <br/>';
		return $c;
	}
	
	function updateBox_master_group_activity_type_iRateperHrs($field, $id, $value) {
		if ($this->input->get('action') == 'view')
			return $value.' per Hour.';
		else {
			$c 	= '<';
			$c .= 'input type="text"';
			$c .= 'name="'.$field.'" ';
			$c .= 'id="'.$id.'" value="'.$value.'" ';
			$c .= 'style="text-align: right" ';
			$c .= 'size="15" '; 
			$c .= 'data-validation="number" ';
			$c .= 'data-validation="required" ';
			//$c .= 'data-validation-error-msg="Mohon masukan berupa angka"';
			$c .= '/>  <br/>';
			return $c;
		}
	}
	
	function listBox_master_group_activity_type_cUpdatedBy($value, $pk, $name, $rowData) {
    	$sql = "SELECT * FROM hrd.employee WHERE cNip = '".$value."' LIMIT 1";
		$query = $this->db->query($sql);
		if( $query->num_rows() > 0 ) {
			$row = $query->row_array();
			return $row['vName'];
		}
		return $value;
    }
	
	public function insertBox_master_group_activity_type_cUpdatedBy($field, $id) {
		$vName='';
		$cNip = $this->user->gNIP;
		$sql = "SELECT * FROM hrd.employee WHERE cNip = '".$cNip."' ";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$vName=$row->vName;
		}
		$o  = '<input name="'.$field.'" id="'.$id.'" type="hidden" value="'.$cNip.'" />';
		$o .= $vName;
		return $o;
	}

	public function updateBox_master_group_activity_type_cUpdatedBy($field, $id, $value,$rowData) {
		$vName='';
		$cNip = $this->user->gNIP;
		$sql = "SELECT * FROM hrd.employee WHERE cNip = '".$value."' ";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			$vName=$row['vName'];
		}
		$o = '<input name="'.$field.'" id="'.$id.'" type="hidden" value="'.$cNip.'" />';
		$o.= $vName;
		return $o;
	}

	public function insertBox_master_group_activity_type_tCreated($field, $id) {
		return '';
	}
	
	public function updateBox_master_group_activity_type_tCreated($field, $id, $value) {
		return date('l, d F Y g:i:s A', strtotime($value));
	}

	public function insertBox_master_group_activity_type_tUpdated($field, $id) {
		return '';
	}
	
	public function updateBox_master_group_activity_type_tUpdated($field, $id, $value) {
		return date('l, d F Y g:i:s A', strtotime($value));
	}
	
	public function before_insert_processor($value, $post) {
		unset($post['tUpdated']);
		unset($post['tCreated']);
		$post['cUpdatedby'] = $this->user->gNIP;
		return $post;
	}	
	
	public function before_update_processor($value, $post) {
		unset($post['tCreated']);
		unset($post['tUpdated']);
		$post['cUpdatedby'] = $this->user->gNIP;
		return $post;
	}
	
    public function manipulate_update_button($button) {
        if ($this->input->get('action') == 'view') {
                unset($button['update']);
        }
        return $button;
    }
	
    public function output(){
            $this->index($this->input->get('action'));
    }
}