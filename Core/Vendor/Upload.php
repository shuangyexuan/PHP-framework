<?php
/**
 * @author TOP糯米 <1130395124@qq.com> 2017
 */

namespace Vendor;

/**
 * 文件上传类
 * @author TOP糯米
 */
class Upload {
    private static $instance;
    private static $fileType;
    private static $dirName;
    private $error;

    private function __construct() {
    }
    
    /**
     * 静态调用时传入保存目录以及文件类型
     * @param string $dirName
     * @param string $fileType
     * @return \Vendor\Upload
     */
    public static function init($dirName = '', $fileType = '') {
        if (!self::$instance) {
            self::$instance = new self();
        }
        self::$dirName = $dirName;
        self::$fileType = ($fileType) ? $fileType : ['jpg', 'jpeg', 'png', 'gif', 'JPG', 'JPEG', 'PNG', 'GIF'];
        return self::$instance;
    }

    /**
     * 获取错误信息
     * @return string
     */
    public function getError() {
        return $this->error;
    }

    /**
     * 上传
     * @param string $fileName
     * @return string|boolean
     */
    public function doUpload($fileName = '') {
        $verifyToken = md5('unique_salt' . $_POST['timestamp']);
        if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
            $tempFile = $_FILES['Filedata']['tmp_name'];
            $type = getimagesize($tempFile)['mime'];
            if ($type != 'image/gif') {
                $image = @imagecreatefromstring(file_get_contents($tempFile));
                if ($image) {
                    //服务器暂时没有exif扩展，注释掉图片旋转

                    /*$exif = exif_read_data($tempFile);
                    if(!empty($exif['Orientation'])) {
                        switch($exif['Orientation']) {
                            case 8:
                                $image = imagerotate($image,90,0);
                                break;
                            case 3:
                                $image = imagerotate($image,180,0);
                                break;
                            case 6:
                                $image = imagerotate($image,-90,0);
                                break;
                        }
                    }*/
                    $targetPath = $_SERVER['DOCUMENT_ROOT'] . self::$dirName;
                    if (!is_dir($targetPath)) mkdir($targetPath, 0777, true);
                    $targetFile = rtrim($targetPath, '/') . '/' . ((!$fileName) ? $_FILES['Filedata']['name'] : $fileName);
                    $fileParts = pathinfo($_FILES['Filedata']['name']);
                    $result = false;
                    switch ($type) {
                        case 'image/jpeg':
                            $result = @imagejpeg($image, $targetFile . '.' . $fileParts['extension']);
                            break;
                        case 'image/png':
                            $result = @imagepng($image, $targetFile . '.' . $fileParts['extension']);
                            break;
                    }
                    if ($result) {
                        return rtrim(self::$dirName, '/') . '/' . $fileName . '.' . $fileParts['extension'];
                    } else {
                        $this->error = 'error.';
                        return false;
                    }
                }
            } else if ($type == 'image/gif') { //gif图片单独上传，避免丢失帧
                $targetPath = $_SERVER['DOCUMENT_ROOT'] . self::$dirName;
                if (!is_dir($targetPath)) mkdir($targetPath, 0777, true);
                $targetFile = rtrim($targetPath, '/') . '/' . ((!$fileName) ? $_FILES['Filedata']['name'] : $fileName);
                $fileParts = pathinfo($_FILES['Filedata']['name']);
                if (in_array($fileParts['extension'], self::$fileType)) {
                    if (move_uploaded_file($tempFile, $targetFile . '.' . $fileParts['extension'])) {
                        return rtrim(self::$dirName, '/') . '/' . $fileName . '.' . $fileParts['extension'];
                    } else {
                        $this->error = 'error.';
                        return false;
                    }
                }
            }
            $this->error = 'Invalid file type.';
            return false;
        }
    }
}