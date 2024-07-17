<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lib_utilitas {
	private $_ci;
	public $url;
	public $session;
	public $userinfo;
	public $moduleinfo;
	public $tmp;
	public function __construct() {
		$this->_ci=& get_instance();
	
	}

	public function set_url($url) {
		$this->url = $url;
	}

	function send_email($to, $cc, $subject, $content, $path) {
		$this->_ci->load->helper('email');
		$this->_ci->load->library('email');
		
		$config['protocol'] = 'smtp';
		$config['smtp_host'] = '10.1.48.4';
		$config['mailpath'] = '/usr/sbin/sendmail';
		$config['wordwrap'] = FALSE;
		$config['mailtype'] = 'html';
		$config['charset'] = 'utf-8';
		$config['crlf'] = "\r\n";
		$config['newline'] = "\r\n";
		
		$from = "postmaster@novellpharm.com";
		$to = $to;//"aliwidi.maulana@novellpharm.com";
		$cc = $cc;//"";
		$subject = $subject;//"test email aplikasi pm-bic";
		$content = $content;//"test email aplikasi pm-bic";
		$path=$path;//"";
		
		$this->_ci->email->initialize($config);
		$this->_ci->email->from($from, 'MIS-Service');
		$this->_ci->email->to($to);
		//if(valid_email($cc))
		$this->_ci->email->cc($cc);
		$this->_ci->email->subject($subject);
		$this->_ci->email->message($content);
			
		if(file_exists($path)){
			$files = get_filenames($path);
			if(count($files)>0){					
				$this->_ci->email->attach($path);
			}
		}
		//print_r($this->_ci->email);
		//exit;
		$this->_ci->email->send();
	}

	function get_name_by_nip($nip='') {
		if($nip=='') return false;
		$sql = "SELECT * FROM hrd.employee where cNip = '$nip' LIMIT 1";
		$query = $this->_ci->db->query( $sql );
		if($query->num_rows()>0) {
			$row = $query->row_array();
			$name = $row['vName'];
			return $name;
		}

		return false;
	}

	function get_pos_by_nip($nip='') {
		if($nip=='') return false;
		$sql = "SELECT (SELECT vDescription FROM hrd.`position` a WHERE a.iPostId = hrd.employee.iPostId) as jabatan FROM hrd.employee where cNip = '$nip' LIMIT 1";
		$query = $this->_ci->db->query( $sql );
		if($query->num_rows()>0) {
			$row = $query->row_array();
			$name = $row['jabatan'];
			return $name;
		}

		return false;
	}
	
	function get_all_inferior( $nip = '' ) {
		if($nip=='') return false;
		
		$sql = "SELECT cNip FROM hrd.employee
				WHERE ( dResign='0000-00-00' OR dResign > CURRENT_DATE() )
				AND cUpper = '".$nip."';";

		$query = $this->_ci->db->query($sql);
		if($query->num_rows()>0) {
			$rows = $query->result_array();
			foreach($rows as $row) {
				$this->tmp[] = $row['cNip'];
				
				$this->get_all_inferior( $row['cNip'] );
			}
			return $this->tmp;
		}
		return false;
	}

	function get_all_inferior_dept( $nip = '', $dept='' ) {
		if($nip=='') return false;
		
		$sql = "SELECT cNip FROM hrd.employee
				WHERE ( dResign='0000-00-00' OR dResign > CURRENT_DATE() )
				AND cUpper = '".$nip."' and iDepartementID='".$dept."'";

		$query = $this->_ci->db->query($sql);
		if($query->num_rows()>0) {
			$rows = $query->result_array();
			foreach($rows as $row) {
				$this->tmp[] = $row['cNip'];
				
				$this->get_all_inferior_dept( $row['cNip'] );
			}
			return $this->tmp;
		}
		return false;
	}

	public function scanUpper($cNip, &$tmp, $bypassDirector=false) {
		$sql = "Select cNip, cUpper from hrd.employee where cNip = '{$cNip}'";
		$rslt = mysql_query($sql);
		while($row = mysql_fetch_array($rslt)) {
			if ($row['cUpper'] == '') break;
			if($bypassDirector && $row['cUpper'] =='N00923') break;
			$tmp[] = $row['cUpper'];
			$this->scanUpper($row['cUpper'], $tmp);
		}

		return $tmp;
	}

	function get_email_by_nip($nip='') {
		if($nip=='') return false;
		$this->_ci->load->helper('email');
		$sql = "SELECT * FROM hrd.employee where cNip = '$nip' LIMIT 1";
		$query = $this->_ci->db->query( $sql );
		if($query->num_rows()>0) {
			$row = $query->row_array();
			if(valid_email( $row['vEmail'] )) {
				return $row['vEmail'];
			}
		}
		
		return false;
	}

	function getNipSubOrdinat($cNip, &$tmpNip)
    { //cari bawahan
        $nipUnder = array();

        //$nipUnder = $this->_user_identity->tempNipA;
        $nipUnder = $this->scanNipAll($cNip, $tmpNip);
        $jmlUnder = sizeOf($nipUnder);

        $in = 'IN (';
        if ($jmlUnder > 0) {

            foreach ($nipUnder as $nUnder) {
                $in .= '"' . $nUnder . '",';
            }
            $in = substr($in, 0, strlen($in) - 1);
        } else {
            $in .= '""';
        }

        $q = $in . ')';
        return $q;
    }

    function scanNipAll($cNip, &$tmpNip)
    {

        $sql   = "SELECT cUpper from hrd.employee where cNip='" . $cNip . "' limit 1";
        $query = $this->_ci->db->query($sql);
        if ($query->num_rows > 0) {
            $row     = $query->row();
            $tCUpper = $row->cUpper;
        } else {
            $tCUpper = '';
        }

        $sql = "SELECT cNip from hrd.employee where cUpper='" . $cNip . "' GROUP BY cNip";

        $query = $this->_ci->db->query($sql);
        if ($query->num_rows > 0) {
            foreach ($query->result_array() as $row) {
                $tmpNip[] .= $row['cNip'];
                $cNip = $row['cNip'];

                if (strlen($tCUpper)  > 0)
                    $this->scanNipAll($cNip, $tmpNip);
            }
        }
        return $tmpNip;
    }

    function scanNipAllActive($cNip, &$tmpNip)
    {

        $sql   = "SELECT cUpper 
        			FROM hrd.employee 
        			WHERE lDeleted = 0
        			AND (dResign = '0000-00-00' OR dResign > NOW())
        			AND cNip = '".$cNip."'
        			LIMIT 1";
        $query = $this->_ci->db->query($sql);
        if ($query->num_rows > 0) {
            $row     = $query->row();
            $tCUpper = $row->cUpper;
        } else {
            $tCUpper = '';
        }

        $sql = "SELECT cNip 
        		FROM hrd.employee 
        		WHERE lDeleted = 0
        		AND (dResign = '0000-00-00' OR dResign > NOW())
        		AND cUpper = '".$cNip ."' 
        		GROUP BY cNip";

        $query = $this->_ci->db->query($sql);
        if ($query->num_rows > 0) {
            foreach ($query->result_array() as $row) {
                $tmpNip[] .= $row['cNip'];
                $cNip = $row['cNip'];

                if (strlen($tCUpper)  > 0)
                    $this->scanNipAllActive($cNip, $tmpNip);
            }
        }
        return $tmpNip;
    }

    function nipNotifProjectRequirement($nipSubmit, $raw_id){
    	// get nip atasan yg submit
        $nipCc = '';
        $sqlUp = "SELECT a.cUpper
                    FROM hrd.employee a
                    WHERE a.lDeleted = 0
                    AND (a.dresign = '0000-00-00' OR a.dresign > NOW())
                    AND a.cNip = '".$nipSubmit."' ";
        $rowUp = $this->_ci->db->query($sqlUp)->row_array();
        if(!empty($rowUp['cUpper'])){
            $nipCc .= $rowUp['cUpper'];
        }

        // get nip Project Manager
        $sqlPm = "SELECT b.pic
                    FROM hrd.ss_raw_problems a
                    JOIN hrd.ss_raw_pic b ON b.rawid = a.id
                    AND a.Deleted = 'No' AND b.Deleted = 'No'
                    AND b.pic IS NOT NULL
                    AND b.iRoleId = 1
                    AND a.id = '".$raw_id."' ";
        $rowPm = $this->_ci->db->query($sqlPm)->row_array();
        if(!empty($rowPm['pic'])){
            $nipCc .= ','.$rowPm['pic'];
        }

        // get nip PM dan seluruh team nya
        $tmpNip = '';
        if(!empty($rowPm['pic'])){
            $arrBawahan = $this->scanNipAllActive($rowPm['pic'], $tmpNip);
        }

        $nipBawah = '';
        $countBawah = count($arrBawahan);
        foreach ($arrBawahan as $key => $bwh) {
            $nipBawah .= $bwh;
            if($key != $countBawah - 1){
                $nipBawah .= ',';
            }
        }

        if(!empty($nipBawah)){
            $nipCc .= ','.$nipBawah;
        }

    	return $nipCc;
    }

}
