<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of UserTemplateService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class UserTemplateService
{
    const TIP_UPDATE_SUCCESS = 'update template success';
    const TIP_CREATE_SUCCESS = 'create template success';

    /**
     * edit template
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   array       $templateInfo       template information
     * @return  array                           edit result information
     */
    public static function editTemplate($templateInfo)
    {
        $resultInfo = array();
        $updateFlag = false;
        $title = trim($templateInfo['title']);
        $userTemplate = UserTemplate::model()->findByAttributes(array('type' => $templateInfo['type'],
                    'product_id' => $templateInfo['product_id'],
                    'created_by' => Yii::app()->user->id,
                    'title' => $title));
        if(false != $userTemplate)
        {
            $updateFlag = true;
            $userTemplate->updated_at = date(CommonService::DATE_FORMAT);
        }
        else
        {
            $userTemplate = new UserTemplate();
            $userTemplate->created_by = Yii::app()->user->id;
            $userTemplate->created_at = date(CommonService::DATE_FORMAT);
            $userTemplate->updated_at = $userTemplate->created_at;
            $userTemplate->product_id = $templateInfo['product_id'];
            $userTemplate->type = $templateInfo['type'];
            $userTemplate->title = $title;
        }
        $userTemplate->template_content = serialize($templateInfo['template_content']);

        if($userTemplate->save())
        {
            $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
            if($updateFlag)
            {
                $resultInfo['detail'] = array('id' => Yii::t('Common', self::TIP_UPDATE_SUCCESS));
            }
            else
            {
                $resultInfo['detail'] = array('id' => Yii::t('Common', self::TIP_CREATE_SUCCESS));
            }
        }
        else
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail'] = $userTemplate->getErrors();
        }
        return $resultInfo;
    }

    public static function getUserTemplateByTitle($type, $userId, $title)
    {
        $searchResult = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{user_template}}')
                        ->where('type = :type and created_by = :createdBy and title = :title',
                                array(':type' => $type,
                                    ':createdBy' => $userId,
                                    ':title' => "02"))
                        ->queryRow();
        return $searchResult;
    }

    public static function getUserTemplate($productId, $type, $userId)
    {
        $searchResult = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{user_template}}')
                        ->where('product_id = :product_id and type = :type and created_by = :createdBy',
                                array(':product_id' => $productId, ':type' => $type, ':createdBy' => $userId))
                        ->queryAll();
        return $searchResult;
    }

    public static function getAccessableTemplateId($productId, $type, $userId)
    {
        $accessableTemplateArr = self::getUserTemplate($productId, $type, $userId);
        $idArr = array();
        foreach($accessableTemplateArr as $templateInfo)
        {
            $idArr[] = $templateInfo['id'];
        }
        return $idArr;
    }

    public static function getCreateLinkStr($productId, $type, $userId)
    {
        $actionStr = '';
        $myTemplateArr = self::getUserTemplate($productId, $type, $userId);
        foreach($myTemplateArr as $template)
        {
            $deleteLinkStr = CommonService::getDeleteLink('template', $template['id']);
            $actionStr .= '<li id="user_template_' . $template['id'] . '">' .
                    $deleteLinkStr . '&nbsp;&nbsp;<a href="' .
                    Yii::app()->createUrl('info/edit', array('type' => $type,
                        'action' => Info::ACTION_OPEN, 'template_id' => $template['id'])) .
                    '" target="_blank" title="' .
                    $template['title'] . '">' .
                    CHtml::encode($template['title']) . '</a></li>';
        }
        return $actionStr;
    }

}

?>
