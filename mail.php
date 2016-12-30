<?php
	require_once 'php-email.php';
	$email=new Email();
	$template=$email->getSubDocuments("mailtemplate.php");

	$to='mark@manngo.net';
	$from='mark@comparity.net';
	$subject='Test';

	$text='This is a text';
$message='This is a test';

	$html='<p>This is a text<br>Groovey</p><img src="cid:white-girl.png" alt="White Girl">';
$html=<<<HTML
<div id="logo">
	<img src="cid:image.png" alt="White Girl">
</div>
<h1 id="subject">Subject: $subject</h1>
<h2>Message</h2>
<div id="message">$message</div>
HTML;
	
	$email->setOptions(array(
		'text'=>$text,
		'html'=>$html,
		'image'=>'white-girl.png|image.png',
#		'attachment'=>$template['attachment'],
		'to'=>$to,
		'from'=>$from,
		'cc'=>$from,
		'subject'=>$subject,
#		'headers'=>$headers,
		'css'=>$template['css'],
	));

	$email->send();

#print_r($template);

	
	
#	print mail($to,$subject,$message,$headers);
?>