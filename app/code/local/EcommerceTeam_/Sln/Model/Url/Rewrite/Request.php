<?php

class EcommerceTeam_Sln_Model_Url_Rewrite_Request extends Enterprise_UrlRewrite_Model_Url_Rewrite_Request {

    public function rewrite() {
       
        parent::rewrite();

        // start developer7 ==     

        $request = Mage::app()->getFrontController()->getRequest();
	
        $oldRequestPath = trim($request->getOriginalPathInfo(), '/'); 
        $oldRequestPath = trim($oldRequestPath, '/');
        $path = explode('/', $oldRequestPath);
         
        $helper = Mage::helper('ecommerceteam_sln');
        $separator = $helper->getConfigData('url_separator');      
       
        $filterKey = array_search($separator, $path);
        if ($filterKey !== false) {	
            $filterParams = implode('/', array_slice($path, $filterKey + 1));
            $currentRequestPath = trim($request->getPathInfo(), '/');
            $this->_request->setPathInfo($currentRequestPath . '/' . $filterParams);
        }
        // end developer7 ==  
        return true;
    } 
    
   
    
     protected function _getSystemPaths($requestPath)
    {
       
         // ====== BOF  by developer7 =========
       $helper = Mage::helper('ecommerceteam_sln');
       $separator = $helper->getConfigData('url_separator'); 
       
        if (strpos($requestPath,$separator) !== false) {
          $requestPath = substr($requestPath ,0, strpos($requestPath,$separator)-1 );
        }
         // ====== EOF by developer7  =========
        
        $parts = explode('/', $requestPath);    
        $suffix = array_pop($parts);
        if (false !== strrpos($suffix, '.')) {
            $suffix = substr($suffix, 0, strrpos($suffix, '.'));
        }
        $paths = array('request' => $requestPath, 'suffix' => $suffix);
        if (count($parts)) {
            $paths['whole'] = implode('/', $parts) . '/' . $suffix;
        }
        
        return $paths;
    }
    

}
