<?php

class SpcUpload {

    /**
     * Default options for file to be uploaded
     *
     * @var array
     */
    protected $_defaultOptions = array(
        'fileFieldName' => 'user-file',
        'fileTypes' => array('txt', 'ics', 'jpeg', 'png', 'jpg', 'gif', 'csv', 'pdf'),
        'maxFileSize' => 10000000,
        'uploadPath' => './'
    );

    /**
     * PHP File attributes currently being uploaded file ($_FILES['file-field-name'])
     *
     * @var array
     */
    protected $_userFile;

    /**
     * HTML form file field name
     *
     * @var string
     */
	protected $_fileFieldName;

    /**
     * Save path for uploaded file
     *
     * @var string
     */
	protected $_uploadPath;

    /**
     * Temporary file name for being uploaded file
     *
     * @var string
     */
    protected $_tmpFileName;

    /**
     * Filename to be uploaded file
     *
     * @var string
     */
	protected $_fileName;

    /**
     * Allowed upload file types
     *
     * @var array
     */
	protected $_fileTypes;

    /**
     * Maximum file size in bytes for file to be uploaded
     *
     * @var int
     */
	protected $_maxfileSize;

    /**
     * Uplaoded file extension
     *
     * @var string
     */
    protected $_fileExt;

    /**
     * Uploaded file size
     *
     * @var int
     */
    protected $_fileSize;

    /**
     * Upload error message
     *
     * @var string
     */
    protected $_error;

	/**
	 * Constructor - Sets Default Upload Options
     *
     * @param array $options
	 */
	public function __construct($options = array()) {
        $this->_defaultOptions = array_merge($this->_defaultOptions, $options);

        $this->_fileFieldName = $this->_defaultOptions['fileFieldName'];
        $this->_uploadPath = $this->_defaultOptions['uploadPath'];
        $this->_fileTypes = $this->_defaultOptions['fileTypes'];
        $this->_maxFileSize = $this->_defaultOptions['maxFileSize'];

        $this->_userFile = $_FILES[$this->_fileFieldName];
        $this->_fileName = $this->_userFile['name'];
        $this->_fileExt = end(@explode('.', $this->_fileName));
        $this->_tmpFileName = $this->_userFile['tmp_name'];
        $this->_fileSize = $this->_userFile['size'];

        $this->_defaultOptions['fileTypes'] = explode(',', FILES_UPLOAD_FILE_TYPES);

        $this->_defaultOptions['maxFileSize'] = $this->getMaxFileSize();
	}

    public function getMaxFileSize($size = null) {
        if (!$size) {
            $size = FILES_UPLOAD_MAX_FILE_SIZE;
        }

        list($size, $type) = explode(' ', $size);
        $sizeInBytes = (int)$size;
        switch ($type) {
            case 'GB':
                $sizeInBytes *= 1024;
            case 'MB':
                $sizeInBytes *= 1024;
            case 'KB':
                $sizeInBytes *= 1024;
        }

        return $sizeInBytes;
    }

	/**
	 * Checks File and Uploads File
     *
     * @return bool
	 */
	public function uploadFile()
	{
		if ($this->checkUploadError() > 0) {
			return false;
		}

		if ($this->checkFileType() != true) {
			$this->_error = 'Invalid file type.';
			return false;
		}

		if ($this->checkFileSize() != true) {
			$this->_error = 'Exceeded max file size specified by Upload class or'
							. ' check your _maxfileSize';
			return false;
		}

		if (!is_uploaded_file($this->_tmpFileName)) {
			$this->_error = 'Possible file upload attack!';
			return false;
		}

        //upload file to AWS-S3
        if (isset($this->_defaultOptions['uploadToAwsS3'])) {
            require_once SPC_SYSPATH . '/libs/aws/S3.php';
            $s3 = new S3(FILES_AWS_S3_ACCESS_KEY, FILES_AWS_S3_SECRET_KEY);

            $bucketAddr = FILES_AWS_S3_BUCKET . '.s3.amazonaws.com';
            $folder = str_replace($bucketAddr, '', $this->_uploadPath);
            $newFolderName = trim($folder, '/') . '/' . $this->_fileName;
            #$newFolderName = ltrim($newFolderName, '/');

            if (!$s3->putObjectFile($this->_tmpFileName, FILES_AWS_S3_BUCKET, $newFolderName, S3::ACL_PUBLIC_READ)) {
                $this->_error = 'S3 Upload error.';
                return false;
            }
        } else if (!move_uploaded_file($this->_tmpFileName, $this->_uploadPath . '/' . $this->_fileName)) {
			$this->_error = 'Upload error. Check your upload directory and file name.';
			return false;
		}

        return true;
	}

    /**
	 * Gets Uploaded File's Name
	 *
	 * @param void
	 * @return string
	 */
	public function getUploadedFilename() {
		return $this->_fileName;
    }

    public function getUploadedFileTmpName() {
        return $this->_tmpFileName;
    }

    /**
     * Gets uploaded file's full path
     *
     * @return string
     */
    public function getUploadedFilePath() {
        return $this->_uploadPath . '/' . $this->_fileName;
    }

    /**
	 * Gets Upload Errors
	 *
	 * @param void
	 * @return string
	 */
	public function getError() {
		return $this->_error;
	}


	/**
	 * Checks File Size Specified by User
	 *
	 * @param void
	 * @return bool
	 */
	public function checkFileSize() {
        return true;
		if ($this->_fileSize > $this->_maxFileSize ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks File (Extension) Mime Type
	 *
	 * @param void
	 * @return void
	 */
	public function checkFileType() {
        return true;
		if (in_array($this->_fileExt, $this->_fileTypes)) {
			return true;
		}

		return false;
	}

	/**
	 * Checks File Upload Errors
	 *
	 * @param void
	 * @return int
	 */
	public function checkUploadError() {
		$errorNumber = $this->_userFile['error'];

		switch ($errorNumber) {
			case 0:
                $this->_error = 'File successfully received. Keep going baby!';
                return 0;

			case 1:
                $this->_error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini.';
                return 1;

			case 2:
                $this->_error = 'The uploaded file exceeds the maxfileSize directive that was specified in the HTML form';
                return 2;

			case 3:
                $this->_error = 'The uploaded file was only partially uploaded.';
                return 3;

			case 4:
                $this->_error = 'No file was uploaded.';
                return 4;

			case 6:
                $this->_error = 'Missing a temporary folder.';
                return 6;

			case 7:
                $this->_error = 'Failed to write file to disk.';
                return 7;

			case 8:
                $this->_error = 'File upload stopped by extension.';
                return 8;

			default:
                $this->_error = 'Unidentified error!';
                return 9;
		}
	}
}