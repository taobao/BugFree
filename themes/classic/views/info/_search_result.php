<div>
    <table>
      <tr>
        <td >
            <div id="toolbar" style="width:100%;" class="span-18">
            <?php echo '<div style="float:left;"><a onclick="$(\'#select_show_field_dialog\').dialog(\'open\'); return false;" id="CustomSetLink" href="javascript:void(0);">' .
    Yii::t('Common','Custom Fields').'</a>&nbsp;|&nbsp;<span id="VReport"><a href="javascript:void(0);">' .
    Yii::t('Common','Report').'</a></span>&nbsp;|&nbsp;<a href="javascript:exportXml();">'.Yii::t('Common','Export').'</a></div>';
            echo '<div style="float:right;margin-right:20px;">'.$pageInfo.'</div>';
            ?>
            </div>
        </td>
      </tr>
      <tr>
        <td>
          <div  id="searchresult-grid" style="overflow-y:scroll;overflow-x:scroll;" class="span-18">
            <table style="white-space:nowrap;">
              <tr class="title" style="background-color: #EEEEEE;" >
              <?php
              foreach($viewColumnArr as $columnName)
              {
                      echo '<td style="white-space: nowrap;">';
                      echo '<a href="'.$columnName['name'].'">'.$columnName['header'].'</a>';
                      echo '</td>';
              }
              ?>
              </tr>
              <?php
              foreach($rawData as $rowData)
              {
                  echo '<tr>';
                  foreach($viewColumnArr as $columnName)
                  {
                      echo '<td style="white-space: nowrap;">';
                      echo $rowData[$columnName['name']];
                      echo '</td>';
                  }
                  echo '</tr>';
              }
              ?>
            </table>
          </div>
        </td>
      </tr>
    </table>
  </div>

