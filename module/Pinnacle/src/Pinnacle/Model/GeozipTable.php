<?php
// module/Pinnacle/src/Pinnacle/Model/GeozipTable.php:
namespace Pinnacle\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\AbstractTableGateway;

class GeozipTable extends AbstractTableGateway
{
    protected $table ='tgeozip';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;

        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Geozip());

        $this->initialize();
    }

    public function fetchAll($where = null) {
        $resultSet = $this->select($where);
        return $resultSet;
    }
    
    public function getZipCodes(array $data) {
        // form helper
        $lat = $data['iplat'];
        $lon = $data['iplong'];
        $mil = $data['miles'];
        return $this->zipCodes($lat,$lon,$mil);
    }

    public function zipCodes($lat,$long,$miles) {
        // finds all zip codes in a boundary, returns array of stings (zip codes)
        // that is also the function to use in searches
        $lat = (double) $lat;
        $long = (double) $long;
        $miles = (double) $miles;
        
        $bounds = Geozip::getBoundary($lat, $long, $miles);
        $where = "maxy >= $bounds[1] and miny <= $bounds[3] and maxx >= $bounds[0] and minx <= $bounds[2]";
        $resultSet = $this->select($where);
        $entries   = array();
        foreach ($resultSet as $row) {
            $entries[] = $row->zip;
        }
        return $entries;
    }

    public function getGeozip($id) {
        // sanitize $id
        $id = preg_replace('/[^0-9-]/','',$id);
        $rowset = $this->select(array(  'zip' => $id,   ));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find zip $id");
        }
        return $row;
    }

    public function saveGeozip($x) {
        throw new \Exception('Geo zip is read only');
    }

    public function deleteGeozip($id) {
        throw new \Exception('Geozip is read only');
    }
}
