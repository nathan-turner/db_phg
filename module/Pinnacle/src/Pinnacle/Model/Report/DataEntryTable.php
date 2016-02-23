<?php
// module/Pinnacle/src/Pinnacle/Model/Report/PlacementTable.php:
namespace Pinnacle\Model\Report;

use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class PlacementTable extends ReportTable
{
    public function __construct(Adapter $adapter) {
        parent::__construct($adapter,'vplacmarketrpt2',
                            '`pl_id`, `pl_date`, `ctr_id`, `ctr_no`, `ctr_date`, `ctr_spec`, `st_name`, `ctr_location_c`, `ctr_location_s`, `cli_sys`, `mark_uname`, `mark_id`, `emp_uname`, `ctct_name`, `ctct_phone`, `cli_id`, `s2pl`, `pl_annual`, `pl_split_emp`, `split_uname`, `ctr_nurse`, `at_abbr`');
    }

    /**
     * @param array $ar     keys: date1, date2, spec (0=All,'---'=Midlevels), st_code (0=All)
    */
    public function fetchAll(array $ar = null) {
        $select = new Select($this->table);
        if( is_array($ar) ) {
            $where = new Where();
            if( $ar['spec'] === '---' ) $where->equalTo('ctr_nurse',1);
            elseif( $ar['spec'] ) $where->equalTo('ctr_spec',$ar['spec']);
            
            if( $ar['st_code'] ) $where->equalTo('ctr_location_s', $ar['st_code'] );
            
            if( $ar['date1'] && $ar['date2'] )
                $where->between('pl_date',$ar['date1'],$ar['date2']);
            elseif( $ar['date1'] ) $where->greaterThanOrEqualTo('pl_date',$ar['date1']);
            elseif( $ar['date2'] ) $where->lessThanOrEqualTo('pl_date',$ar['date2']);
            
            $select->where($where);
        }
        $select->order('ctr_location_s, ctct_name, ctr_spec, pl_date DESC');
        $resultSet = $this->selectWith($select);
        return $resultSet;
    }
	
	//new get activities
	public function getActivities() {         
            $userid = $_COOKIE["phguid"];			
			
			$result = $this->adapter->query('select act_code,act_name,act_need_note from dctactivity where act_need_ref=0 and act_hidden=0 ',
            array());
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['act_code']=$row->act_code;
					$ar[$i]['act_name']=$row->act_name;
					$ar[$i]['act_need_note']=$row->act_need_note;
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//new get placement report
	public function getPlacementReport($pl_id) {         
            $userid = $_COOKIE["phguid"];			
			//get is nurse
			$result = $this->adapter->query('select pipl_nurse from tctrpipl where pipl_id = ? ',
            array($pl_id));
			if($result)
			{			
				foreach ($result as $row) {
					$pipl_nurse=$row->pipl_nurse;				
				}
			}
			
			if($pipl_nurse==1 || $pipl_nurse=="1")
				$result = $this->adapter->query('select * from vplacereport3 where pl_id = ? ', array($pl_id));
			else
				$result = $this->adapter->query('select * from vplacereport where pl_id =  ? ', array($pl_id));
			$ar = array();
			$ar["pipl_nurse"]=$pipl_nurse;
			
			if($result)
			{
			
				foreach ($result as $row) {
					$ar['req_name']=$row->req_name;
					$ar['cli_name']=$row->cli_name;
					$ar['cli_city']=$row->cli_city;
					$ar['cli_state']=$row->cli_state;
					$ar['ctr_no']=$row->ctr_no;
					$ar['sp_name']=$row->sp_name;
					$ar['ctr_pro_date']=$row->ctr_pro_date;
					$ar['pl_date']=$row->pl_date;
					$ar['s2pl']=$row->s2pl;
					$ar['ph_name']=$row->ph_name;
					$ar['ph_city']=$row->ph_city;
					$ar['ph_state']=$row->ph_state;
					$ar['ph_DOB']=$row->ph_DOB;
					$ar['ph_sex']=$row->ph_sex;
					$ar['ph_citizen']=$row->ph_citizen;
					$ar['ph_spm_bc']=$row->ph_spm_bc;
					$ar['ph_md']=$row->ph_md;
					$ar['pl_term']=$row->pl_term;
					$ar['pl_annual']=$row->pl_annual;
					$ar['pl_guar_net']=$row->pl_guar_net;
					$ar['pl_guar_gross']=$row->pl_guar_gross;
					$ar['pl_guar']=$row->pl_guar;
					$ar['pl_incent']=$row->pl_incent;
					$ar['pl_met_coll']=$row->pl_met_coll;
					$ar['pl_met_pro']=$row->pl_met_pro;
					$ar['pl_met_num']=$row->pl_met_num;
					$ar['pl_met_oth']=$row->pl_met_oth;
					$ar['pl_partner']=$row->pl_partner;
					$ar['pl_partner_yrs']=$row->pl_partner_yrs;
					$ar['pl_buyin']=$row->pl_buyin;
					$ar['pl_based_ass']=$row->pl_based_ass;
					$ar['pl_based_rec']=$row->pl_based_rec;
					$ar['pl_based_sto']=$row->pl_based_sto;
					$ar['pl_based_oth']=$row->pl_based_oth;
					$ar['pl_loan']=$row->pl_loan;
					$ar['pl_vacat']=$row->pl_vacat;
					$ar['pl_cme_wks']=$row->pl_cme_wks;
					$ar['pl_cme']=$row->pl_cme;
					$ar['pl_reloc']=$row->pl_reloc;
					$ar['pl_health']=$row->pl_health;
					$ar['pl_dental']=$row->pl_dental;
					$ar['pl_fam_health']=$row->pl_fam_health;
					$ar['pl_signing']=$row->pl_signing;
					$ar['pl_fam_dental']=$row->pl_fam_dental;
					$ar['pl_st_dis']=$row->pl_st_dis;
					$ar['pl_lt_dis']=$row->pl_lt_dis;
					$ar['pl_oth_ben']=$row->pl_oth_ben;
					$ar['pl_replacement']=$row->pl_replacement;
					$ar['ref_name']=$row->ref_name;
					$ar['pl_ref_emp']=$row->pl_ref_emp;
					$ar['pl_source']=$row->pl_source;
					$ar['ph_id']=$row->ph_id;
					$ar['cli_population']=$row->cli_population;
					$ar['cli_type']=$row->cli_type;
					$ar['ct_name']=$row->ct_name;
					$ar['pl_exp_years']=$row->pl_exp_years;
					$ar['split_name']=$row->split_name;
					$ar['pl_split_emp']=$row->pl_split_emp;
					$ar['ctr_cli_bill']=$row->ctr_cli_bill;
					$ar['cli_id']=$row->cli_id;
					$ar['src_name']=$row->src_name;
					$ar['pl_text1']=$row->pl_text1;
					$ar['pl_text2']=$row->pl_text2;
					$ar['pl_text3']=$row->pl_text3;
					$ar['pl_text4']=$row->pl_text4;										
					
				}
			}
			
			return $ar;
    }
	
	//new get calls for usersumst report
	public function getCalls() {         
            $userid = $_COOKIE["phguid"];			
			
			$result = $this->adapter->query("select call_emp_id, sum(call_numin+call_numout) as sumout, avg(call_timein+call_timeout) as timout from tcalls where (MONTH(call_date)=MONTH(NOW()) AND YEAR(call_date)=YEAR(NOW())) and WEEKDAY(call_date) not in (5,6) group by call_emp_id",
            array());
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['call_emp_id']=$row->call_emp_id;
					$ar[$i]['sumout']=$row->sumout;
					$ar[$i]['timout']=$row->timout;
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//new get calls for usersumst report
	public function getGoals() {         
            $userid = $_COOKIE["phguid"];			
			
			$result = $this->adapter->query("call GetGoalsActualsNew (DATE_FORMAT(NOW(),'%Y-01-01'),NOW())",
            array());
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['category']=$row->category;
					$ar[$i]['uname']=$row->uname;
					$ar[$i]['emp_id']=$row->emp_id;
					$ar[$i]['goal1']=$row->goal1;
					$ar[$i]['goal2']=$row->goal2;
					$ar[$i]['goal3']=$row->goal3;
					$ar[$i]['goal4']=$row->goal4;
					$ar[$i]['act1']=$row->act1;
					$ar[$i]['act2']=$row->act2;
					$ar[$i]['act3']=$row->act3;
					$ar[$i]['act4']=$row->act4;
					$ar[$i]['moal1']=$row->moal1;
					$ar[$i]['moal2']=$row->moal2;
					$ar[$i]['moal3']=$row->moal3;
					$ar[$i]['moal4']=$row->moal4;
					$ar[$i]['mact1']=$row->mact1;
					$ar[$i]['mact2']=$row->mact2;
					$ar[$i]['mact3']=$row->mact3;
					$ar[$i]['mact4']=$row->mact4;
					$ar[$i]['yoal1']=$row->yoal1;
					$ar[$i]['yoal2']=$row->yoal2;
					$ar[$i]['yoal3']=$row->yoal3;
					$ar[$i]['yoal4']=$row->yoal4;
					$ar[$i]['emp_status']=$row->emp_status;
					
					
					$i+=1;
				}
			}
			
			return $ar;
    }
	
	//new get goals for usersumst report
	public function getGoalsTotal() {         
            $userid = $_COOKIE["phguid"];			
			
			$result = $this->adapter->query("call GetGoalsTotal (DATE_FORMAT(NOW(),'%Y-01-01'),NOW())",
            array());
			
			$ar = array();
			if($result)
			{
			$i=0;
				foreach ($result as $row) {
					$ar[$i]['mon']=$row->mon;
					$ar[$i]['utype']=$row->utype;
					$ar[$i]['v1']=$row->v1;
					$ar[$i]['v2']=$row->v2;										
					$i+=1;
				}
			}
			
			return $ar;
    }

}
