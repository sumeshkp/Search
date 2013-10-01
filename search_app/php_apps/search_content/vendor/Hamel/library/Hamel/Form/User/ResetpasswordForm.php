<?php
/**
 * Byblio.
 * User account reset password form, user not logged in
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Hamel\Form\User;

use Zend\Form\Form;

class ResetpasswordForm extends Form{
	
	public function __construct($inputList){
		
		// form name
		if(key_exists('formName', $inputList)){
			$formName = $inputList['formName'];
		} else {
			$formName = 'form_user-resetPassword';
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
		
		
		
		// ** Form:
	
		// post
		$this->setAttribute('method', 'post');
		
		// csrf
		$this->add(array(
		     'type' => 'Zend\Form\Element\Csrf',
		     'name' => 'resetPasswordCsrf',
		     
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
				'name' => 'resetPassword-email',
				'type' => 'Zend\Form\Element\Email',
				'attributes' => array(
					'placeholder' =>$placeholder,
					'class' => 'input',
					'type' => 'email',
				),
		));
		
		
		// submit:		
		if(key_exists('submit', $buttonValues)){
			$value = $buttonValues['submit'];
		} else {
			$value = 'Reset &amp; send email';
		}
		$this->add(array(
				'name' => 'resetPassword-submit',
				'attributes' => array(
						'type'  => 'submit',
						'value' => $value,
						'id' => 'resetPassword_submit',
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