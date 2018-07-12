<?php
/**
 * 为每层目录创建一个index.html文件以防止目录浏览
 * @param string $dir
 */
function create_file($dir = './', $fileName = 'index.html') {
    $dirArr = scandir($dir);
    for ($i = 2; $i < count($dirArr); $i ++) {
        if (is_dir($dir . $dirArr[$i])) {
            $dirName = $dir . $dirArr[$i] . '/';
            $fileName = $dirName . $fileName;
            if (!is_file($fileName)) {
                file_put_contents($fileName, '');
            }
            create_file($dirName);
        }
    }
}

create_file();