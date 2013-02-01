<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of TreeService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
class TreeService
{

    /**
     * get search condition according to the search row array
     *
     * @author                                              youzhao.zxw<swustnjtu@gmail.com>
     * @param   array          $infos                       product module info array
     * @param   array          $parentIdArr                 module's parent id array
     * @return  array                                       module info used for select
     *
     */
    public static function formSelectedTreeData($infos, $parentIdArr)
    {
        $treeArr = array();
        $parentGradeArr = array_keys($parentIdArr);
        $topGrade = $parentGradeArr[count($parentGradeArr) - 1];
        $bottomGrade = $parentGradeArr[0];
        foreach($infos as $info)
        {
            if(1 == $info['grade'])
            {
                $treeNode = new TreeDataModel();
                $treeNode->id = $info['id'];
                $treeNode->pId = $info['parent_id'];
                $treeNode->name = $info['name'];
                $treeNode->isParent = false;
                $treeArr[1][$info['id']] = $treeNode;
            }
            else
            {
                if(!empty($parentIdArr[$info['grade']]))
                {
                    if($info['parent_id'] == $parentIdArr[$info['grade'] - 1])
                    {
                        $treeNode = new TreeDataModel();
                        $treeNode->id = $info['id'];
                        $treeNode->pId = $info['parent_id'];
                        $treeNode->name = $info['name'];
                        $treeNode->isParent = false;
                        $treeArr[$info['grade']][$info['id']] = $treeNode;
                        $treeArr[$info['grade'] - 1][$info['parent_id']]->nodes[] = $treeNode;
                        $treeArr[$info['grade'] - 1][$info['parent_id']]->isParent = true;
                        $treeArr[$info['grade'] - 1][$info['parent_id']]->open = true;
                    }
                    else
                    {
                        if(!empty($treeArr[$info['grade'] - 1][$info['parent_id']]))
                        {
                            $treeArr[$info['grade'] - 1][$info['parent_id']]->isParent = true;
                        }
                    }
                }
                else if($bottomGrade + 1 == $info['grade'])
                {
                    if(isset($treeArr[$info['grade'] - 1][$info['parent_id']]))
                    {
                        $treeArr[$info['grade'] - 1][$info['parent_id']]->isParent = true;
                    }
                }
            }
        }
        $topArr = array();
        if(!empty($treeArr[1]))
        {
            foreach($treeArr[1] as $topModel)
            {
                $topArr[] = $topModel;
            }
        }
        return $topArr;
    }

    public static function formOptionTreeData($infos, $parentId)
    {
        $parentModuleInfo = ProductModule::model()->findByPk($parentId);
        if($parentModuleInfo == null)
        {
            return array();
        }
        else
        {
            $topGrade = $parentModuleInfo->grade + 1;
            $paNameLen = strlen($parentModuleInfo->full_path_name);
        }
        $treeArr = array();
        foreach($infos as $info)
        {
            $treeNode = new TreeDataModel();
            $treeNode->id = $info['id'];
            $treeNode->pId = $info['parent_id'];
            $filteredName = substr($info['full_path_name'], $paNameLen, strlen($info['full_path_name']));
            $treeNode->name = $filteredName;
            $treeArr[$info['grade']][$info['id']] = $treeNode;
            if(isset($treeArr[$info['grade'] - 1][$info['parent_id']]))
            {
                $treeArr[$info['grade'] - 1][$info['parent_id']]->nodes[] = $treeNode;
            }
        }

        if(!empty($treeArr[$topGrade]))
        {
            return $treeArr[$topGrade];
        }
        else
        {
            return array();
        }
    }

    public static function formAjaxTreeData($infos, $childInfos)
    {
        $treeArr = array();
        foreach($infos as $info)
        {
            $treeNode = new TreeDataModel();
            $treeNode->id = $info['id'];
            $treeNode->pId = $info['parent_id'];
            $treeNode->name = $info['name'];
            $treeNode->open = false;
            if(self::checkChildren($childInfos, $info['id']))
            {
                $treeNode->isParent = true;
            }
            $treeArr[] = $treeNode;
        }
        return $treeArr;
    }

    private static function checkChildren($childInfos, $parentId)
    {
        foreach($childInfos as $childInfo)
        {
            if($parentId == $childInfo['parent_id'])
            {
                return true;
            }
        }
        return false;
    }

}

?>
