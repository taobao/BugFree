<?php
/**
 * This is sheet convert class
 * 
 * @package bugfree.protected.compontents
 */
class SheetConv
{
    const XML_WORKSHEET = 'Worksheet';
    const XML_TABLE = 'Table';
    const XML_ROW = 'Row';
    const XML_CELL = 'Cell';
    
    /**
     * translate sheet.xml to array
     * 
     * @param mixed $xml file or string
     * @return array
     */
    public static function xml2array($xml)
    {
        $arr = array();
        
        if(is_file($xml))
        {
            $xml = file_get_contents($xml);
        }
        $xml = (string)$xml;
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadXML($xml);
        $sheets = $dom->getElementsByTagName(SheetConv::XML_WORKSHEET);
        for($i = 0; $i < $sheets->length; $i++)
        {
            $vals = array();
            $sheet = $sheets->item($i);
            $tables = $sheet->getElementsByTagName(SheetConv::XML_TABLE);          
            // the sheet has only one table
            if($tables->length > 0)
            {
                $table = $tables->item(0);
                $rows = $table->getElementsByTagName(SheetConv::XML_ROW);
                // the table should has more than one row, first row was the field
                if($rows->length > 0)
                {
                    $fields = array();
                    $fieldRow = $rows->item(0);
                    $fieldCells = $fieldRow->getElementsByTagName(SheetConv::XML_CELL);
                    // field cells index
                    for($fci = 0; $fci < $fieldCells->length; $fci++)
                    {
                        // $row->cell->data->value
                        $fields[$fci] = $fieldCells->item($fci)->nodeValue;
                    }
                    
                    // value rows index
                    for($vri = 1; $vri < $rows->length; $vri++)
                    {
                        $val = array();
                        $valRow = $rows->item($vri);
                        $valCells = $valRow->getElementsByTagName(SheetConv::XML_CELL);
                        $current = 0;
                        // value cells index
                        for($vci = 0; $vci < $valCells->length; $vci++)
                        {
                           if($valCells->item($vci)->getAttribute('ss:Index'))
                           {
                               $val[$fields[$vci]] = '';
                               $current = $valCells->item($vci)->getAttribute('ss:Index') - 1;
                           }
                           if(isset($fields[$current]))
                           {
                               $val[$fields[$current]] = $valCells->item($vci)->nodeValue;
                               $current++;
                           }
                        }
                        $vals[] = $val;
                    }
                }
            }
            $arr[$i] = $vals; 
        }
        return $arr;
    }
}
?>