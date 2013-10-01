<?php
/**
 * Byblio.
 * User account log in form - same for any user type
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Hamel\Form\User;

use Zend\Form\Form;
use Zend\Form\Element\Captcha;
use Zend\Captcha\Image as CaptchaImage;
use Zend\Captcha\AdapterInterface as CaptchaAdapter;

class LoginForm extends Form{
	
	public $captchaImage;
	
	public function __construct($inputList){
		
		// captcha url and fonts folder
		$captchaImageURL = IMAGES_PATH ."/../captcha/images";
		$captchaImageDir = SITE_PUBLIC_FILEPATH ."/captcha/images";
		$captchaFontPath = SITE_PUBLIC_FILEPATH ."/captcha/fonts/Arial_Narrow_Bold.ttf";
		
		
		
		// form name
		if(key_exists('formName', $inputList)){
			$formName = $inputList['formName'];
		} else {
			$formName = 'form_user-login';
		}
		
		parent::__construct($formName);
		
		// ** input values:
		
		// placeholders
		if(key_exists('placeholder', $inputList)){
			$placeholders = $inputList['placeholder'];
		} else {
			$placeholders = array();
		}
		// button values
		if(key_exists('buttonValues', $inputList)){
			$buttonValues = $inputList['buttonValues'];
		} else {
			$buttonValues = array();
		}
		
		// add captcha (default: false)
		if(key_exists('addCaptcha', $inputList)){
			$addCaptcha = $inputList['addCaptcha'];
		} else {
			$addCaptcha = false;
		}
		
		
		
		// ** Form:
	
		// post
		$this->setAttribute('method', 'post');
		
		// csrf
		$this->add(array(
		     'type' => 'Zend\Form\Element\Csrf',
		     'name' => 'loginCsrf',
		     
		 ));
		
		// form name (hidden)
		$this->add(array(
				'name' => 'formname',
				'attributes' => array(
						'type'  => 'hidden',
						'value' => $formName,
				),
		));
		
		// email address:
		if(key_exists('email', $placeholders)){
			$placeholder = $placeholders['email'];
		} else {
			$placeholder = 'Email';
		}
		$this->add(array(
				'name' => 'login-email',
				'type' => 'Zend\Form\Element\Email',
				'attributes' => array(
					'placeholder' =>$placeholder,
					'class' => 'input',
					'type' => 'email',
				),
		));
		
		// password:
		if(key_exists('password', $placeholders)){
			$placeholder = $placeholders['password'];
		} else {
			$placeholder = 'Password';
		}
		$this->add(array(
				'name' => 'login-password',
				'attributes' => array(
						'type'  => 'password',
						'placeholder' => $placeholder,
						'class' => 'input',
				),
		));
		
		
		// captcha (not added by default)
		if($addCaptcha){
			$captchaImage = new CaptchaImage(array(
					'width' => 150,
					'height' => 60,
					'dotNoiseLevel' => 40,
					'lineNoiseLevel' => 3,
					'expiration' => 120, // 120 seconds
					'imgUrl' => $captchaImageURL,
					'imgDir' => $captchaImageDir,
					'font' =>$captchaFontPath
					)
			);
			
			
			// place holder
			if(key_exists('captcha', $placeholders)){
				$placeholder = $placeholders['captcha'];
			} else {
				$placeholder = 'Security code';
			}
			
			// add captcha element
			$this->add(array(
					'type' => 'Zend\Form\Element\Captcha',
					'name' => 'login-captcha',
					'options' => array(
							'captcha' => $captchaImage,
					),
					'attributes' => array('placeholder' => $placeholder),
			));
		}

		
		
		// submit:		
		if(key_exists('submit', $buttonValues)){
			$value = $buttonValues['submit'];
		} else {
			$value = 'Log in';
		}
		$this->add(array(
				'name' => 'login-submit',
				'attributes' => array(
						'type'  => 'submit',
						'value' => $value,
						'id' => 'login_submit',
						'class' => 'button',
				),
		));
	}
	
	public function populateValues($data){
		
		foreach($data as $key=>$row){
				
			if (is_array(@json_decode($row))){
				$data[$key] =   new \ArrayObject(\Zend\Json\Json::decode($row), \ArrayObject::ARRAY_AS_PROPS);
			}
		}
		
		parent::populateValues($data);
		
	}
	
}