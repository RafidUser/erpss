<?php
class lib_manufacturing { 	
	private $_ci;
    private $sess_auth;
    function __construct() {
        $this->_ci=&get_instance();
        $this->_ci->load->library('Zend', 'Zend/Session/Namespace');
        $this->sess_auth = new Zend_Session_Namespace('auth');
		$this->logged_nip = $this->sess_auth->gNIP;
		$this->arrUpper = array();
		
		//$this->updetIformula();
    }
    function user() {
		return $this->sess_auth;
	}
	

	/*-------------------------------------------------  auth    					start-------------------------------------------------------*/

	

	/*-------------------------------------------------  auth       				end --------------------------------------------------------*/

	


	function is_in_array($array, $key, $key_value){
		// fungsinya untuk in_array pada array associatif
        $within_array = 'no';
        foreach( $array as $k=>$v ){
          if( is_array($v) ){
              $within_array = $this->is_in_array($v, $key, $key_value);
              if( $within_array == 'yes' ){
                  break;
              }
          } else {
                  if( $v == $key_value && $k == $key ){
                          $within_array = 'yes';
                          break;
                  }
          }
        }
        return $within_array;
  	}

	/*get activity  modul*/
	
	function hasTeam($nip){
		$teams 	= '';
		$sql 	= "SELECT t.vTipe
					FROM ps.team t
					WHERE t.lDeleted = 0 AND t.vNip = ?

					UNION

					SELECT ts.vTipe
					FROM ps.team t1
					JOIN ps.team_sub ts ON ts.cTeam=t1.cTeam
					WHERE t1.lDeleted = 0 AND ts.lDeleted=0 AND ts.vNip = ?
										
					UNION

					SELECT ts1.vTipe
					FROM ps.team t2
					JOIN ps.team_sub ts1 ON ts1.cTeam=t2.cTeam
					JOIN ps.team_sub_item tsi ON tsi.cTeam_sub=ts1.cTeam_sub
					WHERE t2.lDeleted = 0 
					AND ts1.lDeleted=0 
					AND tsi.lDeleted=0 
					AND tsi.vNip = ?

					#selama belum migrasi team, tambahkan ini
					UNION
					SELECT t11.vTipe
					FROM ps.team t11
					JOIN ps.team_sub ts1 ON ts1.cTeam=t11.cTeam
					WHERE t11.lDeleted = 0 AND ts1.lDeleted=0 
					AND ts1.vNip = ?

					UNION

					SELECT t2.vTipe
					FROM ps.team t2
					JOIN ps.team_sub ts1 ON ts1.cTeam=t2.cTeam
					JOIN ps.team_sub_item tsi ON tsi.cTeam_sub=ts1.cTeam_sub
					WHERE t2.lDeleted = 0 
					AND ts1.lDeleted=0 
					AND tsi.lDeleted=0 
					AND tsi.vNip = ? ";
		
		$query 	= $this->_ci->db->query($sql, array($nip, $nip, $nip, $nip, $nip));
		$jmlRow = $query->num_rows();
		if ($jmlRow > 0) {
			$rows = $query->result_array();
			$i=0;
			foreach ($rows as $data ) {
				$teams = ( $i == 0 ) ? $data['vTipe'] : $teams.','.$data['vTipe'];
				$i++;
			}

		}

		return $teams;
	}

	function hasTeamID($nip){
		$teams 	= '';
		$sql 	= "SELECT t.iTeam FROM plc3.team t
					WHERE t.lDeleted = 0
						AND t.vNip =  ?

					UNION
					SELECT t1.iTeam FROM plc3.team_item ti 
					JOIN plc3.team t1 ON t1.iTeam=ti.iTeam
					WHERE ti.lDeleted = 0
						AND ti.vNip = ? ";
		
		/*echo $sql;*/
		$query 	= $this->_ci->db->query($sql, array($nip, $nip));
		$jmlRow = $query->num_rows();
		if ($jmlRow > 0) {
			$rows = $query->result_array();
			$i=0;
			foreach ($rows as $data ) {
				$teams = ( $i == 0 ) ? $data['iTeam'] : $teams.','.$data['iTeam'];
				$i++;
			}

		}

		return $teams;
	}


	function managerAndChiefInCode($vTeam){
		$arrvTeam = explode(',',$vTeam);
		$iteam_id = '"0"';
		foreach($arrvTeam as $key => $vTim){
			$sql 	= "SELECT ts.cTeam_sub
					FROM ps.team t1
					JOIN ps.team_sub ts ON ts.cTeam=t1.cTeam
					WHERE t1.lDeleted = 0 AND ts.lDeleted=0 
					AND ts.vTipe = ? ";
			$rteams 	= $this->_ci->db->query($sql, array($vTim))->result_array();

			foreach($rteams as $rteam ){
				$iteam_id .= ',"'.$rteam['cTeam_sub'].'"';
			}

			


		}

		$nips = '';
		$sql = 
				'SELECT ts.vNip
					FROM ps.team t1
					JOIN ps.team_sub ts ON ts.cTeam=t1.cTeam
					WHERE t1.lDeleted = 0 AND ts.lDeleted=0 
					AND ts.cTeam_sub in ('.$iteam_id.')

					union 

					SELECT tsi.vNip
					FROM ps.team t2
					JOIN ps.team_sub ts1 ON ts1.cTeam=t2.cTeam
					JOIN ps.team_sub_item tsi ON tsi.cTeam_sub=ts1.cTeam_sub
					WHERE t2.lDeleted = 0 
					AND ts1.lDeleted=0 
					AND tsi.lDeleted=0 
					AND tsi.iapprove=1
					AND ts1.cTeam_sub in ('.$iteam_id.')

					union 

					SELECT t11.vNip
					FROM ps.team t11
					JOIN ps.team_sub ts1 ON ts1.cTeam=t11.cTeam
					WHERE t11.lDeleted = 0 AND ts1.lDeleted=0 
					AND ts1.cTeam_sub in ('.$iteam_id.')

					
					
				';

				// echo '<pre>'.$sql;
				// exit;
		$query = $this->_ci->db->query($sql);
		$jmlRow = $query->num_rows();
		if ($jmlRow > 0) {
			$rows = $query->result_array();
			$i=0;
			foreach ($rows as $data ) {
				if($i==0){
					$nips = $data['vNip'];
				}else{
					$nips .= ','.$data['vNip'];
				}
				$i++;
			}

		}

		return $nips;

	}


	function isAdmin($isNiP=0){
		$this->_ci->db->like('value',$isNiP);
		$this->_ci->db->where('vKode','SU');
		$query = $this->_ci->db->get('ps.sysparam');
		$ret=false;
		if($query->num_rows()>0){
			$ret=true;
		}
		return $ret;
	}

	function whoAmI($nip) { 
        $sql = 'select b.vDescription as vdepartemen,a.*,b.*,c.iLvlemp 
                        from hrd.employee a 
                        join hrd.msdepartement b on b.iDeptID=a.iDepartementID
                        join hrd.position c on c.iPostId=a.iPostID
                        where a.cNip ="'.$nip.'"
                        ';
        
        $data = $this->_ci->db->query($sql)->row_array();
        return $data;
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

	
	

	function generateAndSendNotificationPersonal($team, $subject, $content, $data,$to,$cc){

        if ( !empty($data) ){

            $html  	= '<p>Kepada Yth. Bapak / Ibu</p>';
            $html 	.= '<br>';
            $html 	.= '<p>'.$content.', dengan rincian sebagai berikut : </p>';
            $html 	.= '<br>';
            $html 	.= '<table class="table-notification">';
            foreach ($data as $key => $value) {
                $html 	.= '   <tr>';
                $html 	.= '       <td> '.$key.' </td>';
                $html 	.= '       <td> : </td>';
                $html 	.= '       <td> '.$value.' </td>';
                $html 	.= '   </tr>';
            }
            $html 	.= ' 		<tr>';
            $html 	.= '       		<td>Link Aplikasi</td>';
            $html 	.= '       		<td> : </td>';
            $html 	.= '       		<td> http://www.npl-net.com/erp </td>';
            $html 	.= '   		</tr>';

            $html 	.= '</table>';
            $html 	.= '<br>';
            $html 	.= '<p>Terima Kasih</p>';
            $html 	.= '<style>
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

            $cc       = $cc.','.$this->user()->gNIP;
            
            $notification = $this->_ci->load->library('sess_auth');
            $notification->send_message_erp($this->_ci->uri->segment_array(),$to, $cc, $subject, $html);
        }
	}

	function generateAndSendNotification($team, $subject, $content, $data){
		$arrTeam 	= explode(',', $team);
		$teams 	 	= '';
		foreach ($arrTeam as $tm) {
			$teams 	= ( !empty($teams) ) ? $teams.', "'.$tm.'"' : '"'.$tm.'"';
		}

		$sqlTeam 	= 'SELECT t.vNip  FROM plc3.team t WHERE t.lDeleted = 0 /*AND t.iCompanyId = 3*/ AND t.vTipe IN ('.$teams.')
						UNION
						SELECT i.vNip FROM plc3.team_item i 
						JOIN plc3.team t ON i.iTeam = t.iTeam
						WHERE i.lDeleted = 0 /*AND t.iCompanyId = 3*/ AND t.vTipe IN ('.$teams.')';

        $dataTeam 	= $this->_ci->db->query($sqlTeam)->result_array();

        if ( !empty($data) ){

            $html  	= '<p>Kepada Yth. Bapak / Ibu</p>';
            $html 	.= '<br>';
            $html 	.= '<p>'.$content.', dengan rincian sebagai berikut : </p>';
            $html 	.= '<br>';
            $html 	.= '<table class="table-notification">';
            foreach ($data as $key => $value) {
                $html 	.= '   <tr>';
                $html 	.= '       <td> '.$key.' </td>';
                $html 	.= '       <td> : </td>';
                $html 	.= '       <td> '.$value.' </td>';
                $html 	.= '   </tr>';
            }
            $html 	.= ' 		<tr>';
            $html 	.= '       		<td>Link Aplikasi</td>';
            $html 	.= '       		<td> : </td>';
            $html 	.= '       		<td> http://www.npl-net.com/erp </td>';
            $html 	.= '   		</tr>';

            $html 	.= '</table>';
            $html 	.= '<br>';
            $html 	.= '<p>Terima Kasih</p>';
            $html 	.= '<style>
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

            $to       = '';
			$cc       = $this->user()->gNIP;
			//$dataTeam = array_unique($dataTeam['vNip']);
            foreach ($dataTeam as $ad) {
                $to   = ( $to == '' ) ? $ad['vNip'] : $to.','.$ad['vNip'];
            }
            
            $notification = $this->_ci->load->library('sess_auth');
            $notification->send_message_erp($this->_ci->uri->segment_array(),$to, $cc, $subject, $html);
        }
	}

	function getCompany(){
        $getComp = $this->_ci->db->get_where('hrd.employee', array('cNip' => $this->user()->gNIP))->row_array();
        $iCompanyID = (!empty($getComp))?$getComp['iCompanyID']:0;
        // $iCompanyID = $this->input->get('company_id');
        return $iCompanyID;
	}

	function getUpper($nip,$notfirst=false){
		
		
		$sql = 'SELECT e.cNip, pe.iLvlemp AS lvl_emp, u.cNip AS cUpper, pu.iLvlemp AS lvl_upper, 
					u.iDivisionID, u.iCompanyID
				FROM hrd.employee e 
				JOIN hrd.position pe ON e.iPostID = pe.iPostId
				JOIN hrd.employee u ON e.cUpper = u.cNip
				JOIN hrd.position pu ON u.iPostID = pu.iPostId
				WHERE 
				e.cNip = "'.$nip.'"
				GROUP BY e.cNip';
			/* echo '<pre>'.$sql; */
		$emp = $this->_ci->db->query($sql)->row_array();
		if (!empty($emp)){
			//apabila  user level >= 3
			if (intval($emp['lvl_emp']) >= 3 and intval($emp['lvl_emp']) <= 6 and !$notfirst){
				array_push($this->arrUpper, $emp['cNip']);
			}

			$this->getUpper($emp['cUpper']);
		} 
	}

	function getAllUpper($nip){
		$this->arrUpper = array();
		/* SPV sampai Manager */
		$this->getUpper($nip,true);
		return $this->arrUpper;
	}

}
