<?php
/**
 * This is bbcode class
 * 
 * @package bugfree.protected.compontents
 */
class BBCode
{
    /**
     * convert bbcode to html
     * 
     * @param string $data
     * @return string 
     */
    public static function bbcode2html($data)
    {
        $data = htmlspecialchars($data);
        $data = nl2br(stripslashes(addslashes($data)));

        $search = array("\n", "\r");
        $replace = array("", "");
        $data = str_replace($search, $replace, $data);
        
        $data = str_replace('<br />', "<br />\r\n", $data);

        $search = array(
            "/\[email\](.*?)\[\/email\]/si",
            "/\[email=(.*?)\](.*?)\[\/email\]/si",
            "/\[url\](.*?)\[\/url\]/si",
            "/\[url=(.*?)\]([^]]*?)\[\/url\]/si",
            "/\[img\](.*?)\[\/img\]/si",
            "/\[code\](.*?)\[\/code\]/si",
            "/\[pre\](.*?)\[\/pre\]/si",
            "/\[list\](.*?)\[\/list\]/si",
            "/\[\*\](.*?)/si",
            "/\[b\](.*?)\[\/b\]/si",
            "/\[i\](.*?)\[\/i\]/si",
            "/\[u\](.*?)\[\/u\]/si",
        );
        $replace = array(
            "<a href=\"mailto:\\1\">\\1</a>",
            "<a href=\"mailto:\\1\">\\2</a>",
            "<a href=\"\\1\" target=\"_blank\">\\1</a>",
            "<a href=\"\\1\" target=\"_blank\">\\2</a>",
            "<img src=\"\\1\" border=\"0\">",
            "<p><blockquote><font size=\"1\">code:</font><hr noshade size=\"1\"><pre>\\1</pre><br><hr noshade size=\"1\"></blockquote></p>",
            "<pre>\\1<br></pre>",
            "<ul>\\1</ul>",
            "<li>\\1</li>",
            "<strong>\\1</strong>",
            "<i>\\1</i>",
            "<u>\\1</u>",
        );
        $data = preg_replace($search, $replace, $data);

        $search = array(
            "/\[bug\](\d*?)\[\/bug\]/si",
            "/\[case\](\d*?)\[\/case\]/si",
            "/\[result\](\d*?)\[\/result\]/si",
        );
        $replace = array(
            "<a href=\"Bug.php?BugID=\\1\" target=\"_blank\">\\1</a>",
            "<a href=\"Case.php?CaseID=\\1\" target=\"_blank\">\\1</a>",
            "<a href=\"Result.php?ResultID=\\1\" target=\"_blank\">\\1</a>",
        );
        $data = preg_replace($search, $replace, $data);

        return $data;
    }
    
    
    public static function html2bbcode($data)
    {
        $search = array(
            "/<a href=\"Bug.php\?BugID=(.*?)\" target=\"_blank\">(.*?)<\/a>/si",
            "/<a href=\"Case.php\?CaseID=(.*?)\" target=\"_blank\">(.*?)<\/a>/si",
            "/<a href=\"Result.php\?ResultID=(.*?)\" target=\"_blank\">(.*?)<\/a>/si",
        );
        $replace = array(
            "[bug]\\1[/bug]",
            "[case]\\1[/case]",
            "[result]\\1[/result]",
        );
        $data = preg_replace($search, $replace, $data);
        
        $search = array(
            "/<a href=\"mailto:(.*?)\">(.*?)<\/a>/si",
            "/<a href=\"mailto:(.*?)\">(.*?)<\/a>/si",
            "/<a href=\"(.*?)\" target=\"_blank\">(.*?)<\/a>/si",
            "/<a href=\"(.*?)\" target=\"_blank\">(.*?)<\/a>/si",
            "/<img src=\"(.*?)\" border=\"0\">/si",
            "/<p><blockquote><font size=\"1\">code:<\/font><hr noshade size=\"1\"><pre>(.*?)<\/pre><br><hr noshade size=\"1\"><\/blockquote><\/p>/si",
            "/<pre>(.*?)<br><\/pre>/si",
            "/<ul>(.*?)<\/ul>/si",
            "/<li>(.*?)<\/li>/si",
            "/<strong>(.*?)<\/strong>/si",
            "/<i>(.*?)<\/i>/si",
            "/<u>(.*?)<\/u>/si",
        );
        $replace = array(
            "[email]\\1[/email]",
            "[email=\\1]\\2[/email]",
            "[url]\\1[/url]",
            "[url=\\1]\\2[/url]",
            "[img]\\1[/img]",
            "[code]\\1[/code]",
            "[pre]\\1[/pre]",
            "[list]\\1[/list]",
            "[*]\\1",
            "[b]\\1[/b]",
            "[i]\\1[/i]",
            "[u]\\1[/u]",
        );
        $data = preg_replace($search, $replace, $data);
        $data = str_replace("<br />\r\n", '<br />', $data);
        
        $search = array("<br />", "<br/>");
        $replace = array("\n", "\n");
        $data = str_replace($search, $replace, $data);
        $data = htmlspecialchars_decode($data);
        
        return $data;
    }
}
?>