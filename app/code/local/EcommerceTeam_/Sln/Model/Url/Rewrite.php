<?php

class EcommerceTeam_Sln_Model_Url_Rewrite extends Enterprise_UrlRewrite_Model_Url_Rewrite {

    public function loadByRequestPath($paths) {


        $this->setId(null);
       
        $rewriteRows = $this->_getResource()->getRewrites($paths);

        $matchers = $this->_factory->getSingleton('enterprise_urlrewrite/system_config_source_matcherPriority')
                ->getRewriteMatchers();

        foreach ($matchers as $matcherIndex) {
            $matcher = $this->_factory->getSingleton($this->_factory->getConfig()->getNode(
                            sprintf(self::REWRITE_MATCHERS_MODEL_PATH, $matcherIndex), 'default'
                    ));

            foreach ($rewriteRows as $row) {
                if ($matcher->match($row, $paths['request'])) {
                    $this->setData($row);
                    break(2);
                }
            }
        } // exit;
        $this->_afterLoad();
        $this->setOrigData();
        $this->_hasDataChanges = false;
        return $this;
    }

}
