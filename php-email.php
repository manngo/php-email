<?php
/*	Email Class
	================================================

	Mark Simon
	Share & Enjoy

	================================================ */

	function printr($data) {
		print sprintf("<pre>%s</pre>",print_r($data,true));
	}

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
			'binary'=>null,
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

			if(array_key_exists('images',$options)) $this->setImage($options['images']);
			if(array_key_exists('attachments',$options)) $this->setAttachment($options['attachments']);
			if(array_key_exists('binary',$options)) $this->setBinaryAttachment($options['binary']);

		}

		function filterEmail($text) {
			return $text;
			return filter_var($text,FILTER_VALIDATE_EMAIL);
		}

		function filterHeader($text) {
			if(preg_match('/[\r\n]/',$text)) return hull;
		}

		function setAttachment($file) {
			if(!$file) {
				$this->data['attachment']=null;
				return;
			}
			if(is_array($file)) {
				foreach($file as $f) $this->addAttachment($f);
			}
			else $this->addAttachment($file);
		}

		function setBinaryAttachment($binary) {
			if(!$binary) {
				$this->data['attachment']=null;
				return;
			}
			if(!$this->data['attachments']) $this->data['attachments']=array();


#			if(is_array($binary)) {
#				foreach($binary as $b) $this->data['attachments'][]=$b;
#			}
#			else $this->data['attachments'][]=$binary;

			$this->data['attachments'][]=$binary;		//	single attachment only
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
			$this->data['attachments'][]=$this->addFile($file);		//	single attachment only
		}

		function addImage($file) {
			if(!$this->data['images']) $this->data['images']=array();
			$this->data['images'][]=$this->addFile($file);
		}

		function addFile($path) {
			//	Optional File Name
				list($path,$name)=array_slice(explode('|',"$path|"),0,2);
				if(!$name) $name=basename($path);
print "$path,$name";
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
			//	Construct Header
				$headers=array();
				//	Standard
					$headers[]="From: {$this->data['from']}";
					$headers[]="Reply-to: {$this->data['from']}";
				//	cc & bcc
					if($this->data['cc']) $headers[]="Cc: {$this->data['cc']}";
					if($this->data['bcc']) $headers[]="Bcc: {$this->data['bcc']}";
				//	Additional Optional
					if($this->data['headers']) foreach($this->data['headers'] as $header) $headers[]=$header;

			//	Boundaries & Headers
				$boundary=md5(time());

				$mixed = $this->data['attachments'] ? "--mixed--$boundary" : '';
				$alternative = $this->data['html'] ? "--alternative--$boundary" : '';
				$related = $this->data['images'] ? "--related--$boundary" : '';

			//	Headers
				if($mixed || $alternative)	$headers[]='MIME-Version: 1.0';
				if($mixed) $headers[]="Content-Type: multipart/mixed; boundary=\"$mixed\"";
				elseif($alternative) $headers[]="Content-Type: multipart/alternative; boundary=\"$alternative\"";

			//	Message
				$message=array();

			//	This is a multi-part message in MIME format.
				if($mixed || $alternative) $message[]='This is a multi-part message in MIME format.';
				
			//	Attachments (see later)
				if($mixed) $message[]="--$mixed";

			//	HTML or Attachment
				if($mixed && $alternative) $message[]="Content-Type: multipart/alternative; boundary=\"$alternative\"\r\n";
				if($alternative) $message[]="--$alternative";
				if($mixed || $alternative) $message[]="Content-Type: text/plain; charset=utf-8; format=flowed\r\nContent-Transfer-Encoding: 7bit\r\n";
				
			//	Text
				$message[]=$this->data['text'];

			//	HTML & Images
				if($alternative) $message[]="\r\n--$alternative";
				if($alternative && $related) $message[]="Content-Type: multipart/related; boundary=\"$related\"\r\n";
				if($alternative && $related) $message[]="--$related";
				if($alternative) $message[]="Content-Type: text/html; charset=\"utf-8\"\r\nContent-Transfer-Encoding: 7bit\r\n";
				if($alternative) $message[]=$this->data['html'];

				if($alternative && $related) foreach($this->data['images'] as $image) {
					$message[]="--$related";
					$message[]="Content-Type: {$image['mime']}; name=\"{$image['name']}\"";
					$message[]="Content-Transfer-Encoding: base64";
					$message[]="Content-ID: <{$image['name']}>";
					$message[]="Content-Disposition: inline; filename=\"{$image['name']}\"";
					$message[]='';
					$message[]=$image['data'];
				}

				if($alternative && $related) $message[]="--$related";
				if($alternative) $message[]="\r\n--$alternative--\r\n";
				
			//	Attachments
				if($mixed) {
					foreach($this->data['attachments'] as $attachment) {
						$message[]="--$mixed";
						$message[]="Content-Type: {$attachment['mime']}; name=\"{$attachment['name']}\"";
						$message[]="Content-Transfer-Encoding: base64";
						$message[]="Content-Disposition: attachment; filename=\"{$attachment['name']}\"";
						$message[]='';
						$message[]=$attachment['data'];
					}
					$message[]="--$mixed--";
				}

			//	Implode
				$headers=implode("\r\n",$headers);
				$message=implode("\r\n",$message);

			//	Extra Parameters (currently hard coded)
				$parms="-f {$this->data['from']} -r {$this->data['from']}";

			mail($this->data['to'],$this->data['subject'],$message,$headers,$parms);
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
