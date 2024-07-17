<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class master_problem_type extends MX_Controller {
    private $sess_auth;
    private $dbset;
    function __construct() {
        parent::__construct();
		$this->load->library('auth');
		$this->load->library('lib_utilitas');
		$this->user = $this->auth->user();
        $this->sess_auth = new Zend_Session_Namespace('auth'); 
        $this->dbset = $this->load->database('hrd', true);
        $this->url = 'master_problem_type'; 
    }
    
    function index($action = '') {
    	$action = $this->input->get('action');
		
    	//Bikin Object Baru Nama nya $grid		
        $grid = new Grid;		
        $grid->setTitle('Problem Type');		
        $grid->setTable('ss.support_type');		
        $grid->setUrl('master_problem_type');		
        $grid->addList('entityName','typeName','assign','changeRequest','mis','title','lDeleted' );//'lPersen', 'yPersen',
		$grid->setLabel('entityName','Entity');
		$grid->setWidth('entityName','70');
		$grid->setLabel('typeName','Problem Type');
		$grid->setWidth('typeName','300');
		
		$grid->setLabel('assign','Assign');
		$grid->setWidth('assign','50');
		$grid->setLabel('changeRequest','Request');
		$grid->setWidth('changeRequest','50');
		
		$grid->setLabel('mis','Mis');
		$grid->setWidth('mis','50');
		$grid->setLabel('title','Title');
		$grid->setWidth('title','400');
		
		$grid->setLabel('tCreated','Create Date');
		$grid->setWidth('tCreated','150');
		$grid->setLabel('tUpdated','Last Updated Date');
		$grid->setWidth('tUpdated','150');
		$grid->setLabel('cUpdatedBy','Last Updated By');
		$grid->setWidth('cUpdatedBy','150');
		
		
        $grid->setLabel('lDeleted', 'Status Record');
		$grid->setWidth('lDeleted','90');		
		
		$grid->addFields('entityName','typeName','assign','changeRequest','mis','title','lDeleted','tCreated','tUpdated','cUpdatedBy');
        
		$grid->changeFieldType('lDeleted','combobox', '', array(''=>'-- All --', 0=>'Active', 1=>'Deleted'));
		
        $grid->setQuery('ss.support_type.lDeleted', 0);
		
	//set search
        $grid->setSearch('typeName' );
		
        //set required
        $grid->setRequired('entityName','typeName','assign','changeRequest','mis','title' );//Field yg mandatori

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
    
	
	function listBox_master_problem_type_cUpdatedBy($value, $pk, $name, $rowData) {

		$sql = "SELECT a.vName from hrd.employee a where a.cNip = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->vName;
		}
		
		return $nama_group;
	}
	
	public function insertbox_master_problem_type_cUpdatedBy($field, $id) {
        $o  = "<input type='hidden' name='".$field."' id='".$id."' />";       
        
        return $o;
    }
	
	
	
	public function updatebox_master_problem_type_cUpdatedBy($field, $id, $value, $data) {
		
       $sql = "SELECT a.vName from hrd.employee a where a.cNip = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->vName;
		}
		
		return $nama_group;
    }	
	
	public function insertbox_master_problem_type_tCreated($field, $id) {
        $o  = "<input type='hidden' name='".$field."' id='".$id."' />";       
        
        return $o;
    }
	
	public function updatebox_master_problem_type_tCreated($field, $id, $value, $data) {
		
		$o  = "<input type='hidden' name='".$field."' id='".$id."' value='".$value."'/>";
        $o .= $value;
        
        return $o;
       
    }
	
	public function insertbox_master_problem_type_tUpdated($field, $id) {
        $o  = "<input type='hidden' name='".$field."' id='".$id."' />";       
        
        return $o;
    }
	
	public function updatebox_master_problem_type_tUpdated($field, $id, $value, $data) {
		
		$o  = "<input type='hidden' name='".$field."' id='".$id."' value='".$value."'/>";
        $o .= $value;
        
        return $o;
       
    }
	
	
	
	
	public function updatebox_master_problem_type_typeName($field, $id, $value, $data) {
		
		//$o  = "<input type='hidden' name='".$field."' id='".$id."' value='".$value."'/>";
		$o  = "<textarea name='".$field."' id='".$id."' value='".$value."'>$value</textarea>";
        //$o .= $value;
        
        return $o;       
    }
	
	public function updatebox_master_problem_type_title($field, $id, $value, $data) {
		
		//$o  = "<input type='hidden' name='".$field."' id='".$id."' value='".$value."'/>";
		$o  = "<textarea name='".$field."' id='".$id."' value='".$value."'>$value</textarea>";
        //$o .= $value;        
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