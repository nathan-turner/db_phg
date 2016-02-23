<?php
// module/Pinnacle/src/Pinnacle/Model/Geozip.php:
namespace Pinnacle\Model;


class Geozip 
{
    const E2 = 6.69437999014E-3;    // eccentricity squared
    const A = 6378137.0;            // equatorial radius in meters
    const LAT1 = 111132.954;        // delta Lat const 1
    const LAT2 = 559.822;           // delta Lat const 2
    const M2M  = 1609.0;            // meters in a mile

    public $zip;
    public $iplong;
    public $iplat;
    public $miles;
    public $minx;
    public $miny;
    public $maxx;
    public $maxy;

    public function exchangeArray($data) {
        $this->zip     = (isset($data['zip']))     ? $data['zip']      : null;
        $this->iplong  = (isset($data['iplong']))  ? $data['iplong']   : null;
        $this->iplat   = (isset($data['iplat']))   ? $data['iplat']    : null;
        $this->miles   = (isset($data['miles']))   ? $data['miles']    : null;
        $this->minx    = (isset($data['minx']))    ? $data['minx']     : null;
        $this->miny    = (isset($data['miny']))    ? $data['miny']     : null;
        $this->maxx    = (isset($data['maxx']))    ? $data['maxx']     : null;
        $this->maxy    = (isset($data['maxy']))    ? $data['maxy']     : null;
    }

    public function setBounds( array $bounds ) {
        $this->minx = (double) $bounds[0];
        $this->miny = (double) $bounds[1];
        $this->maxx = (double) $bounds[2];
        $this->maxy = (double) $bounds[3];
        return $this;
    }

    public function getBounds() {
        $bounds = array( $this->minx, $this->miny, $this->maxx, $this->maxy );
        return $bounds;
    }

    public function isWithin(array $bounds) {
        // true if this object intersects with given bounds
        return $this->maxy >= $bounds[1] && $this->miny <= $bounds[3] &&
                    $this->maxx >= $bounds[0] && $this->minx <= $bounds[2];
    }

    public function isInside($lat, $long) {
        // true if given point is inside bounds of this object
        return $this->minx <= $long && $this->maxx >= $long
                && $this->miny <= $lat && $this->maxy >= $lat;
    }

    public function getArrayCopy() {
        return get_object_vars($this);
    }
    
    static function deltaLat( $lattitude ) {
        // distance of 1 degree of lattitude change, in meters
        $lat = deg2rad($lattitude);
        return self::LAT1 - self::LAT2 * cos($lat * 2) + 1.175 * cos($lat * 4);
    }
    
    static function deltaLong( $lattitude ) {
        // distance of 1 degree of longitude change, at given lattitude, in meters
        if( $lattitude == 90 ) return 0; // not supposed to get here, anyway
        $lat = deg2rad($lattitude);
        return M_PI * self::A * cos($lat) /( 180.0 * sqrt(1 - self::E2*pow(sin($lat),2)) );
    }
    
    static function getBoundary( $lattitude, $longitude, $miles ) {
        // pre-calculated for zip codes. Useful for other entities
        // works reasonably well on miles distance 0 .. 200
        $da = self::deltaLat( $lattitude );
        $du = self::deltaLong( $lattitude );
        $dx = $du? $miles * self::M2M / $du : 0; $dy = $miles * self::M2M / $da;
        $bound = array( $longitude - $dx, $lattitude - $dy, $longitude + $dx,
                       $lattitude + $dy ); // minx miny maxx maxy
        return $bound;
    }

}
