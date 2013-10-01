<?php
 
/**
 * Byblio
 * Search engine
 * CMS delete content test form
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace ByblioSearch\Form\CMS;

use Zend\Form\Form;

class DeleteContentForm extends Form{
	
	public $classList;
	public $formName;
	public $elementList;
	
	
	
	public function __construct($translate, $inputList){
		
		// form name
		if(key_exists('formName', $inputList)){
			$this->formName = $inputList['formName'];
		} else {
			$this->formName = 'form_user-addContent';
		}
		
		parent::__construct($this->formName);
		
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
		
		// class list
		if(key_exists('classList', $inputList)){
			$this->classList = $inputList['classList'];
		} else {
			$this->classList = array();
		}
		
		// element list
		if(key_exists('elementList', $inputList)){
			$this->elementList = $inputList['elementList'];
		} else {
			$this->elementList = array();
		}
		
		
		
		// ** Form:
	
		// post
		$this->setAttribute('method', 'post');
		
		// csrf
		$this->add(array(
				'type' => 'Zend\Form\Element\Csrf',
				'name' => 'deleteContentCsrf',
				 
		));
		
		// form name (hidden)
		$this->add(array(
				'name' => 'formname',
				'attributes' => array(
						'type'  => 'hidden',
						'value' => $this->formName,
				),
		));
		
		
		// content id
		$name = 'cms-delete-contentId';
		if(key_exists('contentId', $this->elementList)){
			$elInfo = $this->elementList['contentId'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}
		}
		if(key_exists('contentId', $placeholders)){
			$placeholder = $placeholders['contentId'];
		} else {
			$placeholder = 'Content id';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						//'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'text',
						'data-optiontype' =>'text',
						'data-variablename' =>'contentid'
				),
		));
		
		
		// authorisation code - alpha
		$name = 'cms-delete-alpha';
		if(key_exists('authAlpha', $this->elementList)){
			$elInfo = $this->elementList['authAlpha'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}
		}
		if(key_exists('authAlpha', $placeholders)){
			$placeholder = $placeholders['authAlpha'];
		} else {
			$placeholder = 'Authorisation alpha';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						//'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'text',
						'data-optiontype' =>'text',
						'data-variablename' =>'authalpha'
				),
		));
		
		
		// authorisation code - beta
		$name = 'cms-delete-beta';
		if(key_exists('authBeta', $this->elementList)){
			$elInfo = $this->elementList['authBeta'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}
		}
		if(key_exists('authBeta', $placeholders)){
			$placeholder = $placeholders['authBeta'];
		} else {
			$placeholder = 'Authorisation beta';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						//'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'text',
						'data-optiontype' =>'text',
						'data-variablename' =>'authbeta'
				),
		));
		
		
		
		
		
		// submit:	
		$name = 'cms-delete-submit';
		if(key_exists('submit', $this->elementList)){
			$elInfo = $this->elementList['submit'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}	
		}	
		if(key_exists('submit', $buttonValues)){
			$value = $buttonValues['submit'];
		} else {
			$value = 'Delete';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						'type'  => 'submit',
						'value' => $value,
						'id' => $id,
						'class' => 'button',
						'data-optiontype' =>'button'
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