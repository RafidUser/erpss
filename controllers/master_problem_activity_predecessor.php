<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class master_problem_activity_predecessor extends MX_Controller {
    private $sess_auth;
    private $dbset;
    function __construct() {
        parent::__construct();
        $this->sess_auth = new Zend_Session_Namespace('auth'); 
		$this->load->library('auth');
		$this->load->library('lib_utilitas');
		$this->user = $this->auth->user();
        $this->dbset = $this->load->database('hrd', true);
        $this->url = 'master_problem_activity_predecessor'; 
    }
    
    function index($action = '') {
    	$action = $this->input->get('action');
		
    	//Bikin Object Baru Nama nya $grid		
        $grid = new Grid;		
        $grid->setTitle('Problem Aktivity Predecessor');		
        $grid->setTable('ss.problem_act_predecessor');		
        $grid->setUrl('master_problem_activity_predecessor');		
        $grid->addList('typeId','activity_id','iPredecessor','cUpdatedBy','lDeleted' );//'lPersen', 'yPersen',
		$grid->setLabel('typeId','Problem Type');
		$grid->setWidth('typeId','300');
		$grid->setLabel('activity_id','Activity Type');
		$grid->setWidth('activity_id','300');
		$grid->setLabel('iPredecessor','Predecessor');
		$grid->setWidth('iPredecessor','300');	
		
		$grid->setLabel('tCreated','Create Date');
		$grid->setWidth('tCreated','150');
		$grid->setLabel('tUpdated','Update Date');
		$grid->setWidth('tUpdated','150');
		$grid->setLabel('cUpdatedBy','Update By');
		$grid->setWidth('cUpdatedBy','150');
		
		$grid->setLabel('lDeleted', 'Status Record');
		$grid->setWidth('lDeleted','90');		
		
		
		
        $grid->addFields('typeId','activity_id','iPredecessor','tCreated','tUpdated','cUpdatedBy','lDeleted' );
		
		$grid->changeFieldType('lDeleted','combobox', '', array(''=>'-- All --', 0=>'Active', 1=>'Deleted'));
		
        $grid->setQuery('ss.problem_act_predecessor.lDeleted', 0);
       
	//set search
        $grid->setSearch('typeId','activity_id' );
		
        //set required
        $grid->setRequired('typeId','activity_id' );//Field yg mandatori

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
	
	
	function listBox_master_problem_activity_predecessor_cUpdatedBy($value, $pk, $name, $rowData) {

		$sql = "SELECT a.vName from hrd.employee a where a.cNip = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->vName;
		}
		
		return $nama_group;
	}
	
	public function insertbox_master_problem_activity_predecessor_cUpdatedBy($field, $id) {
        $o  = "<input type='hidden' name='".$field."' id='".$id."' />";       
        
        return $o;
    }
	
	
	
	public function updatebox_master_problem_activity_predecessor_cUpdatedBy($field, $id, $value, $data) {
		
       $sql = "SELECT a.vName from hrd.employee a where a.cNip = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->vName;
		}
		
		return $nama_group;
    }	
	
	public function insertbox_master_problem_activity_predecessor_tCreated($field, $id) {
        $o  = "<input type='hidden' name='".$field."' id='".$id."' />";       
        
        return $o;
    }
	
	public function updatebox_master_problem_activity_predecessor_tCreated($field, $id, $value, $data) {
		
		$o  = "<input type='hidden' name='".$field."' id='".$id."' value='".$value."'/>";
        $o .= $value;
        
        return $o;
       
    }
	
	public function insertbox_master_problem_activity_predecessor_tUpdated($field, $id) {
        $o  = "<input type='hidden' name='".$field."' id='".$id."' />";       
        
        return $o;
    }
	
	public function updatebox_master_problem_activity_predecessor_tUpdated($field, $id, $value, $data) {
		
		$o  = "<input type='hidden' name='".$field."' id='".$id."' value='".$value."'/>";
        $o .= $value;
        
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
		$post['tCreated'] = date('Y-m-d H:i:s', time());
		$post['cUpdatedBy'] = $this->user->gNIP;
		return $post;
	}
	
	
	function insertbox_master_problem_activity_predecessor_typeId($field, $id) {
		$o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value=''>Pilih</option>";
            $sql = "Select typeId as typeId, typeName as vDescription 
                    from ss.support_type order by typeName";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {                       
                       $o .= "<option value='".$row['typeId']."'>".$row['vDescription']."</option>";
                }
            }
		

            $o .= "</select>";
			
			return $o;
	}
	
	function searchBox_master_problem_activity_predecessor_typeId($field, $id) {
		$o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value=''>Pilih</option>";
            $sql = "Select typeId as typeId, typeName as vDescription 
                    from ss.support_type order by typeName";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {                       
                       $o .= "<option value='".$row['typeId']."'>".$row['vDescription']."</option>";
                }
            }
		

            $o .= "</select>";
			
			return $o;
	}
	
	public function updateBox_master_problem_activity_predecessor_typeId($field, $id, $value) {
        
        if ($this->input->get('action') == 'view') {
            $sql = "Select typeName as vDescription 
                    from ss.support_type where typeId = '{$value}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o = $row->vDescription;
            }
        } else {

            $o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select typeId as typeId, typeName as vDescription 
                    from ss.support_type order by typeName";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($value == $row['typeId']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['typeId']."'>".$row['vDescription']."</option>";
                }
            }
		}	

            $o .= "</select>";
			
			return $o;
    }
	
	
	
	public function insertbox_master_problem_activity_predecessor_activity_id($field, $id) {
        if ($this->input->get('action') == 'view') {
            $sql = "Select activity as vDescription 
                    from ss.activity_type where activity_id = '{$id}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o = $row->vDescription;
            }
        } else {

            $o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select activity_id as activity_id, activity as vDescription 
                    from ss.activity_type order by activity";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($id == $row['activity_id']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['activity_id']."'>".$row['vDescription']."</option>";
                }
            }
		}	

            $o .= "</select>";
			
			return $o;
    }
	
	function searchBox_master_problem_activity_predecessor_activity_id($field, $id) {
		$o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value=''>Pilih</option>";
            $sql = "Select activity_id as activity_id, activity as vDescription 
                    from ss.activity_type order by activity";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {                       
                       $o .= "<option value='".$row['activity_id']."'>".$row['vDescription']."</option>";
                }
            }
		

            $o .= "</select>";
			
			return $o;
	}
	
	
	public function updateBox_master_problem_activity_predecessor_activity_id($field, $id, $value) {
        
        if ($this->input->get('action') == 'view') {
            $sql = "Select activity as vDescription 
                    from ss.activity_type where activity_id = '{$value}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o = $row->vDescription;
            }
        } else {

            $o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select activity_id as activity_id, activity as vDescription 
                    from ss.activity_type order by activity";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($value == $row['activity_id']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['activity_id']."'>".$row['vDescription']."</option>";
                }
            }
		}	

            $o .= "</select>";
			
			return $o;
    }
	
	
	
	
	function listBox_master_problem_activity_predecessor_typeId($value, $pk, $name, $rowData) {

		$sql = "SELECT a.typeName from ss.support_type a where a.typeId = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->typeName;
		}
		
		return $nama_group;
	}
	
	
	function listBox_master_problem_activity_predecessor_activity_id($value, $pk, $name, $rowData) {

		$sql = "SELECT a.activity from ss.activity_type a where a.activity_id = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->activity;
		}
		
		return $nama_group;
	}
	
	function listBox_master_problem_activity_predecessor_iPredecessor($value, $pk, $name, $rowData) {

		$sql = "SELECT a.activity from ss.activity_type a where a.activity_id = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->activity;
		}
		
		return $nama_group;
	}
	
	
	
	
	public function insertBox_master_problem_activity_predecessor_iPredecessor($field, $id) {
        
        if ($this->input->get('action') == 'view') {
            $sql = "Select activity as vDescription 
                    from ss.activity_type where activity_id = '{$value}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o = $row->vDescription;
            }
        } else {

            $o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select activity_id as activity_id, activity as vDescription 
                    from ss.activity_type order by activity";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($id == $row['activity_id']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['activity_id']."'>".$row['vDescription']."</option>";
                }
            }
		}	

            $o .= "</select>";
			
			return $o;
    }
	
	
	public function updateBox_master_problem_activity_predecessor_iPredecessor($field, $id, $value) {
        
        if ($this->input->get('action') == 'view') {
            $sql = "Select activity as vDescription 
                    from ss.activity_type where activity_id = '{$value}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o = $row->vDescription;
            }
        } else {

            $o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select activity_id as activity_id, activity as vDescription 
                    from ss.activity_type order by activity";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($value == $row['activity_id']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['activity_id']."'>".$row['vDescription']."</option>";
                }
            }
		}	

            $o .= "</select>";
			
			return $o;
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