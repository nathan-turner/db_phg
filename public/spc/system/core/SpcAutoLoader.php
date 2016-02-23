<?php

class SpcAutoLoader {
    public function autoloader($className) {

        //laad core app class
        if (preg_match('/^Spc/', $className)) {
            $filename = SPC_APP_DIR . '/system/core/' . $className;

        //load the specific application's class
        } else {

            //?TODO: check only Spc namespace
            if (!preg_match('/controller|model|helper/i', $className)) {
                #trigger_error($className . ':' . join(', ', get_included_files()), E_USER_NOTICE);
                return;
            }

            $nameArr = explode('_', $className);
            //application module (calendar, contacts, tasks, etc)
            $module = strtolower($nameArr[0]);

            //class directory
            $classDir = $nameArr[1];
            switch ($classDir) {
                case 'Controller':
                    $classDir = 'controllers';
                    break;

                case 'Model':
                    $classDir = 'models';
                    break;

                case 'Helper':
                    $classDir = 'helpers';
                    break;
            }

            //classname
            $className = $nameArr[2];

            //final class path
            $filename = SPC_APP_DIR . '/system/apps/' . $module . '/' . $classDir . '/' . $className;
        }

        $filename = $filename . '.php';

        //Load Spc System or Spc Application Class
        if (file_exists($filename)) {
            return require_once $filename;
        }
    }
}