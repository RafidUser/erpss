<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class master_default_pic extends MX_Controller {
    private $sess_auth;
    private $dbset;
    function __construct() {
        parent::__construct();
        $this->sess_auth = new Zend_Session_Namespace('auth'); 
		$this->load->library('auth');
		$this->load->library('lib_utilitas');
		$this->user = $this->auth->user();
        $this->dbset = $this->load->database('hrd', true);
        $this->url = 'master_default_pic'; 
    }
    
    function index($action = '') {
    	$action = $this->input->get('action');
		
    	//Bikin Object Baru Nama nya $grid		
        $grid = new Grid;		
        $grid->setTitle('Default Pic');		
        $grid->setTable('ss.default_pic');		
        $grid->setUrl('master_default_pic');		
        $grid->addList('iProblemCatID','iProblemTypeID','iCompanyId','iLocationID','iActivityID','cPIC','cUpdatedBy','lDeleted' );//'lPersen', 'yPersen',
		$grid->addFields('iProblemCatID','groupPic','company','default_pic');
       
		$grid->setLabel('iCompanyId','Company');
		$grid->setWidth('iCompanyId','300');
		$grid->setLabel('iLocationID','Location');
		$grid->setWidth('iLocationID','140');
		
		$grid->setLabel('iProblemCatID','Problem Category');
		$grid->setWidth('iProblemCatID','220');
		$grid->setLabel('iProblemTypeID','Problem Type');
		$grid->setWidth('iProblemTypeID','200');
		$grid->setLabel('iActivityID','Activity');
		$grid->setWidth('iActivityID','200');
		$grid->setLabel('cPIC','PIC');
		$grid->setWidth('cPIC','100');
		
		
		$grid->setLabel('cUpdatedBy','Update By');
		$grid->setLabel('tCreated','Create Date');
		$grid->setLabel('tUpdated','Update Date');
		
		$grid->setWidth('tCreated','90');
		$grid->setWidth('tUpdated','90');		
		$grid->setWidth('cUpdatedBy','200');
		
		$grid->setLabel('lDeleted', 'Status Record');
		$grid->setWidth('lDeleted','90');		
		
       
	    $grid->changeFieldType('lDeleted','combobox', '', array(''=>'-- All --', 0=>'Active', 1=>'Deleted'));
		
        $grid->setQuery('ss.default_pic.lDeleted', 0);
		
	//set search
        $grid->setSearch('iCompanyId','iLocationID' );
		
        //set required
        $grid->setRequired('iProblemCatID','iProblemTypeID','iCompanyId','iLocationID','iActivityID','cPIC' );//Field yg mandatori

        $grid->setGridView('grid');

        switch ($action) {
                case 'json':
                        $grid->getJsonData();
                        break;
				case 'getemployee':
						echo $this->getEmployee();
						break;
                case 'view':
                        $grid->render_form($this->input->get('id'), true);
                        break;
                case 'create':
                        $grid->render_form();
                        break;
				case 'pic':
					$this->pic();
				case 'npl':
					$this->npl();
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
    function getEmployee() {
		
		$term = $this->input->get('term');		
		$data = array();
		$pic_exists = "";
		print_r($_POST);
		foreach($_GET as $key=>$val) {
			if ($key == "l_pic3") {
				foreach ($val as $k=>$v) {					
					$pic_exists .= "'".$v."',";
				}
			}
		}
	
	
		$pic_exists = substr($pic_exists, 0, strlen($pic_exists)-1);		
		if (strlen($pic_exists) == 0) $qq = "";
		else $qq = " AND a.cNip NOT IN ({$pic_exists})";
		
		$sql = "SELECT a.cNip, a.vName as nama from hrd.employee a where a.cNip like '%".$term."%' or a.vName like '%".$term."%' 
				AND a.iDivisionID = 6 and (a.dResign > date_format(now(), '%Y-%m-%d') or a.dResign = '0000-00-00') 
				".$qq."
				ORDER BY vName ASC";
		$query = $this->dbset->query($sql);
		if ($query->num_rows > 0) {
			foreach($query->result_array() as $line) {
	
				$row_array['value'] = trim($line['nama']).' - '.$line['cNip'];
				$row_array['id']    = $line['cNip'];
	
				array_push($data, $row_array);
			}
		}
	
		echo json_encode($data);
		exit;
	}
	function npl() {
		$term = $this->input->get('term');
		$return_arr = array();
		//print_r('cNip',$term);;
		$this->db->like('I_LOCATION_ID',$term);
		$this->db->or_like('V_LOCATION_NAME',$term);
		$lines = $this->db->get('hrd.worklocation')->result_array();
		$i=0;
		foreach($lines as $line) {
			
				$row_array["value"] = trim($line["V_LOCATION_NAME"]);
				$row_array["id"] = trim($line["I_LOCATION_ID"]);
				array_push($return_arr, $row_array);
			
		}
		echo json_encode($return_arr);
		exit();
	}
	function pic() {
		$term = $this->input->get('term');
		$return_arr = array();
		//print_r('cNip',$term);;
		$this->db->where('iDivisionID',6);
		$this->db->like('cNip',$term);
		$this->db->or_like('vName',$term);
	
		$lines = $this->db->get('hrd.employee')->result_array();
		$i=0;
		foreach($lines as $line) {
			
				$row_array["value"] = trim($line["vName"]).' - '.trim($line["cNip"]);
				$row_array["id"] = trim($line["cNip"]);
				array_push($return_arr, $row_array);
			
		}
		echo json_encode($return_arr);
		exit();
	}
	
	public function insertBox_master_default_pic_default_pic($field, $id) {
		$sql ="Select typeId as typeId, typeName as typeName from ss.support_type order by typeName";
		$data['activity'] = $this->dbset->query($sql)->result_array();
		
		return $this->load->view('default_pic',$data,TRUE);
	}
	public function insertBox_master_default_pic_groupPic($field, $id) {
		$sql ="Select typeId as typeId, typeName as typeName from ss.support_type order by typeName";
		$data['activity'] = $this->dbset->query($sql)->result_array();
		
		return $this->load->view('group_pic',$data,TRUE);
	}
	public function insertBox_master_default_pic_company($field, $id) {
		$this->db->where('ldeleted', 0);
		$this->db->order_by('vCompName', 'ASC');
		$data['company'] = $this->db->get('hrd.company')->result_array();
		return $this->load->view('company',$data,TRUE);
		}
	public function updateBox_proses_svc_cPIC($field, $id, $value, $rowData) {
		$sql = "SELECT mst.*, ma.I_LOCATION_ID as lokasi FROM asset.asset_category ac
					inner join asset.master_asset ma on ma.iAsset_category_id=ac.iAsset_category_id
					inner join asset.team2category te on te.iAsset_category_id=ac.iAsset_category_id
					inner join asset.msservice_team mst on mst.iTeam_id=te.iTeam_id
				WHERE ma.iMaster_asset_id='".$rowData['iMaster_asset_id']."' limit 1";
		$query = $this->db->query($sql);
		if($query->num_rows()>0) {
			$row = $query->row_array();
			$iDeptID=$row['iDeptID'];
			$ilok=$row['lokasi'];
			$team=$row['vNama_team'];
		}
		//print_r($row);
		
		$url = base_url().'processor/ss/cpic/popup?';
		
		$picNip = '';
		$picName = '';
		$cssShowDetail = 'display:none;';
		if($value) {
			$picNip = $value;
			$picName = $this->lib_utilitas->get_name_by_nip($value);
			$cssShowDetail = '';
		}
		
		$o = '<style>
			#'.$id.'_detail {
				clear:both;
				'.$cssShowDetail.'
			}
			#'.$id.'_detail span {
				display:block;
				float:left;
				padding:5px;
			}
			#'.$id.'_detail input[type=text] {
				width:150px;
				text-align:left;
			}
			</style>';
		$o .='
		<div>
			<input type="text" id="'.$id.'_nip" name="'.$id.'_nip" value="'.$picNip.'" readonly />&nbsp;
		</div>
		<div id="'.$id.'_detail">
			<span>Nama &nbsp;:<input type="text" id="'.$id.'_nama" value="'.$picName.'" disabled /></span>
			<span>&nbsp;</span>
		</div>
		<div style="clear:both;padding:0;margin:0;">&nbsp;</div>
		';
			
		return $o;
	}
	
	
	
	public function insertBox_master_default_pic_tCreated($field, $id) {
		return '<input type="hidden" value="'.date('Y-m-d H:i:s').'" name="'.$field.'" id="'.$id.'" />'.date('l, d F Y g:i:s A');
	}
	public function updateBox_master_default_pic_tCreated($field, $id, $value) {
		return '<input type="hidden" value="'.date('Y-m-d H:i:s', strtotime($value)).'" name="'.$field.'" id="'.$id.'" />'.date('l, d F Y g:i:s A', strtotime($value));
		
	}	

	public function insertBox_master_default_pic_tUpdated($field, $id) {
		return '<input type="hidden" value="'.date('Y-m-d H:i:s').'" name="'.$field.'" id="'.$id.'" />'.date('l, d F Y g:i:s A');
	}
	public function updateBox_master_default_pic_tUpdated($field, $id, $value) {
		return '<input type="hidden" value="'.date('Y-m-d H:i:s', strtotime($value)).'" name="'.$field.'" id="'.$id.'" />'.date('l, d F Y g:i:s A', strtotime($value));
		
	}	
	
	function listBox_master_default_pic_cUpdatedBy($value, $pk, $name, $rowData) {

		$sql = "SELECT * FROM hrd.employee WHERE cNip = '".$value."' LIMIT 1";
		$query = $this->db->query($sql);
		if( $query->num_rows() > 0 ) {
			$row = $query->row_array();
			return $row['vName'];
		}
		return $value;
	}
	
	public function insertBox_master_default_pic_cUpdatedBy($field, $id) {
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

	public function updateBox_master_default_pic_cUpdatedBy($field, $id, $value,$rowData) {
		
		$vName='';
		$cNip = $this->user->gNIP;		
		$sql = "SELECT * FROM hrd.employee WHERE cNip = '".$value."' ";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row_array();
			$vName=$row['vName'];
		}
		$o ='
		<input name="'.$field.'" id="'.$id.'" type="hidden" value="'.$cNip.'"/>';
		$o.= $vName;
		return $o;
	}
	
	public function before_insert_processor($value, $post) {
	
		
	}	
	
	public function before_update_processor($value, $post) {
		$post['tUpdated'] = date('Y-m-d H:i:s', time());
		//$post['tCreated'] = date('Y-m-d H:i:s', time());
		$post['cUpdatedBy'] = $this->user->gNIP;
		return $post;
	}
	
	function insertbox_master_default_pic_iCompanyId($field, $id) {
		$o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value=''>Pilih</option>";
            $sql = "Select iCompanyId as iCompanyId, vCompName as vDescription 
                    from hrd.company order by vCompName";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {                       
                       $o .= "<option value='".$row['iCompanyId']."'>".$row['vDescription']."</option>";
                }
            }
		

            $o .= "</select>";
			
			return $o;
	}
	
	
	
	function listBox_master_default_pic_iCompanyId($value, $pk, $name, $rowData) {

		$sql = "SELECT a.vCompName from hrd.company a where a.iCompanyId = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->vCompName;
		}
		
		return $nama_group;
	}
	
	function searchBox_master_default_pic_iCompanyId($field, $id) {
		$o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value=''>Pilih</option>";
            $sql = "Select iCompanyId as iCompanyId, vCompName as vDescription 
                    from hrd.company order by vCompName";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {                       
                       $o .= "<option value='".$row['iCompanyId']."'>".$row['vDescription']."</option>";
                }
            }
		

            $o .= "</select>";
			
			return $o;
	}
	
	public function updateBox_master_default_pic_iCompanyId($field, $id, $value) {
        
        if ($this->input->get('action') == 'view') {
            $sql = "Select vCompName as vDescription 
                    from hrd.company where iCompanyId = '{$value}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o = $row->vDescription;
            }
        } else {

            $o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select iCompanyId as iCompanyId, vCompName as vDescription 
                    from hrd.company order by vCompName";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($value == $row['iCompanyId']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['iCompanyId']."'>".$row['vDescription']."</option>";
                }
            }
		}	

            $o .= "</select>";
			
			return $o;
    }
	
	function listBox_master_default_pic_iLocationID($value, $pk, $name, $rowData) {

		$sql = "SELECT a.V_LOCATION_NAME from hrd.worklocation a where a.I_LOCATION_ID = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->V_LOCATION_NAME;
		}
		
		return $nama_group;
	}
	
	function insertbox_master_default_pic_iLocationID($field, $id) {
		$vCompName='';
		$iCompanyId='';
		$cNip = $this->user->gNIP;
		$sql = "SELECT a.V_LOCATION_NAME, b.iWorkArea
				FROM hrd.worklocation a, hrd.employee b WHERE a.I_LOCATION_ID = b.iWorkArea and b.cNip = '".$cNip."' ";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$vCompName=$row->V_LOCATION_NAME;
			$iCompanyId=$row->iWorkArea;
				
		}
			$o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "SELECT I_LOCATION_ID AS I_LOCATION_ID, V_LOCATION_NAME AS vDescription 
                    FROM hrd.worklocation";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($iCompanyId == $row['I_LOCATION_ID']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['I_LOCATION_ID']."'>".$row['vDescription']."</option>";
                }
            }
			$o .="</select>";
		
		return $o;
	}
	
	function searchBox_master_default_pic_iLocationID($field, $id) {
		$o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value=''>Pilih</option>";
            $sql = "Select I_LOCATION_ID as I_LOCATION_ID, V_LOCATION_NAME as vDescription 
                    from hrd.worklocation order by V_LOCATION_NAME";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {                       
                       $o .= "<option value='".$row['I_LOCATION_ID']."'>".$row['vDescription']."</option>";
                }
            }
		

            $o .= "</select>";
			
			return $o;
	}
	
	public function updateBox_master_default_pic_iLocationID($field, $id, $value) {
        
        if ($this->input->get('action') == 'view') {
            $sql = "Select V_LOCATION_NAME as vDescription 
                    from hrd.worklocation where I_LOCATION_ID = '{$value}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o = $row->vDescription;
            }
        } else {

            $o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select I_LOCATION_ID as I_LOCATION_ID, V_LOCATION_NAME as vDescription 
                    from hrd.worklocation order by V_LOCATION_NAME";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($value == $row['I_LOCATION_ID']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['I_LOCATION_ID']."'>".$row['vDescription']."</option>";
                }
            }
		}	

            $o .= "</select>";
			
			return $o;
    }
	
	function listBox_master_default_pic_iProblemCatID($value, $pk, $name, $rowData) {

		$sql = "SELECT a.item from ss.support a where a.id = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->item;
		}
		
		return $nama_group;
	}
	
	
	
	function insertbox_master_default_pic_iProblemCatID($field, $id) {
		$o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value=''>Pilih</option>";
            $sql = "Select id as id, item as vDescription 
                    from ss.support order by item";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {                       
                       $o .= "<option value='".$row['id']."'>".$row['vDescription']."</option>";
                }
            }
		

            $o .= "</select>";
			
			return $o;
	}
	
	public function updateBox_master_default_pic_iProblemCatID($field, $id, $value) {
        
        if ($this->input->get('action') == 'view') {
            $sql = "Select item as vDescription 
                    from ss.support where id = '{$value}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o = $row->vDescription;
            }
        } else {

            $o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select id as id, item as vDescription 
                    from ss.support order by item";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($value == $row['id']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['id']."'>".$row['vDescription']."</option>";
                }
            }
		}	

            $o .= "</select>";
			
			return $o;
    }	
	
	function listBox_master_default_pic_iProblemTypeID($value, $pk, $name, $rowData) {

		$sql = "SELECT a.typeName from ss.support_type a where a.typeId = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->typeName;
		}
		
		return $nama_group;
	}
	
	function insertbox_master_default_pic_iProblemTypeID($field, $id) {
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
	
	public function updateBox_master_default_pic_iProblemTypeID($field, $id, $value) {
        
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
	
	
	function listBox_master_default_pic_iActivityID($value, $pk, $name, $rowData) {

		$sql = "SELECT a.activity from ss.activity_type a where a.activity_id = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->activity;
		}
		
		return $nama_group;
	}
	
	function insertbox_master_default_pic_iActivityID($field, $id) {
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
	
	public function updateBox_master_default_pic_iActivityID($field, $id, $value) {
        
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
	
	function listBox_master_default_pic_cPIC($value, $pk, $name, $rowData) {

		$sql = "SELECT a.vName from hrd.employee a where a.cNip = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama_group = '-';
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$nama_group = $row->vName;
		}
		
		return $nama_group;
	}
	
	function insertbox_master_default_pic_cPIC($field, $id) {
		$o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value=''>Pilih</option>";
            $sql = "Select cNip as cNip, vName as vDescription 
                    from hrd.employee where IDivisionID=6 and  dResign ='000-00-00' order by vName ";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {                       
                       $o .= "<option value='".$row['cNip']."'>".$row['vDescription']."</option>";
                }
            }
		

            $o .= "</select>";
			
			return $o;
	}
	
	public function updateBox_master_default_pic_cPIC($field, $id, $value) {
        
        if ($this->input->get('action') == 'view') {
            $sql = "Select vName as vDescription 
                    from hrd.employee where cNip = '{$value}'";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $row = $query->row();
                $o = $row->vDescription;
            }
        } else {

            $o  = "<select name='".$field."' id='".$id."'>";
            $o .= "<option value='0'>Pilih</option>";
            $sql = "Select cNip as cNip, vName as vDescription 
                    from hrd.employee where IDivisionID=6 order by vName ";
            $query = $this->dbset->query($sql);
            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                foreach($result as $row) {
                       if ($value == $row['cNip']) $selected = " selected";
                       else $selected = '';
                       $o .= "<option {$selected} value='".$row['cNip']."'>".$row['vDescription']."</option>";
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