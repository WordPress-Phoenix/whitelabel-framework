<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<script src="/wp-includes/js/jquery/jquery.js?ver=1.4.4" type="text/javascript"></script>
<script src="../js/jquery.validationEngine-en.js" type="text/javascript"></script>
<script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
<link rel="stylesheet" href="../appearance/validationEngine.jquery.css" type="text/css" media="screen" charset="utf-8" />
<script type="text/javascript"> 
	jQuery(document).ready(function() { jQuery("#webForm").validationEngine({promptPosition: "topRight"}) })
</script>

<title></title>
<style type="text/css">
body {margin:0;padding:0; }
#content { text-align:left; font-size:14px; font-weight:bold; font-family:Arial, Helvetica, sans-serif;}
input { width:200px; }
textarea { width:300px; }
label { display: block; font-weight:normal;	margin-top:18px; }
#button { margin-top:18px; width:auto; }
#copy { width:auto; }
#confirmation, #tempcontent { display:none;}
<?php if(isset($_GET['sent'])) { ?>
#confirmation { display:block; font-weight:bold;}
#webForm { display:none;}
<?php } ?>
</style>
<script>
// Get the page content and store it in hidden form field
jQuery(document).ready(function() {
	jQuery.get("<?php echo $_GET['thelink']; ?>", function(data){
	jQuery('#tempcontent').html(jQuery('#theContent',data).html());
	jQuery('#tempcontent h1 a').attr("href", '<?php echo $_GET['thelink']; ?>');
	jQuery('#tempcontent img').attr("align", 'right');
	jQuery('#tempcontent .hr').html('&nbsp;');
	jQuery("#pageContent").val(jQuery('#tempcontent').html());
	});
});
</script>

</head>
<body>
<div id="content">
<?php 			
		$mailer = false;
			if(isset($_POST['sendername'])) {					
					/* recipients */
					$recipient = $_POST['recipemail'];
					if (stripos($recipient, ',')) 
						$recipient = substr($recipient, 0, stripos($recipient, ',')); 
					
					/* subject */
					$subject = 'Information About Anthem Education Group ';
					
					/* headers */
					$headers = 'From: ' . $_POST['sendername'] .' <'.$_POST['senderemail'].'>' . PHP_EOL;
					//$headers = 'From: ' . $_POST['senderemail'] . PHP_EOL;
					$headers .= 'MIME-Version: 1.0' . PHP_EOL;
					$headers .= 'Content-type: text/html; charset=iso-8859-1' . PHP_EOL;
					//add cc if requested
					if ($_POST['copy'] =='yes') {
					$headers .= 'Cc: '.$_POST['senderemail']. PHP_EOL;	
					}
					$message = '<style type="text/css">'. PHP_EOL;
					$message .= '#content { font-size:12px; font-family:Arial, Helvetica, sans-serif;}'. PHP_EOL;
					$message .= 'h1 { color:#4E6389; font-size:16px; padding:0; border-bottom:0; line-height:normal; margin:0; }'. PHP_EOL;
					$message .= 'h1 a { color:#4E6389; font-size:16px; padding:0; border-bottom:0; line-height:normal; margin:0; text-decoration:none; }'. PHP_EOL;
					$message .= 'a { color:#4E6389; }'. PHP_EOL;
					$message .= 'h2 { color:#4E6389; font-size:13px; line-height:100%; margin:0px; margin-bottom:5px; }'. PHP_EOL;
					$message .= '.hr { margin:0; padding:0;	margin-bottom:18px;	height:1px;	border-bottom:1px solid #ccc; }'. PHP_EOL;
					$message .= 'img { float:right; }'. PHP_EOL;
					$message .= '</style>'. PHP_EOL;
					$message .= '<div id="content">'. PHP_EOL;
					$message .= 'Dear '.$_POST['recipname']. '<br>'. PHP_EOL;
					$message .= $_POST['sendername'].' (' .$_POST['senderemail']. ') saw this on the Anthem Education Group website and thought you might be interested<br><br>'. PHP_EOL;	
									
					if($_POST['message'] != '')
						$message .= '<strong>'. $_POST['sendername'].' says:</strong><br>'. $_POST['message']. '<br><br>'. PHP_EOL;	
					$message .= '<br>' .$_POST['pageContent'].PHP_EOL;
					$message .= '<a href="'.$_POST['link'].'">Read about it on the Anthem Education Group website</a><br><br>'. PHP_EOL;
					$message .= '</div>'. PHP_EOL;				
					
					$mailer= @mail($recipient, $subject, $message, $headers);
					if ($mailer) {
?>
<div id="confirmation">Your Message has been successfuly sent to <?php echo $recipient ?> </div>
					<?php } else { ?>
                    <div id="error">Your Message has not been sent, please <a href="<?php echo basename($_SERVER['PHP_SELF']) ?>">try again</a> or contact the administrator. </div>
                    <?php } ?>
<?php } else { ?>
    <form id="webForm" name="webForm" class="form" method="post" action="<?php echo basename($_SERVER['PHP_SELF']) ?>?sent=true">
      <label for="sender-name">Your Name *</label>
      <input type="text" name="sendername" id="sendername" class="validate[required]" />
      <label for="sender-email">Your Email *</label>
      <input type="text" name="senderemail" id="senderemail" class="validate[required,custom[email]] text-input" />
      <label for="recip-name">Recipient Name *</label>
      <input type="text" name="recipname" id="recipname" class="validate[required]" />
      <label for="recip-email">Recipient Email *</label>
      <input type="text" name="recipemail" id="recipemail" class="validate[required,custom[email]] text-input" />
      <label for="message">Message</label>
      <textarea name="message" id="message" cols="45" rows="5"></textarea>
      <input type="hidden" name="link" id="link" value="<?php echo $_GET['thelink']; ?>" />
      <input type="hidden" name="pageContent" id="pageContent" value="" size="45" />
	  <label for="copy"><input name="copy" type="checkbox" id="copy" value="yes" />Send me a copy of this email</label>
      <input type="submit" name="button" id="button" value="Send It" />
      <div id="tempcontent"></div>
    </form>
 <?php } ?>   
</div>
</body>
</html>
