<?php

class SpcError {
    public function __construct() {
        error_reporting(E_ALL | E_STRICT);
        set_error_handler(array($this, 'errorHandler'));
        set_exception_handler(array($this, 'exceptionHandler'));
    }

    public function errorHandler($errNo, $errStr, $errFile, $errLine, $errCtx) {
        $errMsg = "ErrNo: $errNo, <br />
                    ErrStr: $errStr, <br />
                    ErrFile: $errFile, <br />
                    ErrLine: $errLine";

        if ($errNo == E_NOTICE || $errNo == E_WARNING) {
            #$this->logError($errMsg);
            return;
        }

        if (SPC_ENV == 'development') {
            throw new Exception($errMsg);
        } else {
            $this->logError($errMsg);
        }
    }

    public function exceptionHandler($exc) {
        echo Spc::jsonEncode(array('errorMsg' => $exc->getMessage()), false);
        ob_end_flush();
        exit;
    }

    public function logError($errMsg) {
        $handle = fopen('errors.log', 'a+');
        if (!$handle) {
            echo Spc::jsonEncode(array('errorMsg' => $errMsg . ' (Cannot open file errors.log)'), false);
            ob_end_flush();
            exit;
        }

        if (fwrite($handle, $errMsg) === FALSE) {
            echo Spc::jsonEncode(array('errorMsg' => $errMsg . ' (Cannot write to file errors.log)'), false);
            ob_end_flush();
            exit;
        }
    }
}