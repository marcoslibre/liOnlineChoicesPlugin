<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of apiActions
 *
 * @author Glenn Cavarlé <glenn.cavarle@libre-informatique.fr>
 * @author Baptiste SIMON <baptiste.simon@libre-informatique.fr>
 */
class jsonActions extends sfActions
{
    /**
     * 
     */
    public function preExecute()
    {
        // to be tested only if the module "is_secure"
        if ( $this->getSecurityValue('is_secure', false) )
        {
            /* @var $oauthService ApiAuthService */
            $oauthService = $this->getService('oauth_service');
            
            if ( !$oauthService->isAuthenticated($this->getRequest()) )
            {
                $this->getResponse()->setStatusCode(ApiHttpStatus::UNAUTHORIZED);
                throw new sfException('Invalid authentication credentials');
            }
        }

        //disable layout
        $this->setLayout(false);
        //json response header
        $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    }

    /**
     * Create a json response from an array and a status code
     * 
     * @param array $data
     * @return string (sfView::NONE)
     */
    protected function createJsonResponse(array $data, $status = ApiHttpStatus::SUCCESS)
    {
        $this->getResponse()->setStatusCode($status);
        return $this->renderText(json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Retrieve a service by name
     * The service configurations is in SF_ROOT_DIR/config/services.yml and in SF_PLUGINS_DIR/[plugin]/config/services.yml
     */
    public function getService($aServiceName)
    {
        return $this->getContext()->getContainer()->get($aServiceName);
    }
}
