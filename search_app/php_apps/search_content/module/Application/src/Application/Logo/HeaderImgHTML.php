<?php
// creates img html of site logos used in header

namespace Application\Logo;

use Zend\View\Helper\AbstractHelper;

class HeaderImgHTML extends AbstractHelper {

	protected $imgHTML;

    public function __construct(){
    	
    	
    	$logoPath = IMAGES_PATH ."/logo";
    	
        // create list
        $this->imgHTML = array(
	        // logos
	        'default' => "<img src=\"" . $logoPath ."/byblio_logo_nobg.png\" alt=\"byblio.com;\" width=\"73\" height=\"36\" />", // default
	        'orange' => "<img src=\"" . $logoPath ."/byblio_logo_orange.png\" alt=\"byblio.com;\" width=\"73\" height=\"36\"/>", 
	        'blue' => "<img src=\"" . $logoPath ."/byblio_logo_blue.png\" alt=\"byblio.com;\" width=\"73\" height=\"36\" />", 
	        'green' => "<img src=\"" . $logoPath ."/byblio_logo_green.png\" alt=\"byblio.com;\" width=\"73\" height=\"36\" />",
	        'pink' => "<img src=\"" . $logoPath ."/byblio_logo_pink.png\" alt=\"byblio.com;\" width=\"73\" height=\"36\" />",
	        'purple' => "<img src=\"" . $logoPath ."/byblio_logo_purple.png\" alt=\"byblio.com;\" width=\"73\" height=\"36\" />",
	        'brown' => "<img src=\"" . $logoPath ."/byblio_logo_brown.png\" alt=\"byblio.com;\" width=\"73\" height=\"36\" />", 
	        'red' => "<img src=\"" . $logoPath ."/byblio_logo_red.png\" alt=\"byblio.com;\" width=\"73\" height=\"36\" />", 
	        'yellow' => "<img src=\"" . $logoPath ."/byblio_logo_yellow.png\" alt=\"byblio.com;\" width=\"73\" height=\"36\" />", 
    	);
        
    }

    public function __invoke($logoType){
    	
    	
    	
    	
    	
        // return list
        return $this->imgHTML[$logoType];
    }
}