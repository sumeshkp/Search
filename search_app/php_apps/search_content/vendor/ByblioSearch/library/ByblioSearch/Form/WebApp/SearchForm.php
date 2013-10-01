<?php
 
/**
 * Byblio
 * Search engine
 * Web app search test form
 * @copyright 2013 Byblio.com
 * @author: Paul A. Oliver
 *
 */

namespace ByblioSearch\Form\WebApp;

use Zend\Form\Form;

class SearchForm extends Form{
	
	public $classList;
	public $formName;
	public $elementList;
	
	
	
	public function __construct($inputList){
		
		// form name
		if(key_exists('formName', $inputList)){
			$this->formName = $inputList['formName'];
		} else {
			$this->formName = 'form_user-search';
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
				'name' => 'searchCsrf',
				 
		));
		
		// form name (hidden)
		$this->add(array(
				'name' => 'formname',
				'attributes' => array(
						'type'  => 'hidden',
						'value' => $this->formName,
				),
		));
		
		// ip address (hidden)
		if(key_exists('ipAddress', $this->elementList)){
			$name = $this->elementList['ipAddress']['name'];
			$value = $this->elementList['ipAddress']['value'];
		} else {
			$name = 'webapp-search-ipAddress';
			$value = "";
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						'type'  => 'hidden',
						'value' => $value,
						'data-optiontype' =>'hidden'
				),
		));
		
		// username (hidden)
		if(key_exists('username', $this->elementList)){
			$name = $this->elementList['username']['name'];
			$value = $this->elementList['username']['value'];
		} else {
			$name = 'webapp-search-username';
			$value = "";
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						'type'  => 'hidden',
						'value' => $value,
						'data-optiontype' =>'hidden'
				),
		));
		
		
		// query
		if(key_exists('query', $this->elementList)){
			$name = $this->elementList['query']['name'];
		} else {
			$name = 'webapp-search-query';
		}
		if(key_exists('query', $placeholders)){
			$placeholder = $placeholders['query'];
		} else {
			$placeholder = 'Search query';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'text',
						'data-optiontype' =>'text'
				),
		));
		
		
		
		
		// facet: use facets
		$name = 'webapp-search-facet-use';
		if(key_exists('facet-use', $this->elementList)){
			$elInfo = $this->elementList['facet-use'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}
		}
		$this->add(array(
				'type' => 'Zend\Form\Element\Checkbox',
				'name' => $name,
				'options' => array(			
						'checked_value' => 'yes',
						'unchecked_value' => 'no',
						'use_hidden_element' => false,
				),
				'attributes' => array(
						//'checked' => 'checked',
						'data-optiontype' =>'checked'
				)
		));
		
		
		// facet: search authors
		$name = 'webapp-search-facet-author';
		if(key_exists('facet-author', $this->elementList)){
			$elInfo = $this->elementList['facet-author'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}
		}
		$this->add(array(
				'type' => 'Zend\Form\Element\Checkbox',
				'name' => $name,
				'options' => array(			
						'checked_value' => 'yes',
						'unchecked_value' => 'no',
						'use_hidden_element' => false,
				),
				'attributes' => array(
						'data-optiontype' =>'checked'
				)
		));
		
		// facet: search title
		$name = 'webapp-search-facet-title';
		if(key_exists('facet-title', $this->elementList)){
			$elInfo = $this->elementList['facet-title'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}
		}
		$this->add(array(
				'type' => 'Zend\Form\Element\Checkbox',
				'name' => $name,
				'options' => array(			
						'checked_value' => 'yes',
						'unchecked_value' => 'no',
						'use_hidden_element' => false,
				),
				'attributes' => array(
						'data-optiontype' =>'checked'
				)
		));
		
		// facet: search publishers
		$name = 'webapp-search-facet-publisher';
		if(key_exists('facet-publisher', $this->elementList)){
			$elInfo = $this->elementList['facet-publisher'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}	
		}
		$this->add(array(
				'type' => 'Zend\Form\Element\Checkbox',
				'name' => $name,			
				'options' => array(			
						'checked_value' => 'yes',
						'unchecked_value' => 'no',
						'use_hidden_element' => false,
				),
				'attributes' => array(
						'data-optiontype' =>'checked'
				)
		));
		
		// facet: search summary
		$name = 'webapp-search-facet-summary';
		if(key_exists('facet-summary', $this->elementList)){
			$elInfo = $this->elementList['facet-summary'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}	
		}
		$this->add(array(
				'type' => 'Zend\Form\Element\Checkbox',
				'name' => $name,
				'options' => array(			
						'checked_value' => 'yes',
						'unchecked_value' => 'no',
						'use_hidden_element' => false,
				),
				'attributes' => array(
						'data-optiontype' =>'checked'
				)
		));
		
		// facet: search genre
		$name = 'webapp-search-facet-genre';
		if(key_exists('facet-genre', $this->elementList)){
			$elInfo = $this->elementList['facet-genre'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}	
		}
		$this->add(array(
				'type' => 'Zend\Form\Element\Radio',
				'name' => $name,
				'options' => array(
                     'value_options' => array(
                             'all' => 'Any',
                             'fiction' => 'Fiction',
                             'nonFiction' => 'Non fiction',
                     ),
             ),
				'attributes' => array(
						'value'=>'all',
						'class'=>'input',
						'data-optiontype' =>'checked'
				)
		));
		
		
		// facet: year of publication
		$name = 'webapp-search-facet-yop';
		if(key_exists('facet-yop', $this->elementList)){
			$elInfo = $this->elementList['facet-yop'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}	
		}
		if(key_exists('yop', $placeholders)){
			$placeholder = $placeholders['yop'];
		} else {
			$placeholder = 'Year of publication';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'text',
						'data-optiontype' =>'text'
				),
		));
		
		// facet: month of publication
		$name = 'webapp-search-facet-mop';
		if(key_exists('facet-mop', $this->elementList)){
			$elInfo = $this->elementList['facet-mop'];
			if(is_array($elInfo)){
				if(is_string($elInfo['name'])){
					$name = $elInfo['name'];
				}
			}	
		}
		if(key_exists('mop', $placeholders)){
			$placeholder = $placeholders['mop'];
		} else {
			$placeholder = 'Month of publication';
		}
		$this->add(array(
				'name' => $name,
				'attributes' => array(
						'placeholder' =>$placeholder,
						'class' => 'input',
						'type' => 'text',
						'data-optiontype' =>'text'
				),
		));
		
		
		
		
		// submit:	
		$name = 'webapp-search-submit';
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
			$value = 'Search';
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