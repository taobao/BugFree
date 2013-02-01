<script type="text/javascript">
    var $returnUrl = '<?php echo Yii::app()->createUrl('userGroup/index');?>';
    var timeout;
    $(document).ready(function() {
        var options = {
            dataType:'json',
            beforeSubmit:showRequest,
            success:showResponse
        };
        $('form').submit(function() {
            $('#UserGroup_group_user option').attr('selected','true');
            $(this).ajaxSubmit(options);
            return false;
        });
    });
    function showRequest(formData, jqForm, options) {
        $('.error').removeClass('error');
        return true;
    }
    function showResponse(responseText, statusText, xhr, $form)  {
        if('failed' == responseText.status)
        {
            var detailedArr = responseText.detail;
            var msg = '';
            for(var field in detailedArr)
            {
                msg += detailedArr[field]+'<br/>';
                $('#UserGroup_'+field).addClass('error');
            }
            showErrorMsg(msg);
        }
        else
        {
            showSuccessMsg(responseText.detail+'|<a href="'+$returnUrl+'"><?php echo Yii::t('AdminCommon','Back To Group List');?></a>',$returnUrl);
        }
    }
    function searchUser()
    {       
        $keyword = $('#search_name').val();
        jQuery.get("<?php echo Yii::app()->createUrl('search/userList');?>", {'q':$keyword,'type':'id'}, function (data, textStatus){
        if('success' == textStatus)
        {
            $('#search_name_result option').remove();
            var $dataArr = data.split('\n');
            var showCount = ($dataArr.length>50)?50:$dataArr.length;
            for(var i=0;i<showCount;i++)
            {
                if($dataArr[i].indexOf('|')>=0)
                {
                    var arrTmp = $dataArr[i].split('|');
                    $('<option value="'+arrTmp[0]+'">'+arrTmp[1]+'</option>').appendTo('#search_name_result');
                }
            }
        }
        });
    }

    function bindSearch()
    {
        $('#search_name').bind(($.browser.opera ? "keypress" : "keydown"), function(event) {
            clearTimeout(timeout);
            timeout = setTimeout(searchUser, 400);
        })
    }
    function addUser($selectedArr)
    {
        var $selectedUserStr = ';';
        var $existedUserArr = $('#UserGroup_group_user option');
        $existedUserArr.each(function(){
            $selectedUserStr += $(this).val()+';';
        })
        $selectedArr.each(function(){
            if($selectedUserStr.indexOf(';'+$(this).val()+';')<0)
            {
                $selectedUserStr += $(this).val()+';';
                $(this).appendTo('#UserGroup_group_user');
            }
        })
    }
    $(function(){
        bindSearch();
	$('#addUser').click(function() {
            addUser($('#search_name_result option:selected').clone());              
	});
	$('#deleteUser').click(function() {
            $('#UserGroup_group_user option:selected').remove();
	});

        $('#UserGroup_group_user').dblclick(function(){
            $("option:selected",this).remove();
        });
        $('#search_name_result').dblclick(function(){
            addUser($("option:selected",this).clone().clone());
        });
    });
</script>
<div class="form administration">

    <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'user-group-form',
	'enableAjaxValidation'=>false,
)); ?>
	<?php echo $form->errorSummary($model); ?>
        <h2 class="margin-left-190"><?php echo $actionName; ?></h2>
        <hr />
        <?php
        //lock_version should be the keyword to check if this record has been modified by other action
        echo $form->hiddenField($model,'lock_version',array('value'=>$model->lock_version));
        ?>
	<div class="row">
		<?php echo $form->label($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255,'class'=>'required')); ?>
	</div>      
        <div class="row">
		<?php echo $form->label($model,'group_user'); ?>
                <table class="usergroup">
                    <tr>
                        <td style="width:240px;">
                        <?php
                            echo CHtml::textField('search_name',Yii::t('Common','Type keyword to search'),array('style'=>'width:99%;',
                                'onfocus'=>"if(value =='".Yii::t('Common','Type keyword to search')."'){value =''}",
                            'onblur'=>"if(value ==''){value='".Yii::t('Common','Type keyword to search')."'}")).'<br/>';
                            echo CHtml::dropDownList('search_name_result', '',  array(),
                                    array('multiple'=>true,'style'=>'width:240px;height:240px;margin-top:1px;'));
                        ?>
                        </td>
                        <td style="width:100px;text-align: center;">
                            <div class="SelectButton" style="padding-top:80px;">
                                <input id="addUser" type="button" value="&gt;&gt;" class="btn">
                                <br/><br/>
                                <input id="deleteUser" type="button" value="&lt;&lt;" class="btn">
                            </div>
                        </td>
                        <td style="width:240px;">
                        <?php
                            echo $form->dropDownList($model,'group_user',$model->group_user,
                                    array('multiple'=>true,'style'=>'width:240px;height:260px;',
                                        'class'=>'required'));
                        ?>
                        </td>
                    </tr>
                </table>
	</div>
        <div class="row">
		<?php echo $form->label($model,'group_manager'); ?>
		<?php
                    $this->widget('application.extensions.autocomplete.AutoCompleteWidget', array(
                                    'model' => $model,
                                    'attribute' => 'group_manager',
                                    'config' => '{multiple:true}',
                                    'htmlOptions' => array('style'=>'width:590px;','class'=>'required'),
                                    'urlOrData' => TestUser::getSearchUserUrl()
                                ));
                ?>
	</div>
        <div class="row">
		<?php echo $form->label($model,'is_dropped'); ?>
                <?php echo $form->dropDownList($model,'is_dropped',
                        CommonService::getTrueFalseOptions(),array('class'=>'required')); ?>
	</div>
        <div style="clear:both;"></div>
        <hr/>
	<?php echo CHtml::submitButton(Yii::t('Common','Save'),array('class'=>'margin-left-190')); ?>

<?php $this->endWidget(); ?>

</div>