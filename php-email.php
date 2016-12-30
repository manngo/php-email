<?php
/*	Email Class
	================================================

	Mark Simon
	Share & Enjoy

	Required:
		to
		from
		subject
		text

	Optional
		html		HTML Message

		cc
		bcc

		images		path of image file or array of paths
		attachment	path of attachment

		headers		array of additional email headers

	Image

		To include an image in your message:

		1	Add the image as above
		2	HTML: <img src="cid:[image]" alt="…">
			[image] is the basename of the image file
		3	Obviously, html is also required.

	Sample:

		$smtp='localhost';

		$text="…";
		$attachment='…';
		$image='…';
		$html=" … <img src=\"cid:$image\" alt=\"oops\"> …";

		$to='…';
		$from='…';
		$cc='…';
		$bcc='…';
		$subject='…';

		$headers='…';

		$email=new Email(array(
			'smtp'=>$smtp,
			'text'=>$text,
			'html'=>$html,
			'image'=>$image,
			'attachment'=>$attachment,
			'to'=>$to,
			'from'=>$from,
			'cc'=>$cc,
			'bcc'=>$bcc,
			'subject'=>$subject,
			'headers'=>$headers,
		));

		//	Alteratively:

			$email=newEmail();
			$email->setOptions(array(…)); //etc

	$email->send();
	================================================ */

	class Email {
#		private $smtp='10.0.0.15';
		private $smtp='localhost';
		private $data=array(
			'smtp'=>null,
			'text'=>null,
			'html'=>null,
			'css'=>null,
			'images'=>null,
			'attachments'=>null,
			'to'=>null,
			'from'=>null,
			'cc'=>null,
			'bcc'=>null,
			'subject'=>null,
			'headers'=>null,
		);

		function __construct($options=array()) {
			if($options) $this->setOptions($options);
		}

		function setOptions($options) {
			if(isset($options['smtp'])) $this->smtp=$options['smtp'];
			ini_set('SMTP',$this->smtp);

			if(array_key_exists('subject',$options))
				$this->data['subject']=$options['subject'] or $this->data['subject']='[No Subject]';

			if(array_key_exists('text',$options))
				$this->data['text']=$options['text'] or $this->data['text']=null;
			if(array_key_exists('html',$options))
				$this->data['html']=$options['html'] or $this->data['html']=null;
			if(array_key_exists('css',$options))
				$this->data['css']=$options['css'] or $this->data['css']=null;

			if(array_key_exists('to',$options))
				$this->data['to']=$this->filterEmail($options['to']) or $this->data['to']=null;
			if(array_key_exists('from',$options))
				$this->data['from']=$this->filterEmail($options['from']) or $this->data['from']=null;
			if(array_key_exists('cc',$options))
				$this->data['cc']=$this->filterEmail($options['cc']) or $this->data['cc']=null;
			if(array_key_exists('bcc',$options))
				$this->data['bcc']=$this->filterEmail($options['bcc']) or $this->data['bcc']=null;

			if(array_key_exists('image',$options)) $this->setImage($options['image']);
			if(array_key_exists('attachment',$options)) $this->setAttachment($options['attachment']);
		}

		function filterEmail($text) {
			return $text;
			return filter_var($text,FILTER_VALIDATE_EMAIL);
		}

		function filterHeader($text) {
			if(preg_match('/[\r\n]/',$text)) return hull;
		}

		function setAttachment($attachment) {
			if(!$attachment) {
				$this->data['attachment']=null;
				return;
			}
			if(is_array($file)) {
				foreach($file as $f) $this->addAttachment($f);
			}
			else $this->addAttachment($file);
		}

		function setImage($file) {
			if(!$file || !$this->data['html']) {
				$this->image=null;
				return;
			}
			if(is_array($file)) {
				foreach($file as $f) $this->addImage($f);
			}
			else $this->addImage($file);
		}

		function addAttachment($file) {
			if(!$this->data['attachments']) $this->data['attachments']=array();
			$this->data['attachments'][]=$this->addFile($file);
		}

		function addImage($file) {
			if(!$this->data['images']) $this->data['images']=array();
			$this->data['images'][]=$this->addFile($file);
		}

		function addFile($path) {
			//	Optional File Name
				list($path,$name)=array_slice(explode('|',"$path|"),0,2);
				if(!$name) $name=basename($path);

			$file=array();
			$file['file']=$file;
			$finfo = new finfo(FILEINFO_MIME_TYPE);
			$file['mime']=$finfo->file($path);
			$file['data']=file_get_contents($path);
			$file['data']=chunk_split(base64_encode($file['data']));
			$file['name']=$name;
			return $file;
		}

		function send() {
			$boundary=md5(time());
			$message=array();

			$mixed="--mixed--$boundary";
			$alternative="--alternative--$boundary";
			$related="--related--$boundary";

			$textHeader="Content-Type: text/plain; charset=\"utf-8\"\r\nContent-Transfer-Encoding: 7bit";
			$htmlHeader="Content-Type: text/html; charset=\"utf-8\"\r\nContent-Transfer-Encoding: 7bit";

			$mixedHeader="Content-Type: multipart/mixed; boundary=\"$mixed\"";
			$alternativeHeader="Content-Type: multipart/alternative; boundary=\"$alternative\"";
			$relatedHeader="Content-Type: multipart/related; boundary=\"$related\"";

			if($this->data['images']) {
				$imageHeader=array();
				foreach($this->data['images'] as $i) {
					$h=array();
					$h[]=sprintf('Content-Type: %s; name="%s"',$i['mime'],$i['name']);
					$h[]='Content-Transfer-Encoding: base64';
					$h[]=sprintf('Content-ID: <%s>',$i['name']);
#					$h[]=sprintf('Content-ID: <%s>',$name);
					$h[]=sprintf('Content-Disposition: inline; filename="%s"',$i['name']);
					$imageHeader[]=implode("\r\n",$h);
				}
			}
			if($this->data['attachments']) {
				$attachmentHeader=array();
				foreach($this->data['attachments'] as $a) {
					$h=array();
					$h[]=sprintf('Content-Type: %s; name="%s"',$a['mime'],$a['name']);
					$h[]='Content-Transfer-Encoding: base64';
					$h[]=sprintf('Content-Disposition: attachment; filename="%s"',$a['name']);
					$attachmentHeader[]=implode("\r\n",$h);
				}
			}

			if($this->data['attachments']) $header=$mixedHeader;
			elseif($this->data['html']) $header=$alternativeHeader;
			else $header='';

			//	Construct Message

				if($this->data['attachments']) {
					$message[]="--$mixed";
				}
				if($this->data['html']) {
					if($this->data['attachments']) {
						$message[]="$alternativeHeader";
						$message[]="";
					}
					$message[]="--$alternative";
					$message[]="$textHeader";
					$message[]="";
				}
				$message[]=$this->data['text'];
				if($this->data['html']) {
					$message[]="--$alternative";
				}
				if($this->data['images']) {
					$message[]="$relatedHeader";
					$message[]="";
					$message[]="--$related";
				}
				if($this->data['html']) {
					$message[]="$htmlHeader";
					$message[]="";
				}
				if($this->data['html']) {
					if($this->data['css']) {
						$message[]='<style type="text/css">';
						$message[]=$this->data['css'];
						$message[]='</style>';
					}
					$message[]=$this->data['html'];
				}

				if($this->data['images']) {
					foreach($this->data['images'] as $i=>$image) {
						$message[]="--$related";
						$message[]=$imageHeader[$i];
						$message[]="";
						$message[]=$image['data'];
					}
					$message[]="--$related--";
				}
				if($this->data['html']) $message[]="--$alternative--";
				if($this->data['attachments']) {
					foreach($this->data['attachments'] as $a=>$attachment) {
						$message[]="--$mixed";
						$message[]=$attachmentHeader[$a];
						$message[]="";
						$message[]=$attachment['data'];
					}
					$message[]="--$mixed--";
				}

				$message=implode("\n",$message);

			//	Construct Header
				$headers=array();
				//	Standard
					$headers[]="From: {$this->data['from']}";
					$headers[]="Reply-to: {$this->data['from']}";
				//	cc & bcc
					if($this->data['cc']) $headers[]="Cc: {$this->data['cc']}";
					if($this->data['bcc']) $headers[]="Bcc: {$this->data['bcc']}";
				//	Additional Optional
					if($this->data['headers'])
						foreach($this->data['headers'] as $h) $headers[]=$h;
				//	Message Header
					$headers[]=$header;
				$headers=implode("\r\n",$headers);

			//	Extra Parameters (currently hard coded)
				$parms="-f {$this->data['from']} -r {$this->data['from']}";
#print $headers;
#print "==== {$this->data['cc']}<br>\n";
			mail($this->data['to'],$this->data['subject'],$message,$headers,$parms);
#			mail($this->data['to'],$this->data['subject'],$message,$headers);
		}

		static function text2p($text) {
			return  '<p>'.preg_replace('/\n/','</p><p>',$text).'</p>';
		}

		static function getSubDocuments($text,$eol="\r\n") {
			$data=array();
			$document=array();
			$subdocument=null;
			$text=file($text);
			foreach($text as $line) {
				$line=rtrim($line);
				if(!$line) continue;
				switch($line[0]) {
					case '#':	break;
					case '+':	$subdocument=substr($line,1);
								break;
					case '-':	$subdocument=null;
								break;
					default:	if($subdocument) $data[$subdocument][]=substr($line,1);
				}
			}
			foreach($data as $subdocument=>$text) $document[$subdocument]=implode($eol,$text);
			return $document;
		}

	}
?>
