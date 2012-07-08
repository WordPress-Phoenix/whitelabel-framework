<?php 
$url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$url = preg_replace('/\?.*/', '', $url);
?>
<div id="smShare"><g:plusone size="tall" href="<?php echo $url; ?>"></g:plusone>
<!-- Place this render call where appropriate -->
<script type="text/javascript">
  (function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	po.src = 'https://apis.google.com/js/plusone.js'; 
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
  })();
</script>
</div>