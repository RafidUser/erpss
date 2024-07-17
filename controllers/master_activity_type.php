<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class master_activity_type extends MX_Controller {
    private $sess_auth;
    private $dbset;
    function __construct() {
        parent::__construct();
		$this->load->library('auth');
		$this->load->library('lib_utilitas');
		$this->user = $this->auth->user();
		$this->dbset = $this->load->database('hrd', true);
		$this->url = 'master_activity_type';
		$this->nipInferior = $this->lib_utilitas->get_all_inferior( $this->user->gNIP );			
    }
    
    function index($action = '') {
    	$action = $this->input->get('action');
		
    	//Bikin Object Baru Nama nya $grid		
        $grid = new Grid;		
        $grid->setTitle('Activity Type');		
        $grid->setTable('ss.activity_type');		
        $grid->setUrl('master_activity_type');		
        $grid->addList('iGrp_activity_id','activity', 'mDescription','entityType','needDoc','isSLA','isModule');//'lPersen', 'yPersen',
		$grid->setLabel('iGrp_activity_id','Group Name');
		$grid->setWidth('iGrp_activity_id','90');
		$grid->setLabel('activity','Activity Name');
		$grid->setWidth('activity','245');
		$grid->setLabel('mDescription','Description');
		$grid->setWidth('mDescription','400');
		$grid->setLabel('entityType','Entity');
		$grid->setWidth('entityType','50');
		$grid->setLabel('cStatus','Active');
		$grid->setWidth('cStatus','50');
		$grid->setLabel('needDoc','Documentation');
		$grid->setWidth('needDoc','50');
		$grid->setLabel('isSLA','Use SLA');
		$grid->setWidth('isSLA','50');
		$grid->setLabel('isModule','Module');
		$grid->setWidth('isModule','50');
		$grid->setLabel('cUpdate','Last Updated By');
		$grid->setWidth('cUpdate','100');
		$grid->setLabel('tUpdate','Last Updated Date');
	
        $grid->addFields('iGrp_activity_id','activity', 'mDescription','entityType','needDoc','isSLA','isModule','cStatus','cUpdate','tUpdate');

		$grid->changeFieldType('cStatus','combobox', '', array(''=>'-- All --', 'A'=>'Active', 'N'=>'Deleted'));
		
		//set search
        $grid->setSearch('activity','iGrp_activity_id','entityType', 'mDescription');
		
        $grid->setQuery('activity_type.cStatus', 'A');

		//set required
        $grid->setRequired('iGrp_activity_id','activity', 'mDescription','entityType','cStatus','needDoc','isSLA','isModule');//Field yg mandatori

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

	public function searchBox_master_activity_type_iGrp_activity_id($field, $id) {
		$rows = '';
		$sql = "SELECT * FROM ss.grp_activity_type ";
		$query = $this->db->query( $sql );
		if( $query->num_rows() > 0 ) {
			$rows = $query->result_array();
		}
		if(is_array($rows)) {
			$o = "<select name='".$id."' id='".$id."' style='width: 200px'>";
			$o .= "<option value='' selected='selected'>--All--</option>";
			foreach( $rows as $row ) {
				$o .= "<option value='".$row['iGrp_activity_id']."'>".$row['vGrpName']."</option>";
			}
			$o.= '</select>';
		}
    	$o .= "<script type='text/javascript'>
                            $(document).ready(function() {
                                $('#search_grid_master_activity_type_iGrp_activity_id').change(function() {
                                      javascript:reload_grid('grid_".$this->url."');
                                      //loadDataSearch_".$this->url.";
                                });
                            });
    
                       </script>
                    ";
       
    	return $o;
    }
	function listBox_master_activity_type_tUpdate($value) {
		//return date('d M Y', strtotime($value));
		return date('d/m/Y H:i:s', strtotime($value));
	}

	public function insertBox_master_activity_type_tUpdate($field, $id) {
		return '<input type="hidden" value="'.date('Y-m-d H:i:s').'" name="'.$field.'" id="'.$id.'" />'.date('l, d F Y g:i:s A');
	}
	public function updateBox_master_activity_type_tUpdate($field, $id, $value) {
		return '<input type="hidden" value="'.date('Y-m-d H:i:s', strtotime($value)).'" name="'.$field.'" id="'.$id.'" />'.date('l, d F Y g:i:s A', strtotime($value));
		
	}
	
	function listBox_master_activity_type_iGrp_activity_id($value, $pk, $name, $rowData) {

		$sql = "SELECT a.vGrpName from ss.grp_activity_type a where a.iGrp_activity_id = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->vGrpName;
		}
		
		return $nama_group;
	}
	
	function listBox_master_activity_type_cUpdate($value, $pk, $name, $rowData) {

		$sql = "SELECT a.vName from hrd.employee a where a.cNip = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->vName;
		}
		
		return $nama_group;
	}
	
	public function insertbox_master_activity_type_cUpdate($field, $id) {
        $o  = "<input type='hidden' name='".$field."' id='".$id."' />";       
        
        return $o;
    }
	
	public function updatebox_master_activity_type_cUpdate($field, $id, $value, $data) {
		
       $sql = "SELECT a.vName from hrd.employee a where a.cNip = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->vName;
		}
		
		return $nama_group;
    }
	
	public function updateBox_master_activity_type_iGrp_activity_id($field, $id, $value) {
        
        if ($this->input->get('action') == 'view') {
            $sql = "Select vGrpName as vDescription 
                    from ss.grp_activity_type where iGrp_activity_id = '{$value}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o = $row->vDescription;
            }
        } else {

            $o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select iGrp_activity_id as iGrp_activity_id, vGrpName as vDescription 
                    from ss.grp_activity_type order by vGrpName";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($value == $row['iGrp_activity_id']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['iGrp_activity_id']."'>".$row['vDescription']."</option>";
                }
            }
		}	

            $o .= "</select>";
			
			return $o;
    }
	public function insertbox_master_activity_type_iGrp_activity_id($field, $id) {
        
        if ($this->input->get('action') == 'view') {
            $sql = "Select vGrpName as vDescription 
                    from ss.grp_activity_type where iGrp_activity_id = '{$id}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o = $row->vDescription;
            }
        } else {

            $o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select iGrp_activity_id as iGrp_activity_id, vGrpName as vDescription 
                    from ss.grp_activity_type order by vGrpName";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($id == $row['iGrp_activity_id']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['iGrp_activity_id']."'>".$row['vDescription']."</option>";
                }
            }
		}	

            $o .= "</select>";
			
			return $o;
    }	

	public function before_insert_processor($value, $post) {
		$post['tUpdate'] = date('Y-m-d H:i:s', time());
		//$post['tCreated'] = date('Y-m-d H:i:s', time());
		$post['cUpdate'] = $this->user->gNIP;
		return $post;
	}	
	
	public function before_update_processor($value, $post) {
		$post['tUpdate'] = date('Y-m-d H:i:s', time());
		//$post['tCreated'] = date('Y-m-d H:i:s', time());
		$post['cUpdate'] = $this->user->gNIP;
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