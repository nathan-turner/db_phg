<?php

class Core_Model_Login extends SpcDbTable {
    protected $_name = 'users';

    public function checkLogin($username, $password) {
        $select = $this->select()
                        ->where("username = '$username'")
                        ->where("password = '$password'");

        return $this->fetchRow($select);
    }

    public function hashPassword($username, $password) {
		$salt = 'xQ._0_2' . md5($username[0] . $password . $username[(strlen($username) - 1)]);
		return hash('sha256', $salt . $password);
    }

    public function resetPass($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address');
        }

        $select = $this->select(array('username', 'id'))->where("email = '{$email}'");
        if ($this->numRows($select) != 1) {
            throw new Exception('There is no such a user');
        }

        $userName = $this->fetchColumn($select);

        $newPass = uniqid();
        $hashPass = $this->hashPassword($userName, $newPass);
        $this->update(array(
            'password' => $hashPass
        ), "email = '{$email}'");

        $mailer = new Core_Controller_Email();
        $mailer->send(EMAIL_REMINDER_FROM, $email, 'Smart PHP Calendar - Reset Password', 'Your new password is: ' . $newPass);
    }
}