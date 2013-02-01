function openWindow(Url,WindowName,Width,Height)
{
    if(typeof(WindowName) == 'undefined')
    {
        WindowName = 'newTestWindow';
    }
    if(typeof(Width) == 'undefined' || typeof(Height) == 'undefined')
    {
        var opened=window.open(Url,WindowName,'height='+screen.width+',width='+
            screen.width+', toolbar=yes, menubar=yes, scrollbars=yes, resizable=yes,location=yes, status=yes');
    }
    else
    {
        var opened=window.open(Url,WindowName,'height='+Height+', width='+Width);
    }
    opened.focus();
}

function showErrorMsg($msg)
{
    $("#flash-message").hide();
    $("#flash-message").html($msg);
    $("#flash-message").attr('class','flash-error');
    $("#flash-message").fadeIn('slow',function(){
        $(this).removeAttr('filter');
    });
}

function showSuccessMsg($msg,$returnUrl)
{
    $("#flash-message").html($msg);
    $("#flash-message").attr('class','flash-success');
    setTimeout(function(){
        $("#flash-message").fadeOut(3000,function(){
            window.location = $returnUrl;
        });
    },2000); 
}

function showGotoBCR(e)
{
    e = (e) ? e : ((window.event) ? window.event : "");
    var keyCode =  getKeyCode(e);
    if(e.ctrlKey && keyCode == 71)
    {
        setTimeout('showGotoBCRPrompt()',200);
        return false;
    }
}

function showGotoBCRPrompt()
{
    var id = prompt('Enter the '+$infoType+' ID:', '');
    if(id !== null && /^[1-9][0-9]*$/.test(id))
    {
        if('bug' == $infoType)
        {
            openWindow($baseUrl+'/Bug.php?BugID='+id);
        }
        else if('case' == $infoType)
        {
            openWindow($baseUrl+'/Case.php?CaseID='+id);
        }
        else if('result' == $infoType)
        {
            openWindow($baseUrl+'/Result.php?ResultID='+id);
        }        
    }
    return true;
}

function getKeyCode(e)
{
    if (document.all) {
        return event.keyCode;
    }
    else if (document.getElementById) {
        return (e.keyCode != 0) ? e.keyCode : e.charCode;
    }
    else if (document.layers) {
        return e.which;
    }
}

function initShowGotoBCR()
{
    document.onkeydown=showGotoBCR;
}