<?php
/**
 * Pinnacle Health Group (http://phg.com/)
 *
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
// module/Application/src/Application/Model/LoginService.php:

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Mvc\Controller\AbstractActionController;

class LoginService extends AuthenticationService {
    protected $dbAdapter = null;
    //const COOKIE_SALT = '';
    //const PWD_SALT = '';
	const COOKIE_SALT = '';
    const PWD_SALT = '';
    const USER_TABLE = 'tbl';  // identity abstraction:
    const USER_NAME = 'emp_uname';      // username
    const USER_REAL = 'emp_realname';   // realname
    const USER_ID = 'emp_id';           // uid
    const USER_PASS = 'emp_password';   // password
    const USER_STATUS = 'emp_status';   // status
    const USER_ACCESS = 'emp_access';   // access
    const USER_DEPT = 'emp_dept';       // department

    public function __construct(Adapter $dbAdapter = null)
    {
        parent::__construct();
        $this->dbAdapter = $dbAdapter;
    }

    public function clearIdentity() {
        if( $this->hasIdentity() ) {
            // clear cookies
            setcookie('phguid','0',time()-60); setcookie('phguha','0',time()-60);
        }
        $result = parent::clearIdentity();
        return $result;
    }
    
    public function setDbAdapter(Adapter $dbAdapter) {
        $this->dbAdapter = $dbAdapter;
    }

    protected function storeIdentity($ar) {
        /* $ar-> username, realname, uid, access, password
         * password is not stored in memory cache
         * */
        // create cookies
	setcookie('phguid',$ar->uid,time()+3600*24*60);  // 6 days
	setcookie('phguha',
            md5($ar->password. $_SERVER['REMOTE_ADDR'].
                $ar->username. $ar->uid. self::COOKIE_SALT),
            time()+3600*24*60
        );
		setcookie('username',$ar->username,time()+3600*24*60);
		setcookie('realname',$ar->realname,time()+3600*24*60);
		setcookie('dept',$ar->dept,time()+3600*24*60);
		setcookie('access',$ar->access,time()+3600*24*60);
	
		
        unset($ar->password);
        parent::clearIdentity();
        $storage = $this->getStorage();
        $storage->write($ar);
    }
    
    public function checkAuth(AbstractActionController $ctrl = null) {
        if ($this->hasIdentity()) 
            return $this->getIdentity();
        else {
            // try cookie auth
            if( $ctrl ) {
                $sm = $ctrl->getServiceLocator();
                $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            }
            else $dbAdapter = $this->dbAdapter;
            $result = $this->cookieAuth($dbAdapter);
            if( $result->isValid() ) return $result->getIdentity();
        }
        return null;
    }
    
    public function cookieAuth(Adapter $dbAdapter = null) {
        // auth using cookies
        $myuid = isset($_COOKIE['phguid'])? $_COOKIE['phguid']: 0;
        $myuha = isset($_COOKIE['phguha'])? $_COOKIE['phguha']: 0;
        $ermsg = array();
        if( $myuha && $myuid && is_numeric($myuid) ) {
            // got cookies; check adapter
            if( !$dbAdapter ) $dbAdapter = $this->dbAdapter;
            if( !$dbAdapter ) throw new \Exception('DB Adapter is not set');
            $sql = vsprintf('select `%s` as `username`,`%s` as `realname`,`%s` as `uid`,`%s` as `dept`,`%s` as `access`, `%s` as `password` from `%s` where `%s`=? and `%s`=1',
                    array( self::USER_NAME, self::USER_REAL, self::USER_ID, self::USER_DEPT,
                          self::USER_ACCESS, self::USER_PASS, self::USER_TABLE,
                          self::USER_ID, self::USER_STATUS )
            );
            $rowSet = $dbAdapter->query( $sql, array($myuid) );
            if( !$rowSet )
                return new Result( Result::FAILURE_IDENTITY_NOT_FOUND, null,
                                  array('User not found') );
            foreach ($rowSet as $row) { // must be only one, but anyway
                $hash = md5(
                    $row->password. $_SERVER['REMOTE_ADDR'].
                    $row->username. $row->uid. self::COOKIE_SALT
                );
                if( $hash === $myuha ) {
                    // Success. Clear password, although salted and encrypted here
                    unset($row->password);
                    $storage = $this->getStorage();
                    $storage->write($row);
                    return new Result( Result::SUCCESS, $row, array('Success') );
                }
            }
            $ermsg[] = 'Credentials do not match';
        }
        else $ermsg[] = 'No Cookies Found';
        $ermsg[] = 'Cookie Authentication Failed';
        return new Result( Result::FAILURE, null, $ermsg );
    }
    
    public function loginAuth($my_username, $my_password, Adapter $dbAdapter = null) {
        // auth using user name and password
        if( !$dbAdapter ) $dbAdapter = $this->dbAdapter;
        if( !$dbAdapter ) throw new \Exception('DB Adapter is not set');
        $sql = vsprintf('sha1(concat(`%s`,?,`%s`,\'%s\')) and `%s`=1',
                array( self::USER_NAME, self::USER_REAL, self::PWD_SALT, self::USER_STATUS )
        );
        $authAdapter = new AuthAdapter($dbAdapter,
            self::USER_TABLE, self::USER_NAME, self::USER_PASS, $sql
        );

        $authAdapter
            ->setIdentity($my_username)
            ->setCredential($my_password)
        ;
        $result = $authAdapter->authenticate();
		
        if( $result->isValid() ) {		
            // Authentication succeeded
            $ar = $authAdapter->getResultRowObject(array(
                self::USER_NAME, self::USER_REAL, self::USER_ID, self::USER_DEPT,
                          self::USER_ACCESS, self::USER_PASS
            ));
            // translate from DB to abstraction
            if( self::USER_NAME != 'username' ) {
                $member= self::USER_NAME; $ar->username= $ar->$member; unset($ar->$member);
            }
            if( self::USER_REAL != 'realname' ) {
                $member= self::USER_REAL; $ar->realname= $ar->$member; unset($ar->$member);
            }
            if( self::USER_ID != 'uid' ) {
                $member= self::USER_ID; $ar->uid= $ar->$member; unset($ar->$member);
            }
            if( self::USER_DEPT != 'dept' ) {
                $member= self::USER_DEPT; $ar->dept= $ar->$member; unset($ar->$member);
            }
            if( self::USER_ACCESS != 'access' ) {
                $member= self::USER_ACCESS; $ar->access= $ar->$member; unset($ar->$member);
            }
            if( self::USER_PASS != 'password' ) {
                $member= self::USER_PASS; $ar->password= $ar->$member; unset($ar->$member);
            }

            //$ar->password = $my_password; // for cookie
            $this->storeIdentity($ar);
        }
        return $result;
    }
    
    public function changePassword($old_password, $new_password, Adapter $dbAdapter = null) {
        // for current identity
        $ar = $this->getIdentity();
        if( $ar && $ar->uid && is_numeric($ar->uid) ) {
            // let's fetch password
            $old_hash = sha1($ar->username. $old_password . $ar->realname. self::PWD_SALT);
            if( !$dbAdapter ) $dbAdapter = $this->dbAdapter;
            if( !$dbAdapter ) throw new \Exception('DB Adapter is not set');
            $sql = vsprintf('select `%s` as `username`,`%s` as `realname`,`%s` as `uid`,`%s` as `access`, `%s` as `password` from `%s` where `%s`=? and `%s`=1',
                    array( self::USER_NAME, self::USER_REAL, self::USER_ID,
                          self::USER_ACCESS, self::USER_PASS, self::USER_TABLE,
                          self::USER_ID, self::USER_STATUS )
            );
            $rowSet = $dbAdapter->query( $sql, array($ar->uid) );
            if( !$rowSet ) return false;
            foreach ($rowSet as $row) { // must be only one, but anyway
                if( $row->password === $old_hash ) {
                    // cool, change it already
                    $sql = vsprintf('update `%s` set `%s` = sha1(concat(`%s`,?,`%s`,\'%s\')) where `%s` = ? and `%s`=1',
                        array( self::USER_TABLE, self::USER_PASS, self::USER_NAME,
                              self::USER_REAL, self::PWD_SALT,
                              self::USER_ID, self::USER_STATUS )
                    );
                    $result = $dbAdapter->query( $sql,
                        array($new_password,$row->uid)
                    );
                    if( $result ) {
                        // calc it again to make a new cookie set
                        $row->password = sha1($row->username. $new_password . $row->realname. self::PWD_SALT);
                        $this->storeIdentity($row);
                    }
                    return $result;
                }
            }
        }
        return false;
    }
}
