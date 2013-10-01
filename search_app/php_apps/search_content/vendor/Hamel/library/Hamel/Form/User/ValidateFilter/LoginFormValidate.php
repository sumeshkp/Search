<?php
 
/**
 * Byblio.
 * validater and filter for User account login form
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace Hamel\Form\User\ValidateFilter;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Csrf;


class LoginFormValidate implements InputFilterAwareInterface{
	
	public $email;
	public $password;
	
	public function exchangeArray($data){
		$this->email      = (isset($data['login-email'])) ? $data['login-email'] : null;
		$this->password    = (isset($data['login-password'])) ? $data['login-password'] : null;
	}
	
	
	public function getArrayCopy(){
		return get_object_vars($this);
	}
	
	public function setInputFilter(InputFilterInterface $inputFilter)
	{
		throw new \Exception("Not used");
	}
	
	// input filter
	public function getInputFilter(){
		if (!$this->inputFilter) {
			$inputFilter = new InputFilter();
			$factory     = new InputFactory();
	
			
			// password
			$inputFilter->add($factory->createInput(array(
					'name'     => 'login-password',
					'required' => true,
					'filters'  => array(
							array('name' => 'StripTags'),
							array('name' => 'StringTrim'),
					),
					'validators' => array(
							array(
									'name'    => 'StringLength',
									'options' => array(
											'encoding' => 'UTF-8',
											'min'      => 5,
											'max'      => 50,
									),
							),
					),
			)));
	
			// email address
			$inputFilter->add($factory->createInput(array(
					'name'     => 'login-email',
					'validators' => array(
							array(
									'name'    => 'EmailAddress'
							),
					),
			)));
	

	
			
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	
}