<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of NoticeController
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class NoticeController extends Controller
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow all users to perform 'index'
                'actions' => array('notice'),
                'users' => array('*'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    /**
     * This is notice action
     */
    public function actionNotice()
    {
        if(!isset($_GET['authkey']) || ('12315454364625223345467' != $_GET['authkey']))
        {
            echo 'notice authkey wrong!';
            return;
        }
        $activeProductIdStr = $this->getActiveProductId();
        $assignToUserArr = $this->getNoticeUser($activeProductIdStr);
        foreach($assignToUserArr as $assignUser)
        {
            $assignedBugArr = Yii::app()->db->createCommand()
                            ->select('id,severity,priority,title,bug_status,created_by_name,assign_to_name,resolved_by_name,solution,updated_at')
                            ->from('{{bugview}}')
                            ->where('assign_to=' . $assignUser['id'] . ' and product_id in(' . $activeProductIdStr . ') and bug_status<>"Closed"')
                            ->order('id desc')
                            ->queryAll();
            $subject = Yii::t('BugInfo', 'Bugs assigned to you by now:') . ' ' . count($assignedBugArr);
            $messageContent = $this->render('notice',
                    array('bugArr' => $assignedBugArr,
                        'assignedUser' => $assignUser), true);

            MailService::sysMail($assignUser['email'], '', $subject, $messageContent);
        }
    }

    private function getActiveProductId()
    {
        $activeProductArr = Product::model()->findAllByAttributes(array('is_dropped' => '0'));
        $productIdArr = array();
        foreach($activeProductArr as $product)
        {
            $productIdArr[] = $product['id'];
        }
        return join(',', $productIdArr);
    }

    private function getNoticeUser($activeProductIdStr)
    {
        $searchResult = Yii::app()->db->createCommand()
                        ->select('*')
                        ->from('{{test_user}}')
                        ->where("id in (select distinct(assign_to) from {{bug_info}} 
                                where bug_status<>'Closed' and product_id in($activeProductIdStr)) and id>0
                                and email_flag='1' and email<>'' and email is not null and is_dropped='0'")
                        ->queryAll();
        return $searchResult;
    }

}

?>
