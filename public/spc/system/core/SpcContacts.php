<?php

class SpcContacts {
    public static $_defaultContactGroupId;

    public static function init() {

    }

    public static function getDefaultContactGroupId() {
        if (!self::$_defaultContactGroupId) {
            $db = new SpcDb();
            $userId = SPC_USERID;
            $select = $db->select('id', 'spc_contacts_groups')->where("user_id = {$userId}")->order('id');
            self::$_defaultContactGroupId = $db->fetchColumn($select);
        }
        
        return self::$_defaultContactGroupId;
    }
}