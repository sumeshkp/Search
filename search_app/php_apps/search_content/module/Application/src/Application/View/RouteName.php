<?php
// helper to return current route name

namespace Application\View;

use Zend\View\Helper\AbstractHelper;

class RouteName extends AbstractHelper {

	protected $routeMatch;

    public function __construct($routeMatch)
    {
        $this->routeMatch = $routeMatch;
    }

    public function __invoke(){
    	
    	if($this->routeMatch){
    		$routeName = $this->routeMatch->getMatchedRouteName();
    	} else {
    		$routeName = "";
    	}
        return $routeName;
    }
}