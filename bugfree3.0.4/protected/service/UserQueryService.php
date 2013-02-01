<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of UserQueryService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class UserQueryService
{
    const TIP_UPDATE_SUCCESS = 'update query success';
    const TIP_CREATE_SUCCESS = 'create query success';

    /**
     * edit user query
     *
     * @author                                   youzhao.zxw<swustnjtu@gmail.com>
     * @param   string       $title              user query title
     * @param   int          $productId          product id
     * @param   string       $type               bug,case or result
     * @param   array        $searchConditionArr search condition array
     * @return  array                            edit user query result information
     */
    public static function editUserQuery($title, $productId, $type, $searchConditionArr)
    {
        $resultInfo = array();
        $updateFlag = false;
        $title = trim($title);
        $userQuery = UserQuery::model()->findByAttributes(array('query_type' => $type,
                    'product_id' => $productId,
                    'created_by' => Yii::app()->user->id,
                    'title' => $title));
        if(false != $userQuery)
        {
            $updateFlag = true;
            $userQuery->updated_at = date(CommonService::DATE_FORMAT);
        }
        else
        {
            $userQuery = new UserQuery();
            $userQuery->created_by = Yii::app()->user->id;
            $userQuery->created_at = date(CommonService::DATE_FORMAT);
            $userQuery->updated_at = $userQuery->created_at;
            $userQuery->query_type = $type;
            $userQuery->product_id = $productId;
            $userQuery->title = $title;
        }


        $searchRowConditionArr = SearchService::getSearchConditionArr($searchConditionArr);
        $queryConditonArr = array();
        $keyArr = array('leftParenthesesName', 'field', 'operator', 'value', 'rightParenthesesName', 'andor');
        foreach($searchRowConditionArr as $rowCondtion)
        {
            foreach($rowCondtion as $key => $value)
            {
                $queryConditonArr[$key][] = $value;
            }
        }


        $userQuery->left_parentheses = serialize($queryConditonArr['leftParenthesesName']);
        $userQuery->fieldlist = serialize($queryConditonArr['field']);
        $userQuery->operatorlist = serialize($queryConditonArr['operator']);
        $userQuery->valuelist = serialize($queryConditonArr['value']);
        $userQuery->right_parentheses = serialize($queryConditonArr['rightParenthesesName']);
        $userQuery->andorlist = serialize($queryConditonArr['andor']);

        if($userQuery->save())
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
            $resultInfo['detail'] = $userQuery->getErrors();
        }
        return $resultInfo;
    }

    public static function getQueryConditionById($queryId)
    {
        $resultInfo = array();
        $queryInfo = UserQuery::model()->findByPk($queryId);
        if($queryInfo == null)
        {
            $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
            $resultInfo['detail']['id'] = 'query not existed';
            return $resultInfo;
        }
        else
        {
            $userAccessableProductIdArr = Yii::app()->user->getState('visit_product_id');
            if(!in_array($queryInfo['product_id'], $userAccessableProductIdArr))
            {
                $resultInfo['status'] = CommonService::$ApiResult['FAIL'];
                $resultInfo['detail']['id'] = 'has not visit right';
                return $resultInfo;
            }
        }
        $parsedQueryInfo = array();
        $parsedQueryInfo['product_id'] = $queryInfo['product_id'];
        $parsedQueryInfo['query_type'] = $queryInfo['query_type'];
        $parsedQueryInfo['title'] = $queryInfo['title'];
        $searchArr['leftParenthesesName'] = unserialize($queryInfo->left_parentheses);
        $searchArr['field'] = unserialize($queryInfo->fieldlist);
        $searchArr['operator'] = unserialize($queryInfo->operatorlist);
        $searchArr['value'] = unserialize($queryInfo->valuelist);
        $searchArr['rightParenthesesName'] = unserialize($queryInfo->right_parentheses);
        $searchArr['andor'] = unserialize($queryInfo->andorlist);
        $returnArr = array();
        $fieldCount = count($searchArr['field']);
        $keyArr = array('leftParenthesesName', 'field', 'operator', 'value', 'rightParenthesesName', 'andor');
        for($i = 0; $i < $fieldCount; $i++)
        {
            $rowArr = array();
            foreach($keyArr as $key)
            {
                $rowArr[$key] = $searchArr[$key][$i];
            }
            $returnArr[] = $rowArr;
        }
        $parsedQueryInfo['search_condition'] = $returnArr;
        $resultInfo['status'] = CommonService::$ApiResult['SUCCESS'];
        $resultInfo['detail'] = $parsedQueryInfo;
        return $resultInfo;
    }

    public static function getOpenByMeQuery()
    {
        $searchRowArr[] = array('leftParenthesesName' => '',
            'field' => 'created_by_name', 'operator' => '=',
            'value' => Yii::app()->user->realname,
            'rightParenthesesName' => '',
            'andor' => 'And');

        return array('title' => Yii::t('Common', 'Opened by me'), 'search_condition' => $searchRowArr);
    }

    public static function getAssignToMeQuery()
    {
        $searchRowArr[] = array('leftParenthesesName' => '',
            'field' => 'assign_to_name', 'operator' => '=',
            'value' => Yii::app()->user->realname,
            'rightParenthesesName' => '',
            'andor' => 'And');
        return array('title' => Yii::t('Common', 'Assign to me'), 'search_condition' => $searchRowArr);
    }

    public static function getMarkByMeQuery()
    {
        $searchRowArr[] = array('leftParenthesesName' => '',
            'field' => Info::MARK, 'operator' => '=',
            'value' => 1,
            'rightParenthesesName' => '',
            'andor' => 'And');
        return array('title' => Yii::t('Common', 'Mark by me'), 'search_condition' => $searchRowArr);
    }

    public static function getMailedToMeQuery()
    {
        $searchRowArr[] = array('leftParenthesesName' => '',
            'field' => 'mail_to', 'operator' => 'LIKE',
            'value' => Yii::app()->user->realname,
            'rightParenthesesName' => '',
            'andor' => 'And');
        return array('title' => Yii::t('Common', 'Mailed to me'), 'search_condition' => $searchRowArr);
    }

    public static function getUserQuery($productId, $queryType, $userId)
    {
        $searchResult = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{user_query}}')
                        ->where('product_id = :product_id and query_type = :query_type and created_by = :createdBy',
                                array(':product_id' => $productId, ':query_type' => $queryType, ':createdBy' => $userId))
                        ->queryAll();
        return $searchResult;
    }

    private static function getOpenedByMeLink($productId, $infoType, $userId)
    {
        $rawData = Yii::app()->db->createCommand()
                        ->select('count(id) as totalNum')
                        ->from('{{' . $infoType . '_info}}')
                        ->where('product_id = :productId and created_by=:createdBy', array(':productId' => $productId, ':createdBy' => $userId))
                        ->queryRow();
        $count = $rawData['totalNum'];
        if(0 < $count)
        {
            return '<span style="font-weight:bold;">' . Yii::t('Common', 'Opened by me') . '(' . $count . ')</span>';
        }
        else
        {
            return Yii::t('Common', 'Opened by me');
        }
    }

    private static function getAssignToMeLink($productId, $infoType, $userId)
    {
        $rawData = Yii::app()->db->createCommand()
                        ->select('count(id) as totalNum')
                        ->from('{{' . $infoType . '_info}}')
                        ->where('product_id = :productId and assign_to=:assignTo', array(':productId' => $productId, ':assignTo' => $userId))
                        ->queryRow();

        $count = $rawData['totalNum'];
        if(0 < $count)
        {
            return '<span style="font-weight:bold;">' . Yii::t('Common', 'Assigned to me') . '(' . $count . ')</span>';
        }
        else
        {
            return Yii::t('Common', 'Assigned to me');
        }
    }

    private static function getMailedToMeLink($productId, $infoType)
    {
        $realname = Yii::app()->user->getState('realname');
        $sql = 'select count(id) as totalNum from {{' . $infoType . '_info}} where product_id=' .
                $productId . ' and mail_to like ' . Yii::app()->db->quoteValue('%' . $realname . '%');
        $count = Yii::app()->db->createCommand($sql)->queryScalar();

        if(0 < $count)
        {
            return '<span style="font-weight:bold;">' . Yii::t('Common', 'Mailed to me') . '(' . $count . ')</span>';
        }
        else
        {
            return Yii::t('Common', 'Mailed to me');
        }
    }

    private static function getMarkedByMeLink($productId, $infoType, $userId)
    {
        $rawData = Yii::app()->db->createCommand()
                        ->select('count(id) as totalNum')
                        ->from('{{' . $infoType . '_info}}')
                        ->where('id in (select info_id from {{map_user_' . $infoType . '}} where test_user_id = :userId) and product_id = :productId', array(':productId' => $productId, ':userId' => $userId))
                        ->queryRow();
        $count = $rawData['totalNum'];
        if(0 < $count)
        {
            return '<span style="font-weight:bold;">' . Yii::t('Common', 'Marked by me') . '(' . $count . ')</span>';
        }
        else
        {
            return Yii::t('Common', 'Marked by me');
        }
    }

    public static function getQueryLinkStr($productId, $type, $userId)
    {
        $actionStr = '';
        $actionStr .= '<li><a class="basic" href="' . Yii::app()->createUrl('info/index', array('type' => $type, 'product_id' => $productId, 'query_id' => -3)) . '" title="' .
                Yii::t('Common', 'Marked by me') . '">' .
                self::getMarkedByMeLink($productId, $type, $userId) . '</a></li>';
        $actionStr .= '<li><a class="basic" href="' . Yii::app()->createUrl('info/index', array('type' => $type, 'product_id' => $productId, 'query_id' => -2)) . '" title="' .
                Yii::t('Common', 'Assigned to me') . '">' .
                self::getAssignToMeLink($productId, $type, $userId) . '</a></li>';
        $actionStr .= '<li><a class="basic" href="' . Yii::app()->createUrl('info/index', array('type' => $type, 'product_id' => $productId, 'query_id' => -4)) . '" title="' .
                Yii::t('Common', 'Mailed to me') . '">' .
                self::getMailedToMeLink($productId, $type) . '</a></li>';
        $actionStr .= '<li><a class="basic" href="' . Yii::app()->createUrl('info/index', array('type' => $type, 'product_id' => $productId, 'query_id' => -1)) . '" title="' .
                Yii::t('Common', 'Opened by me') . '">' .
                self::getOpenedByMeLink($productId, $type, $userId) . '</a></li>';
        $myQueryArr = self::getUserQuery($productId, $type, $userId);
        foreach($myQueryArr as $query)
        {
            $deleteLinkStr = CommonService::getDeleteLink('query', $query['id']);
            $actionStr .= '<li id="user_query_' . $query['id'] . '">' .
                    $deleteLinkStr . '<a href="' . Yii::app()->createUrl('info/index',
                            array('type' => $type, 'product_id' => $productId, 'query_id' => $query['id'])) .
                    '" class="user_query_a">' .
                    CHtml::encode($query['title']) . '</a></li>';
        }
        return $actionStr;
    }

}

?>
