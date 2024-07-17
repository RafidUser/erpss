<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    class master_problem_category extends MX_Controller {
    private $sess_auth;
    private $dbset;
    function __construct() {
        parent::__construct();
		$this->load->library('auth');
		$this->load->library('lib_utilitas');
		$this->user = $this->auth->user();
		$this->dbset = $this->load->database('hrd', true);
		$this->url = 'master_problem_category';
		$this->nipInferior = $this->lib_utilitas->get_all_inferior( $this->user->gNIP );			
		
    }
    
    function index($action = '') {
    	$action = $this->input->get('action');
		
    	//Bikin Object Baru Nama nya $grid		
        $grid = new Grid;		
        $grid->setTitle('Master Problem Category');		
        $grid->setTable('ss.support');		
        $grid->setUrl('master_problem_category');		
        $grid->addList('item','description','company','pic','cUpdate','tUpdate','eAktive' );
		
		$grid->setLabel('item','Problem Category');
		$grid->setLabel('description','Description');
		$grid->setLabel('cUpdate','Update By');
		$grid->setLabel('tUpdate','Update Date');
		$grid->setLabel('eAktive','Status Record');
		$grid->setLabel('groupPic','');
		$grid->setSortBy('id');
		$grid->setSortOrder('desc');
		$grid->setLabel('iActivityID','Activity');
		$grid->setWidth('eAktive','90');
		$grid->setWidth('tUpdate','120');		
		$grid->setWidth('cUpdate','150');
		$grid->setWidth('item','250');
		$grid->setWidth('description','335');


        $grid->addFields('item','description','company','cUpdate','tUpdate','eAktive','pic','groupPic');
		
		$grid->changeFieldType('eAktive','combobox', '', array(''=>'-- All --', 'Yes'=>'Active', 'No'=>'Deleted'));
		
        $grid->setQuery('ss.support.eAktive', 'Yes');
		
	//set search
        $grid->setSearch('item','description');
		
        //set required
        $grid->setRequired('item','description');//Field yg mandatori

        $grid->setGridView('grid');
				
		$grid->setFormUpload(TRUE);
        switch ($action) {
                case 'json':
                        $grid->getJsonData();
                        break;
				case 'pic':
					$this->pic();
				case 'npl':
						$this->npl();
                case 'view':
                        $grid->render_form($this->input->get('id'), true);
                        break;
				case 'getemployee':
						echo $this->getEmployee();
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
	
	function getCompanyName($id) {
		$sql = "Select vCompName from hrd.company where iCompanyId = '{$id}'";
		$query = $this->dbset->query($sql);
		$nm_comp = '';
		if ($query->num_rows() > 0) {
			$r = $query->row();
			$nm_comp = $r->vCompName;
		}
		
		return $nm_comp;
	}
	
	function insertbox_master_problem_category_item($field, $id) {
	
		$o = "<input type='text' class='required' name='".$id."' id='".$id."'  size='50' data-validation='required' data-validation-error-msg='Nama sudah melebihi 250 karakter atau kosong sama sekali'  />";
		$o .= "<script>
                   $('#".$id."').restrictLength($('#maxlength'));
               </script>";
        $o .= '<br/>tersisa <span id="maxlength">250</span> karakter<br/>';
        $this->load->validator(1);
        
	                                            
		return $o;
	}
	function updatebox_master_problem_category_item($field, $id,$value) {
		if ($this->input->get('action') == 'view') { 
			$o = $value;
		
		}else{
		$o = "<input type='text' class='required' value='".$value."'name='".$id."' id='".$id."'  size='50' data-validation='required' data-validation-error-msg='Nama sudah melebihi 250 karakter atau kosong sama sekali'  />";
		$o .= "<script>
                   $('#".$id."').restrictLength($('#maxlength'));
               </script>";
        $o .= '<br/>tersisa <span id="maxlength">250</span> karakter<br/>';
        $this->load->validator(1);
        }
	                                            
		return $o;
	}
	function insertbox_master_problem_category_description($field, $id) {
		if ($this->input->get('action') == 'view') { 
			$o = "<label title='Description'>".$value."</label>";
		
		}else{
		$o 	= "<textarea name='".$id."' id='".$id."' class='required' data-validation='required' data-validation-error-msg='Nama sudah melebihi 250 karakter atau kosong sama sekali'   style='width: 240px; height: 50px;'size='250'></textarea>";		
		$o .= "<script>
	               $('#".$id."').restrictLength($('#maxlengthnote'));
	           </script>";
	    $o .= '<br/>tersisa <span id="maxlengthnote">250</span> karakter<br/>';
	    $this->load->validator(1);
	                    
		return $o;
		}
	}
	function updatebox_master_problem_category_description($field, $id, $value, $rowData) {
		if ($this->input->get('action') == 'view') { 
			$o = "<label title='Description'>".nl2br($value)."</label>";
		
		}else{
		$o 	= "<textarea name='".$id."' id='".$id."' class='required' data-validation='required' data-validation-error-msg='Nama sudah melebihi 250 karakter atau kosong sama sekali'   style='width: 240px; height: 50px;'size='250'>".nl2br($value)."</textarea>";		
		$o .= "<script>
	               $('#".$id."').restrictLength($('#maxlengthnote'));
	           </script>";
	    $o .= '<br/>tersisa <span id="maxlengthnote">250</span> karakter<br/>';
	    $this->load->validator(1);
	                    
		return $o;
		}
		
	}
      function getEmployee() {
	
		$term = $this->input->get('term');		
		$data = array();
		$pic_exists = "";
		foreach($_GET as $key=>$val) {
			if ($key == "l_pic") {
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
	public function listBox_master_problem_category_company($value, $pk, $name, $rowData) {
		
		$sql = "select iCompanyId from ss.default_pic_company where iProblemCatID = {$rowData->id}" ;
		$query = $this->dbset->query($sql);
		$i=1;
		$nama_company='';
		if ($query->num_rows() > 0) {
			foreach($query->result() as $r) {
				if ($i%2) 
					$nama_company .= "<b>".$this->getCompanyName($r->iCompanyId)."</b><br/>";
				else $nama_company .= $this->getCompanyName($r->iCompanyId)."<br/>";
				}
				$i++;
		}
		
		return $nama_company;
		
	}
	public function listBox_master_problem_category_pic($value, $pk, $name, $rowData) {
		//print_r($rowData);
		$sql = "SELECT cPic from ss.default_pic_detail where iProblemCatID = {$rowData->id}";
		$query = $this->dbset->query($sql);
		$i=0;
		$nama_company='';
		if ($query->num_rows() > 0) {
			foreach($query->result() as $r) {
				if ($i%2) 
					$nama_company .= "<b>".$this->getEmployeeName($r->cPic)."</b><br/>";
				else $nama_company .= $this->getEmployeeName($r->cPic)."<br/>";
				}
				$i++;
		}
		//$nama_company = substr($nama_company, 0, strlen($nama_company));
		
		$sql = "SELECT count(*) as c from ss.pic_group where iProblemCatID = {$rowData->id} and ldeleted = 0";
		$query = $this->dbset->query($sql);
		$total = 0;
		if ($query->num_rows() > 0) {
			$r = $query->row();
			$total = $r->c;
		}
		
		return $nama_company.($total > 0 ? "(<i>".$total." group PIC</i>)" : "");
		
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
		
		$sql = "SELECT a.cNip, a.vName as nama from hrd.employee a where a.cNip like '%".$term."%' or a.vName like '%".$term."%' 
				AND a.iDivisionID = 6 and (a.dResign > date_format(now(), '%Y-%m-%d') or a.dResign = '0000-00-00') 
				ORDER BY vName ASC";
	
		$query = $this->dbset->query($sql);
		if ($query->num_rows > 0) {
			foreach($query->result_array() as $line) {
	
				$row_array['value'] = trim($line['nama']).' - '.$line['cNip'];
				$row_array['id']    = $line['cNip'];
	
				array_push($return_arr, $row_array);
			}
		}
		echo json_encode($return_arr);
		exit();
	}
	public function insertBox_master_problem_category_pic($field, $id) {
		$url = base_url().'processor/ss/master/problem/category?action=getemployee&company_id=3&modul_id=34&group_id=2368';
		$o = '
			  <script language="text/javascript">
					$(document).ready(function() {
						var config = {
							source: function( request, response) {
								$.ajax({
									url: "'.$url.'",
									dataType: "json",
									data: {
										term: request.term,
										l_pic: getLPIC()
									},
									success: function( data ) {
										response( data );
									}
								});
							},
							select: function(event, ui){								
								$("#l_pic").append("<div id=\'div_"+ui.item.id+"\'><input type=\'hidden\' class=\'nip\' id=\'nip_"+ui.item.id+"\' name=\'nip[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");
								
								$(this).val(""); return false;
							},
							minLength: 2,
							autoFocus: true,
						};
	
						$( "#master_problem_category_pic_text" ).livequery(function() {
						 	$( this ).autocomplete(config);
						});
	
					});
					function remove_element(id) {
						$("#div_"+id).remove();
				    }
					function getLPIC() {
						var l_pic = [];
						$( ".nip" ).each(function() {								
							l_pic.push($(this).val());
						});
						
						return l_pic;
					}
		      </script>
			  
			  <input name="'.$id.'" id="'.$id.'" type="hidden"/>
			  <input name="'.$id.'_text" id="'.$id.'_text" type="text" size="50"/>';
		
		$o .= "<div id='l_pic'></div>";
	
		return $o;
	}
	
	public function updateBox_master_problem_category_pic($field, $id, $value) {
		
	
		$url = base_url().'processor/ss/master/problem/category?action=getemployee&company_id=3&modul_id=34&group_id=2368';
		$o = '
			  <script language="text/javascript">
					$(document).ready(function() {
						
						var url = "'.$url.'";
						var config = {							
							source: function( request, response) {
								$.ajax({
									url: "'.$url.'",
									dataType: "json",
									data: {
										term: request.term,
										l_pic: getLPIC()
									},
									success: function( data ) {
										response( data );
									}
								});
							},
							select: function(event, ui){
								//$("#master_problem_category_pic_text").val(ui.item.value);
								//$("#master_problem_category_pic").val(ui.item.id);
								
								$("#l_pic").append("<div id=\'div_"+ui.item.id+"\'><input class=\'nip\' type=\'hidden\' id=\'nip_"+ui.item.id+"\' name=\'nip[]\' value=\'"+ui.item.id+"\'/>"+ui.item.value+" [<span onclick=\'remove_element(\""+ui.item.id+"\");\' style=\'cursor:pointer;color:red;\'> x </span>]</div>");								
								$(this).val(""); return false;
							},
							minLength: 2,
							autoFocus: true,
						};
	
						$( "#master_problem_category_pic_text" ).livequery(function() {	
						 	$( this ).autocomplete(config);
						});
						
						function getLPIC() {
							var l_pic = [];
							$( ".nip" ).each(function() {								
								l_pic.push($(this).val());
							});
							
							return l_pic;
						}
					});
					
					function remove_element(id) {
						$("#div_"+id).remove();
				    }
		      </script>
			 ';
		
		
		$sql = "SELECT a.cNip, a.vName AS nama FROM hrd.employee a where a.cNip = '".$value."'";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$row = $query->row();
				
			$nip 	  = $row->cNip;
			$nama 	  = $row->nama.' - '.$row->cNip;
		} else {
			$nip 	  = '';
			$nama 	  = '';
		}
	
		if ($this->input->get('action') != 'view') {
			$o .= '<input name="'.$id.'" id="'.$id.'" type="hidden"/>
			<input name="'.$id.'_text" id="'.$id.'_text" type="text" size="50"/>';
			
		} else {
			$o .= $nama;
		} 
		
		$o .= "<div id='l_pic'>";
		$sql = "SELECT cPic from ss.default_pic_detail where iProblemCatID = '".$this->input->get('id')."'";
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			foreach($query->result() as $r) {
				$nama_pic = $this->getEmployeeName($r->cPic);
				$o.="<div id='div_".$r->cPic."'>
					<input class='nip' type='hidden' id='nip_".$r->cPic."' name='nip[]' value='".$r->cPic."'/>".$nama_pic." - ".$r->cPic." 
					[<span onclick='remove_element(\"".$r->cPic."\");' style='cursor:pointer;color:red;'> x </span>]</div>";
			}
		}
		$o .= "</div>";
		
	
		return $o;
	}
	
	public function insertBox_master_problem_category_groupPic($field, $id) {
		return "Please Save Record First";
	}
	public function updateBox_master_problem_category_groupPic($field, $id, $value, $data) {
		
			/*$url = base_url().'processor/ss/default/pic/module/';   
          
            $o  = '<table width="120%" style="margin-top:30px;margin-bottom:10px;margin-right:5px;margin-left:-185px">';
            $o .= '<tr><td>';
            $o .= '<script type="text/javascript">';
            $o .= '$(document).ready(function() {    
                        $("#'.$this->url.'_setup_application").tabs();  
                        browse_tab(\''.$url.'?idProb='.$data['id'].'&company_id='.$this->input->get('company_id').'&modul_id='.$this->input->get('modul_id').'&group_id='.$this->input->get('group_id').'\',\'MODULE\', \''.$this->url.'_module\');                          
                    });
                    ';
            $o .= '</script>';
            $o .= '<div id="'.$this->url.'_setup_application" width="100%">';
            $o .= '<ul>                        
                        <li><a href="#'.$this->url.'_module">Group PIC</a></li>
                      </ul>                      
                      <div id="'.$this->url.'_module"></div>
                  ';
            $o .= '</div>';
            $o .= '</td></tr>';            
            $o .= '</table>';
            return $o;
			*/
			$url = base_url().'processor/ss/default/pic/module/';
            $url2 = base_url().'processor/ss/group/pic/module/';
			
            $o  = '<table width="120%" style="margin-top:30px;margin-bottom:10px;margin-right:5px;margin-left:-185px">';
            $o .= '<tr><td>';
            $o .= '<script type="text/javascript">';
            $o .= '$(document).ready(function() {    
                        $("#'.$this->url.'_setup_application").tabs();
					   browse_tab(\''.$url2.'?idProb='.$data['id'].'&company_id='.$this->input->get('company_id').'&modul_id='.$this->input->get('modul_id').'&group_id='.$this->input->get('group_id').'\',\'Group PIC\', \''.$this->url.'_grouppic\');    
                    });
                    ';
            $o .= '</script>';
            $o .= '<div id="'.$this->url.'_setup_application" width="100%">';
            $o .= '<ul>                        
                 		<li><a href="#'.$this->url.'_grouppic">Group PIC</a></li>
						
                   </ul>                      
                 	  <div id="'.$this->url.'_grouppic"></div>
					  
                  ';
            $o .= '</div>';
            $o .= '</td></tr>';            
            $o .= '</table>';
            return $o;
	}
	
	public function insertBox_master_problem_category_company($field, $id) {
		$this->db->where('ldeleted', 0);
		$this->db->order_by('vCompName', 'ASC');
		$data['company'] = $this->db->get('hrd.company')->result_array();
		return $this->load->view('company',$data,TRUE);
	}
	public function updateBox_master_problem_category_company($field, $id, $value,$rowData) {
		$this->db->where('ldeleted', 0);
		$this->db->order_by('vCompName', 'ASC');
		$data['company'] = $this->db->get('hrd.company')->result_array();
		$ids = $rowData['id'];
		$sql ="SELECT iCompanyId FROM ss.default_pic_company WHERE iProblemCatID ='{$ids}'";
		$data['isi'] = $this->dbset->query($sql)->result_array();
		
		return $this->load->view('company_edit',$data,TRUE);
	}
	
	function listBox_master_problem_category_tUpdate($value) {
		return date('d/m/Y H:i:s', strtotime($value));
	}	

		public function listBox_Action($row, $actions) {
	
    	unset($actions['delete']);	
    
    	 
    	return $actions;
    }
	public function insertBox_master_problem_category_tUpdate($field, $id) {
		return '<input type="hidden" value="'.date('Y-m-d H:i:s').'" name="'.$field.'" id="'.$id.'" />'.date('l, d F Y g:i:s A');
	}
	public function updateBox_master_problem_category_tUpdate($field, $id, $value) {
		return '<input type="hidden" value="'.date('Y-m-d H:i:s', strtotime($value)).'" name="'.$field.'" id="'.$id.'" />'.date('l, d F Y g:i:s A', strtotime($value));
		
	}	
	
	function listBox_master_problem_category_cUpdate($value, $pk, $name, $rowData) {
    	$sql = "SELECT * FROM hrd.employee WHERE cNip = '".$value."' LIMIT 1";
		$query = $this->db->query($sql);
		if( $query->num_rows() > 0 ) {
			$row = $query->row_array();
			return $row['vName'];
		}
		return $value;
    }


	public function insertBox_master_problem_category_cUpdate($field, $id) {
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

	public function updateBox_master_problem_category_cUpdate($field, $id, $value,$rowData) {
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
	/*public function manipulate_grid_button($button) {    	
    	
    	unset($button['create']);
    	
    	return $button;
    	
		
    }*/
	
	public function before_insert_processor($value, $post) {
	
		$post['tUpdate'] = date('Y-m-d H:i:s', time());
		$post['cUpdate'] = $this->user->gNIP;
		return $post;
	}	
	
	public function after_insert_processor($fields, $id, $postData) {
		
		$arr_nip = array();
		$arr_com= array();

		foreach($_POST as $k=>$v) {
			if ($k == 'iCom') {
				$arr_com[] = $v; 
			}
			if ($k == 'nip') {
				$arr_nip[] = $v; 
			}
		
		}
		foreach($arr_nip as $value) {
			foreach($value as $k=>$v) {
				$sql_nip[]="Insert Into ss.default_pic_detail (iProblemCatID,cPic) 
				values('".$id."','".$v."')";
			}
		}
		
		foreach($arr_com as $value) {
			foreach($value as $k=>$v) {
				$sql_com[]="Insert Into ss.default_pic_company (iCompanyId,iProblemCatID) 
				values('".$v."','".$id."')";
			}
		}
		
		foreach($sql_nip as $v) {
			try {
				$this->dbset->query($v);
			}catch(Exception $e) {
				die('Error');
			}
		}
		foreach($sql_com as $v) {
			try {
				$this->dbset->query($v);
			}catch(Exception $e) {
				die('Error');
			}
		}
		
	}
	public function before_update_processor($value, $post) {
		
		$post['tUpdate'] = date('Y-m-d H:i:s', time());
		$post['cUpdate'] = $this->user->gNIP;
		return $post;
	}
	
	public function after_update_processor($field,$id){
		
		$sql = "Delete from ss.default_pic_company where iProblemCatID ='".$id."'";
		$this->dbset->query($sql);
		$sql2 = "Delete from ss.default_pic_detail where iProblemCatID ='".$id."'";
		$this->dbset->query($sql2);

		$arr_nip = array();
		$arr_com= array();

		foreach($_POST as $k=>$v) {
			if ($k == 'iCom') {
				$arr_com[] = $v; 
			}
			if ($k == 'nip') {
				$arr_nip[] = $v; 
			}
		
		}
		foreach($arr_nip as $value) {
			foreach($value as $k=>$v) {
				$sql_nip[]="Insert Into ss.default_pic_detail (iProblemCatID,cPic) 
				values('".$id."','".$v."')";
			}
		}
		
		foreach($arr_com as $value) {
			foreach($value as $k=>$v) {
				$sql_com[]="Insert Into ss.default_pic_company (iCompanyId,iProblemCatID) 
				values('".$v."','".$id."')";
			}
		}
		
		foreach($sql_nip as $v) {
			try {
				$this->dbset->query($v);
			}catch(Exception $e) {
				die('Error');
			}
		}
		foreach($sql_com as $v) {
			try {
				$this->dbset->query($v);
			}catch(Exception $e) {
				die('Error');
			}
		}
		
		
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