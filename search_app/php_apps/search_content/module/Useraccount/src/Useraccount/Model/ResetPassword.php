<?php
/**
 * Byblio reset user password
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Useraccount\Model;
use Hamel\Usermanagement\Upload as UsermanagementUpload;
use Hamel\Usermanagement\Download as UsermanagementDownload;
use Hamel\Email\SendEmail;
use Hamel\Db\General as DbGeneral;
require 'vendor/Hamel/library/Hamel/Password/password.php';


class ResetPassword {
	
	private $viewHelperMgr;
	
	
    public function __construct($viewHelperMgr){
    	
    	// ref view helper manager
    	$this->viewHelperMgr = $viewHelperMgr;
    }
    
    
    
    // creates a new user account
    function resetPassword($inputList){
        
        // defaults
        $email = "";
        $emailNotInUse = false;
        $passwordReset = false;
       
        // get values
        if(key_exists('email', $inputList)){
            $email = trim(mb_strtolower($inputList['email'])); // NOTE: lowercase email stored
        }
        
       	// min required value
        if($email !=""){
        	
            // check email in db (and get user name if is)
        	$usermanagementDownload = new UsermanagementDownload();
        	$username = $usermanagementDownload->checkUserEmailInDb($email, false);
            
        	if($username){ // email in db
        		
        		// create new password
        		$usermanagementUpload = new UsermanagementUpload();
        		$password = $usermanagementUpload->createNewUserPassword();
        		
        		// encrypt  password
        		$encPwd = password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));

        		if(password_verify($password, $encPwd)){ // if hashed password sucessfully
        			
	        		// get user info
	        		$userInfo = $usermanagementDownload->getUserCoreDetailsByUsername(array('username'=>$username), true);
	        		$firstName = $userInfo['firstName'];
	        		$lastName = $userInfo['lastName'];
	        				
	        		// create new activation key
			        $activationKey = $usermanagementUpload->createNewActivationKey();	       
			           
			        // scramble the username
			        $dbGeneral = new DbGeneral();
			        $usernameScramble = $dbGeneral->scrambleString($username);
	
			          	
			        // record new password to complete
			        $infoList = array();
			        $infoList['activationKey'] = $activationKey;
			        $infoList['username'] = $username;
			        $infoList['usernameScramble'] = $usernameScramble;
			        $infoList['password'] = $encPwd;
			        $usermanagementUpload->addNewUserPwdResetToComplete($infoList, true);
			          		
	        		// send email
			        $infoList = array();
			        $infoList['password'] = $password;
			        $infoList['activationKey'] = $activationKey;
			        $infoList['firstName'] = $firstName;
			        $infoList['lastName'] = $lastName;
			        $infoList['email'] = $email;
			        $infoList['usernameScramble'] = $usernameScramble;
	        		$this->sendEmail($infoList);
	        			
	        		// flag
	        		$passwordReset = true;
        		}

        	} else {
        		// flag
        		$emailNotInUse = true;
        	}
            
 			
        }
        
        // return new id and password
        $returnInfo = array('emailNotInUse'=>$emailNotInUse, 'passwordReset'=>$passwordReset);
        
        return $returnInfo;
    }
    
    
    // sends activation email
    public function sendEmail($inputList){
    	
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
    	
    	if(key_exists('usernameScramble', $inputList)){
    		$usernameScramble = $inputList['usernameScramble'];
    	}
    	if(key_exists('password', $inputList)){
    		$password = $inputList['password'];
    	}
    	
    	// min required values
    	if($email !="" && $firstName !="" && $lastName !="" && $activationKey !="" && $password !="" && $usernameScramble !=""){
    		
    		$cssType = 'resetPassword';
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
    		$message = $this->_createMessage($messageInfo);
    		
    		// title
    		$title = $translate('Reset your password');
    		
    		// send info
    		$sendInfo = array('from'=>'registration', 'type'=>'userResetPassword');
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
    	
    	$usernameScramble = $inputList['usernameScramble'];
    	$activationKey = $inputList['activationKey'];
    	$lastName = $inputList['lastName'];
    	$firstName = $inputList['firstName'];
    	$password = $inputList['password'];
    	$css = $inputList['css'];
    	
    	// body
    	$hlink = "http://www.byblio.com/useraccount/resetpassword/" .$usernameScramble  ."/" .$firstName ."/" .$lastName ."/" .$activationKey;
    	$iconStr = "<img class=\"iconInTextPos\" src=\"" .EMAIL_PUBLIC_PATH ."/images/email/clickhere16.png\">";
    	$bgImgStr = "<img class=\"imageRightPos\" src=\"" .EMAIL_PUBLIC_PATH ."/images/email/email_p_r.jpg\">";
    	$logoStr = "<img src=\"" .EMAIL_PUBLIC_PATH ."/images/logo/byblio_logo_purple.png\">";
    	
    	// header
    	$emailHeaderStr = "<head>" .$css."</head>";
    	
    	// email html, including css
    	$emailHTMLbody = "<body>"
    			."<div class=\"heading\">" .$translate('Reset your password') ."</div>"
    			."<div class=\"clearFloats\"></div>"
    			."<div class=\"mainBody\">"
    				."<div class=\"logo\">" .$logoStr ."</div>"
    				."<div class=\"clearFloats\"></div>"
    				."<div class=\"message\">"
    					."<div>"
    					."<p>" .$translate('Dear') ." " .$firstName .",</p>"
    					."<p>" .$translate('We\'ve received a request to reset your password.') ."</p>"
    					."<div>"
	    			   		."<p>" .$translate('If you sent the request, please follow the link below - it will take you to the reset page, which is a key step to ensure security of your details.') ."</p>"
	    					."<div class=\"link\">" .$translate('Click here') ." " .$iconStr ." <a href=\"$hlink\">Byblio.com/account/resetpassword</a></div>"
    					."</div>"
    					."<div>"
    						."<p>" .$translate('This is your new password: ') ." <span class=\"highlight\">" .$password ."</span> . (" .$translate('If you don\'t follow the link then your old password will remain valid') .").</p>"
    					."</div>"
    					."<div>"
	    					."<p><span class=\"highlight\">" .$translate('Not you?') ."</span></p>"
	    					."<p>" .$translate('If you did not request a new password, and are concerned about the security of your account, you can contact us by replying to this email (it\'s a real one, with a real person at the other end).') ."</p>"
    					."</div>"
    					."<div>$bgImgStr</div>"
    							
						."<div class=\"clearFloats\"></div>"
						."<div class=\"footer\">
								<div class=\"footerHeader\">" .$translate('Who is Byblio? - Why have you sent me this email?') ."</div>"
    							."<p>" .$translate('We have sent you this email because we think that you have a new password for your account at byblio.com (you should check it out - it\'s an awesome tool to build on-line libraries!). If so, you\'ll need to follow the link to activate your account with this password within the next 48 hours - it is a key step to ensure security of your details. If you do not wish to have an account with us, please accept our apologies, and you don\'t need to do anything: we will automatically remove this account after 48 hours.') ."</p>"
    					."</div>"
    				."</div>"
    			."</div>"
    		."</body>";
    	
    	
    	$emailMessageStr = "<html>" .$emailHeaderStr .$emailHTMLbody ."</html>";
    	
    	// return
    	return $emailMessageStr;
    	
    }
    
    
    
    
}





