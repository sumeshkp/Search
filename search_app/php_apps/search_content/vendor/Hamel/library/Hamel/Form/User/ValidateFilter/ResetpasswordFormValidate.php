<?php
 
/**
 * Byblio.
 * validater and filter for User account reset password form
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


class ResetpasswordFormValidate implements InputFilterAwareInterface{
	
	public $email;
	
	public function exchangeArray($data){
		$this->email      = (isset($data['resetPassword-email'])) ? $data['login-email'] : null;
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
	
			// email address
			$inputFilter->add($factory->createInput(array(
					'name'     => 'resetPassword-email',
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