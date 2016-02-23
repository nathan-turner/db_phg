<?php

class Core_Controller_Login extends SpcController {

    public $login;

    public function __construct() {
        parent::__construct();

        $this->login = new Core_Model_Login();
    }

    /**
     * Checks user login request, initializes application modules
     * and logs the user in the application
     *
     * @param string $username
     * @param string $password
     */
    public function checkLogin($username, $password) {
        $hPassword = $this->login->hashPassword($username, $password);
        $user = $this->login->checkLogin($username, $hPassword);

        if (!$user) {
            echo Spc::jsonEncode(
                array('errorMsg' => 'The username or password you entered is incorrect!')
                ,false
            );
        } else {
            if ($user['activated'] == '0') {
                echo Spc::jsonEncode(
                    array('errorMsg' => 'Your account has not been activated. Please contact calendar administrator.')
                    ,false
                );
            } else {
                //key for whole application login
                $_SESSION['spcUserLoggedIn'] = true;
                $_SESSION['SPC_VERSION'] = SPC_VERSION;

                //init user session
                $userModel = new Core_Model_User;
                $_SESSION['spcUserPrefs'] = $userModel->getUserPrefs($user['id']);
                $userModel->initAppConstants();

                //init user calendars
                SpcCalendar::initUserCalendars();
            }
        }
    }

    public function hashPassword($username, $password) {
		$salt = 'xQ._0_2' . md5($username[0] . $password . $username[(strlen($username) - 1)]);
		return hash('sha256', $salt . $password);
    }

    public function resetPass($email) {
        $this->login->resetPass($email);
    }

    public function logout() {
        unset($_SESSION['spcUserPrefs']);
        unset($_SESSION['spcUserLoggedIn']);
    }
}