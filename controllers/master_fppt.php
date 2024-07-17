<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class master_fppt extends MX_Controller {
    private $sess_auth;
    private $dbset;
    function __construct() {
        parent::__construct();
		$this->load->library('auth');
		$this->load->library('lib_utilitas');
		$this->user = $this->auth->user();
		$this->dbset = $this->load->database('hrd', true);
		$this->url = 'master_fppt';
		$this->nipInferior = $this->lib_utilitas->get_all_inferior( $this->user->gNIP );			
		
    }
    
    function index($action = '') {
    	$action = $this->input->get('action');
		
    	//Bikin Object Baru Nama nya $grid		
        $grid = new Grid;		
        $grid->setTitle('FPPT');		
        $grid->setTable('ss.fppt');		
        $grid->setUrl('master_fppt');		
        $grid->addList('yActTargetDev','ofppt','cUpdatedBy','tCreated','tUpdated','lDeleted' );
		
		$grid->setLabel('yActTargetDev','Pencapaian Target Kerja');
		$grid->setLabel('ofppt','FPPT');
		$grid->setLabel('cUpdatedBy','Update By');
		$grid->setLabel('tCreated','Create Date');
		$grid->setLabel('tUpdated','Update Date');
		
		$grid->setWidth('tCreated','120');
		$grid->setWidth('tUpdated','120');		
		$grid->setWidth('cUpdatedBy','200');
		$grid->setWidth('yActTargetDev','150');
		$grid->setWidth('ofppt','100');

		//$grid->changeFieldType('cUpdatedBy','hidden');
		//$grid->changeFieldType('tUpdated','hidden');
		//$grid->changeFieldType('tCreated','hidden');	

		
		$grid->setAlign('yActTargetDev', 'right');
		$grid->setAlign('ofppt', 'right');

		$grid->setLabel('lDeleted', 'Status Record');
		$grid->setWidth('lDeleted','90');		
		
        $grid->addFields('yActTargetDev','ofppt','cUpdatedBy','tCreated','tUpdated','lDeleted' );
		
		$grid->changeFieldType('lDeleted','combobox', '', array(''=>'-- All --', 0=>'Active', 1=>'Deleted'));
		
        $grid->setQuery('ss.fppt.lDeleted', 0);
		
	//set search
        $grid->setSearch('yActTargetDev' );
		
        //set required
        $grid->setRequired('yActTargetDev','ofppt' );//Field yg mandatori

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
    
	function listBox_master_fppt_tCreated($value) {
		//return date('d M Y', strtotime($value));
		return date('d/m/Y H:i:s', strtotime($value));
	}
	function listBox_master_fppt_tUpdated($value) {
		return date('d/m/Y H:i:s', strtotime($value));
	}	
	public function insertBox_master_fppt_tCreated($field, $id) {
		return '<input type="hidden" value="'.date('Y-m-d H:i:s').'" name="'.$field.'" id="'.$id.'" />'.date('l, d F Y g:i:s A');
	}
	public function updateBox_master_fppt_tCreated($field, $id, $value) {
		return '<input type="hidden" value="'.date('Y-m-d H:i:s', strtotime($value)).'" name="'.$field.'" id="'.$id.'" />'.date('l, d F Y g:i:s A', strtotime($value));
		
	}	

	public function insertBox_master_fppt_tUpdated($field, $id) {
		return '<input type="hidden" value="'.date('Y-m-d H:i:s').'" name="'.$field.'" id="'.$id.'" />'.date('l, d F Y g:i:s A');
	}
	public function updateBox_master_fppt_tUpdated($field, $id, $value) {
		return '<input type="hidden" value="'.date('Y-m-d H:i:s', strtotime($value)).'" name="'.$field.'" id="'.$id.'" />'.date('l, d F Y g:i:s A', strtotime($value));
		
	}	
	
	function listBox_master_fppt_cUpdatedBy($value, $pk, $name, $rowData) {
    	$sql = "SELECT * FROM hrd.employee WHERE cNip = '".$value."' LIMIT 1";
		$query = $this->db->query($sql);
		if( $query->num_rows() > 0 ) {
			$row = $query->row_array();
			return $row['vName'];
		}
		return $value;
    }


	public function insertBox_master_fppt_cUpdatedBy($field, $id) {
		$vName='';
		$cNip = $this->user->gNIP;
		$sql = "SELECT * FROM hrd.employee WHERE cNip = '".$cNip."' ";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$vName=$row->vName;
		}
		$o ='
		<input name="'.$field.'" id="'.$id.'" type="hidden" value="'.$cNip.'"  />
		<input name="'.$field.'_text" id="'.$id.'_text" type="text" size="50" value="'.$vName.'" readonly  />
		';
		return $o;
	}

	public function updateBox_master_fppt_cUpdatedBy($field, $id, $value,$rowData) {
		$vName='';
		$cNip = $this->user->gNIP;
		$sql = "SELECT * FROM hrd.employee WHERE cNip = '".$value."' ";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			$vName=$row['vName'];
		}
		$o ='
		<input name="'.$field.'" id="'.$id.'" type="hidden" value="'.$value.'"/>';
		$o.= $vName;
		return $o;
	}
	
	public function before_insert_processor($value, $post) {
		$post['tUpdated'] = date('Y-m-d H:i:s', time());
		$post['tCreated'] = date('Y-m-d H:i:s', time());
		$post['cUpdatedBy'] = $this->user->gNIP;
		return $post;
	}	
	
	public function before_update_processor($value, $post) {
		$post['tUpdated'] = date('Y-m-d H:i:s', time());
		//$post['tCreated'] = date('Y-m-d H:i:s', time());
		$post['cUpdatedBy'] = $this->user->gNIP;
		return $post;
	}
	
	/*public function after_update_processor($id,$rowData){
		$cNip = $this->user->gNIP;
		$ql = 'Update ss.fppt set tUpdated = CURRENT_TIMESTAMP, cUpdatedBy = 
	}*/
	
	
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