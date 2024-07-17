<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class master_parameter extends MX_Controller { 
    function __construct() {
        parent::__construct();
        // $this->db = $this->load->database('erpss',false, true);
        $this->load->library('lib_sub_core');
        $this->modul_id = $this->input->get('modul_id');
        $this->iModul_id = $this->lib_sub_core->getIModulID($this->input->get('modul_id'));
        $this->db = $this->load->database('hrd',false, true);
        $this->load->library('auth');
        $this->user = $this->auth->user();

        // $this->team = $this->lib_sub_core->hasTeam($this->user->gNIP);
        // $this->teamID = $this->lib_sub_core->hasTeamID($this->user->gNIP);
        // $this->isAdmin=$this->lib_sub_core->isAdmin($this->user->gNIP);

        $this->title = 'Master Parameter';
        $this->url = 'master_parameter';
        $this->urlpath = 'erpss/'.str_replace("_","/", $this->url);

        $this->maintable = 'hrd.biflow_ms_parameter';   
        $this->main_table = $this->maintable;   
        $this->main_table_pk = 'id';    
        $datagrid['islist'] = array(
            'iSort' => array('label'=>'Urutan', 'width'=>40, 'align'=>'center', 'search'=>false),
            'cKode_parameter' => array('label'=>'Kode Generate', 'width'=>110, 'align'=>'center', 'search'=>true),
            'vName'      => array('label'=>'Nama Parameter', 'width'=>400, 'align'=>'left', 'search'=>true),
            'mKeterangan'  => array('label'=>'Keterangan', 'width'=>300, 'align'=>'left', 'search'=>false),
            // 'lDeleted'  => array('label'=>'Status', 'width'=>90, 'align'=>'center', 'search'=>true),
            // 'iSubmit'      => array('label'=>'Status','width'=>100,'align'=>'left','search'=>false)
        );

        $datagrid['setQuery']=array(
            0=>array('vall'=>'biflow_ms_parameter.lDeleted','nilai'=>0)
        );

        $datagrid['shortBy']=array('id'=>'Desc');

        // $datagrid['jointableinner']=array(
        //                             0=>array('plc2.erp_privi_upb_formula'=>'erp_privi_upb_formula.ifor_id=mikro_fg.ifor_id')
        //                             ,1=>array('plc2.erp_privi_upb'=>'plc2.erp_privi_upb_formula.iupb_id = plc2.erp_privi_upb.iupb_id')
        //                         ); 
        
        $this->datagrid=$datagrid;

        
    }

    function index($action = '') {
        $grid = new Grid;       
        $grid->setTitle($this->title);      
        $grid->setTable($this->maintable );
        $grid->setUrl($this->url);

        // $grid->changeFieldType('lDeleted', 'combobox','',array( 0 => 'Aktif', 1 => 'Tidak Aktif'));

        /*$grid->setGroupBy($this->setGroupBy);*/
        /*Untuk Field*/
        $grid->addFields('form_detail');
        foreach ($this->datagrid as $kv => $vv) {
            /*Untuk List*/
            if($kv=='islist'){
                foreach ($vv as $list => $vlist) {
                    $grid->addList($list);
                    foreach ($vlist as $kdis => $vdis) {
                        if($kdis=='label'){
                            $grid->setLabel($list, $vdis);
                        }
                        if($kdis=='width'){
                            $grid->setWidth($list, $vdis);
                        }
                        if($kdis=='align'){
                            $grid->setAlign($list, $vdis);
                        }
                        if($kdis=='search' && $vdis==true){
                            $grid->setSearch($list);
                        }
                    }
                }
            }

            /*Untuk Short List*/
            if($kv=='shortBy'){
                foreach ($vv as $list => $vlist) {
                    $grid->setSortBy($list);
                    $grid->setSortOrder($vlist);
                }
            }

            if($kv=='inputGet'){
                foreach ($vv as $list => $vlist) {
                    $grid->setInputGet($list,$vlist);
                }
            }

            if($kv=='jointableinner'){
                foreach ($vv as $list => $vlist) {
                    foreach ($vlist as $tbjoin => $onjoin) {
                        $grid->setJoinTable($tbjoin, $onjoin, 'inner');
                    }
                }
            }
            if($kv=='setQuery'){
                foreach ($vv as $list => $vlist) {
                    $grid->setQuery($vlist['vall'], $vlist['nilai']);
                }
            }

        }

        $grid->setGridView('grid');

        switch ($action) {
            case 'json':
                $grid->getJsonData();
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
            case 'view':
                $grid->render_form($this->input->get('id'),TRUE);
                break;
            case 'updateproses':
                $post=$this->input->post();
                //print_r($post);
                
                $get=$this->input->get();
                $lastId=isset($post[$this->url."_".$this->main_table_pk])?$post[$this->url."_".$this->main_table_pk]:"0";
                //echo $lastId;
                
                $dataFieldUpload=$this->lib_sub_core->getUploadFileFromField($this->input->get('modul_id'));
                if(count($dataFieldUpload)>0 && $lastId!="0" && $lastId!=""){
                    foreach ($dataFieldUpload as $kf => $vUpload) {
                        $pathf=$vUpload['filepath'];
                        $iddetails=$vUpload['fielddetail'];

                        $validdetails=array();

                        foreach ($post as $kk => $vv) {
                            if($kk=='fileerpss_'.$iddetails){
                                foreach ($vv as $kv2 => $vv2) {
                                    $validdetails[]=$vv2;
                                }
                                
                            }
                        }
                        $dataupdate[$vUpload['fldeleted']]=1;
                        $dataupdate[$vUpload['fdUpdate']]=date("Y-m-d H:i:s");
                        $dataupdate[$vUpload['fcupdate']]=$this->user->gNIP;
                        if(count($validdetails)>0){
                            /* echo "atas";
                            print_r($validdetails);
                            exit; */
                            $this->db->where($vUpload['fieldheader'],$lastId)
                                            ->where_not_in($vUpload['fielddetail'],$validdetails)
                                            ->update($vUpload['filetable'],$dataupdate);
                        }else{
                            /* echo "bawh";
                            exit; */
                            $this->db->where($vUpload['fieldheader'],$lastId)
                                            ->update($vUpload['filetable'],$dataupdate);
                        }
                        
                        /*Delete File*/
                        $where=array($vUpload['fldeleted']=>1,$this->main_table_pk=>$lastId);
                        $this->db->where($where);
                        $qq=$this->db->get($vUpload['filetable']);
                        if($qq->num_rows() > 0){
                            $result=$qq->result_array();
                            foreach ($result as $kr => $vr) {
                                if(isset($vr["vFilename_generate"])){
                                    $pathf=$vUpload['filepath'];
                                    $path = realpath($pathf);
                                    if(file_exists($path."/".$lastId."/".$vr["vFilename_generate"])){
                                        unlink($path."/".$lastId."/".$vr["vFilename_generate"]);
                                    }
                                }
                            }

                        }
                    }
                }
                echo $grid->updated_form();
                break;  
            case 'delete':
                echo $grid->delete_row();
                break;

            /*Option Case*/
            case 'getFormDetail':
                echo $this->getFormDetail();
                break;
            case 'get_data_prev':
                echo $this->get_data_prev();
                break;

            /*Confirm*/
            case 'confirm':
                echo $this->confirm_view();
                break;
            case 'confirm_process':
                echo $this->confirm_process();
                break;

            case 'download':
                $this->load->helper('download');        
                $name = $_GET['file'];
                $id = $_GET['id'];
                $path = $_GET['path'];

                $this->db->select("*")
                    ->from("erp_privi.sys_masterdok")
                    ->where("filename",$path);
                $row=$this->db->get()->row_array();

                if(count($row)>0 && isset($row["filepath"])){
                    $path = file_get_contents('./'.$row['filepath'].'/'.$id.'/'.$name); 
                    force_download($name, $path);
                }else{
                    echo "File Not Found - 0x01";
                }

                
                break;

            case 'uploadFile':
                $lastId=$this->input->get('lastId');
                $dataFieldUpload=$this->lib_sub_core->getUploadFileFromField($this->input->get('modul_id'));
                if(count($dataFieldUpload)>0){
                    foreach ($dataFieldUpload as $kf => $vUpload) {
                        $pathf=$vUpload['filepath'];
                        $path = realpath($pathf);
                        if(!file_exists($path."/".$lastId)){
                            if (!mkdir($path."/".$lastId, 0777, true)) { //id review
                                die('Failed upload, try again!');
                            }
                        }

                        $fKeterangan = array();
                        foreach($_POST as $key=>$value) {                       
                            if ($key == 'erp_privi_'.$this->url.'_'.$vUpload['vNama_field']."_".$vUpload['filename'].'_fileketerangan') {
                                foreach($value as $k=>$v) {
                                    $fKeterangan[$k] = $v;
                                }
                            }
                        }
                        $i=0;
                        foreach ($_FILES['erp_privi_'.$this->url.'_'.$vUpload['vNama_field']."_".$vUpload['filename'].'_upload_file']["error"] as $key => $error) {
                            if ($error == UPLOAD_ERR_OK) {
                                $tmp_name = $_FILES['erp_privi_'.$this->url.'_'.$vUpload['vNama_field']."_".$vUpload['filename'].'_upload_file']["tmp_name"][$key];
                                $name =$_FILES['erp_privi_'.$this->url.'_'.$vUpload['vNama_field']."_".$vUpload['filename'].'_upload_file']["name"][$key];
                                $data['filename'] = $name;
                                $data['dInsertDate'] = date('Y-m-d H:i:s');
                                $filenameori=$name;
                                $now_u = date('Y_m_d__H_i_s');
                                $name_generate = $i.'__'.$now_u.'__'.$name;
                                
                                $datatb=explode(".", $vUpload['filetable']);
                                $sql = "SELECT c.`COLUMN_NAME`,c.`COLUMN_KEY` , c.`COLUMN_TYPE`, c.`DATA_TYPE`, c.`CHARACTER_MAXIMUM_LENGTH` 
                                        FROM `information_schema`.`COLUMNS` c
                                        WHERE c.`TABLE_SCHEMA` = '".$datatb[0]."' AND c.`TABLE_NAME`='".$datatb[1]."'";

                                $qq=$this->db->query($sql);

                                if($qq->num_rows()>0){
                                    $namafield=array();
                                    foreach ( $qq->result_array() as $kky => $vvy) {
                                        $namafield[$vvy['COLUMN_NAME']]=1;
                                    }

                                    if(isset($namafield['vFilename_generate'])){

                                    }else{
                                        $sqlinsert="ALTER TABLE `".$datatb[1]."`
                                            ADD COLUMN `vFilename_generate` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nama Generate Upload File' AFTER ".$vUpload['ffilename'];
                                        $this->db->query($sqlinsert);
                                    }


                                }else{
                                    echo "Table File Not Found";
                                    exit();
                                }
                                
                                    if(move_uploaded_file($tmp_name, $path."/".$lastId."/".$name_generate)) {
                                        $datainsert=array();
                                        $datainsert[$vUpload['fieldheader']]=$lastId;
                                        $datainsert[$vUpload['fdcreate']]= date('Y-m-d H:i:s');
                                        $datainsert[$vUpload['fccreate']]= $this->user->gNIP;
                                        $datainsert[$vUpload['ffilename']]= $name;
                                        $datainsert['vFilename_generate']= $name_generate;
                                        $datainsert[$vUpload['ftKeterangan']]= $fKeterangan[$i];
                                        $this->db->insert($vUpload['filetable'],$datainsert);
                                        $i++;   
                                    }
                                    else{
                                        echo "Upload ke folder gagal";  
                                    }
                            }
                        }
                    }
                    $r['message']="Data Berhasil Disimpan";
                    $r['status'] = TRUE;
                    $r['last_id'] = $this->input->get('lastId');                    
                    echo json_encode($r);

                }else{
                    $r['message']="Data Upload Not Found";
                    $r['status'] = TRUE;
                    $r['last_id'] = $this->input->get('lastId');                    
                    echo json_encode($r);
                }
                break;
            case 'get_lembaga':
                $this->getLembaga();
                break;
            case 'get_negara':
                $this->getNegara();
                break;
            default:
                $grid->render_grid();
                break;
        }
    }

    function getFormDetail(){
        $post=$this->input->post();
        $get=$this->input->get();
        $data['html']="";

        $sqlFields = 'select * from erp_privi.m_modul_fields a where a.lDeleted=0 and  a.iM_modul='.$this->iModul_id.' order by a.iSort ASC';
        $dFields = $this->db->query($sqlFields)->result_array();

        $hate_emel = "";

        if($get['formaction']=='update'){
                $aidi = $get['id'];
        }else{
                $aidi = 0;
        }

        $hate_emel .= '
            <table class="hover_table" style="width:99%; border: 1px solid #dddddd; text-align: center; margin-left: 5px; border-collapse: collapse" cellspacing="0" cellpadding="1">
                <thead>
                    <tr style="width: 100%; border: 1px solid #dddddd; background: #b3d2ea; border-collapse: collapse">
                        <th style="border: 1px solid #dddddd;">Activity Name</th>
                        <th style="border: 1px solid #dddddd;">Status</th>
                        <th style="border: 1px solid #dddddd;">at</th>      
                        <th style="border: 1px solid #dddddd;">by</th>      
                        <th style="border: 1px solid #dddddd;">Remark</th>      
                    </tr>
                </thead>
                <tbody>';

                $hate_emel .= $this->lib_sub_core->getHistoryActivity($this->modul_id,$aidi);

        $hate_emel .='
                </tbody>
            </table>
            <br>
            <br>
            <hr>
        ';


        if(!empty($dFields)){

            foreach ($dFields as $form_field) {
                
                $data_field['iM_jenis_field']= $form_field['iM_jenis_field'] ;
                
                $data_field['form_field']= $form_field;
                $data_field['get']= $get;
                $data_field['post']= $post;

                $controller = $this->url;
                $data_field['id']= $controller.'_'.$form_field['vNama_field'];
                //$data_field['field']= $controller.'_'.$form_field['vNama_field'] ;
                $data_field['field']= $form_field['vNama_field'] ;

                $data_field['act']= $get['act'] ;
                // $data_field['hasTeam']= $this->team ;
                // $data_field['hasTeamID']= $this->teamID ;
                // $data_field['isAdmin']= $this->isAdmin ;

                /*untuk keperluad file upload*/
                if($form_field['iM_jenis_field'] == 7){
                    $data_field['tabel_file']= $form_field['vTabel_file'] ;
                    $data_field['tabel_file_pk']= $this->main_table_pk;
                    $data_field['tabel_file_pk_id']= $form_field['vTabel_file_pk_id'] ;

                    $path = 'files/plc/dok_tambah';
                    $createname_space =$this->url;
                    $tempat = 'dok_tambah';
                    $FOLDER_APP = 'plc';

                    $data_field['path'] = $path;
                    $data_field['FOLDER_APP'] = $FOLDER_APP;
                    $data_field['createname_space'] = $createname_space;
                    $data_field['tempat'] = $tempat;

                    if ($form_field['iRequired']==1) {
                        $data_field['field_required']= 'required';
                    }else{
                        $data_field['field_required']= '';
                    }


                }
                /*untuk keperluad file upload*/

                $return_field="";
                if($get['formaction']=='update'){
                    $id = $get['id'];

                    $sqlGetMainvalue= 'select * from '.$this->main_table.' where lDeleted=0 and '.$this->main_table_pk.'= '.$id.'   ';
                    $dataHead = $this->db->query($sqlGetMainvalue)->row_array();

                    $data_field['dataHead']= $dataHead;
                    $data_field['main_table_pk']= $this->main_table_pk;
                    
                    if($form_field['iM_jenis_field'] == 6){
                        $data_field['vSource_input']= $form_field['vSource_input_edit'] ;
                    }else{
                        $data_field['vSource_input']= $form_field['vSource_input'] ;
                    }
                    $return_field = $this->load->view('v3_form_detail_update',$data_field,true);   
                }else{
                    $data_field['vSource_input']= $form_field['vSource_input'] ;
                    //$return_field = $this->load->view('partial/v3_form_detail',$data_field,true);    
                    $return_field = $this->load->view('v3_form_detail',$data_field,true);    
                    
                }
                

                $hate_emel .='  <div class="rows_group" style="overflow:fixed;">
                                    <label for="'.$controller.'_form_detail_'.$form_field['vNama_field'].'" class="rows_label">'.$form_field['vDesciption'].'
                                    ';
                if ($form_field['iRequired']==1) {
                    $hate_emel .='<span class="required_bintang">*</span>';    
                    $data_field['field_required']= 'required';
                }else{
                    $data_field['field_required']= '';
                }

                if ($form_field['vRequirement_field']<> "") {
                    $hate_emel .='<span style="float:right;" title="'.$form_field['vRequirement_field'].'" class="ui-icon ui-icon-info"></span>';    
                }else{
                    $hate_emel .='';    
                }
                $hate_emel .='      </label>
                                    <div class="rows_input">'.$return_field.'</div>
                                </div>';
            }

        }else{
            $hate_emel .='Field belum disetting';
        }

        $hate_emel .= '<input type="hidden" name="isdraft" id="isdraft">';
        
        $data["html"] .= $hate_emel;
        return json_encode($data);
    }

    function get_data_prev(){
        $post=$this->input->post();
        $get=$this->input->get();
        $nmTable=isset($post["nmTable"])?$post["nmTable"]:"0";
        $grid=isset($post["grid"])?$post["grid"]:"0";
        $grid=isset($post["grid"])?$post["grid"]:"0";
        $namefield=isset($post["namefield"])?$post["namefield"]:"0";

        $this->db->select("*")
                    ->from("erp_privi.sys_masterdok")
                    ->where("filename",$namefield);
        $row=$this->db->get()->row_array();
        
        $where=array('lDeleted'=>0,$row["fieldheader"]=>$post["id"]);
        $this->db->where($where);
        $q=$this->db->get($row["filetable"]);
        $rsel=array($row["ffilename"],$row["ftKeterangan"],'iact');
        $data = new StdClass;
        $data->records=$q->num_rows();
        $i=0;
        foreach ($q->result() as $k) {
            $data->rows[$i]['id']=$i;
            $z=0;

            $value=$k->vFilename_generate;
            $id=$k->{$row["fieldheader"]};
            $linknya = 'No File';
            if($value != '') {
                if (file_exists('./'.$row["filepath"].'/'.$id.'/'.$value)) {
                    $link = base_url().'processor/'.$this->urlpath.'?action=download&id='.$id.'&file='.$value.'&path='.$row['filename'];
                    $linknya = '<a class="ui-button-text" href="javascript:;" onclick="window.location=\''.$link.'\'">[Download]</a>&nbsp;&nbsp;&nbsp;';
                }
            }
            $linknya=$linknya.'<a class="ui-button-text" href="javascript:;" onclick="javascript:hapus_row_'.$nmTable.'('.$i.')">[Hapus]</a><input type="hidden" class="num_rows_'.$nmTable.'" value="'.$i.'" /><input type="hidden" name="fileerpss_'.$row["fielddetail"].'[]" value="'.$k->{$row["fielddetail"]}.'" />';


            foreach ($rsel as $dsel => $vsel) {
                if($vsel=="iact"){
                    $dataar[$dsel]=$linknya;
                }else{
                    $dataar[$dsel]=$k->{$vsel};
                }
                $z++;
            }
            $data->rows[$i]['cell']=$dataar;
            $i++;
        }
        return json_encode($data);
    }


    function getLembaga(){
        $term = $this->input->get('term');
        $sql  = 'SELECT a.vNama_lembaga 
                    FROM hrd.master_parameter a 
                    WHERE a.lDeleted = 0 AND a.vNama_lembaga LIKE  "%'.$term.'%"                     
                    ORDER BY a.vNama_lembaga ASC
                    LIMIT 150';
        $lines = $this->db->query($sql)->result_array();

        $return_arr = array();
        $i=0;
        foreach($lines as $line) {
            $row_array["value"] = trim($line["vNama_lembaga"]);
            $row_array["id"] = trim($line["vNama_lembaga"]);
            array_push($return_arr, $row_array);
        }
        echo json_encode($return_arr);exit();
    }

    function getNegara(){
        $term = $this->input->get('term');
        $sql  = 'SELECT a.vNegara 
                    FROM hrd.master_parameter a 
                    WHERE a.lDeleted = 0 AND a.vNegara LIKE  "%'.$term.'%"                     
                    ORDER BY a.vNegara ASC
                    LIMIT 150';
        $lines = $this->db->query($sql)->result_array();

        $return_arr = array();
        $i=0;
        foreach($lines as $line) {
            $row_array["value"] = trim($line["vNegara"]);
            $row_array["id"] = trim($line["vNegara"]);
            array_push($return_arr, $row_array);
        }
        echo json_encode($return_arr);exit();
    }


    function searchBox_master_parameter_vNama_lembaga($rowData, $id) {
        $o = '<input type="hidden" id="'.$id.'" name="'.$id.'" value="">';
        $o .= '<input type="text" id="'.$id.'_id" name="'.$id.'_id" value="">';

        $o .= "<script>";

        $o .= "$(document).ready(function(){
                    var config1 = {
                        source: base_url+'processor/erpss/master/lembaga?action=get_lembaga',                  
                        select: function(event, ui){

                            var i = $('#search_grid_master_parameter_vNama_lembaga_id').index(this);
                            $('#search_grid_master_parameter_vNama_lembaga_id').eq(i).val(ui.item.value);  
                            $('#search_grid_master_parameter_vNama_lembaga').eq(i).val(ui.item.value); 

                        },
                        minLength: 2,
                        autoFocus: true,
                    };
                    $('#search_grid_master_parameter_vNama_lembaga_id').livequery(function(){
                        $(this).autocomplete(config1);
                        var i = $('#search_grid_master_parameter_vNama_lembaga_id').index(this);
                        $(this).keypress(function(e, ui){
                            if(e.which != 13) {
                                $('#search_grid_master_parameter_vNama_lembaga').eq(i).val('');
                            }     

                            if(e.which == 13){
                                reload_grid('grid_master_parameter');
                            }     

                        });
                        $(this).blur(function(){
                            if($('#search_grid_master_parameter_vNama_lembaga').eq(i).val() == '') {
                                $(this).val(''); 
                            }           
                        }); 
                    });

                    // Synchronize two input fields
                    $('#search_grid_master_parameter_vNama_lembaga_id').bind('keyup paste', function() {
                        $('#search_grid_master_parameter_vNama_lembaga').val($(this).val());
                    });

                });";

        $o .= "</script>";
        
        return $o;
    } 


    function searchBox_master_parameter_vNegara($rowData, $id) {
        $o = '<input type="hidden" id="'.$id.'" name="'.$id.'" value="">';
        $o .= '<input type="text" id="'.$id.'_id" name="'.$id.'_id" value="">';

        $o .= "<script>";

        $o .= "$(document).ready(function(){
                    var config2 = {
                        source: base_url+'processor/erpss/master/lembaga?action=get_negara',                  
                        select: function(event, ui){

                            var i = $('#search_grid_master_parameter_vNegara_id').index(this);
                            $('#search_grid_master_parameter_vNegara_id').eq(i).val(ui.item.value);  
                            $('#search_grid_master_parameter_vNegara').eq(i).val(ui.item.value); 

                        },
                        minLength: 2,
                        autoFocus: true,
                    };
                    $('#search_grid_master_parameter_vNegara_id').livequery(function(){
                        $(this).autocomplete(config2);
                        var i = $('#search_grid_master_parameter_vNegara_id').index(this);
                        $(this).keypress(function(e, ui){
                            if(e.which != 13) {
                                $('#search_grid_master_parameter_vNegara').eq(i).val('');
                            }     

                            if(e.which == 13){
                                reload_grid('grid_master_parameter');
                            }     

                        });
                        $(this).blur(function(){
                            if($('#search_grid_master_parameter_vNegara').eq(i).val() == '') {
                                $(this).val(''); 
                            }           
                        }); 
                    });

                    // Synchronize two input fields
                    $('#search_grid_master_parameter_vNegara_id').bind('keyup paste', function() {
                        $('#search_grid_master_parameter_vNegara').val($(this).val());
                    });

                });";

        $o .= "</script>";
        
        return $o;
    } 


    function listBox_master_parameter_iSubmit($value) {
        if($value==0){$vstatus='Draft';}
        elseif($value==1){$vstatus='Submitted';}
        // elseif($value==2){$vstatus='Approved';}
        return $vstatus;
    }

    function listBox_Action($row, $actions) {
        $peka = $row->id;
        $getLastactivity = $this->lib_sub_core->getLastactivity($this->modul_id,$peka);
        $isOpenEditing = $this->lib_sub_core->getOpenEditing($this->modul_id,$peka);

        if ( $getLastactivity == 0 ) { 
                
        }else{
            if($isOpenEditing){

            }else{
                unset($actions['edit']);    
            }
            
        }

        return $actions;
    }


    function insertBox_master_parameter_form_detail($field,$id){
        $get=$this->input->get();
        $post=$this->input->post();
        foreach ($get as $kget => $vget) {
            if($kget!="action"){
                $in[]=$kget."=".$vget;
            }
            if($kget=="action"){
                $in[]="act=".$vget;
            }
        }
        $g=implode("&", $in);
        $return = '<script>
            var sebelum = $("label[for=\''.$this->url.'_form_detail\']").parent();
            $("label[for=\''.$this->url.'_form_detail\']").remove();
            sebelum.attr("id","'.$id.'");
            sebelum.html("");
            sebelum.removeAttr("class");
            sebelum.removeAttr("style");
            $.ajax({
                url: base_url+"processor/'.$this->urlpath.'?action=getFormDetail&formaction=addnew&'.$g.'",
                type: "post",
                data: iupb_id=0,
                success: function(data) {
                    var o = $.parseJSON(data);
                    sebelum.html(o.html);
                }       
            });
        </script>';
        return $return;
    }

    function updateBox_master_parameter_form_detail($field,$id,$value,$rowData){
        $get=$this->input->get();
        $post=$this->input->post();
        foreach ($get as $kget => $vget) {
            if($kget!="action"){
                $in[]=$kget."=".$vget;
            }
            if($kget=="action"){
                $in[]="act=".$vget;
            }
        }
        $g=implode("&", $in);
        $return = '<script>
            var sebelum = $("label[for=\''.$this->url.'_form_detail\']").parent();
            $("label[for=\''.$this->url.'_form_detail\']").remove();
            sebelum.attr("id","'.$id.'");
            sebelum.html("");
            sebelum.removeAttr("class");
            sebelum.removeAttr("style");
            $.ajax({
                url: base_url+"processor/'.$this->urlpath.'?action=getFormDetail&formaction=update&'.$g.'",
                type: "post",
                data: iupb_id=0,
                success: function(data) {
                    var o = $.parseJSON(data);
                    sebelum.html(o.html);
                }       
            });
        </script>';
        return $return;
    }

    function insertBox_master_parameter_feedback($field,$id){
        $get=$this->input->get();
        $post=$this->input->post();
        foreach ($get as $kget => $vget) {
            if($kget!="action"){
                $in[]=$kget."=".$vget;
            }
            if($kget=="action"){
                $in[]="act=".$vget;
            }
        }
        $g=implode("&", $in);
        $return = '<script>
            var sebelum = $("label[for=\''.$this->url.'_feedback\']").parent();
            $("label[for=\''.$this->url.'_feedback\']").remove();
            sebelum.attr("id","'.$id.'");
            sebelum.html("");
            sebelum.removeAttr("class");
            sebelum.removeAttr("style");
            $.ajax({
                url: base_url+"processor/'.$this->urlpath.'?action=getFormDetail&formaction=addnew&'.$g.'",
                type: "post",
                data: iupb_id=0,
                success: function(data) {
                    var o = $.parseJSON(data);
                    sebelum.html(o.html);
                }       
            });
        </script>';
        return $return;
    }

    function updateBox_master_parameter_feedback($field,$id,$value,$rowData){
        $get=$this->input->get();
        $post=$this->input->post();
        foreach ($get as $kget => $vget) {
            if($kget!="action"){
                $in[]=$kget."=".$vget;
            }
            if($kget=="action"){
                $in[]="act=".$vget;
            }
        }
        $g=implode("&", $in);
        $return = '<script>
            var sebelum = $("label[for=\''.$this->url.'_feedback\']").parent();
            $("label[for=\''.$this->url.'_feedback\']").remove();
            sebelum.attr("id","'.$id.'");
            sebelum.html("");
            sebelum.removeAttr("class");
            sebelum.removeAttr("style");
            $.ajax({
                url: base_url+"processor/'.$this->urlpath.'?action=getFormDetail&formaction=update&'.$g.'",
                type: "post",
                data: iupb_id=0,
                success: function(data) {
                    var o = $.parseJSON(data);
                    sebelum.html(o.html);
                }       
            });
        </script>';
        return $return;
    }


      
    //Ini Merupakan Standart Approve yang digunakan di erp
    function approve_view() { 
        $echo = '<script type="text/javascript">
                     function submit_ajax(form_id) {
                        return $.ajax({
                            url: $("#"+form_id).attr("action"),
                            type: $("#"+form_id).attr("method"),
                            data: $("#"+form_id).serialize(),
                            success: function(data) {
                                var o = $.parseJSON(data);
                                var last_id = o.last_id;
                                var group_id = o.group_id;
                                var modul_id = o.modul_id;
                                var url = "'.base_url().'processor/'.$this->urlpath.'";                             
                                if(o.status == true) { 
                                    $("#alert_dialog_form").dialog("close");
                                         $.get(url+"?action=update&id="+last_id+"&foreign_key=0&company_id=3&group_id="+group_id+"&modul_id="+modul_id, function(data) {
                                         $("div#form_master_parameter").html(data);
                                         
                                    });
                                    
                                }
                                    reload_grid("grid_master_parameter");
                            }
                            
                         })
                     }
                 </script>';
        $echo .= '<h1>Approve</h1><br />';
        $echo .= '<form id="form_master_parameter_approve" action="'.base_url().'processor/'.$this->urlpath.'?action=approve_process" method="post">';
        $echo .= '<div style="vertical-align: top;">';
        $echo .= 'Remark : 
                <input type="hidden" name="'.$this->main_table_pk.'" value="'.$this->input->get($this->main_table_pk).'" />
                <input type="hidden" name="modul_id" value="'.$this->input->get('modul_id').'" />
                <input type="hidden" name="lvl" value="'.$this->input->get('lvl').'" />
                <input type="hidden" name="group_id" value="'.$this->input->get('group_id').'" />
                <input type="hidden" name="iM_modul_activity" value="'.$this->input->get('iM_modul_activity').'" />
                
                <textarea name="vRemark"></textarea>
        <button type="button" onclick="submit_ajax(\'form_master_parameter_approve\')">Approve</button>';
            
        $echo .= '</div>';
        $echo .= '</form>';
        return $echo;
    } 

    function approve_process() {
        $post = $this->input->post();
        $cNip= $this->user->gNIP;
        $vName= $this->user->gName; 
        $pk = $post[$this->main_table_pk];
        $vRemark = $post['vRemark'];
        $modul_id = $post['modul_id'];
        $iM_modul_activity = $post['iM_modul_activity'];
        $lvl = $post['lvl'];

        $activity = $this->db->get_where('erp_privi.m_modul_activity', array('iM_modul_activity'=>$iM_modul_activity, 'lDeleted'=>0))->row_array();

        $field = $activity['vFieldName'];
        $update = array($field => 2);
        $this->db->where($this->main_table_pk, $pk);
        $this->db->update($this->_table, $update);

        $this->lib_sub_core->InsertActivityModule($this->ViewUPB($pk),$modul_id,$pk,$activity['iM_activity'],$activity['iSort'],$vRemark,2);

        $getLastactivity = $this->lib_sub_core->getLastactivity($modul_id,$pk);
        if ($getLastactivity != 0){
            $data = $this->db->get_where($this->_table, array($this->main_table_pk => $pk, 'lDeleted' => 0))->row_array();
            $revisi = (count($data) > 0)?intval($data['irevisi']):0;
            if ($revisi > 0){
                $soi['iupb_id'] = $data['iupb_id'];
                $soi['vrevisi'] = $revisi;
                $soi['irevisi'] = $revisi;
                $soi['ineed_revisi'] = 0;
                $soi['itype'] = 1;
                $soi['vkode_surat'] = $data['vNoDraft'].' -- After Revisi Draft';
                $this->db->insert('erpss.erpss_upb_soi_bahanbaku', $soi);
            }
        }

        $data['status']  = true;
        $data['last_id'] = $post[$this->main_table_pk];
        $data['group_id'] = $post['group_id'];
        $data['modul_id'] = $post['modul_id'];
        return json_encode($data);
    }

    //Ini Merupakan Standart Confirm yang digunakan di erp
    function confirm_view() { 
        $echo = '<script type="text/javascript">
                     function submit_ajax(form_id) {
                        return $.ajax({
                            url: $("#"+form_id).attr("action"),
                            type: $("#"+form_id).attr("method"),
                            data: $("#"+form_id).serialize(),
                            success: function(data) {
                                var o = $.parseJSON(data);
                                var last_id = o.last_id;
                                var group_id = o.group_id;
                                var modul_id = o.modul_id;
                                var url = "'.base_url().'processor/'.$this->urlpath.'";                             
                                if(o.status == true) { 
                                    $("#alert_dialog_form").dialog("close");
                                         $.get(url+"?action=update&id="+last_id+"&foreign_key=0&company_id=3&group_id="+group_id+"&modul_id="+modul_id, function(data) {
                                         $("div#form_master_parameter").html(data);
                                         
                                    });
                                    
                                }
                                    reload_grid("grid_master_parameter");
                            }
                            
                         })
                     }
                 </script>';
        $echo .= '<h1>Confirm</h1><br />';
        $echo .= '<form id="form_master_parameter_confirm" action="'.base_url().'processor/'.$this->urlpath.'?action=confirm_process" method="post">';
        $echo .= '<div style="vertical-align: top;">';
        $echo .= 'Remark : 
                <input type="hidden" name="'.$this->main_table_pk.'" value="'.$this->input->get($this->main_table_pk).'" />
                <input type="hidden" name="modul_id" value="'.$this->input->get('modul_id').'" />
                <input type="hidden" name="lvl" value="'.$this->input->get('lvl').'" />
                <input type="hidden" name="group_id" value="'.$this->input->get('group_id').'" />
                <input type="hidden" name="iM_modul_activity" value="'.$this->input->get('iM_modul_activity').'" />
                
                <textarea name="vRemark"></textarea>
        <button type="button" onclick="submit_ajax(\'form_master_parameter_confirm\')">Confirm</button>';
            
        $echo .= '</div>';
        $echo .= '</form>';
        return $echo;
    } 

    function confirm_process() {
        $post = $this->input->post();
        $cNip= $this->user->gNIP;
        $vName= $this->user->gName;
        $pk = $post[$this->main_table_pk];
        $vRemark = $post['vRemark'];
        $lvl = $post['lvl'];
        $modul_id = $post['modul_id'];
        $iM_modul_activity = $post['iM_modul_activity'];

        $activity = $this->db->get_where('erp_privi.m_modul_activity', array('iM_modul_activity'=>$iM_modul_activity, 'lDeleted'=>0))->row_array();

        $field = $activity['vFieldName'];
        $update = array($field => 2);
        $this->db->where($this->main_table_pk, $pk);
        $this->db->update('erpss.'.$this->main_table, $update);

        $this->lib_sub_core->InsertActivityModule($this->ViewUPB($pk),$modul_id,$pk,$activity['iM_activity'],$activity['iSort'],$vRemark,2);
        
        $data['status']  = true;
        $data['last_id'] = $post[$this->main_table_pk];
        $data['group_id'] = $post['group_id'];
        $data['modul_id'] = $post['modul_id'];
        return json_encode($data);
    }

    //Ini Merupakan Standart Reject yang digunakan di erp
    function reject_view() {
        $echo = '<script type="text/javascript">
                     function submit_ajax(form_id) {
                        var remark = $("#master_parameter_remark").val();
                        if (remark=="") {
                            alert("Remark tidak boleh kosong ");
                            return
                        }

                        return $.ajax({
                            url: $("#"+form_id).attr("action"),
                            type: $("#"+form_id).attr("method"),
                            data: $("#"+form_id).serialize(),
                            success: function(data) {
                                var o = $.parseJSON(data);
                                var last_id = o.last_id;
                                var group_id = o.group_id;
                                var modul_id = o.modul_id;
                                var url = "'.base_url().'processor/'.$this->urlpath.'";                             
                                if(o.status == true) { 
                                    $("#alert_dialog_form").dialog("close");
                                         $.get(url+"?action=update&id="+last_id+"&foreign_key=0&company_id=3&group_id="+group_id+"&modul_id="+modul_id, function(data) {
                                         $("div#form_master_parameter").html(data);
                                         
                                    });
                                    
                                }
                                    reload_grid("grid_master_parameter");
                            }
                            
                         })
                     }
                 </script>';
        $echo .= '<h1>Reject</h1><br />';
        $echo .= '<form id="form_master_parameter_reject" action="'.base_url().'processor/'.$this->urlpath.'?action=reject_process" method="post">';
        $echo .= '<div style="vertical-align: top;">';
        $echo .= 'Remark : 
                <input type="hidden" name="'.$this->main_table_pk.'" value="'.$this->input->get($this->main_table_pk).'" />
                <input type="hidden" name="modul_id" value="'.$this->input->get('modul_id').'" />
                <input type="hidden" name="lvl" value="'.$this->input->get('lvl').'" />
                <input type="hidden" name="group_id" value="'.$this->input->get('group_id').'" />
                <input type="hidden" name="iM_modul_activity" value="'.$this->input->get('iM_modul_activity').'" />
                
                <textarea name="vRemark" id="reject_master_parameter_remark"></textarea>
        <button type="button" onclick="submit_ajax(\'form_master_parameter_reject\')">Reject</button>';
            
        $echo .= '</div>';
        $echo .= '</form>';
        return $echo;
    }
    
    function reject_process() {
        $post = $this->input->post();
        $cNip= $this->user->gNIP;
        $vName= $this->user->gName;
        $pk = $post[$this->main_table_pk];
        $vRemark = $post['vRemark'];
        $lvl = $post['lvl'];
        $modul_id = $post['modul_id'];
        $iM_modul_activity = $post['iM_modul_activity'];

        $activity = $this->db->get_where('erp_privi.m_modul_activity', array('iM_modul_activity'=>$iM_modul_activity, 'lDeleted'=>0))->row_array();

        $field = $activity['vFieldName'];
        $update = array($field => 1);
        $this->db->where($this->main_table_pk, $pk);
        $this->db->update('erpss.'.$this->main_table, $update);

        $this->lib_sub_core->InsertActivityModule($this->ViewUPB($pk),$modul_id,$pk,$activity['iM_activity'],$activity['iSort'],$vRemark,1);

        $data['status']  = true;
        $data['last_id'] = $post[$this->main_table_pk];
        $data['group_id'] = $post['group_id'];
        $data['modul_id'] = $post['modul_id'];
        return json_encode($data);
    }



    //Standart Setiap table harus memiliki dCreate , cCreate, dUpdate, cUpdate
    function before_insert_processor($row, $postData) { 
        $postData['dCreate'] = date('Y-m-d H:i:s');
        $postData['cCreate'] = $this->user->gNIP;
        $postData['lDeleted'] = 0;
        if($postData['isdraft'] == true){
            $postData['iSubmit'] = 0;
        } else {
            $postData['iSubmit'] = 1;
        }

        $postData = $this->lib_sub_core->getAutoNumberModule($this->iModul_id, $postData, $this->url);

        return $postData;
    }

    function before_update_processor($row, $postData) {
        $postData['dUpdate'] = date('Y-m-d H:i:s');
        $postData['cUpdate'] = $this->user->gNIP;
        
        if($postData['isdraft'] == true){
            $postData['iSubmit'] = 0;
        } else {
            $postData['iSubmit'] = 1;
        }
        return $postData;
    }    

    function after_insert_processor($fields, $id, $postData) { 
        $post = $this->input->post();

        // if ($postData['iSubmit'] == 1 || $postData['isdraft'] != true){
        //     $modul_id = $this->modul_id; 
        //     $activity = $this->db->get_where('erp_privi.m_modul_activity', array('iM_modul_activity'=>$post['iM_modul_activity'], 'lDeleted'=>0))->row_array();
        //     $this->lib_sub_core->InsertActivityModule($this->ViewUPB($id),$modul_id,$id,$activity['iM_activity'],$activity['iSort']);
        // }
    }

    function after_update_processor($fields, $id, $postData) {
        $post = $this->input->post();

        // if ($postData['iSubmit'] == 1){
        //     $modul_id = $this->modul_id; 
        //     $activity = $this->db->get_where('erp_privi.m_modul_activity', array('iM_modul_activity'=>$post['iM_modul_activity'], 'lDeleted'=>0))->row_array();
        //     $this->lib_sub_core->InsertActivityModule($this->ViewUPB($id),$modul_id,$id,$activity['iM_activity'],$activity['iSort']);
        // }

    }
    function manipulate_insert_button($buttons){
         $cNip= $this->user->gNIP;
         $data['upload']='upload_custom_grid';
         $js = $this->load->view('js/standard_js',$data,TRUE);
        //$js .= $this->load->view('js/upload_js');

        $iframe = '<iframe name="'.$this->url.'_frame" id="'.$this->url.'_frame" height="0" width="0"></iframe>';
        
        $save_draft = '<button onclick="javascript:save_draft_btn_multiupload(\''.$this->url.'\', \' '.base_url().'processor/'.$this->urlpath.'?draft=true&company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').'\',this,true )"  id="button_save_draft_'.$this->url.'"  class="ui-button-text icon-save" >Save as Draft</button>';
        $save = '<button onclick="javascript:save_btn_multiupload(\''.$this->url.'\', \' '.base_url().'processor/'.$this->urlpath.'?company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').' \',this,true )"  id="button_save_submit_'.$this->url.'"  class="ui-button-text icon-save" >Save</button>';

        $AuthModul = $this->lib_sub_core->getAuthorModul($this->modul_id);
        $arrTeam = explode(',',$this->team);
        $nipAuthor = explode(',', $AuthModul['vNip_author']);

        if( in_array($AuthModul['vDept_author'],$arrTeam )  || in_array($this->user->gNIP, $nipAuthor) || $this->isAdmin==TRUE  ){

            $buttons['save'] = $iframe.$save.$js;
        }else{
            unset($buttons['save']);
            $buttons['save'] = '<span style="color:red;" title="'.$AuthModul['vDept_author'].'">You\'re Dept not Authorized</span>';
        }
        
        
        return $buttons;
    }

    function manipulate_update_button($buttons, $rowData) { 
        $peka=$rowData[$this->main_table_pk];
        $iupb_id = 0;

        //Load Javascript In Here 
        $cNip= $this->user->gNIP;
        $data['upload'] = 'uploadCustomGrid';
        $js = $this->load->view('js/standard_js', $data, TRUE);
        $js .= $this->load->view('js/upload_js');

        $iframe = '<iframe name="master_parameter_frame" id="master_parameter_frame" height="0" width="0"></iframe>';
        
        if ($this->input->get('action') == 'view') {
            unset($buttons['update']);
        }
        else{ 
            
            $sButton = $iframe.$js;

            $isOpenEditing = $this->lib_sub_core->getOpenEditing($this->modul_id,$peka);

            if($isOpenEditing){
                $update_draft = '<button onclick="javascript:update_draft_btn(\''.$this->url.'\', \' '.base_url().'processor/'.$this->urlpath.'?draft=true \',this,true )"  id="button_update_draft_master_parameter"  class="ui-button-text icon-save" >Update open Editing</button>';
                $sButton .= $update_draft;
            }else{


                $activities = $this->lib_sub_core->get_current_module_activities($this->modul_id,$peka);
                $getLastStatusApprove = $this->lib_sub_core->getLastStatusApprove($this->modul_id,$peka); 

                foreach ($activities as $act) {
                    $update_draft = '<button onclick="javascript:update_draft_btn(\''.$this->url.'\', \' '.base_url().'processor/'.$this->urlpath.'?draft=true&modul_id='.$this->input->get('modul_id').'&iM_modul_activity='.$act['iM_modul_activity'].' \',this,true )"  id="button_update_draft_master_parameter"  class="ui-button-text icon-save" >Update</button>';

                    $update = '<button onclick="javascript:update_btn_back(\''.$this->url.'\', \' '.base_url().'processor/'.$this->urlpath.'?company_id='.$this->input->get('company_id').'&modul_id='.$this->input->get('modul_id').'&iM_modul_activity='.$act['iM_modul_activity'].'&group_id='.$this->input->get('group_id').'modul_id='.$this->input->get('modul_id').' \',this,false )"  id="button_update_submit_master_parameter"  class="ui-button-text icon-save" >Update</button>';

                    $approve = '<button onclick="javascript:load_popup(\' '.base_url().'processor/'.$this->urlpath.'?action=approve&iM_modul_activity='.$act['iM_modul_activity'].'&iM_activity='.$act['iM_activity'].'&'.$this->main_table_pk.'='.$peka.'&iupb_id='.$iupb_id.'&company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').' \')"  id="button_approve_master_parameter"  class="ui-button-text icon-save" >Approve</button>';

                    $reject = '<button onclick="javascript:load_popup(\' '.base_url().'processor/'.$this->urlpath.'?action=reject&iM_modul_activity='.$act['iM_modul_activity'].'&iM_activity='.$act['iM_activity'].'&'.$this->main_table_pk.'='.$peka.'&iupb_id='.$iupb_id.'&company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').' \' )"  id="button_reject_master_parameter"  class="ui-button-text icon-save" >Reject</button>';

                    $confirm = '<button onclick="javascript:load_popup(\' '.base_url().'processor/'.$this->urlpath.'?action=confirm&iM_modul_activity='.$act['iM_modul_activity'].'&iM_activity='.$act['iM_activity'].'&'.$this->main_table_pk.'='.$peka.'&iupb_id='.$iupb_id.'&company_id='.$this->input->get('company_id').'&group_id='.$this->input->get('group_id').'&modul_id='.$this->input->get('modul_id').' \')"  id="button_approve_master_parameter"  class="ui-button-text icon-save" >Confirm</button>';

                    

                    switch ($act['iType']) {
                        case '1':
                            # Update
                            $sButton .= $update;
                            break;
                        case '2':
                            # Approval
                            if($getLastStatusApprove){
                                $sButton .= $approve.$reject;
                            }else{
                                $sButton .= 'Last Activity Reject';
                            }
                            
                            break;
                        case '3':
                            # Confirmation
                            if($getLastStatusApprove){
                                $sButton .= $confirm;
                            }else{
                                $sButton .= 'Last Activity Reject';
                            }
                            
                            break;
                        default:
                            # Update
                            $sButton .= $update_draft;
                            break;
                    }
                    $arrNipAssign = explode(',',$act['vNip_assigned'] );
                    $arrTeam = explode(',',$this->team);

                    $arrTeamID = explode(',',$this->teamID);
                    // $upbTeamID = $this->lib_sub_core->upbTeam($peka);

                    if(in_array($act['vDept_assigned'], $arrTeam ) || in_array($this->user->gNIP, $arrNipAssign)  ){
                        
                        /*// jika Dept id yang ditunjuk ada pada team id yang dimiliki
                        if(in_array($upbTeamID[$act['vDept_assigned']], $arrTeamID) || in_array($this->user->gNIP, $arrNipAssign) ){*/
                            //get manager from Team ID
                            // $magrAndCief = $this->lib_sub_core->managerAndChief(20);

                            // // jika activitynya approval keatas
                            // if($act['iType'] > 1){
                            //     // nip harus ada pada nip manager atau chief dari Dept, atau nip yang ditunjuk di table modul activity
                            //     $arrmgrAndCief = explode(',', $magrAndCief);
                            //     if(in_array($this->user->gNIP, $arrmgrAndCief) ||in_array($this->user->gNIP, $arrNipAssign)){
                                    
                            //     }else{
                            //         $sButton = '<span style="color:red;" title="'.print_r($arrmgrAndCief).'">You\'re not Authorized to Approve</span>';
                            //     }
                            // }

                        /*}else{
                            $sButton = '<span style="color:red;" arrTeamID="'.$this->teamID.'" title="'.$upbTeamID[$act['vDept_assigned']].'" >You\'re Team not Authorized </span>';
                        }*/


                        

                    }else{
                        $sButton = '<span style="color:red;" title="'.$act['vDept_assigned'].'">You\'re Dept not Authorized</span>';
                        
                    }
                }
            }

            $buttons['update'] = $sButton;
            
        }
        
        return $buttons;
    }

    

    function whoAmI($nip) { 
        $sql = 'select b.vDescription as vdepartemen,a.*,b.*,c.iLvlemp 
                        from hrd.employee a 
                        join hrd.msdepartement b on b.iDeptID=a.iDepartementID
                        join hrd.position c on c.iPostId=a.iPostID
                        where a.cNip ="'.$nip.'"
                        ';
        
        $data = $this->db->query($sql)->row_array();
        return $data;
    }

    function download($vFilename) { 
        $this->load->helper('download');        
        $name = $vFilename;
        $id = $_GET['id'];
        $tempat = $_GET['path'];    
        $path = file_get_contents('./files/erpss/'.$tempat.'/'.$id.'/'.$name);    
        force_download($name, $path);
    }

    //Output
    public function output(){
        $this->index($this->input->get('action'));
    }


    private function ViewUPB ($id=0){
        $upb = $this->db->get_where($this->main_table, array($this->main_table_pk=>$id, 'lDeleted'=>0))->result_array();
        $arrUPB = array();
        foreach ($upb as $u) {
            if (isset($u['iupb_id'])){
                array_push($arrUPB, $u['iupb_id']);
            }
        }
        return $arrUPB;
    }



}
