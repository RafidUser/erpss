<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class queue_task_sizing extends MX_Controller {
	var $dbset;
	var $url;
	var $prequest_id;
	public function __construct() {
		parent::__construct();
		$this->url = 'queue_task_sizing';
		$this->dbset = $this->load->database('pm', true);
		$this->prequest_id = $this->input->get('prequest_id');
		$this->load->library('lib_utilitas');
		$this->sess_auth = new Zend_Session_Namespace('auth');
	}
	
	function index($action = '') {		
    	$action = $this->input->get('action') ? $this->input->get('action') : 'create';	
		
    	//Bikin Object Baru Nama nya $grid		
		$grid = new Grid;		
		$grid->setTitle('Pembatalan Project');		
		$grid->setTable("pm.pm_request");		
		$grid->setUrl($this->url);
		
		$grid->addFields('cNipReq', 'iDeptID', 'vProjectCode', 'vProjectName', 'txtBackground', 'txtGoal', 'vCancelReason');
		
		$grid->setSortBy('dRequestedAt'); //sort by
		$grid->setSortOrder('DESC'); //sort ordernya
		
		$grid->setLabel('iDeptID', 'Project Belong To');
		$grid->setLabel('vProjectCode', 'Project Number'); //Ganti Label
		$grid->setLabel('vProjectName', 'Project Name'); //Ganti Label
		$grid->setLabel('cNipReq','Requestor'); //Ganti Label	
		$grid->setLabel('txtBackground','Project Background'); //Ganti Label
		$grid->setLabel('txtGoal','Project Goal'); //Ganti Label
		$grid->setLabel('vCancelReason', 'Alasan Pembatalan');
		
		$grid->setRequired('vCancelReason');	//Field yg mandatori
		
   		switch ($action) {		
			case 'update':
				$grid->render_form($this->input->get('id'));
				break;
			case 'view':
				$grid->render_form($this->input->get('id'), TRUE);
				break;
			case 'updateproses':
				echo $grid->updated_form();
				break;
			default:
				$grid->render_grid();
				break;
		}
    }
	
	function manipulate_update_button($button) {
		unset($button['update']);
		$btnSave    = '';		
		$btnSave  =  "<script type='text/javascript'>
							function update_btn_back_".$this->url."(grid, url, dis) {
	
								var req = $('#form_update_'+grid+' input.required, #form_update_'+grid+' select.required, #form_update_'+grid+' textarea.required');
								var conf=0;
								var alert_message = '';
								var tot_err = 0;
								var adaDiStockOpname = 0;
								
								if ($('#queue_task_sizing_vCancelReason').val() == '') {
									alert('Lengkapi Alasan Pembatalan');
									return false;
								}
			
								custom_confirm(comfirm_message,function(){
									$.ajax({
										url: $('#form_update_'+grid).attr('action'),
										type: 'post',
										data: $('#form_update_'+grid).serialize(),
										success: function(data) {
											var o = $.parseJSON(data);
											var info = 'Error';
											var header = 'Error';
											var last_id = o.last_id;

											if(o.status == true) {
											   
												$('#alert_dialog_form').dialog('close');
												
												reload_grid('grid_project_request');
												info = 'info';
												header = 'Info';
											}
											_custom_alert(o.message,header,info, grid, 1, 20000);
										}
									})
								});
							}
						  </script>";
		$btnSave .= "<button type='button'
							name='button_update_".$this->url."
							id='button_update_".$this->url."
							class='icon-save ui-button'
							onclick='javascript:update_btn_back_".$this->url."(\"".$this->url."\", \"".base_url()."processor/pm/project/request/cancel?company_id=".$this->input->get('company_id')."&group_id=".$this->input->get('group_id')."&modul_id=".$this->input->get('modul_id')."\", this)'>Update
							</button>";
			
		if ($this->input->get('action') != 'view') {		
			$button['update'] = $btnSave;
		}
		
		return $button;
	}
	
	public function updateBox_queue_task_sizing_cNipReq($field, $id, $value, $rowData) {
		$o  = "<input type='hidden' name='{$id}' id='{$id}' value = '{$value}'/>";
		$sql = "SELECT concat_ws(' - ', vName, cNip) as nama FROM hrd.employee where cNip = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama = "";
		if ($query->num_rows() > 0) {
			$r = $query->row();
			$nama = $r->nama;
		}
		
		$o .= $nama;
		
		return $o;
	}
	
	public function updateBox_queue_task_sizing_iDeptId($field, $id, $value, $rowData) {
		$o  = "<input type='hidden' name='{$id}' id='{$id}' value = '{$value}'/>";
		$sql = "SELECT vDescription as nama FROM hrd.msdivision where iDivId = '{$value}'";
		$query = $this->dbset->query($sql);
		$nama = "";
		if ($query->num_rows() > 0) {
			$r = $query->row();
			$nama = $r->nama;
		}
		
		$o .= $nama;
		
		return $o;
	}
	
	public function updateBox_queue_task_sizing_vProjectCode($field, $id, $value, $rowData) {
		$o  = "<input type='hidden' name='{$id}' id='{$id}' value = '{$value}'/>";
		$o .= $value;
		
		return $o;
	}
	
	public function updateBox_queue_task_sizing_vProjectName($field, $id, $value, $rowData) {
		$o  = "<input type='hidden' name='{$id}' id='{$id}' value = '{$value}'/>";
		$o .= $value;
		
		return $o;
	}
	
	public function updateBox_queue_task_sizing_txtBackground($field, $id, $value, $rowData) {
		$o  = "<textarea style='width:400px;background-color:#DEDEDE;' readonly name='{$id}' id='{$id}' rows='4' cols='40'>{$value}</textarea>";
		
		return $o;
	}
	
	public function updateBox_queue_task_sizing_txtGoal($field, $id, $value, $rowData) {
		$o  = "<textarea style='width:400px;background-color:#DEDEDE;' readonly name='{$id}' id='{$id}' rows='4' cols='40'>{$value}</textarea>";
		
		return $o;
	}
	
	public function updateBox_queue_task_sizing_vCancelReason($field, $id, $value, $rowData) {
		$bgcolor = 'background-color:#FFFFFF;';
		$readonly = '';
		if ($this->input->get('action') == 'view') {
			$bgcolor = 'background-color:#DEDEDE;';
			$readonly = 'readonly';
		}
		
		$o  = "<textarea {$readonly} style='width:400px;{$bgcolor}' name='{$id}' id='{$id}' rows='4' cols='40'>{$value}</textarea>";
				
		return $o;
	}
	
	function before_update_processor($row, $post) {
		$post['isCanceled'] = 1;
		$where=array('ID'=>$post['ID']);
		$pk=array('base'=>'ID','target'=>'idpm_request');
		$this->lib_utilitas->insertRecordHistory('pm.pm_request','pm.pm_request_log',$where,$pk);
		return $post;
	}
	
	public function after_update_processor($fields, $id, $post) {
		//print_r($id);
		//update status pm_project_request;
		$sql = "UPDATE pm.pm_request set tUpdatedAt = CURRENT_TIMESTAMP, cUpdatedBy = '".$this->sess_auth->gNIP."' 
				WHERE id = '{$id}'";
		$this->dbset->query($sql);
		
		//update status pm_project_request;
		$sql = "SELECT id from pm.pm_charter where idpm_request = '{$id}' limit 1";
		$idpm_charter = 0;
		$query = $this->dbset->query($sql);
		if ($query->num_rows() > 0) {
			$r = $query->row();
			$idpm_charter = $r->id;
		}		
		$sql = "UPDATE pm.pm_charter set isCanceled = 1, tUpdatedAt = CURRENT_TIMESTAMP, cUpdatedBy = '".$this->sess_auth->gNIP."' 
				WHERE idpm_request = '{$id}'";
		$this->dbset->query($sql);
		
		//update status pm_project_task;
		$sql = "UPDATE pm.pm_task set isCanceled = 1, tUpdatedAt = CURRENT_TIMESTAMP, cUpdatedBy = '".$this->sess_auth->gNIP."' 
				WHERE idpm_charter = '{$idpm_charter}'";
		$this->dbset->query($sql);
	}
	
	public function output(){
		$this->index('create');
	}
}

?>