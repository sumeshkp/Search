<?php
/**
 * Byblio new user account functions
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Useraccount\Model;
require 'vendor/Hamel/library/Hamel/Password/password.php';
use Hamel\Usermanagement\Upload as UsermanagementUpload;
use Hamel\Usermanagement\Download as UsermanagementDownload;
use Hamel\Email\SendEmail;
use Hamel\Db\General as DbGeneral;

class NewAccount {
	
	private $viewHelperMgr;
	
    public function __construct($viewHelperMgr){
    	
    	// ref view helper manager
    	$this->viewHelperMgr = $viewHelperMgr;
    }
    
    // creates a new user account
    function createNewAccount($inputList, $inputCleaned){
        
        // defaults
        $email = "";
        $firstName = "";
        $lastName = "";
        $password = "";
        $accountType = 1; // free account
        $active = '0'; // inactive
        $userId = null;
        
        
       
        // get values
        if(key_exists('email', $inputList)){
            $email = trim(mb_strtolower($inputList['email'])); // NOTE: lowercase email stored
        }
        if(key_exists('firstName', $inputList)){
            $firstName = trim($inputList['firstName']);
        }
        if(key_exists('lastName', $inputList)){
            $lastName = trim($inputList['lastName']);
        }
        
        if(key_exists('active', $inputList)){
            $active = trim($inputList['active']);
        }
        if(key_exists('accountType', $inputList)){
            $accountType = trim($inputList['accountType']);
        }
        if(key_exists('password', $inputList)){
            $password = trim($inputList['password']);
        }
        
        
       	// min required values
        if($email !="" && $firstName !="" && $lastName !="" && $password !=""){
            
 			// encrypt  password
        	$encPwd = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));

            if(password_verify($password, $encPwd)){ // if hashed password sucessfully
            	
	 			// create new activation key
            	$mgtUpload = new UsermanagementUpload();
	            $activationKey = $mgtUpload->createNewActivationKey();	       
	           
	            // create unique username
	            $mgtDownload = new UsermanagementDownload();
	          	$username = $mgtDownload->generateUniqueUsername($firstName);
	          	
	          	// scramble the username
	          	$dbGeneral = new DbGeneral();
	          	$usernameScramble = $dbGeneral->scrambleString($username);
	          	
	          	// create user file id
	          	$userFileId = $mgtUpload->createUserFileID($username);
	          	
	            // record info
	            $infoList = array();
	            $infoList['password'] = $encPwd;
	            $infoList['accountType'] = $accountType;
	            $infoList['status'] = $active;
	            $infoList['activationKey'] = $activationKey;
	            $infoList['firstName'] = $firstName;
	            $infoList['lastName'] = $lastName;
	            $infoList['email'] = $email;
	            $infoList['username'] = $username;
	            $infoList['usernameScramble'] = $usernameScramble;
	            $infoList['userFileId'] = $userFileId;
	            
	            // add new user
	            $userId = $mgtUpload->createNewUser($infoList, $inputCleaned);
            }
        }
        
        // return new id and password
        $returnInfo = array('accountType'=>$accountType, 'userId'=>$userId, 'password'=>$password, 'activationKey'=>$activationKey, 'email'=>$email, 'usernameScramble'=>$usernameScramble, 'username'=>$username, 'firstName'=>$firstName, 'lastName'=>$lastName);
        
        return $returnInfo;
    }
    
    
    
    // sends another actviation email
    public function resendActivationEmail($inputList){
    	
    	// translate
    	$translate = $this->viewHelperMgr->get('translate');
    	
    	// get values
    	if(key_exists('email', $inputList)){
    		$email = $inputList['email'];
    	}
    	if(key_exists('firstName', $inputList)){
    		$firstName = $inputList['firstName'];
    	}
    	if(key_exists('lastName', $inputList)){
    		$lastName = $inputList['lastName'];
    	}
    	if(key_exists('username', $inputList)){
    		$username = $inputList['username'];
    	}
    	
    	// create new activation key
    	$mgtUpload = new UsermanagementUpload();
    	$activationKey = $mgtUpload->createNewActivationKey();
    	
    	// scramble the username
    	$dbGeneral = new DbGeneral();
    	$usernameScramble = $dbGeneral->scrambleString($username);
  

    	// min required values
    	if($email !="" && $firstName !="" && $lastName !="" && $username !="" && $activationKey !=""){
    	
    		// delete any existing registration details for this user
    		$inputList = array();
    		$inputList['username'] = $username;
    		$mgtUpload->deleteAllRegistrationToComplete($inputList, true);
    		 
    		// record registration details
    		$inputList = array();
    		$inputList['activationKey'] = $activationKey;
    		$inputList['usernameScramble'] = $usernameScramble;
    		$inputList['username'] = $username;
    		$mgtUpload->addNewRegistrationToComplete($inputList, true);
    		
    		
    		// get css
    		$cssType = 'resendActivationEmail';
    		$css = $this->_getEmailCSS($cssType);
    	
    		// create message
    		$messageInfo = array();
    		$messageInfo['css'] = $css;
    		$messageInfo['firstName'] = $firstName;
    		$messageInfo['lastName'] = $lastName;
    		$messageInfo['usernameScramble'] = $usernameScramble;
    		$messageInfo['activationKey'] = $activationKey;
    		$messageInfo['email'] = $email;
    		$messageInfo['messageType'] = 'resendActivationEmail';
    		$message = $this->_createMessage($messageInfo);
    	
    		// title
    		$title = $translate('Account activation');
    	
    		// send info
    		$sendInfo = array('from'=>'registration', 'type'=>'userNewAccountActivationEmail');
    		// to info
    		$fullName = $firstName ." " .$lastName;
    		$toInfo = array('name'=>$fullName, 'email'=>$email);
    	
    		// send message
    		$emailInfo = array();
    		$emailInfo['css'] = $css;
    		$emailInfo['sendInfo'] = $sendInfo;
    		$emailInfo['toInfo'] = $toInfo;
    		$emailInfo['message'] = $message;
    		$emailInfo['title'] = $title;
    	
    	
    		$sendEmail = new SendEmail($this->viewHelperMgr);
    		$emailSent = $sendEmail->sendEmail($emailInfo);
    	
    	}
    	 
    	return $emailSent;
 
    }
    
    
    
    // sends initial activation email
    public function sendActivationEmail($inputList){
    	
    	// translate
    	$translate = $this->viewHelperMgr->get('translate');
    	
    	// defaults
    	$emailSent = false;
    	$email = "";
    	$firstName = "";
    	$lastName = "";
    	$username = "";
    	$activationKey = "";
    	$password = "";
    	
    	// get values
    	if(key_exists('email', $inputList)){
    		$email = $inputList['email'];
    	}
    	if(key_exists('firstName', $inputList)){
    		$firstName = $inputList['firstName'];
    	}
    	if(key_exists('lastName', $inputList)){
    		$lastName = $inputList['lastName'];
    	}
    	
    	if(key_exists('activationKey', $inputList)){
    		$activationKey = $inputList['activationKey'];
    	}
    	if(key_exists('username', $inputList)){
    		$username = $inputList['username'];
    	}
    	if(key_exists('usernameScramble', $inputList)){
    		$usernameScramble = $inputList['usernameScramble'];
    	}
    	if(key_exists('password', $inputList)){
    		$password = $inputList['password'];
    	}
    	
    	// min required values
    	if($email !="" && $firstName !="" && $lastName !="" && $username !="" && $activationKey !=""){
    		
    		// get css
    		$cssType = 'newAccountActivationEmail';
    		$css = $this->_getEmailCSS($cssType);
    		
    		// create message
    		$messageInfo = array();
    		$messageInfo['css'] = $css;
    		$messageInfo['firstName'] = $firstName;
    		$messageInfo['lastName'] = $lastName;
    		$messageInfo['usernameScramble'] = $usernameScramble;
    		$messageInfo['activationKey'] = $activationKey;
    		$messageInfo['email'] = $email;
    		$messageInfo['password'] = $password;
    		$messageInfo['messageType'] = 'newAccountActivationEmail';
    		$message = $this->_createMessage($messageInfo);
    		
    		// title
    		$title = $translate('Account activation');
    		
    		// send info
    		$sendInfo = array('from'=>'registration', 'type'=>'userSelfRegister');
    		// to info
    		$fullName = $firstName ." " .$lastName;
    		$toInfo = array('name'=>$fullName, 'email'=>$email);
    		
    		// send message
    		$emailInfo = array();
    		$emailInfo['css'] = $css;
    		$emailInfo['sendInfo'] = $sendInfo;
    		$emailInfo['toInfo'] = $toInfo;
    		$emailInfo['message'] = $message;
    		$emailInfo['title'] = $title;
    				
    		
    		$sendEmail = new SendEmail($this->viewHelperMgr);
    		$emailSent = $sendEmail->sendEmail($emailInfo);
    		
    	}
    	
    	return $emailSent;
    }

	
    // returns css used in email
    private function _getEmailCSS($cssType){
    	
    	// default
    	$css = "";
    	
    	// get css array
    	$filePath = __DIR__ .'/Email.css.php';
    	if(file_exists($filePath)){
    		// read array
    		$allCSS = include($filePath);
    		
    		if(key_exists($cssType, $allCSS)){
    			$css = $allCSS[$cssType];
    		}
    	}
    	
    	// return
    	return $css;
    	
    }
    
    
    // creates message body (HTML)
    private function _createMessage($inputList){
    	
    	// translate
    	$translate = $this->viewHelperMgr->get('translate');
    	
    	// message type
    	$messageType = $inputList['messageType'];
    	
    	switch($messageType){
    		case 'newAccountActivationEmail':
    			
    			$usernameScramble = $inputList['usernameScramble'];
		    	$activationKey = $inputList['activationKey'];
		    	$lastName = $inputList['lastName'];
		    	$firstName = $inputList['firstName'];
		    	$email = $inputList['email'];
		    	$password = $inputList['password'];
		    	$css = $inputList['css'];
		    	
		    	// body
		    	$hlink = "http://www.byblio.com/useraccount/activate/" .$usernameScramble  ."/" .$firstName ."/" .$lastName ."/" .$activationKey;
		    	$iconStr = "<img class=\"iconInTextPos\" src=\"" .EMAIL_PUBLIC_PATH ."/images/email/clickhere16.png\">";
		    	$bgImgStr = "<img class=\"imageRightPos\" src=\"" .EMAIL_PUBLIC_PATH ."/images/email/email_nai.jpg\">";
		    	$logoStr = "<img src=\"" .EMAIL_PUBLIC_PATH ."/images/logo/byblio_logo_blue.png\">";
		    	
		    	// header
		    	$emailHeaderStr = "<head>" .$css."</head>";
		    	
		    	// email html, including css
		    	$emailHTMLbody = "<body>"
		    			."<div class=\"heading\">" .$translate('Welcome') ."</div>"
		    			."<div class=\"clearFloats\"></div>"
		    			."<div class=\"mainBody\">"
		    				."<div class=\"logo\">" .$logoStr ."</div>"
		    				."<div class=\"clearFloats\"></div>"
		    				."<div class=\"message\">"
		    					."<div>"
		    					."<p>" .$translate('Dear') ." " .$firstName .",</p>"
		    					."<p>" .$translate('Welcome to byblio.com!') ."</p>"
		    					."<div>"
			    			   		."<p>" .$translate('Please follow the link below - it will take you to the activation page, which is a key step to ensure security of your details.') ."</p>"
			    					."<div class=\"link\">" .$translate('Click here') ." " .$iconStr ." <a href=\"$hlink\">Byblio.com/account/activate</a></div>"
		    					."</div>"
		    					."<div>"
			    					."<p>" .$translate('Your registered email address is') ." <span class=\"highlight\">" .$email ."</span> " .$translate('and your password is') ." <span class=\"highlight\">" .$password ."</span>.</p>"
			    					."<p>" .$translate('If you have any questions, you can reply to this email (it\'s a real one, with a real person at the other end).') ."</p>"
		    					."</div>"
		    					."<div>$bgImgStr</div>"
		    							
								."<div class=\"clearFloats\"></div>"
								."<div class=\"footer\">
										<div class=\"footerHeader\">" .$translate('Who is Byblio? - Why have you sent me this email?') ."</div>"
		    							."<p>" .$translate('We have sent you this email because we think that you have registered to set up an account at byblio.com (you should check it out - it\'s an awesome tool to build on-line libraries!). If so, you\'ll need to follow the link to activate your account with this password within the next 48 hours - it is a key step to ensure security of your details. If you do not wish to have an account with us, please accept our apologies, and you don\'t need to do anything: we will automatically remove this account after 48 hours.') ."</p>"
		    					."</div>"
		    				."</div>"
		    			."</div>"
		    		."</body>";
    	
    	
    			$emailMessageStr = "<html>" .$emailHeaderStr .$emailHTMLbody ."</html>";
    	
    		break;

    		
    		case 'resendActivationEmail':
	 
    			$usernameScramble = $inputList['usernameScramble'];
		    	$activationKey = $inputList['activationKey'];
		    	$lastName = $inputList['lastName'];
		    	$firstName = $inputList['firstName'];
		    	$email = $inputList['email'];
		    	$css = $inputList['css'];
		    	
		    	// body
		    	$hlink = "http://www.byblio.com/useraccount/activate/" .$usernameScramble  ."/" .$firstName ."/" .$lastName ."/" .$activationKey;
		    	$iconStr = "<img class=\"iconInTextPos\" src=\"" .EMAIL_PUBLIC_PATH ."/images/email/clickhere16.png\">";
		    	$bgImgStr = "<img class=\"imageRightPos\" src=\"" .EMAIL_PUBLIC_PATH ."/images/email/email_nai.jpg\">";
		    	$logoStr = "<img src=\"" .EMAIL_PUBLIC_PATH ."/images/logo/byblio_logo_orange.png\">";
		    	
		    	// header
		    	$emailHeaderStr = "<head>" .$css."</head>";
		    	
		    	// email html, including css
		    	$emailHTMLbody = "<body>"
		    			."<div class=\"heading\">" .$translate('Welcome again!') ."</div>"
		    			."<div class=\"clearFloats\"></div>"
		    			."<div class=\"mainBody\">"
		    				."<div class=\"logo\">" .$logoStr ."</div>"
		    				."<div class=\"clearFloats\"></div>"
		    				."<div class=\"message\">"
		    					."<div>"
		    					."<p>" .$translate('Dear') ." " .$firstName .",</p>"
		    					."<p>" .$translate('Here is your new activation email!') ."</p>"
		    					."<div>"
			    			   		."<p>" .$translate('Please follow the link below - it will take you to the activation page, which is a key step to ensure security of your details.') ."</p>"
			    					."<div class=\"link\">" .$translate('Click here') ." " .$iconStr ." <a href=\"$hlink\">Byblio.com/account/activate</a></div>"
		    					."</div>"
		    					."<div>"
			    					."<p>" .$translate('Your registered email address is') ." <span class=\"highlight\">" .$email ."</span>. " .$translate('Your password is unchanged. Don\'t worry if you cannot remember what it is - when you follow this link you will be able to change it.') ."</p>"
			    					."<p>" .$translate('If you have any questions, you can reply to this email (it\'s a real one, with a real person at the other end).') ."</p>"
		    					."</div>"
		    					."<div>$bgImgStr</div>"
		    							
								."<div class=\"clearFloats\"></div>"
								."<div class=\"footer\">
										<div class=\"footerHeader\">" .$translate('Who is Byblio? - Why have you sent me this email?') ."</div>"
		    							."<p>" .$translate('We have sent you this email because we think that you requested a new activation email for your account at byblio.com (you should check it out - it\'s an awesome tool to build on-line libraries!). If so, you\'ll need to follow the link to activate your account with this password within the next 48 hours - it is a key step to ensure security of your details. If that is not the case, please accept our apologies, and you don\'t need to do anything: we will automatically remove this account after 48 hours. Note though that someone has entered your email address into out site and requested a new actviation...') ."</p>"
		    					."</div>"
		    				."</div>"
		    			."</div>"
		    		."</body>";
    	
    	   	
		  		$emailMessageStr = "<html>" .$emailHeaderStr .$emailHTMLbody ."</html>";
    		break;
    		
    	}
    	
    	
    	// return
    	return $emailMessageStr;
    	
    }
    
    
    
    
}





