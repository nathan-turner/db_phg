<?php
// module/Pinnacle/src/Pinnacle/Model/GoalsTable.php:
namespace Pinnacle\Model\Admin;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class GoalsTable extends AbstractTableGateway
{
    protected $table ='tgoalsnew';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Goal());

        $this->initialize();
    }

    public function fetchAll() {
        return false;
    }
    
    public function getGoals($id,$y) {
        $id  = (int) $id;
        $y = (int) $y;

        $rowset = $this->select(array(
            'g_emp_id' => $id, 'g_year' => $y
        ));
        
        $goals = new Goals();
        foreach ($rowset as $row) {
            $goals->getFromGoal($row);
        }

        if ($goals->g_emp_id) {
            $goals->man_mode = 'update';
            return $goals;
        }
        return false;
    }

    public function addGoals(Goals $user) {
        $ytd = new Goal(); $result = 0;
        for( $i = 1; $i <= 12; $i++ ) {
            $go = $user->setToGoal($i,new Goal(),$ytd);
            $result += $this->insert($go->getArrayCopy());
        }
        return $result;
    }
    
    public function saveGoals(Goals $user, $id) {
        $id  = (int) $id;
        $y = (int) $user->g_year;
        if( !((int) $user->g_emp_id) || $id != $user->g_emp_id || ! $y )
            return false; // silently fail

        $ytd = new Goal(); $result = 0;
        for( $i = 1; $i <= 12; $i++ ) {
            $go = $user->setToGoal($i,new Goal(),$ytd);
            $result += $this->update($go->getArrayCopy(),array(
                    'g_emp_id' => $id, 'g_year' => $y, 'g_month' => $i
                ));
        }
        return $result;
    }

}
