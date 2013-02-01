<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of LoginService
 * login help class
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
Class LoginService
{

    public static function login($params)
    {
        $resultInfo = array();
        $model = new LoginForm();
        $model->attributes = $params;
        $model->username = trim($model->username);
        if($model->validate())
        {
            $setInfoResult = LoginService::setUserInfo();
            if(!empty($setInfoResult))
            {
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail']['username'] = $setInfoResult;
            }
            else
            {
                $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
            }          
        }
        else
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $model->getErrors();
        }
        return $resultInfo;
    }

    /**
     * Set user related info after login success
     * @param CFilterChain $filterChain the filter chain that the filter is on.
     * @return boolean whether the filtering process should continue and the action
     * should be executed.
     */
    public static function setUserInfo()
    {
        $userId = Yii::app()->user->id;
        $accessableProducts = TestUserService::getAccessableProduct($userId);
        if(empty($accessableProducts))
        {
            Yii::app()->user->logout();
            return Yii::t('LoginForm', 'no accessable product');
        }

        $productCookieKey = $userId . "_product";
        $productIdArr = array();
        foreach($accessableProducts as $productInfo)
        {
            $productOptions[$productInfo['id']] = $productInfo['name'];
            $productIdArr[] = $productInfo['id'];
        }
        $cookies = Yii::app()->request->getCookies();
        if(empty($cookies[$productCookieKey]) || !in_array($cookies[$productCookieKey]->value, $productIdArr))
        {
            $cookie = new CHttpCookie($productCookieKey, $accessableProducts[0]['id']);
            $cookie->expire = time() + 60 * 60 * 24 * 30;  //有限期30天
            Yii::app()->request->cookies[$productCookieKey] = $cookie;
        }

        Yii::app()->user->setState('product', $cookies[$productCookieKey]->value);
        Yii::app()->user->setState('visit_product_list', $productOptions);
        Yii::app()->user->setState('visit_product_id', $productIdArr);
        Yii::app()->user->setState('system_admin', TestUserService::isSystemAdmin(Yii::app()->user->id));
        Yii::app()->user->setState('system_manager', TestUserService::isManager(Yii::app()->user->id));
        Yii::app()->user->setState('my_query_div',1);
    }

    public static function setLanguageCookie($language='en')
    {
        $languageCookie = new CHttpCookie('language', $language);
        $languageCookie->expire = time() + 60 * 60 * 24 * 30;  //有限期30天
        Yii::app()->request->cookies['language'] = $languageCookie;
        Yii::app()->user->setState('language', $language);
    }

}

?>