<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of TestFileService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class TestFileService
{
    const ERROR_UPLOAD_BASE_PATH_NOT_EXIST = 'upload base path not existed';
    const ERROR_FILE_PATH_EMPTY = 'file path is empty';
    static $NotForceDownloadFiletype = array(
            'jpg', 'jpeg', 'gif', 'png', 'bmp', 'html', 'htm'
        );

    public static function saveAttachmentFile($attachmentFile, $actionId, $targetId, $type, $productId)
    {
        $resultInfo = array();
        if(isset($attachmentFile) && count($attachmentFile) > 0)
        {
            $baseUploadPath = Yii::app()->params->uploadPath;
            if(!is_dir($baseUploadPath) && !@mkdir($baseUploadPath))
            {
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail'] = array('attachment_file' => Yii::t('Common', self::ERROR_UPLOAD_BASE_PATH_NOT_EXIST));
                return $resultInfo;
            }
            foreach($attachmentFile as $index => $file)
            {
                if(CommonService::getMaxFileSize() < $file->size)
                {
                    $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                    $resultInfo['detail'] = array('attachment_file' => $file->name . ' ' .
                        Yii::t('Common', 'Max file size exceeded'));
                    return $resultInfo;
                }
                $fileType = self::getFileType($file->name);
                $filePathName = self::createUploadFilePath($productId, $index);
                if('' != $fileType)
                {
                    $filePathName = $filePathName . '.' . $fileType;
                }
                if('' == $filePathName)
                {
                    $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                    $resultInfo['detail'] = array('attachment_file' => Yii::t('Common', self::ERROR_FILE_PATH_EMPTY));
                    return $resultInfo;
                }
                elseif($file->saveAs($filePathName))
                {
                    $uploadPath = Yii::app()->params->uploadPath;
                    $fileLocation = substr($filePathName, strlen($uploadPath) + 1);
                    $fileInfo = new TestFile();
                    $fileInfo->add_action_id = $actionId;
                    $fileInfo->file_location = $fileLocation;
                    $fileInfo->file_size = self::getFileSize($file->size);
                    $fileInfo->file_title = $file->name;
                    $fileInfo->file_type = $fileType;
                    $fileInfo->is_dropped = CommonService::$TrueFalseStatus['FALSE'];
                    $fileInfo->target_id = $targetId;
                    $fileInfo->target_type = $type;
                    if(!$fileInfo->save())
                    {
                        $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                        $resultInfo['detail'] = $fileInfo->getErrors();
                        return $resultInfo;
                    }
                }
            }
        }
        $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        return $resultInfo;
    }

    private static function createUploadFilePath($productId, $index)
    {
        $uploadPath = Yii::app()->params->uploadPath;
        $path = $uploadPath . '/Project' . $productId;
        if(!is_dir($path))
        {
            if(!@mkdir($path, 0755))
            {
                return '';
            }
        }

        // Create current month dir
        $path .= '/' . date("Ym");
        if(!is_dir($path))
        {
            if(!@mkdir($path, 0755))
            {
                return '';
            }
        }
        // Create file path
        $path .= '/' . date("His") . rand(10, 20) . $index;
        return $path;
    }

    public static function getRelatedFileInfos($targetType, $targetId)
    {
        $fileInfos = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{test_file}}')
                        ->where('target_type = :targetType and target_id = :targetId',
                                array(':targetType' => $targetType,
                                    ':targetId' => $targetId))
                        ->order('id')
                        ->queryAll();

        return $fileInfos;
    }

    public static function dropFile($fileIdStr, $actionId)
    {
        $fileIdArr = CommonService::splitStringToArray(',', $fileIdStr);
        for($i = 0; $i < count($fileIdArr); $i++)
        {
            if(!empty($fileIdArr[$i]))
            {
                $fileInfo = TestFile::model()->findByPk($fileIdArr[$i]);
                $fileInfo->is_dropped = CommonService::$TrueFalseStatus['TRUE'];
                $fileInfo->delete_action_id = $actionId;
                $fileInfo->save();
            }
        }
    }

    public static function getRelatedFileHtml($fileInfos, $deleteable = true)
    {
        $fileHtmlStr = '<div id="uploaded_file" style="word-break:break-all;">';
        $uploadFilePath = Yii::app()->params->picPreviewApp;
        for($i = 0; $i < count($fileInfos); $i++)
        {
            $fileInfo = $fileInfos[$i];
            if(CommonService::$TrueFalseStatus['FALSE'] == $fileInfo['is_dropped'])
            {
                $fileType = $fileInfo['file_type'];
                $previewStr = '';
                if(in_array($fileType, array('jpg', 'jpeg', 'gif', 'png', 'bmp')))
                {
                    $previewStr = 'onmouseover="return overlib(\'' .
                            htmlspecialchars('<img style="width:300px;" src="' .
                                    $uploadFilePath . '/' . $fileInfo['file_location'] . '"/>') .
                            '\',OFFSETX,-300,WIDTH,300,BGCOLOR,\'#75736E\',FGCOLOR,\'#75736E\');" onmouseout="return nd();"';
                }

                $fileHtmlStr .= '<span id="file' . $fileInfo['id'] .
                        '">[<a href="' . Yii::app()->createUrl('testFile/view',
                                array('id' => $fileInfo['id'])) . '" ' .
                        $previewStr .
                        ' target="_blank">' . $fileInfo['file_title'] .
                        '</a>';

                if($deleteable)
                {
                    $fileHtmlStr .= '&nbsp;<a href="javascript:void(0);" onclick="deleteFile(' . $fileInfo['id'] . ');return false;"' .
                            ' class="MultiFile-remove"><img src="'.Yii::app()->theme->baseUrl.'/assets/images/deletefile.gif" alt="remove" /></a>';
                }
                $fileHtmlStr .= ']</span>&nbsp;';
            }
        }
        $fileHtmlStr .= '</div>';
        return $fileHtmlStr;
    }

    private static function getFileType($fileName)
    {
        $fileType = CommonService::splitStringToArray(".", $fileName);
        $fileType = strtolower(array_pop($fileType));
        if(strlen($fileType) == strlen($fileName))
        {
            return '';
        }
        return $fileType;
    }

    public static function getFileSize($fileSize)
    {
        if($fileSize <= 1024 * 1024)
        {
            $fileSizeName = round($fileSize / 1024, 5) . "KB";
        }
        else
        {
            $fileSizeName = round($fileSize / (1024 * 1024), 2) . "MB";
        }
        return $fileSizeName;
    }

    public static function viewFile($fileId)
    {
        $fileInfo = TestFile::model()->findByPk($fileId);
        if($fileInfo === null)
        {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        $uploadPath = Yii::app()->params->uploadPath;
        $filePath = $uploadPath . DIRECTORY_SEPARATOR . $fileInfo->file_location;
        if(!preg_match("/" . $fileInfo->file_type . '$/i', $fileInfo->file_title))
        {
            $fileName = $fileInfo->file_title . '.' . $fileInfo->file_type;
        }
        else
        {
            $fileName = $fileInfo->file_title;
        }

        self::sysHeaderFile($filePath, $fileName);
    }

    private static function sysHeaderFile($filePath, $fileName = 'tmpfile')
    {
        if(!is_readable($filePath))
        {
            die($filePath . " is not readalble");
        }

        $fileTypeArray = array(
            'cdf' => 'application/x-cdf',
            'fif' => 'application/fractals',
            'spl' => 'application/futuresplash',
            'hta' => 'application/hta',
            'hqx' => 'application/mac-binhex40',
            'doc' => 'application/msword',
            'pdf' => 'application/pdf',
            'p10' => 'application/pkcs10',
            'p7m' => 'application/pkcs7-mime',
            'p7s' => 'application/pkcs7-signature',
            'cer' => 'application/x-x509-ca-cert',
            'crl' => 'application/pkix-crl',
            'ps' => 'application/postscript',
            'setpay' => 'application/set-payment-initiation',
            'setreg' => 'application/set-registration-initiation',
            'smi' => 'application/smil',
            'edn' => 'application/vnd.adobe.edn',
            'pdx' => 'application/vnd.adobe.pdx',
            'rmf' => 'application/vnd.adobe.rmf',
            'xdp' => 'application/vnd.adobe.xdp+xml',
            'xfd' => 'application/vnd.adobe.xfd+xml',
            'xfdf' => 'application/vnd.adobe.xfdf',
            'fdf' => 'application/vnd.fdf',
            'xls' => 'application/x-msexcel',
            'sst' => 'application/vnd.ms-pki.certstore',
            'pko' => 'application/vnd.ms-pki.pko',
            'cat' => 'application/vnd.ms-pki.seccat',
            'stl' => 'application/vnd.ms-pki.stl',
            'ppt' => 'application/x-mspowerpoint',
            'wpl' => 'application/vnd.ms-wpl',
            'rms' => 'video/vnd.rn-realvideo-secure',
            'rm' => 'application/vnd.rn-realmedia',
            'rmvb' => 'application/vnd.rn-realmedia-vbr',
            'rnx' => 'application/vnd.rn-realplayer',
            'rjs' => 'application/vnd.rn-realsystem-rjs',
            'rjt' => 'application/vnd.rn-realsystem-rjt',
            'rmj' => 'application/vnd.rn-realsystem-rmj',
            'rmx' => 'application/vnd.rn-realsystem-rmx',
            'rmp' => 'application/vnd.rn-rn_music_package',
            'rsml' => 'application/vnd.rn-rsml',
            'z' => 'application/x-compress',
            'tgz' => 'application/x-compressed',
            'etd' => 'application/x-ebx',
            'gz' => 'application/x-gzip',
            'ins' => 'application/x-internet-signup',
            'iii' => 'application/x-iphone',
            'jnlp' => 'application/x-java-jnlp-file',
            'latex' => 'application/x-latex',
            'nix' => 'application/x-mix-transfer',
            'mxp' => 'application/x-mmxp',
            'asx' => 'video/x-ms-asf-plugin',
            'wmd' => 'application/x-ms-wmd',
            'wmz' => 'application/x-ms-wmz',
            'p12' => 'application/x-pkcs12',
            'p7b' => 'application/x-pkcs7-certificates',
            'p7r' => 'application/x-pkcs7-certreqresp',
            'swf' => 'application/x-shockwave-flash',
            'sit' => 'application/x-stuffit',
            'tar' => 'application/x-tar',
            'man' => 'application/x-troff-man',
            'zip' => 'application/x-zip-compressed',
            'xml' => 'text/xml',
            '3gp' => 'video/3gpp-encrypted',
            '3g2' => 'video/3gpp2',
            'aiff' => 'audio/x-aiff',
            'au' => 'audio/basic',
            'mid' => 'midi/mid',
            'mp3' => 'audio/x-mpg',
            'm3u' => 'audio/x-mpegurl',
            'ra' => 'audio/x-realaudio',
            'wav' => 'audio/x-wav',
            'wax' => 'audio/x-ms-wax',
            'wma' => 'audio/x-ms-wma',
            'ram' => 'audio/x-pn-realaudio',
            'bmp' => 'image/bmp',
            'gif' => 'image/gif',
            'jpg' => 'image/pjpeg',
            'jpeg' => 'image/pjpeg',
            'png' => 'image/png',
            'tiff' => 'image/tiff',
            'rp' => 'image/vnd.rn-realpix',
            'ico' => 'image/x-icon',
            'xbm' => 'image/xbm',
            'css' => 'text/css',
            '323' => 'text/h323',
            'htm' => 'text/html',
            'html' => 'text/html',
            'uls' => 'text/iuls',
            'txt' => 'text/plain',
            'wsc' => 'text/scriptlet',
            'rt' => 'text/vnd.rn-realtext',
            'htt' => 'text/webviewhtml',
            'htc' => 'text/x-component',
            'iqy' => 'text/x-ms-iqy',
            'odc' => 'text/x-ms-odc',
            'rqy' => 'text/x-ms-rqy',
            'vcf' => 'text/x-vcard',
            'avi' => 'video/x-msvideo',
            'mpeg' => 'video/x-mpeg2a',
            'rv' => 'video/vnd.rn-realvideo',
            'wm' => 'video/x-ms-wm',
            'wmv' => 'video/x-ms-wmv',
            'wmx' => 'video/x-ms-wmx',
            'wvx' => 'video/x-ms-wvx',
            'mht' => 'message/rfc822');

        $extence = self::getFileType($filePath);
        if(array_key_exists($extence, $fileTypeArray))
        {
            $fileType = $fileTypeArray[$extence];
        }
        else
        {
            $fileType = "application/force-download";
        }

        header("Content-type: {$fileType}");
        $contentDispositionStr = 'filename=' . urlencode($fileName);
        if(!in_array($extence, self::$NotForceDownloadFiletype))
        {
            $contentDispositionStr = ' attachment; ' . $contentDispositionStr;
        }
        header("Content-Disposition:" . $contentDispositionStr);
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
        header("Pragma: public");

        $file_content = file_get_contents($filePath);
        echo $file_content;
        exit;
    }

}

?>
