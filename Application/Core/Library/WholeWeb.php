<?php

class Library_WholeWeb extends Zend_Controller_Action
{    
	
	public $modulesData;
	
	public $regEmail;
	public $regDomain;
	public $regName;
	public $regTel;
	
	
	public $smsLogin    = "";
	public $smsPassword = "";
	
	
	function init()
	{
	   $this->initModules();   
	   
	}

	protected function initModules(){
		$modules 	= new Models_Module();
		$allModules = $modules->getAllItems(null,null);	
		
		$this->modulesData = new stdClass();
		foreach ($allModules as $val){
			$objName = $val->title;
			$this->modulesData->$objName = $val->enabled;			
		}
		$this->view->modulesData = $this->modulesData;
		
		$this->regEmail = array(
			"cz" => "zikaservis@gmail.com"
		);
    /*
		$this->regEmail = array(
			"cz" => "meca.petr@gmail.com",
			"sk" => "meca.petr@gmail.com"
		); */
		
		$this->regDomain = array(
			"cz" => "autoserviszika.cz"
		);
		$this->regName = array(
			"cz" => "Autoservis Zika"
		);
		$this->regTel = array(
			"cz" => "773 593 920"
		);
	}
	
	protected function sendMail($from,$name,$to,$subject,$text,$emailID = -1){

    	    $atachement = new Newsletter_Models_Attachement();
    	    $files      = array();
    	    $send       = true;
    	    
			if($emailID != -1){
				for($i = 1; $i <= 6; $i++){
					if($this->uploadFile("Public/Attachment",'file'.$i)){
							
							//$mime = mime_content_type("./Public/Owngraphics/".$this->filename);
							$files[] = $this->filename;
							$data = array("emailID"=>$emailID,"file"=> $this->filename);
							$atachement->insertData($data);
					}else{
						
						if(filesize($_FILES['file'.$i]['tmp_name']) > 0){
							$send  = false;
							break;
						}
					}									
				}
			}
			if($send && count($to) > 0){
						
				  $text = $this->setEmailLayout($text);
					
					$mail = new Zend_Mail('utf-8');
					/*
					$config = array(
						'ssl' => 'tls',
						'port' => 587,
						'auth' => 'login',
						'username' => '',
						'password' => ''
					);
					$transport = new Zend_Mail_Transport_Smtp('mail.1vis.net', $config);
						
					$mail->setDefaultTransport($transport);  
                                           */ 
      		$mail->addHeader('MIME-Version', '1.0');
      		$mail->addHeader('Content-Transfer-Encoding', '8bit');
      		$mail->addHeader('X-Mailer:', 'PHP/'.phpversion());

					$mail->setReplyTo($from, 'Email');
					$mail->setFrom($from,$name);
					$mail->setSubject ($subject);
      				$mail->setBodyText(html_entity_decode(strip_tags($text), ENT_NOQUOTES, 'UTF-8'));
					$mail->setBodyHtml($text);
				
					$mail->addTo($to);
					
					foreach($files as $f){
						$mail->createAttachment(file_get_contents("./Public/Attachment/".$f),$this->mimetype,Zend_Mime::DISPOSITION_INLINE,Zend_Mime::ENCODING_BASE64,$f);	//priloha
					}
				
					$mail->send();
				
				return true;
			}
			if(!$send){
				
				$where = "emailID = '$emailID'";
					
				$userEmail = new User_Models_UserEmail();
				$email     = new Newsletter_Models_Email();
					
				$old = $atachement->getAllItems($where,null);
				foreach($old as $o){
					unlink("./Public/Attachment/".$o->file);
				}
				$atachement->deleteData($where);
				$userEmail->deleteData($where);
				$email->deleteData($where);
				
				return false;
			}
		
	}
	
	private function setEmailLayout($content){
	
		$translate = Zend_Registry::get("Zend_Translate");
		
		$layout = '
          <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		      <center>
		       <table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
		        <tr>
		         <td align="center" valign="top" style="border-collapse:collapse;color:#525252;background-color:#f5f5f5;">
		          <table border="0" style="border-collapse:collapse;background-color:#ffffff;" width="800">
		           <tr>
		            <td style="text-align: left; background-image: url(http://' . $this->actualDomain . '/Public/Images/Layout/Header/menu_bg.jpg);background-size:cover;height:75px;padding:20px 40px;"><img style="max-height:85px;vertical-align:middle;" alt="logo" style="display:block; margin-left:10px;" src="http://' . $this->actualDomain . '/Public/Images/Layout/menu_logo.jpg" /></td>
		           </tr>
		           <tr>
		            <td style="padding:0px 40px 40px 40px;">
		            	'.$content.'
		            </td>
		           </tr>
		          </table>
		         </td>
		        </tr>
		       </table> 
		      </center>
		     ';
	
		return $layout;
	}

	private function uploadFile($path,$formname){
		 
		$upload      	  = new Library_UploadGraphics();
		$upload->path 	  = $path;
		$upload->formName = $formname;
		$upload->upload();
		
		if(!empty($upload->fileName)){
			
			$this->mimetype  = $upload->mimeType;
			$this->filename  = $upload->fileName;
			$this->extension = $upload->extension;
	
			return true;
				 
		}else return false;
		
	
		 
	}
	/**
	* My function description.
	* 
	* @param string $content [html content]
	* @param string $fileName
	* @param string $stylesheet [path to css stylesheet]
	* @param string $openType ["store" - save to filename destination, "open" - directly open pdf if plug-in, "download" - directly download file]
	* @param boolean $debug
	*/
	public function pdfExport($content,$fileName,$stylesheet = null,$openType = "store",$debug = false){
		$openParam = "F";
		if($openType == "store")
			$openParam = "F";
		else if($openType == "open")
			$openParam = "I";
		else if($openType == "download")
			$openParam = "D";
			
		ini_set("memory_limit","128M");
		include("pdfExport/mpdf.php");
		$mpdf=new mPDF('UTF-8',"A4",0,"",5,5,5,5,5,5);
		$mpdf->debug = $debug;
		//$mpdf->use_kwt = true;
	
		$stylesheet = file_get_contents('./Application/Modules/Shop/Css/pdf_bill.css');
		//$mpdf->SetColumns(3,'',0);
		$mpdf->WriteHTML($stylesheet,1);
		 
		$mpdf->WriteHTML($content,2);
		$mpdf->Output($fileName,$openParam);
	}
  
	public function sendSms($text,$tel){
	
		include_once ("Smssluzbacz/apipost30.php");
			
		$login = $this->smsLogin;
		$pass  = $this->smsPassword;
	
		if(!empty($login) && !empty($pass)){
				
			$apipost = new ApiPost30($login, $pass);
			$apipost->set_recipient($tel);
			$apipost->set_text($text);
	
			$response = $apipost->send();
		}
	
	}
}

