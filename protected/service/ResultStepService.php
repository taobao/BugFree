<?php

/*
 *  BugFree is free software under the terms of the FreeBSD License.
 *  @link        http://www.bugfree.org.cn
 *  @package     BugFree
 */

/**
 * Description of ResultStepService
 *
 * @author youzhao.zxw<swustnjtu@gmail.com>
 * @version 3.0
 */
require(dirname(dirname(__FILE__)) . '/extensions/simple_html_dom.php');

class ResultStepService
{

    /**
     * handle result step
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string  $stepStr                result string
     * @return  string                          handled result step
     */
    public static function handleResultStep($stepStr)
    {
        $handledStr = '';
        if(stripos($stepStr, 'bugfree_table_already_set'))
        {
            $handledStr = self::getWithResultStep($stepStr);
        }
        else
        {
            $handledStr = self::getNoResultStep($stepStr);
        }
        return $handledStr;
    }

    public static function getWithResultStep($stepStr)
    {
        $stepDom = str_get_html($stepStr);
        if(empty($stepDom))
        {
            return $stepStr;
        }
        foreach($stepDom->find('span.bugfree_step_result_span') as $spanElement)
        {
            $spanElement->outertext = self::getSelectStr($spanElement->innertext);
        }
        return $stepDom->__toString();
    }

    public static function removeStepResultForBug($stepStr)
    {
        $stepDom = str_get_html($stepStr);
        if(empty($stepDom))
        {
            return $stepStr;
        }
        foreach($stepDom->find('table') as $tableElement)
        {
            if($tableElement->hasAttribute('bugfree_table_already_set'))
            {
                $stepContent = $tableElement->find('tr td', 0)->innertext;
                $tableElement->outertext = $stepContent . '<br />';
            }
        }
        return $stepDom->__toString();
    }

    public static function getNoResultStepBr($stepStr)
    {
        $handledStr = '';
        $stepArr = preg_split('/<br\s*\/>/i', $stepStr);
        foreach($stepArr as $step)
        {
            if(preg_match('/^(\r\n)*\s*\d+\..*?$/', strip_tags($step)))
            {
                $handledStr.= '<table bugfree_table_already_set><tbody><tr valign="top"><td style="width:370px;">' .
                        $step . '</td>';
                $handledStr .= '<td>' . self::getSelectStr() . '</td>';
                $handledStr .= '</tr></tbody></table>';
            }
            else
            {
                $handledStr.= $step . '<br />';
            }
        }
        if(CommonService::endsWith($handledStr, '<br />'))
        {
            $handledStr = substr($handledStr, 0, strlen($handledStr) - strlen('<br />'));
        }
        return $handledStr;
    }

    private static function getSelectStr($selectedValue='')
    {
        $resultValueColorConfig = ResultInfo::getResultValueColorConfig();
        if('' == $selectedValue)
        {
            $returnStr = '<select class="' . ResultInfo::RESULT_STEP_SELECT_CLASS .
                    '" style="width:90px;" >';
        }
        else
        {
            $returnStr = '<select class="' . ResultInfo::RESULT_STEP_SELECT_CLASS .
                    '" style="width:90px;color:' . $resultValueColorConfig[$selectedValue] . '" >';
        }

        $valueArr = ResultInfo::getResultValueOption();
        foreach($valueArr as $valueTmp)
        {
            $selectedStr = '';
            if($valueTmp == $selectedValue)
            {
                $selectedStr = ' selected="selected" bugfree_set_option="setted"';
            }
            if('' == $valueTmp)
            {
                $returnStr .= '<option value="' . $valueTmp . '"' . $selectedStr . '>' . $valueTmp . '</option>';
            }
            else
            {
                $returnStr .= '<option value="' . $valueTmp . '"' . $selectedStr . ' style="color:' .
                        $resultValueColorConfig[$valueTmp] . ';">' . $valueTmp . '</option>';
            }
        }
        $returnStr .= '</select>';
        return $returnStr;
    }

    public static function getNoResultStep($stepStr)
    {
        $stepStr = self::getNoResultStepBr($stepStr);
        $stepDom = str_get_html($stepStr);
        if(empty($stepDom))
        {
            return $stepStr;
        }
        foreach($stepDom->find('ol') as $olElement)
        {
            $handledStr = '';
            $index = 1;
            foreach($olElement->find('li') as $olliElement)
            {
                $handledStr .= '<table bugfree_table_already_set><tbody><tr valign="top"><td style="width:370px;">' .
                        $index . '. ' . $olliElement->innertext . '</td>';
                $handledStr .= '<td>' . self::getSelectStr() . '</td>';
                $handledStr .= '</tr></tbody></table>';
                $index++;
            }
            $olElement->outertext = $handledStr;
        }
        foreach($stepDom->find('ul') as $ulElement)
        {
            $handledStr = '';
            $index = 1;
            foreach($ulElement->find('li') as $ulliElement)
            {
                $handledStr .= '<table bugfree_table_already_set><tbody><tr valign="top"><td style="width:370px;">' .
                        $index . '. ' . $ulliElement->innertext . '</td>';
                $handledStr .= '<td>' . self::getSelectStr() . '</td>';
                $handledStr .= '</tr></tbody></table>';
                $index++;
            }
            $ulElement->outertext = $handledStr;
        }
        return $stepDom->__toString();
    }

    /**
     * replace html select element with span element
     *
     * @author                                  youzhao.zxw<swustnjtu@gmail.com>
     * @param   string  $stepStr                result st string
     * @return  string                          handled result step
     */
    public static function removeSelectFromResultStep($stepStr)
    {
        $resultValueColorConfig = ResultInfo::getResultValueColorConfig();
        $stepDom = str_get_html($stepStr);
        if(empty($stepDom))
        {
            return $stepStr;
        }
        foreach($stepDom->find('select') as $selectElement)
        {
            $handledStr = '';
            $index = 1;
            foreach($selectElement->find('option') as $optionElement)
            {
                if($optionElement->hasAttribute('bugfree_set_option'))
                {
                    $selectedValue = $optionElement->innertext;
                    if('' == $selectedValue)
                    {
                        $selectElement->outertext = '<span class="bugfree_step_result_span" style="width:90px;font-weight:bold;">' .
                                $selectedValue . '</span>';
                    }
                    else
                    {
                        $selectElement->outertext = '<span class="bugfree_step_result_span" style="width:90px;font-weight:bold;color:' .
                                $resultValueColorConfig[$selectedValue] . '">' . $selectedValue . '</span>';
                    }
                    break;
                }
            }
        }
        return $stepDom->__toString();
    }

}

?>
