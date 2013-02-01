<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of BugfreeBaseModel
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
abstract class BugfreeModel extends CActiveRecord
{
    const ERROR_LOCK_VERSION = 'this record already edited by other action';
    const ACTION_OPEN = 'Open';
    const ACTION_EDIT = 'Edit';
    const LOCK_VERSION_SPECIAL = 0;

    protected function beforeSave()
    {
        if($this->isNewRecord)
        {
            $this->lock_version = 1;
        }
        else
        {
            $this->lock_version = ($this->lock_version + 1) % 254;
        }
        return parent::beforeSave();
    }

    /**
     * Prepares create_at, create_by, update_at and update_by attributes before performing validation.
     */
    protected function beforeValidate()
    {
        if($this->isNewRecord)
        {
            $this->created_at = date(CommonService::DATE_FORMAT);
            $this->updated_at = $this->created_at;
            if(isset(Yii::app()->user->id))
            {
                $this->created_by = Yii::app()->user->id;
                $this->updated_by = $this->created_by;
            }
            else
            {
                if(!isset($this->created_by))
                {
                    $this->created_by = 0;
                }
                if(!isset($this->updated_by))
                {
                    $this->updated_by = 0;
                }
            }
        }
        else
        {
            $this->updated_at = date(CommonService::DATE_FORMAT);
            if(isset(Yii::app()->user->id))
            {
                $this->updated_by = Yii::app()->user->id;
            }
            else
            {
                $this->updated_by = 0;
            }
        }

        if($this->isNewRecord)
        {
            $this->lock_version = 1;
        }
        else
        {
            $dbInfo = $this->model()->findByPk($this->id);
            if((self::LOCK_VERSION_SPECIAL != $this->lock_version) &&
                    ($this->lock_version != $dbInfo->lock_version))
            {
                $this->addError('lock_version', Yii::t('Common', self::ERROR_LOCK_VERSION));
            }
        }
        return parent::beforeValidate();
    }

}

?>
