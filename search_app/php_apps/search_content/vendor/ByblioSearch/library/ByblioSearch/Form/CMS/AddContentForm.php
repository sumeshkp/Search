<?php
 
/**
 * Byblio
 * add engine
 * CMS add content test form
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace ByblioSearch\Form\CMS;

use Zend\Form\Form;

class AddContentForm extends Form{
	
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
				'name' => 'addContentCsrf',
				 
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
		$name = 'cms-add-contentId';
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
		
		
		// title
		$name = 'cms-add-title';
		if(key_exists('title', $this->elementList)){
			$elInfo = $this->elementList['title'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}
		}
		if(key_exists('title', $placeholders)){
			$placeholder = $placeholders['title'];
		} else {
			$placeholder = 'Title';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						//'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'text',
						'data-optiontype' =>'text',
						'data-variablename' =>'title'
				),
		));
		
		
		// is type
		$name = 'cms-add-isType';
		if(key_exists('isType', $this->elementList)){
			$elInfo = $this->elementList['isType'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}
		}
		if(key_exists('isType', $placeholders)){
			$placeholder = $placeholders['isType'];
		} else {
			$placeholder = 'Content type';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						//'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'text',
						'data-optiontype' =>'text',
						'data-variablename' =>'istype'
				),
		));
		
		
		// has type
		$name = 'cms-add-hasType';
		if(key_exists('hasType', $this->elementList)){
			$elInfo = $this->elementList['hastype'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}
		}
		if(key_exists('hasType', $placeholders)){
			$placeholder = $placeholders['hasType'];
		} else {
			$placeholder = 'Content contains';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						//'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'text',
						'data-optiontype' =>'text',
						'data-variablename' =>'hasType'
				),
		));
		
		
		
		
		// summary
		$name = 'cms-add-summary';
		if(key_exists('summary', $this->elementList)){
			$elInfo = $this->elementList['summary'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}	
		}
		if(key_exists('summary', $placeholders)){
			$placeholder = $placeholders['summary'];
		} else {
			$placeholder = 'Summary';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						//'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'textarea',
						'data-optiontype' =>'text',
						'data-variablename' =>'summary',
						'rows'=>3
				),
		));
		
		
		// author
		$name = 'cms-add-author';
		if(key_exists('author', $this->elementList)){
			$elInfo = $this->elementList['author'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}	
		}
		if(key_exists('author', $placeholders)){
			$placeholder = $placeholders['author'];
		} else {
			$placeholder = 'Authors';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						//'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'text',
						'data-optiontype' =>'text',
						'data-variablename' =>'author'
				),
		));
		
		
		
		// publisher
		$name = 'cms-add-publisher';
		if(key_exists('publisher', $this->elementList)){
			$elInfo = $this->elementList['publisher'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}	
		}
		if(key_exists('publisher', $placeholders)){
			$placeholder = $placeholders['publisher'];
		} else {
			$placeholder = 'Publisher';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						//'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'text',
						'data-optiontype' =>'text',
						'data-variablename' =>'publisher'
				),
		));
		
		

		// genre type
		$name = 'cms-add-genre';
		if(key_exists('genre', $this->elementList)){
			$elInfo = $this->elementList['genre'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}
		}
		$allOptions = array(
				array(
						'value'=> 'FICTION',
						'label'=> $translate('Fiction')
					),
				array(
						'value'=> 'NONFICT',
						'label'=> $translate('Non-fiction')
					),
				);
		// options
		$this->add(array(
				'type' => 'Zend\Form\Element\Select',
				'name' => $name,
				'options' => array(
						'value_options' =>$allOptions
				),
				'attributes' => array(
						'class' => 'input',
						'data-optiontype' =>'select',
						'data-variablename' =>'genre'
				),
		));
		
		
		// date of publication
		$name = 'cms-add-dop';
		if(key_exists('yop', $this->elementList)){
			$elInfo = $this->elementList['dop'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}
		}
		if(key_exists('dop', $placeholders)){
			$placeholder = $placeholders['dop'];
		} else {
			$placeholder = 'Date of publication';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						//'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'text',
						'data-optiontype' =>'text',
						'data-variablename' =>'dop'
				),
		));
		
		

		// content text
		$name = 'cms-add-contenttext';
		if(key_exists('summary', $this->elementList)){
			$elInfo = $this->elementList['contentText'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}
		}
		if(key_exists('contentText', $placeholders)){
			$placeholder = $placeholders['contentText'];
		} else {
			$placeholder = 'Text';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						//'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'textarea',
						'data-optiontype' =>'text',
						'data-variablename' =>'contenttext',
						'rows'=>5
				),
		));
		
		
		
		// submit:	
		$name = 'cms-add-submit';
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
			$value = 'Add';
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