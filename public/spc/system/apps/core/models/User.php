<?php

/**
 * Gets User Preferences
 */
class Core_Model_User extends SpcDbTable {

    /**
     * spc_users table
     *
     * @var string
     */
    protected $_name = 'users';

    /**
     * Inits application global constants
     */
    public function initAppConstants() {
        //define constants once for the whole application
        if (defined('SPC_USERID')) {
            return;
        }

        //define some app global constants
        define('SPC_USERID', Spc::getUserPref('id'));
        define('SPC_ADMINID', Spc::getUserPref('admin_id'));
        define('SPC_USERNAME', Spc::getUserPref('username'));
        define('SPC_USER_ROLE', Spc::getUserPref('role'));
        define('SPC_USER_EMAIL', Spc::getUserPref('email'));
        define('SPC_USER_LANG', Spc::getUserPref('language'));
        define('SPC_USER_TIMEZONE', Spc::getUserPref('timezone'));

        //set user's timezone for the whole application
        date_default_timezone_set(SPC_USER_TIMEZONE);

        Spc::initLanguage();
    }

    /**
     * Gets user preferences (with all modules)
     * Core System user prefs will be in the main array and modules will be in their
     * own keys.
     * @example
     * $userPrefs =array('id' => 1, 'username' => 'jsonx', ... //core-prefs
     *                   'calendar' => array('timezone' => 'Europe/Istanbul', ... //calendar prefs
     *                   'other-module-name' => array(...
     *
     * @param int $userId
     * @return array
     */
    public function getUserPrefs($userId, $username = null) {
        if ($username) {
            $select = $this->select(array('id'))->where("username = '$username'");
            $userId = $this->fetchColumn($select);
        }

        //get core user prefs
        $select = $this->select()->where("id = $userId");
        $userPrefs = $this->fetchRow($select);

        //get modules' user prefs
        foreach (Spc::getModules() as $module)  {
            $moduleSettingsTable = 'spc_' . $module . '_settings';
            $select = new SpcDbTableSelect($moduleSettingsTable);
            $select->where("user_id = $userId");

            $userPrefs[$module] = $this->fetchRow($select);
        }

        //get plugins' user prefs
        foreach (Spc::getPlugins() as $plugin)  {
            $pluginSettingsTable = "spc_{$plugin}_settings";
            if (!$this->checkTable($pluginSettingsTable)) {
                continue;
            }
            $select = new SpcDbTableSelect($pluginSettingsTable);
            $select->where("user_id = $userId");
            $pluginUserPrefs = $this->fetchRow($select);
            $userPrefs[$plugin] = $pluginUserPrefs;
            if (!$pluginUserPrefs) {
                $userPrefs[$plugin] = array();
            }
        }

        return $userPrefs;
    }

    /**
     * Gets user by userId, username, email, username | email
     *
     * @param mixed $userInfo ($getByField value)
     * @param string $getByField
     * @return array
     */
    public function getUser($userInfo, $getByField = 'id') {
        $select = $this->select();

        switch ($getByField) {
            case 'id':
                $select->where("id = $userInfo");
                break;

            case 'username':
                $select->where("username = '$userInfo'");
                break;

            case 'email':
                $select->where("email = '$userInfo'");
                break;

            case 'username|email':
                $select->where("username = '$userInfo' OR email = '$userInfo'");
                break;
        }

        return $this->fetchRow($select);
    }

     /**
     * Gets all users belong to an admin or get all users for superuser
     *
     * @return string
     */
    public function getUsers() {
		$role = SPC_USER_ROLE;
		$userId = SPC_USERID;

		$users = array();

        //get all users grouped with their group-managers (admins)
		if ($role == 'super') {
            $select = $this->select(array('id', 'username', 'email', 'role', 'activated'))
                            ->where('role = "admin"')
                            ->order('username');

            $admins = $this->fetchAll($select);

            foreach ($admins as $admin) {
                $adminUsername = $admin['username'];
				$users[$adminUsername] = $admin;
				$users[$adminUsername]['users'] = array();

                $select = $this->select(array('id', 'username', 'email', 'role', 'activated'))
                                ->where('role = "user"')
                                ->where("admin_id = {$admin['id']}")
                                ->order('username');

                $adminUsers = $this->fetchAll($select);
                foreach ($adminUsers as $user) {
                    $users[$adminUsername]['users'][] = $user;
                }
            }

         //get group-manager's (admin's) users
		} else if ($role == 'admin') {
            $select = $this->select(array('id', 'username', 'email', 'role', 'activated'))
                            ->where('role = "user"')
                            ->where("admin_id = $userId")
                            ->order('username');

            $users = $this->fetchAll($select);
		}

        return $users;
    }

    /**
     * Insert new user
     *
     * @param array $user
     * @return type
     */
    public function createUser($user) {
		//check if the username exists
        $username = $user['username'];
        $select = $this->select(array("username"))
                        ->where("username = '$username'");
		if ($this->numRows($select) == 1) {
            throw new Exception('This username is not available!');
		}

		$this->begin();

        $adminId = $user['admin_id'];
        $spcLogin = new Core_Controller_Login;
        $user['password'] = $spcLogin->hashPassword($username, $user['password']);
        $user['timezone'] = isset($user['timezone']) ? $user['timezone'] : SPC_DEFAULT_TIMEZONE;
        $user['language'] = 'en';
        $user['theme'] = 'smart-white';

        //insert new user
        $insertedUserId = $this->insert($user);

        //insert calendar settings
        $calendarOptions = array(
            'user_id' => $insertedUserId,
            'admin_id' => $adminId,
            'shortdate_format' => 'j/n/Y',
            'longdate_format' => 'mid_end',
            'timeformat' => 'standard',
            'custom_view' => 3,
            'start_day' => 'Monday',
            'default_view' => 'day',
            'staff_mode' => '0',
            'calendar_mode' => 'vertical' //vertical or horizontal (timeline) mode
        );
        $this->insert($calendarOptions, 'spc_calendar_settings');

        //insert default calendar
        $defaultCal = array(
            'user_id' => $insertedUserId,
            'name' => $username,
            'description' => '',
            'color' => '#25a338',
            'admin_id' => $user['admin_id'],
            'show_in_list' => '1',
            'public' => '0',
            'reminder_message_email' => '%calendar% - %start-date% - %start-time%, %title%',
            'reminder_message_popup' => '%calendar% - %start-date% - %start-time%, %title%',
            'access_key' => md5($insertedUserId . microtime(true))
        );
        $this->insert($defaultCal, 'spc_calendar_calendars');

		//if role is user add group calendars
		if ($user['role'] == 'user') {
            $select = $this->select()
                            ->from('spc_calendar_calendars')
                            ->where("user_id = {$user['admin_id']} AND type = 'group'");
            $groupCals = $this->fetchAll($select);

			foreach ($groupCals as $groupCal) {
                $groupCalOptions = array(
                    'type' => 'group',
                    'user_id' => $adminId,
                    'cal_id' => $groupCal['id'],
                    'shared_user_id' => $insertedUserId,
                    'permission' => 'see',
                    'name' => $groupCal['name'],
                    'description' => $groupCal['description'],
                    'color' => $groupCal['color'],
                    'status' => 'on',
                    'show_in_list' => '1'
                );
                $this->insert($groupCalOptions, 'spc_calendar_shared_calendars');
			}
		}

        $imageDir = SPC_SYSPATH . "/apps/calendar/files/user-images";

		//create user image directory
        $userImageDir = "{$imageDir}/{$username}";
        if (!file_exists($userImageDir)) {
            mkdir($userImageDir);
        }

        $userOrgImageDir = $userImageDir . '/org';
        if (!file_exists($userOrgImageDir)) {
            mkdir($userOrgImageDir);
        }

        $userThumbImageDir = $userImageDir . '/thumb';
        if (!file_exists($userThumbImageDir)) {
            mkdir($userThumbImageDir);
        }

        //insert plugin tables
        $plugins = Spc::getPlugins();
        foreach ($plugins as $plugin) {
            switch ($plugin) {
                case 'mobile_calendar':
                    $pluginTable = "spc_{$plugin}_settings";
                    $pluginSql = "INSERT INTO {$pluginTable}
                                  SET
                                    user_id = {$insertedUserId},
                                    theme = 'c'";

                    $this->query($pluginSql);
                break;
            }
        }

        $this->commit();
    }

    public function deleteUser($userId) {
        $this->deleteUserFiles($userId);
        $this->delete("id = $userId");
    }

    /**
     * Gets system administrators (group-managers)
     *
     * @return array
     */
    public function getAdmins() {
        $select = $this->select()
                        ->where('role = "admin"')
                        ->order('username');

		$admins = $this->fetchAll($select);
		foreach ($admins as $admin) {
			$admins[$admin['id']] = $admin;
		}

		return $admins;
	}

    /**
     * Gets all users of specified admin
     *
     * @param int $adminId
     * @param bool $withAdmin
     * @param bool $getAllUsers
     * @return array
     */
	public function getAdminUsers($adminId, $withAdmin = false, $getAllUsers = false) {
		$users = array();
        $userFields = array('id', 'username', 'role', 'email');
		//get all users (superuser)
		if ($getAllUsers) {
            //get super user
            $select = $this->select($userFields)
                            ->where('id = 1');
			$users[1] = $this->fetchRow($select);

			$admins = $this->getAdmins();

			foreach ($admins as $adminId => $admin) {
				$users = $users + $this->getAdminUsers($adminId, true);
			}

			return $users;
		}

		//only group users without admin (group-manager)
        $select = $this->select($userFields)
                        ->where('role = "user"')
                        ->where("admin_id = $adminId");

		//only group users with admin (group-manager)
		if ($withAdmin) {
            $select = $this->select($userFields)
                            ->where('role != "super"')
                            ->where("id = $adminId OR admin_id = $adminId")
                            ->order('role')
                            ->order('username');
		}

		foreach ($this->fetchAll($select) as $user) {
			$users[$user['id']] = $user;
		}

		return $users;
	}

    /**
     * Change system password
     *
     * @param string $old
     * @param string $new
     */
    public function changePassword($old, $new) {
        $userId = SPC_USERID;
        $username = SPC_USERNAME;

        $spcLogin = new Core_Controller_Login;

		$old = $spcLogin->hashPassword($username, $old);
        $select = $this->select(array('id'))
                        ->where("id = $userId AND password = '$old'");

        if ($this->numRows($select) != 1) {
            throw new Exception('Wrong old password!');
        }

		$new = $spcLogin->hashPassword($username, $new);
        $this->update(array('password' => $new), "id = $userId");
    }

    /**
     * Updates system settings with included modules
     *
     * @param array $settings
     */
    public function saveSettings($settings) {
        $this->begin();

        //core application settings
        if (isset($settings['core'])) {
            $coreAppSettings = $settings['core'];
            $this->update($coreAppSettings, "id = {$this->_userId}");
        }

        //save modules
        foreach (Spc::getModules() as $module) {
            if (isset($settings[$module])) {
                $moduleSettings = $settings[$module];
                $moduleTable = 'spc_' . $module . '_settings';
                $this->update($moduleSettings, "user_id = {$this->_userId}", $moduleTable);
            }
        }

        //save plugins
        foreach (Spc::getPlugins() as $plugin) {
            if (isset($settings[$plugin])) {
                $pluginSettings = $settings[$plugin];
                $pluginTable = 'spc_' . $plugin . '_settings';
                $this->update($pluginSettings, "user_id = {$this->_userId}", $pluginTable);
            }
        }

        $_SESSION['spcUserPrefs'] = $this->getUserPrefs($this->_userId);

        $this->commit();
    }

    public function deleteUserEventImages($username) {
        //delete calendar event image files
        $imageFolder = SPC_SYSPATH . "/apps/calendar/files/user-images/{$username}";
        if (!file_exists($imageFolder)) {
            return;
        }
        $this->deleteDir($imageFolder);
    }

    public function deleteUserFiles($userId) {
        $user = $this->getUser($userId);

        if ($user['role'] == 'admin') {
            $adminUsers = $this->getAdminUsers($userId, true);
            foreach ($adminUsers as $adminUser) {
                $this->deleteUserEventImages($adminUser['username']);
            }
        } else {
            $this->deleteUserEventImages($user['username']);
        }
    }

    /**
     * Deletes a directory
     *
     * @param string $dirPath
     * @throws InvalidArgumentException
     */
    public function deleteDir($dirPath) {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

    /**
     * Sets multiple users permissions (helper for setPermissions method)
     *
     * @param array $users
     * @param array $permissions
     */
    public function setUsersPermissions($users, $permissions) {
        foreach ($users as $userId) {
            $this->setUserPermissions($userId, $permissions);
        }
    }

    /**
     * Sets single user permissions (helper for setPermissions method)
     *
     * @param int $userId
     * @param array $permissions
     */
    public function setUserPermissions($userId, $permissions) {
        foreach ($permissions as &$permission) {
            $permission['user_id'] = $userId;
        }
        $this->insert($permissions, 'spc_user_permissions', true);
    }

    /**
     * Sets user permissions
     *
     * @param array $user (holds user info, userId, permission list and additionalOptions)
     */
    public function setPermissions($user) {
        $userId = $user['userId'];
        $permissions = $user['permissions'];

        //superuser can do this operation
        if (isset($user['saveForAdminWithGroup'])) {
            //save for specific group and group admin
            $users = $this->getAdminUsers($userId, true);
            $this->setUsersPermissions(array_keys($users), $permissions);
            return;
        }

        //superuser and admin can do this operation
        if (isset($user['saveForGroup'])) {
            //save for specific group
            $users = $this->getAdminUsers($userId);
            $this->setUsersPermissions(array_keys($users), $permissions);
            return;
        }

        //admin and superuser can do this
        //admin saves for his all group-users
        //superuser saves for all system users
        //save for all users
        if (isset($user['saveForAllUsers'])) {
            if (SPC_USER_ROLE == 'super') {
                $select = $this->select('id')->where('role != "super"');
                $rows = $this->fetchAll($select);
            } else if (SPC_USER_ROLE == 'admin') {
                $rows = $this->getUsers();
            }

            $users = array();
            foreach ($rows as $user) {
                $users[] = $user['id'];
            }

            $this->setUsersPermissions($users, $permissions);
            return;
        }

        //save for only specified user
        $this->setUserPermissions($userId, $permissions);
    }

    /**
     * Gets user permissions (permission => value)
     *
     * @param int $userId
     */
    public function getUserPermissions($userId) {
        $select = $this->select()->from('spc_user_permissions')->where("user_id = $userId");
        $userPermissions = array();
        foreach ($this->fetchAll($select) as $permRow) {
            $userPermissions[$permRow['permission']] = $permRow['value'];
        }

        foreach (Spc::getSystemPermissions() as $permission) {
            if (array_key_exists($permission, $userPermissions) === false) {
                $userPermissions[$permission] = '1';
            }
        }

        return $userPermissions;
    }

    public function signup($user) {
        $user['admin_id'] = 1;
        $user['role'] = 'admin';
        $this->createUser($user);

        $emailController = new Core_Controller_Email();
        $fullName = $user['full_name'];
        $company = $user['company'];
        $username = $user['username'];
        $password = $user['password'];
        $email = $user['email'];

        $emailMsg = preg_replace(
                        array('/%fullName%/', '/%company%/', '/%username%/', '/%password%/', '/%email%/'),
                        array($fullName, $company, $username, $password, $email),
                        SIGNUP_EMAIL_TMPL
                    );
        $emailController->send(EMAIL_REMINDER_FROM, $email, SIGNUP_EMAIL_TITLE, $emailMsg);
    }

    /**
     * Updates a db table
     *
     * @param array $options
     *
     * $options => array(array(column => val, col => val, ...), 'table' => 'db-table-name', 'where' => 'where'))
     */
    public function updateDbTable($options) {
        $data = $options['data'];
        $table = $options['table'];
        $where = $options['where'];

        $this->update($data, $where, $table);
    }

    public function getAppProps() {
        Spc::initLanguage();

        return array(
            'domain' => Spc::getDomain(),
            'licenseKey' => Spc::getLicenseKey(),
            'root' => SPC_ROOT,
            'curDate' => date('Y-m-d'),
            'userPrefs' => Spc::getUserPrefs(),
            'i18n' => Spc::getLanguageTranslationArray(),
            'eventReminderCount' => EVENT_REMINDER_COUNT,
            'sessName' => session_name(),
            'userCals' => $_SESSION['calendars']
        );
    }
}