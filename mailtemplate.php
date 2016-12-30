#<?php die('You shouldn’t be reading this …'); ?>
#	$variable='value';
#	$template=' ... $variable ...';
#	$result=eval("return<<< END\n$template\nEND;\n");

#	===================================================================
#	HTML Header
#	===================================================================
+mime
	MIME-Version: 1.0
	Content-Type: multipart/alternative; boundary="alternative--$boundary"
-mime

#	===================================================================
#	HTML Body
#	===================================================================
+body
	This is a multi-part message in MIME format.

	--alternative--$boundary
	Content-Type: text/plain; charset="utf-8"
	Content-Transfer-Encoding: 7bit

	Text Message:

	$textmessage

	--alternative--$boundary
	Content-Type: text/html; charset="utf-8"
	Content-Transfer-Encoding: 7bit

	$htmlmessage

	--alternative--$boundary--
-body

#	===================================================================
#	Header
#	===================================================================
+header
	Date: $date
	From: $from
	Reply-To: $from
	$cc$bcc$messageID
	X-Mailer: PHP Form Mailer
-header

#	===================================================================
#	Attachment
#	===================================================================
+attachment
	--mixed--$boundary
	$message
	--mixed--$boundary
	Content-Type: $fileType; name="$fileName"
	Content-Transfer-Encoding: base64
	Content-Disposition: attachment

	$attachment
	--mixed--$boundary--
-attachment

#	===================================================================
#	Image
#	===================================================================
+image
	--related-$boundary
	Content-Type: image/png
	Content-Transfer-Encoding: base64
	Content-ID: <$image-$boundary>

	$image
	--related-$boundary--
-image

#	===================================================================
#	Log
#	===================================================================
+log
	Date: 	$date
	From: 	$from
	To:   	$to
	CC:   	$cc
	BCC:  	$bcc
	Subject:	$subject
	Message:
	$message
	Misc:
	$misc
-log

#	===================================================================
#	HTML Template
#	===================================================================
+html
	<!DOCTYPE html>
	<html>
	<head>
		<title>PHP Form Mail Message</title>
		<meta charset="utf-8">
	</head>
	<body>
		<style type="text/css">
			body {
				font-family: sans-serif;
				font-size: 100%;
				color: #666;
			}
			h1, h2 {
				margin: 8px 0px;
				color: #333333;
			}
			h1 {
				font-size: 1.6em;
			}
			h2 {
				font-size: 1.2em;
			}
			p {
				margin: .5em 0;
				line-height: 1.4em;
			}
			div {
				margin: 1em 0p;
			}
			h1#subject {
				border: medium #666;
				border-style: solid none;
				color: #30338F;
				padding: .5em;
			}

			div#logo span {
				font-size: 3em;
				font-family: "arial", sans-serif;
				letter-spacing: 1px;
				display: block;
			}
			div#logo span span {
				font-size: 1em;
				display: inline;
				color: #30338F;
				font-weight: bold;
				border: medium #30338F;
				border-style: solid none solid solid;
				line-height: 0;
				padding: 0 0 0 .0625em;
			}


			div#logo p {
				font-size: .65em;
				letter-spacing: 1.5px;
				font-family: "arial narrow", sans-serif;
				margin: 3px 0 0 0;
			}
			div#message>table {
				border-collapse: collapse;
			}
			div#message table th,
			div#message table td {
				text-align: left;
				vertical-align: top;
				border: thin #666;
				border-style: solid none;
				padding: .5em;
			}
		</style>
		<div id="logo">
			<img src="cid:logo" alt="White Girl">
			<span><span>Folder</span>Corp</span>
			<p>creative presentation solutions<sup>®</sup></p>
		</div>
		<h1 id="subject">Subject: $subject</h1>
		<h2>Message</h2>
		<div id="message">$message</div>
	</body>
	</html>
-html
#	===================================================================
#	CSS
#	===================================================================
+css
			body {
				font-family: sans-serif;
				font-size: 100%;
				color: #666;
			}
			h1, h2 {
				margin: 8px 0px;
				color: #333333;
			}
			h1 {
				font-size: 1.6em;
			}
			h2 {
				font-size: 1.2em;
			}
			p {
				margin: .5em 0;
				line-height: 1.4em;
			}
			div {
				margin: 1em 0p;
			}
			h1#subject {
				border: medium #666;
				border-style: solid none;
				color: #30338F;
				padding: .5em;
			}

			div#logo span {
				font-size: 3em;
				font-family: "arial", sans-serif;
				letter-spacing: 1px;
				display: block;
			}
			div#logo span span {
				font-size: 1em;
				display: inline;
				color: #30338F;
				font-weight: bold;
				border: medium #30338F;
				border-style: solid none solid solid;
				line-height: 0;
				padding: 0 0 0 .0625em;
			}


			div#logo p {
				font-size: .65em;
				letter-spacing: 1.5px;
				font-family: "arial narrow", sans-serif;
				margin: 3px 0 0 0;
			}
			div#message>table {
				border-collapse: collapse;
			}
			div#message table th,
			div#message table td {
				text-align: left;
				vertical-align: top;
				border: thin #666;
				border-style: solid none;
				padding: .5em;
			}
-css
