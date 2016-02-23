<?php
// module/Pinnacle/src/Pinnacle/Model/Report/StatisticsTable.php:
namespace Pinnacle\Model\Report;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class StatisticsSel1 {
    public $total;
    public $byStatus;
    public $bySpecialty;
}

class StatisticsSel0 {
    public $totalClients;
    public $byStatusClients;
    public $byTypeClients;
    public $bySourceClients;
    public $bySystemClients;
    
    public $totalContracts;
    public $byStatusContracts;
    public $bySpecialtyContracts;
    public $byTypeContracts;
}

class StatisticsTable
{
    protected $adapter;
    
    const S1Q1 = 'select count(*) as cnt from lstphysicians';
    const S1Q2 = 'select ph_status, st_name, count(ph_id) as cnt from lstphysicians join dctstatus on ph_status = st_id group by ph_status, st_name order by cnt desc';
    const S1Q3 = 'select ph_spec_main, sp_name, sd_gt, count(ph_id) as cnt from lstphysicians join dctspecial on ph_spec_main = sp_code left outer join tspecdemo on ph_spec_main = sd_ama group by ph_spec_main, sp_name, sd_gt having ph_spec_main <> \'---\' order by ph_spec_main';

    const S2Q1 = 'select count(*) as cnt from lstalliednurses';
    const S2Q2 = 'select an_status, st_name, count(an_id) as cnt from lstalliednurses join dctstatus on an_status = st_id group by an_status, st_name order by cnt desc';
    const S2Q3 = 'select an_type, at_name, count(an_id) as cnt from lstalliednurses join dctalliedtypes on an_type = at_code group by an_type, at_name order by an_type';

    const S0Q1 = 'select count(*) as cnt from lstclients';
    const S0Q2 = 'select cli_status, st_name, count(cli_id) as cnt from lstclients join dctstatus on cli_status = st_id group by cli_status, st_name order by cnt desc';
    const S0Q3 = 'select cli_type, ct_name, count(cli_id) as cnt from lstclients join dctclienttypes on cli_type = ct_id group by cli_type, ct_name order by cnt desc';
    const S0Q4 = 'select cli_source, cs_name, count(cli_id) as cnt from lstclients join dctclientsource on cli_source = cs_type group by cli_source, cs_name order by cnt desc';
    const S0Q5 = 'select cli_sys, count(cli_id) as cnt from lstclients group by cli_sys having (cnt > ?) order by cli_sys';
    const S0Q6 = 'select count(*) as cnt from allcontracts';
    const S0Q7 = 'select ctr_status, st_name, count(ctr_id) as cnt from allcontracts join dctstatus on ctr_status = st_id where ctr_cli_id <> 6 group by ctr_status, st_name order by cnt desc';
    const S0Q8 = 'select ctr_spec, sp_name, count(ctr_id) as cnt from allcontracts left outer join dctspecial on ctr_spec = sp_code where ctr_status = 1 and ctr_cli_id <> 6 group by ctr_spec, sp_name order by cnt desc';
    const S0Q9 = 'select ctr_type, count(ctr_id) as cnt from allcontracts where ctr_status = 1 and ctr_cli_id <> 6 group by ctr_type order by cnt desc';


    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
    }
    
    public function getSel0($id) {
        $obj = new StatisticsSel0();
        $ad = $this->adapter;
        
        $rowSet = $ad->query(self::S0Q1)->execute();
        foreach ($rowSet as $rec)
            $obj->totalClients = $rec['cnt'];
        
        $obj->byStatusClients = $ad->query(self::S0Q2)->execute();
        $obj->byTypeClients = $ad->query(self::S0Q3)->execute();
        $obj->bySourceClients = $ad->query(self::S0Q4)->execute();
        $obj->bySystemClients = $ad->query(self::S0Q5, array($id));
        
        $rowSet = $ad->query(self::S0Q6)->execute();
        foreach ($rowSet as $rec)
            $obj->totalContracts = $rec['cnt'];
        
        $obj->byStatusContracts = $ad->query(self::S0Q7)->execute();
        $obj->bySpecialtyContracts = $ad->query(self::S0Q8)->execute();
        $obj->byTypeContracts = $ad->query(self::S0Q9)->execute();

        return $obj;
    }

    public function getSel2() {
        $obj = new StatisticsSel1();
        $ad = $this->adapter;
        
        $rowSet = $ad->query(self::S2Q1)->execute();
        foreach ($rowSet as $rec)
            $obj->total = $rec['cnt'];
        
        $obj->byStatus = $ad->query(self::S2Q2)->execute();
        $obj->bySpecialty = $ad->query(self::S2Q3)->execute();
        
        return $obj;
    }

    public function getSel1() {
        $obj = new StatisticsSel1();
        $ad = $this->adapter;
        
        $rowSet = $ad->query(self::S1Q1)->execute();

        foreach ($rowSet as $rec)
            $obj->total = $rec['cnt'];
        
        $obj->byStatus = $ad->query(self::S1Q2)->execute();
        $obj->bySpecialty = $ad->query(self::S1Q3)->execute();
        
        return $obj;
    }

    public function fetchAll($part, $id) {
        // $part is sel0, sel1 or sel2
        // $id is used on sel0 only, it is snum from form
        $id = (int) $id;
        $obj = null;
        if( $part === 'sel0' ) $obj = $this->getSel0($id);
        elseif( $part === 'sel1' ) $obj = $this->getSel1();
        elseif( $part === 'sel2' ) $obj = $this->getSel2();
        return $obj;
    }
}
