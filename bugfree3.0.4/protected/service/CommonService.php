<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of CommonService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class CommonService
{
    const FLAG_TRUE = '1';
    const FLAG_FALSE = '0';

    const DATE_FORMAT = 'Y-m-d H:i:s';

    static $TrueFalseStatus = array(
        'TRUE' => '1',
        'FALSE' => '0',
    );
    static $ApiResult = array(
        'SUCCESS' => 'success',
        'FAIL' => 'failed',
    );

    /**
     * get true flase option
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @return  array                           true or false array
     */
    public static function getOptionValue($optionName)
    {
        $info = TestOption::model()->findByAttributes(array('option_name' => $optionName));
        if(($info != null) && isset($info['option_value']))
        {
            return $info['option_value'];
        }
        else
        {
            return '';
        }
    }

    /**
     * get now time
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @return  string                          now time string
     */
    public static function getDebugTime()
    {
        list($usec, $sec) = explode(" ", microtime());
        $aa =  ((float) $usec + (float) $sec);
        list($usec, $sec) = explode(".", $aa);
        $date = date('Y-m-d H:i:s.x', $usec);
        return str_replace('x', $sec, $date);
    }

    /**
     * get true flase option
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @return  array                           true or false array
     */
    public static function getTrueFalseOptions()
    {
        return array(
            self::FLAG_FALSE => Yii::t('Common', self::FLAG_FALSE),
            self::FLAG_TRUE => Yii::t('Common', self::FLAG_TRUE),
        );
    }

    /**
     * get name by value
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @return  string                          name
     */
    public static function getNameByValue($array, $value)
    {
        foreach($array as $key => $name)
        {
            if($key == $value)
            {
                return $name;
            }
        }
        return $value;
    }

    /**
     * get page size
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @return  int                             page size
     */
    public static function getPageSize()
    {
        $pageSize = Yii::app()->user->getState('pageSize');
        if($pageSize == null)
        {
            $cookies = Yii::app()->request->getCookies();
            if(!empty($cookies['pageSize']))
            {
                $pageSize = $cookies['pageSize']->value;
                Yii::app()->user->setState('pageSize', $pageSize);
            }
            else
            {
                $pageSize = CommonService::getOptionValue(TestOption::DEFAULT_PAGESIZE);
                if(empty($pageSize))
                {
                    $pageSize = 10;
                }
                $pageSizeCookie = new CHttpCookie('pageSize', $pageSize);
                $pageSizeCookie->expire = time() + 60 * 60 * 24 * 30;  //有限期30天
                Yii::app()->request->cookies['pageSize'] = $pageSizeCookie;
                Yii::app()->user->setState('pageSize', $pageSize);
            }
        }
        return $pageSize;
    }

    /**
     * get query limit number
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @return  int                             query limit number
     */
    public static function getQueryLimitNumber()
    {
        $limitNum = Yii::app()->user->getState('limitNum');
        if($limitNum == null)
        {
            $limitNum = CommonService::getOptionValue(TestOption::QUERY_FIELD_NUMBER);
            if(empty($limitNum))
            {
                $limitNum = 8;
            }
            Yii::app()->user->setState('limitNum', $limitNum);
        }
        return $limitNum;
    }

    /**
     * get query limit number
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @return  int                             query limit number
     */
    public static function getMaxFileSize()
    {
        $fileSize = Yii::app()->user->getState('maxFileSize');
        if($fileSize == null)
        {
            $fileSize = CommonService::getOptionValue(TestOption::MAX_FILE_SIZE);
            if(empty($fileSize))
            {
                $fileSize = 2 * (1024 * 1024);
            }
            Yii::app()->user->setState('maxFileSize', $fileSize);
        }
        return $fileSize;
    }

    /**
     * curl get
     *
     * @author                                        youzhao.zxw<swustnjtu@gmail.com>
     * @param   string        $url                    get url string
     * @return  string                                curl result
     *
     */
    public static function curlGetData($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * curl post data
     *
     * @author                                        youzhao.zxw<swustnjtu@gmail.com>
     * @param   string        $url                    get url string
     * @param   array         $dataArr                post data array
     * @return  string                                curl post result
     *
     */
    public static function curlPostData($url, $dataArr)
    {
        $postDataArr = array();
        foreach($dataArr as $key => $value)
        {
            $postDataArr[$key] = urlencode($value);
            $fields_string .= $key . '=' . urlencode($value) . '&';
        }
        rtrim($fields_string, '&');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($dataArr));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * get true false value according to name
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @return  string                          true false value
     */
    public static function getTrueFalseValue($trueFalseName)
    {
        $trueFalseArr = self::getTrueFalseOptions();
        foreach($trueFalseArr as $key => $value)
        {
            if($value == $trueFalseName)
            {
                return $key;
            }
        }
    }

    /**
     * get search operator config
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @return  array                           search operator config
     */
    public static function getFieldTypeOperation()
    {
        return array(
            'number' => array('=', '!=', '>', '<', '>=', '<=', 'IN'),
            'date' => array('=', '!=', '>', '<', '>=', '<='),
            'string' => array('=', '!=','LIKE', 'NOT LIKE'),
            'people' => array('=', '!='),
            'multipeople' => array('='),
            'option' => array('=', '!='),
            'multioption' => array('=', '!=', 'LIKE', 'NOT LIKE'),
            'path' => array('LIKE', 'NOT LIKE', 'UNDER')
        );
    }

    public static function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        $start = $length * -1; //negative
        return (substr($haystack, $start) === $needle);
    }

    public static function getCalendarLanguage()
    {
        return (isset(Yii::app()->language) && ('zh_cn' == Yii::app()->language)) ? Yii::app()->language : 'en';
    }

    public static function isEmailFormat($emailStr)
    {
        $emailStr = trim($emailStr);
        if(!preg_match("/^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,4}$/i", $emailStr))
        {
            return false;
        }
        return true;
    }

    /**
     * get search operator config
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string        $userIds          user id string
     * @return  string                          user realname string
     */
    public static function getMultiUserRealName($userIds)
    {
        $userIdArr = self::splitStringToArray(',', $userIds);
        $userNameStr = '';
        foreach($userIdArr as $userId)
        {
            $userNameStr .= self::getUserRealName($userId) . ',';
        }
        if('' != $userNameStr)
        {
            $userNameStr = substr($userNameStr, 0, strlen($userNameStr) - 1);
        }
        return $userNameStr;
    }

    /**
     * get user realname according to id
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   int         $userId             user id
     * @return  string                          user realname
     */
    public static function getUserRealName($userId)
    {
        if(0 === $userId)
        {
            return Yii::t('Common', 'system created');
        }
        elseif(empty($userId))
        {
            return '';
        }

        $userInfo = TestUser::model()->findByPk($userId);
        if($userInfo !== null)
        {
            return $userInfo->realname;
        }
        else
        {
            //return Yii::t('Common', 'invalid user');
            return '';
        }
    }

    public static function getDateStr($dateTimeStr)
    {
        if(!empty($dateTimeStr))
        {
            return substr($dateTimeStr, 0, 10);
        }
        else
        {
            return '';
        }
    }

    /**
     * get module's full path name
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   int         $id                 module id
     * @return  string                          module's full path name
     */
    public static function getModuleFullNameById($id)
    {
        if(empty($id))
        {
            return '/';
        }
        else
        {
            $productModuleInfo = ProductModule::model()->findByPk($id);
            if($productModuleInfo == null)
            {
                return '/';
            }
            else
            {
                return $productModuleInfo->full_path_name;
            }
        }
    }

    public static function getTrueFalseName($isDropped)
    {
        return Yii::t('Common', $isDropped);
    }

    public static function getMessageName($messageModel, $keyword)
    {
        return Yii::t($messageModel, $keyword);
    }

    /**
     * split string to array
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string      $delimiter          delimiter
     * @param   string      $string             been splited string
     * @return  array                           split result array
     */
    public static function splitStringToArray($delimiter, $string)
    {
        if(isset($string))
        {
            $arrTmp = explode($delimiter, $string);
            $returnArr = array();
            foreach($arrTmp as $value)
            {
                $value = trim($value);
                if('' != $value)
                {
                    $returnArr[] = $value;
                }
            }
            return array_unique($returnArr);
        }
        else
        {
            return array();
        }
    }

    public static function getDeleteLink($type, $id)
    {
        return '<a href="javascript:void(0);" title="' . Yii::t('Common', 'delete') . '" onclick="deleteTemplateOrQuery(\'' . $type . '\',' .
        $id . ');" target="_self" ><img src="' . Yii::app()->theme->baseUrl . '/assets/images/delete.gif"></a>';
    }

    public static function getRowCss($isDropped)
    {
        $className = '';
        if(CommonService::$TrueFalseStatus['TRUE'] == $isDropped)
        {
            $className = 'disabled';
        }
        return $className;
    }

    /**
     * Return part of a string(Enhance the function substr())
     *
     * @author                  Chunsheng Wang <wwccss@263.net>
     * @global array                 the bug config array.
     * @param  string  $String  the string to cut.
     * @param  int     $Length  the length of returned string.
     * @param  booble  $Append  whether append "...": false|true
     * @return string           the cutted string.
     */
    public static function sysSubStr($String, $Length, $Append = false)
    {
        global $_CFG;
        $I = 0;
        $Count = 0;
        while($Count < $Length)
        {
            $StringTMP = substr($String, $I, 1);
            if(ord($StringTMP) >= 224)
            {
                $StringTMP = substr($String, $I, 3);
                $I = $I + 3;
                $Count += 2;
            }
            elseif(ord($StringTMP) >= 192)
            {
                $StringTMP = substr($String, $I, 2);
                $I = $I + 2;
                $Count++;
            }
            else
            {
                $I = $I + 1;
                $Count++;
            }
            $StringLast[] = $StringTMP;
        }
        if($Count == $Length)
        {
            array_pop($StringLast);
        }
        $StringLast = implode("", $StringLast);
        if($Append && $String != $StringLast)
        {
            $StringLast .= "...";
        }
        return $StringLast;
    }

    /**
     * The start of javascript.
     *
     * @param   booble  $Echo   echo the javascript string or not.
     * @return  string          the start tag of javascript scripts.
     */
    public static function jsStart($Echo = false)
    {
        $JS = "<script language='Javascript'>";
        if($Echo)
        {
            echo $JS;
        }
        return $JS;
    }

    /**
     * The end of javascript.
     *
     * @param   booble  $Echo   echo the javascript string or not.
     * @return  string          the end tag of javascript scripts.
     */
    public static function jsEnd($Echo = false)
    {
        $JS = "</script>";
        if($Echo)
        {
            echo $JS;
        }
        return $JS;
    }

    /**
     * Flush (send) the javascript output buffer
     *
     * @author                     Yupeng Lee <leeyupeng@gmail.com>
     * @param   string  $JsStr
     */
    public static function sysObFlushJs($JsStr)
    {
        self::sysObFlush(self::jsStart() . $JsStr . self::jsEnd());
    }

    /**
     * Flush (send) the  output buffer
     *
     * @author                     Yupeng Lee <leeyupeng@gmail.com>
     * @param   string  $Str
     */
    public static function sysObFlush($Str)
    {
        echo $Str;
        ob_flush();
        flush();
    }

    public static function testRefreshSelf()
    {
        $JS = <<<EOT
try{
window.location.href=window.location.href;
}
catch(e){}
EOT;
        self::sysObFlushJs($JS);
    }

    public static function testRefreshParent()
    {
        $JS = <<<EOT
try{
var parentWin=window.parent;
var openerWin=parentWin.opener;
var indexWin=openerWin.parent;
indexWin.location.href=indexWin.location.href;
}
catch(e){}
EOT;
        self::sysObFlushJs($JS);
    }

    /**
     * change the location of the $Target window to the $URL.
     *
     * @param   string $URL    the url will go to.
     * @param   string $Target the target of the url.
     * @param   booble $Echo   echo the javascript string or not.
     * @return  string         the javascript string.
     */
    public static function jsGoTo($URL, $Target = "self", $Echo = true)
    {
        $JS = self::jsStart();
        if(strtolower($URL) == "back")
        {
            $JS .= "history.back(-1);";
        }
        else
        {
            $JS .= "$Target.location='$URL';";
        }
        $JS .= self::jsEnd();
        if($Echo)
        {
            echo($JS);
        }
        return $JS;
    }

    /**
     * show a alert box.
     *
     * @param   array   $Text   the text to be showd in the alert box.
     * @param   booble  $Echo   echo the javascript string or not.
     * @return  string          the javascript script.
     */
    public static function jsAlert($Text, $Echo = true)
    {
        $JS = self::jsStart();
        $JS .= <<<EOT
    alert('$Text');
EOT;
        $JS .= self::jsEnd();
        if($Echo)
        {
            echo $JS;
        }
        return $JS;
    }

}

?>
