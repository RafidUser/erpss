<?php
class lib_erpss
{
    private $_ci;
    private $sess_auth;
    public function __construct()
    {
        $this->_ci = &get_instance();
        $this->_ci->load->library('Zend', 'Zend/Session/Namespace');
        $this->lib_sub_core = $this->_ci->load->library('lib_sub_core');
        $this->sess_auth = new Zend_Session_Namespace('auth');
        $this->logged_nip = $this->sess_auth->gNIP;
        //$this->arrUpper = array();

        //$this->updetIformula();
    }
    public function user()
    {
        return $this->sess_auth;
    }

    function getUploadFileFromField($iModul_id)
    {
        $where = array(
            "m_modul_fields.iM_jenis_field" => 16, "m_modul.idprivi_modules" => $iModul_id, "sys_masterdok.ldeleted" => 0
        );
        $this->_ci->db->select("*")
            ->from("erp_privi.m_modul_fields")
            ->join("erp_privi.m_modul", "m_modul.iM_modul=m_modul_fields.iM_modul")
            ->join("erp_privi.sys_masterdok", "sys_masterdok.iM_modul_fields=m_modul_fields.iM_modul_fields")
            ->where($where);
        $q = $this->_ci->db->get();
        $row = array();
        if ($q->num_rows() >= 1) {
            $row = $q->result_array();
        }
        return $row;
    }

    function getIModulID($modul_id)
    {
        $sql = 'select *
				from erp_privi.m_modul a 
				where a.lDeleted=0 
				and a.idprivi_modules="' . $modul_id . '"

				';
        /* echo $sql; */
        $query = $this->_ci->db->query($sql);
        $jmlRow = $query->num_rows();
        $rows = array();
        if ($jmlRow > 0) {
            $rows = $query->row_array();
        }
        $return = isset($rows['iM_modul']) ? $rows['iM_modul'] : '';
        return $return;
    }

    function load_raw($post)
    {

        $modul_field    = $post['modul_field'];
        $field_name     = $post['field_name'];
        $field_id       = $post['field_id'];
        $upb            = $post['upb'];
        $key            = $post['key'];

        $listLPB        = array();
        $dtField        = $this->_ci->db->get_where('erp_privi.m_modul_fields', array('iM_modul_fields' => $modul_field))->row_array();

        if (!empty($dtField) && !empty($upb)) {
            $sqlLPB     = $dtField['vSource_input'];
            $listLPB    = $this->_ci->db->query($sqlLPB, array($upb, $key))->result_array();
        }
        echo json_encode($listLPB);
    }


    public function hasTeam($nip)
    {
        $teams = '';
        $sql = "SELECT t.vTipe FROM plc3.team t
					WHERE t.lDeleted = 0
						AND t.vNip =  ?

					UNION
					SELECT t1.vTipe FROM plc3.team_item ti
					JOIN plc3.team t1 ON t1.iTeam=ti.iTeam
					WHERE ti.lDeleted = 0
						AND ti.vNip = ? ";

        $query = $this->_ci->db->query($sql, array($nip, $nip));
        $jmlRow = $query->num_rows();
        if ($jmlRow > 0) {
            $rows = $query->result_array();
            $i = 0;
            foreach ($rows as $data) {
                $teams = ($i == 0) ? $data['vTipe'] : $teams . ',' . $data['vTipe'];
                $i++;
            }
        }

        return $teams;
    }

    public function hasTeamID($nip)
    {
        $teams = '';
        $sql = "SELECT t.iTeam FROM plc3.team t
					WHERE t.lDeleted = 0
						AND t.vNip =  ?

					UNION
					SELECT t1.iTeam FROM plc3.team_item ti
					JOIN plc3.team t1 ON t1.iTeam=ti.iTeam
					WHERE ti.lDeleted = 0
						AND ti.vNip = ? ";

        /*echo $sql;*/
        $query = $this->_ci->db->query($sql, array($nip, $nip));
        $jmlRow = $query->num_rows();
        if ($jmlRow > 0) {
            $rows = $query->result_array();
            $i = 0;
            foreach ($rows as $data) {
                $teams = ($i == 0) ? $data['iTeam'] : $teams . ',' . $data['iTeam'];
                $i++;
            }
        }

        return $teams;
    }

    public function hasTeamCode($nip)
    {
        $teams = '';
        $sql = "SELECT t.cTeam FROM plc3.team t
					WHERE t.lDeleted = 0
						AND t.vNip =  ?

					UNION
					SELECT t1.cTeam FROM plc3.team_item ti
					JOIN plc3.team t1 ON t1.iTeam=ti.iTeam
					WHERE ti.lDeleted = 0
						AND ti.vNip = ? ";

        /*echo $sql;*/
        $query = $this->_ci->db->query($sql, array($nip, $nip));
        $jmlRow = $query->num_rows();
        if ($jmlRow > 0) {
            $rows = $query->result_array();

            foreach ($rows as $i => $data) {
                if (!empty($data['cTeam'])) {
                    $teams = (empty($teams)) ? '"' . $data['cTeam'] . '"' : '' . $teams . ',"' . $data['cTeam'] . '"';
                }
            }
        }

        return $teams;
    }

    public function getNamaTeamUPB($iupb_id)
    {
        $sql = 'select
				*
				,ifnull(b.vTeam,"-") as nmPD
				,ifnull(c.vTeam,"-") as nmBD
				,ifnull(d.vTeam,"-") as nmMKT
				,ifnull(e.vTeam,"-") as nmQA
				,ifnull(f.vTeam,"-") as nmAD


				from plc3.upb a
				left join plc3.team b on b.cTeam=a.cTeam_pd
				left join plc3.team c on c.cTeam=a.cTeam_bd
				left join plc3.team d on d.cTeam=a.cTeam_marketing
				left join plc3.team e on e.cTeam=a.cTeam_qa
				left join plc3.team f on f.cTeam=a.cTeam_ad
				where a.lDeleted=0
				and a.vUpb_no="' . $iupb_id . '"

				';
        $query = $this->_ci->db->query($sql);
        $jmlRow = $query->num_rows();
        if ($jmlRow > 0) {
            $rows = $query->row_array();
        }

        return $rows;
    }

    public function managerAndChief($iteam_id)
    {
        $nips = '';
        $sql = 'SELECT t.vNip FROM plc3.team t
				WHERE t.lDeleted = 0 AND t.iTeam = ?
				UNION
				SELECT ti.vNip FROM plc3.team_item ti
				JOIN plc3.team t1 ON t1.iTeam=ti.iTeam
				WHERE ti.lDeleted = 0
				AND ti.iapprove = 1
				AND t1.iTeam = ?';

        /*echo $sql;
        exit;*/
        $query = $this->_ci->db->query($sql, array($iteam_id, $iteam_id));
        $jmlRow = $query->num_rows();
        if ($jmlRow > 0) {
            $rows = $query->result_array();
            $i = 0;
            foreach ($rows as $data) {
                if ($i == 0) {
                    $nips = $data['vNip'];
                } else {
                    $nips .= ',' . $data['vNip'];
                }
                $i++;
            }
        }

        return $nips;
    }

    public function managerAndChiefIn($iteam_id)
    {
        $nips = '';
        $sql =
            'SELECT t.vNip
					from plc3.team t
					WHERE t.lDeleted=0
					AND t.iTeam in (' . $iteam_id . ')


					union

					select ti.vNip
					from plc3.team_item ti
					join plc3.team t1 on t1.iTeam=ti.iTeam
					where ti.lDeleted=0
					and ti.iapprove=1
					and t1.iTeam in (' . $iteam_id . ')
				';

        /*echo $sql;
        exit;*/
        $query = $this->_ci->db->query($sql);
        $jmlRow = $query->num_rows();
        if ($jmlRow > 0) {
            $rows = $query->result_array();
            $i = 0;
            foreach ($rows as $data) {
                if ($i == 0) {
                    $nips = $data['vNip'];
                } else {
                    $nips .= ',' . $data['vNip'];
                }
                $i++;
            }
        }

        return $nips;
    }

    public function managerAndChiefInCode($vTeam)
    {
        $arrvTeam = explode(',', $vTeam);
        $iteam_id = '0';
        foreach ($arrvTeam as $key => $vTim) {
            $sql = "SELECT t.iTeam
					FROM plc3.team t
					WHERE t.lDeleted = 0
					AND t.vTipe = ? ";
            $rteams = $this->_ci->db->query($sql, array($vTim))->result_array();

            foreach ($rteams as $rteam) {
                $iteam_id .= ',' . $rteam['iTeam'];
            }
        }

        $nips = '';
        $sql =
            'SELECT t.vNip
					from plc3.team t
					WHERE t.lDeleted=0
					AND t.iTeam in (' . $iteam_id . ')


					union

					select ti.vNip
					from plc3.team_item ti
					join plc3.team t1 on t1.iTeam=ti.iTeam
					where ti.lDeleted=0
					and ti.iapprove=1
					and t1.iTeam in (' . $iteam_id . ')
				';

        /* echo '<pre>'.$sql;
        exit; */
        $query = $this->_ci->db->query($sql);
        $jmlRow = $query->num_rows();
        if ($jmlRow > 0) {
            $rows = $query->result_array();
            $i = 0;
            foreach ($rows as $data) {
                if ($i == 0) {
                    $nips = $data['vNip'];
                } else {
                    $nips .= ',' . $data['vNip'];
                }
                $i++;
            }
        }

        return $nips;
    }

    public function upbTeam($iupb_id)
    {
        $sql = 'select
					ifnull(a.iTeamPD,0) as PD
					, ifnull(a.iTeamAndev,0) as AD
					, 10 as QA
			from reformulasi.export_req_refor a
			where a.lDeleted=0
			and a.iexport_req_refor= "' . $iupb_id . '"';

        //echo $sql;
        $query = $this->_ci->db->query($sql);
        $jmlRow = $query->num_rows();
        $rows = array();
        if ($jmlRow > 0) {
            $rows = $query->row_array();
        }

        return $rows;
    }

    public function isAdmin($isNiP = 0)
    {
        $this->_ci->db->like('vContent', $isNiP);
        $this->_ci->db->where('cVariable', 'ADMINPLCACCESS');
        $query = $this->_ci->db->get('plc2.plc_sysparam');
        $ret = false;
        if ($query->num_rows() > 0) {
            $ret = true;
        }
        return $ret;
    }

    public function whoAmI($nip)
    {
        $sql = 'select b.vDescription as vdepartemen,a.*,b.*,c.iLvlemp
                        from hrd.employee a
                        left join hrd.msdepartement b on b.iDeptID=a.iDepartementID
                        left join hrd.position c on c.iPostId=a.iPostID
                        where a.cNip ="' . $nip . '"
                        ';

        $data = $this->_ci->db->query($sql)->row_array();
        return $data;
    }




    /*get activity  modul*/
    public function gridFilterUPBbyTeam($grid, $modul_id)
    {
        $nip = $this->user()->gNIP;
        $isAdmin = $this->isAdmin($nip);
        if (!$isAdmin) {
            $arrTeam = explode(',', $this->hasTeam($nip));
            $AuthModul = $this->lib_sub_core->getAuthorModul($modul_id);
            $nipAuthor = (!empty($AuthModul)) ? explode(',', $AuthModul['vNip_author']) : array();
            $nipParticipant = (!empty($AuthModul)) ? explode(',', $AuthModul['vNip_author']) : array();
            $teamID = $this->hasTeamCode($nip);

            if (in_array('PD', $arrTeam)) {
                $grid->setQuery('plc3.upb.cTeam_pd in (' . $teamID . ')', null);
            } else if (in_array('AD', $arrTeam)) {
                $grid->setQuery('plc3.upb.cTeam_ad in (' . $teamID . ')', null);
            } else if (in_array('QA', $arrTeam)) {
                $grid->setQuery('plc3.upb.cTeam_qa in (' . $teamID . ')', null);
            } else if (in_array('BD', $arrTeam)) {
                $grid->setQuery('plc3.upb.cTeam_bd in (' . $teamID . ')', null);
            } else if (in_array('QC', $arrTeam)) {
                $grid->setQuery('plc3.upb.cTeam_qc in (' . $teamID . ')', null);
            } else if (in_array('MR', $arrTeam)) {
                $grid->setQuery('plc3.upb.cTeam_marketing in (' . $teamID . ')', null);
            } else if (in_array($nip, $nipAuthor) || in_array($nip, $nipParticipant)) {
            }
        }
    }

    public function queryFilterUPBbyTeam($modul_id, $tableAlias = 'export_req_refor')
    {
        $nip = $this->user()->gNIP;
        $isAdmin = $this->isAdmin($nip);
        $filter = '';
        if (!$isAdmin) {
            $arrTeam = explode(',', $this->hasTeam($nip));
            $AuthModul = $this->lib_sub_core->getAuthorModul($modul_id);
            $nipAuthor = explode(',', $AuthModul['vNip_author']);
            $nipParticipant = explode(',', $AuthModul['vNip_author']);
            $teamID = $this->hasTeamID($nip);

            if (in_array('PD', $arrTeam)) {
                $filter = ' AND ' . $tableAlias . '.iTeamPD IN (' . $teamID . ')';
            } else if (in_array('AD', $arrTeam)) {
                $filter = ' AND ' . $tableAlias . '.iTeamAndev IN (' . $teamID . ')';
                // }else if(in_array('QA', $arrTeam)){
                //     $filter = ' AND '.$tableAlias.'.iteamqa_id IN ('.$teamID.')';
                // }else if(in_array('BD', $arrTeam)){
                //     $filter = ' AND '.$tableAlias.'.iteambusdev_id IN ('.$teamID.')';
                // }else if( in_array($nip, $nipAuthor )|| in_array($nip, $nipParticipant)  ){

            }
        }
        return $filter;
    }


    public function getDetailData($pk, $id)
    {
        $sql = '';
        $data = array();
        switch ($pk) {
            case 'iSetting_prioritas':
                $sql = '
						SELECT
						a.vNo_prioritas AS "No Setting"
						, a.iTahun AS "Tahun"
						,a.iSemester AS "Semester"
						FROM plc3.setting_prioritas a
						WHERE a.lDeleted=0
						AND a.iSetting_prioritas=?
				';
                break;
            case 'iSkalaTrial':
                $sql = '
						SELECT b.vUpb_no AS "No UPB"
						,b.dTgl_upb AS "Tgl UPB"
						,b.vNama_usulan AS "Nama Usulan"
						,a.vNoFormula AS "No Formula"
						FROM plc3.skala_trial a
						JOIN plc3.upb b ON b.vUpb_no=a.vUpb_no
						WHERE a.lDeleted=0
						AND a.iSkalaTrial=?
				';
                break;

            default:
                $sql = '';
                break;
        }
        if (!empty($sql)) {
            $data = $this->_ci->db->query($sql, $id)->row_array();
        }
        return $data;
    }

    public function generateAndSendNotificationPersonal($team, $subject, $content, $data,$to,$cc){

        if ( !empty($data) ){
            $html   = '<p>Kepada Yth. Bapak / Ibu</p>';
            $html   .= '<br>';
            $html   .= '<p>'.$content.', dengan rincian sebagai berikut : </p>';
            $html   .= '<br>';
            $html   .= '<table class="table-notification">';
            foreach ($data as $key => $value) {
                $html   .= '   <tr>';
                $html   .= '       <td> '.$key.' </td>';
                $html   .= '       <td> : </td>';
                $html   .= '       <td> '.$value.' </td>';
                $html   .= '   </tr>';
            }
            $html   .= '        <tr>';
            $html   .= '            <td>Link Aplikasi</td>';
            $html   .= '            <td> : </td>';
            $html   .= '            <td> http://www.npl-net.com/erp </td>';
            $html   .= '        </tr>';
            
            $html   .= '</table>';
            $html   .= '<br>';
            $html   .= '<p>Terima Kasih</p>';
            $html   .= '<style>
            .table-notification{
                margin-left     : 10px;
                border-collapse : collapse;
            }
            .table-notification tr td{
                /*border            : 2px solid #222;*/
                border-collapse : collapse;
                padding         : 5px;
            }
            </style>';
            
            $cc       = $cc.','.$this->user()->gNIP;
            
            // print_r($html);
            $notification = $this->_ci->load->library('sess_auth');
            $notification->send_message_erp($this->_ci->uri->segment_array(),$to, $cc, $subject, $html);
        }
    }

    public function generateAndSendNotificationPersonalUat($team, $subject, $content, $data,$to,$cc){

        if ( !empty($data) ){
            $html   = '<p>Kepada Yth. Bapak / Ibu</p>';
            $html   .= '<br>';
            $html   .= '<p>'.$content.', dengan rincian sebagai berikut : </p>';
            $html   .= '<br>';
            $html   .= '<table class="table-notification">';
            foreach ($data as $key => $value) {
                $html   .= '   <tr>';
                $html   .= '       <td> '.$key.' </td>';
                $html   .= '       <td> : </td>';
                $html   .= '       <td> '.$value.' </td>';
                $html   .= '   </tr>';
            }
            $html   .= '        <tr>';
            $html   .= '            <td>Link Aplikasi</td>';
            $html   .= '            <td> : </td>';
            $html   .= '            <td> http://www.npl-net.com/erp </td>';
            $html   .= '        </tr>';
            
            $html   .= '</table>';
            $html   .= '<br>';
            $html   .= '<p>Terima Kasih</p>';
            $html   .= '<style>
            .table-notification{
                margin-left     : 10px;
                border-collapse : collapse;
            }
            .table-notification tr td{
                /*border            : 2px solid #222;*/
                border-collapse : collapse;
                padding         : 5px;
            }
            </style>';
            
            $cc       = $cc.','.$this->user()->gNIP;
            
            // print_r($html);
            $notification = $this->_ci->load->library('sess_auth');
            $notification->send_message_erp_uat($this->_ci->uri->segment_array(),$to, $cc, $subject, $html);
        }
    }

    public function generateAndSendNotification($team, $subject, $content, $data)
    {
        $arrTeam = explode(',', $team);
        $teams = '';
        foreach ($arrTeam as $tm) {
            $teams = (!empty($teams)) ? $teams . ', "' . $tm . '"' : '"' . $tm . '"';
        }

        $sqlTeam = 'SELECT t.vNip  FROM plc3.team t WHERE t.lDeleted = 0 /*AND t.iCompanyId = 3*/ AND t.vTipe IN (' . $teams . ')
						UNION
						SELECT i.vNip FROM plc3.team_item i
						JOIN plc3.team t ON i.iTeam = t.iTeam
						WHERE i.lDeleted = 0 /*AND t.iCompanyId = 3*/ AND t.vTipe IN (' . $teams . ')';

        $dataTeam = $this->_ci->db->query($sqlTeam)->result_array();

        if (!empty($data)) {

            $html = '<p>Kepada Yth. Bapak / Ibu</p>';
            $html .= '<br>';
            $html .= '<p>' . $content . ', dengan rincian sebagai berikut : </p>';
            $html .= '<br>';
            $html .= '<table class="table-notification">';
            foreach ($data as $key => $value) {
                $html .= '   <tr>';
                $html .= '       <td> ' . $key . ' </td>';
                $html .= '       <td> : </td>';
                $html .= '       <td> ' . $value . ' </td>';
                $html .= '   </tr>';
            }
            $html .= ' 		<tr>';
            $html .= '       		<td>Link Aplikasi</td>';
            $html .= '       		<td> : </td>';
            $html .= '       		<td> http://www.npl-net.com/erp </td>';
            $html .= '   		</tr>';

            $html .= '</table>';
            $html .= '<br>';
            $html .= '<p>Terima Kasih</p>';
            $html .= '<style>
            				.table-notification{
            					margin-left 	: 10px;
								border-collapse : collapse;
            				}
							.table-notification tr td{
								/*border 			: 2px solid #222;*/
								border-collapse : collapse;
								padding 		: 5px;
							}
            			</style>';

            $to = '';
            $cc = $this->user()->gNIP;
            //$dataTeam = array_unique($dataTeam['vNip']);
            foreach ($dataTeam as $ad) {
                $to = ($to == '') ? $ad['vNip'] : $to . ',' . $ad['vNip'];
            }

            $notification = $this->_ci->load->library('sess_auth');
            $notification->send_message_erp($this->_ci->uri->segment_array(), $to, $cc, $subject, $html);
        }
    }

    public function getCompany()
    {
        $getComp = $this->_ci->db->get_where('hrd.employee', array('cNip' => $this->user()->gNIP))->row_array();
        $iCompanyID = (!empty($getComp)) ? $getComp['iCompanyID'] : 0;
        // $iCompanyID = $this->input->get('company_id');
        return $iCompanyID;
    }

    public function getUpper($nip, $notfirst = false)
    {

        $sql = 'SELECT e.cNip, pe.iLvlemp AS lvl_emp, u.cNip AS cUpper, pu.iLvlemp AS lvl_upper,
					u.iDivisionID, u.iCompanyID
				FROM hrd.employee e
				JOIN hrd.position pe ON e.iPostID = pe.iPostId
				JOIN hrd.employee u ON e.cUpper = u.cNip
				JOIN hrd.position pu ON u.iPostID = pu.iPostId
				WHERE
				e.cNip = "' . $nip . '"
				GROUP BY e.cNip';
        /* echo '<pre>'.$sql; */
        $emp = $this->_ci->db->query($sql)->row_array();
        if (!empty($emp)) {
            //apabila  user level >= 3
            if (intval($emp['lvl_emp']) >= 3 and intval($emp['lvl_emp']) <= 6 and !$notfirst) {
                array_push($this->arrUpper, $emp['cNip']);
            }

            $this->getUpper($emp['cUpper']);
        }
    }

    // untuk keperluan setting prioritas
    function getCurrent_modul($iupb_id)
    {
        $sql = 'sELECT d.vNameModule as vNama_modul,b.dPassed,0 as iUrut
                FROM plc3.rails_upb_h a
                JOIN plc3.upb u on u.vUpb_no = a.vUpb_no
                JOIN plc3.rails_upb_d b ON b.rails_code_h=a.rails_code_h
                JOIN erp_privi.m_modul c ON c.vCodeModule=b.vCodeModule
                join erp_privi.privi_modules d on d.idprivi_modules = c.idprivi_modules
                WHERE a.lDeleted=0 AND b.lDeleted=0 
                AND u.iUpb="'.$iupb_id.'"
                AND a.iActive=1 AND b.iPassed=1 
                
                UNION
                
                SELECT d.vNameModule as vNama_modul,b.dPassed,0 as iUrut
                FROM plc3.rails_formula_h a
                JOIN plc3.upb u on u.vUpb_no = a.vUpb_no
                JOIN plc3.rails_formula_d b ON b.rails_code_h=a.rails_code_h
                JOIN erp_privi.m_modul c ON c.vCodeModule=b.vCodeModule
                join erp_privi.privi_modules d on d.idprivi_modules = c.idprivi_modules
                WHERE a.lDeleted=0 AND b.lDeleted=0 
                AND u.iUpb="'.$iupb_id.'"
                AND a.iActive=1 AND b.iPassed=1
                ORDER BY dPassed DESC

                LIMIT 1
        ';
        // echo '<pre>'.$sql;
        // exit;
        $dMod = $this->_ci->db->query($sql)->row_array();
        if (empty($dMod)) {
            $data['vNama_modul'] = 'Log Modul tidak ditemukan';
            $data['iUrut'] = 0;
        } else {
            $data['vNama_modul'] = $dMod['vNama_modul'];
            $data['iUrut'] = $dMod['iUrut'];
        }

        //echo $dlast['vNameModule'];

        // $sql = 'select c.vNama_modul,d.iUrut
        // 		from plc3.m_modul_log_upb a 
        // 		join plc3.m_modul_log_activity b on b.iM_modul_log_activity=a.iM_modul_log_activity
        // 		join plc3.m_modul c on c.idprivi_modules=b.idprivi_modules 
        // 		join plc3.m_flow_proses d on d.iM_modul=c.iM_modul and d.iM_flow=1
        // 		where a.lDeleted=0 and  a.iupb_id = "'.$iupb_id.'" 
        // 		order by d.iUrut DESC
        // 		limit 1';

        // $dMod = $this->_ci->db->query($sql)->row_array();

        // if(empty($dMod)){
        // 	$data['vNama_modul'] = 'Log Modul tidak ditemukan';
        // 	$data['iUrut'] = 0;
        // }else{
        // 	$data['vNama_modul'] = $dMod['vNama_modul'];
        // 	$data['iUrut'] = $dMod['iUrut'];
        // }

        return $data;
    }

    public function getAllUpper($nip)
    {
        $this->arrUpper = array();
        /* SPV sampai Manager */
        $this->getUpper($nip, true);
        return $this->arrUpper;
    }

    function generateFilename ($filename, $urut=0){
        $exDot = explode('.', $filename);
        $ext = $exDot[count($exDot)-1];
        $generated = str_replace(' ', '_', $filename);
        $generated = str_replace('.'.$ext, '', $generated);
        $generated = preg_replace('/[^A-Za-z0-9\-]/', '_', $generated);
        $dateNow = date('Y_m_d__H_i_s');
        $nameGenerated = $urut.'__'.$dateNow.'__'.$generated.'.'.$ext;
        return $nameGenerated;
    }

    /*  */

    /*get activity  modul*/


    public function MigrasiInsertActivityModule($iupb_ids, $modul_id, $iKey_id, $iM_activity, $iSort, $vRemark = '', $iApprove = 0, $dapp, $capp)
    {
        $isTrue = 0;
        $data = array();
        $data['iKey_id'] = $iKey_id;
        $data['idprivi_modules'] = $modul_id;
        $data['iM_activity'] = $iM_activity;
        $data['iSort'] = $iSort;

        $data['vRemark'] = $vRemark;
        $data['iApprove'] = $iApprove;
        $data['dCreate'] = $dapp;
        $data['cCreated'] = $capp;

        /* check sudah ada belum , jika sudah / jangan masukin */
        $cekLog = 'SELECT *
						FROM erp_privi.m_modul_log_activity_plcotc a
						WHERE a.iKey_id= "' . $iKey_id . '"
						and a.idprivi_modules= "' . $modul_id . '"
						and a.iM_activity= "' . $iM_activity . '"
						and a.iSort= "' . $iSort . '"
						';
        /* echo '<pre>'.$cekLog;
        exit; */
        $dCekLog = $this->_ci->db->query($cekLog)->result_array();
        if (!empty($dCekLog)) {
            $isTrue = 1;
        } else {
            /* hanya yang belum dimigrasikan saja */
            $ins = $this->_ci->db->insert('erp_privi.m_modul_log_activity_plcotc', $data);
            $insertID = $this->_ci->db->insert_id();
        }

        return $isTrue;
    }
}
