<?php

class Core_Controller_User extends SpcController {

    /**
     * Core User Model
     *
     * @var object
     */
    public $user;

    /**
     * Constructor
     *
     * Inits core user model and userid to use it in its methods
     */
    public function __construct() {
        parent::__construct();
        $this->user = new Core_Model_User;
    }

     /**
     * Gets user by userId, username, email, username | email
     *
     * @param mixed $userInfo ($getByField value)
     * @param string $getByField
     * @return array
     */
    public function getUser($userInfo, $getByField = 'id', $requester = 'calendar:main-application') {
        $user = $this->user->getUser($userInfo, $getByField);

        if (!$user && $requester == 'invitation') {
            $user = array(
                'id' => $userInfo,
                'email' => $userInfo,
                'username' => 'spc-invitee-outside'
            );

            echo Spc::jsonEncode(array('user' => $user));
            return;
        }

        if (!$user) {
            throw new Exception('User account could not be found');
        }

        echo Spc::jsonEncode(array('user' => $user));
    }

    /**
     * Gets all users belong to an admin or get all users for super user
     *
     * @return string
     */
    public function getUsers() {
        $users = $this->user->getUsers();
        echo Spc::jsonEncode(array('users' => $users));
    }

    /**
     * Create new system user
     *
     * @param array $user
     * @return void
     */
    public function createUser($user) {
        $this->user->createUser($user);
    }

    /**
     * Updates user's status (activated)
     *
     * @param int               $userId
     * @param string            $status
     */
    public function updateUserStatus($userId, $status) {
        $this->user->update(array('activated' => $status), "id = $userId");
	}

    /**
     * Deletes system user (only admin and super)
     *
     * @param int $userId
     */
    public function deleteUser($userId) {
        $this->user->deleteUser($userId);
    }

    /**
     * Update account
     *
     * @param array $user
     */
    public function updateAccount($user) {
        $email = $user['email'];
        $this->user->update(array(
            'email' => $email,
            'company' => $user['company'],
            'full_name' => $user['full_name']
            ),
            "id = {$this->_userId}"
        );
    }

    /**
     * Change password
     *
     * @param string $old
     * @param string $new
     */
    public function changePassword($old, $new) {
		$this->user->changePassword($old, $new);
    }

    /**
     * Save user settings
     *
     * @param array $settings
     */
    public function saveSettings($settings) {
        $this->user->saveSettings($settings);
    }

    /**
     * Sets user permissions
     *
     * @param array $user (holds user info, id and permissions)
     */
    public function setPermissions($user) {
        $this->user->setPermissions($user);
    }

    /**
     * Gets user permissions
     *
     * @param int $userId
     */
    public function getUserPermissions($userId) {
        echo Spc::jsonEncode(array(
            'permissions' => $this->user->getUserPermissions($userId)
        ));
    }

    public function signup($user) {
        $this->user->signup($user);
    }

    public function getUploadProgress($formName) {
        $key = 'upload_progress_' . ini_get("session.upload_progress.prefix");
        print_r($_SESSION);
        if (!empty($_SESSION[$key])) {
            $current = $_SESSION[$key]["bytes_processed"];
            $total = $_SESSION[$key]["content_length"];
            echo Spc::jsonEncode(array(
                'progress' => $current < $total ? ceil($current / $total * 100) : 100,
                'key' => $key
            ));
        } else {
            echo Spc::jsonEncode(array(
                'progress' => 100,
                'key0' => $key
            ));
        }
    }

    /**
     * Updates a db table
     *
     * @param type $options
     *
     * $options => array(array(column => val, col => val, ...), 'table' => 'db-table-name', 'where' => 'where'))
     */
    public function updateDbTable($options) {
        $this->user->updateDbTable($options);
    }

    public function getAppProps() {
        echo Spc::jsonEncode(array(
            'props' => $this->user->getAppProps()
        ));
    }
}