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
 */
class jsonActions extends sfActions
{

    /**
     * 
     */
    public function preExecute()
    {
        /* @var $oauthService ApiAuthService */
        $oauthService = $this->getService('oauth_service');

        if (!$oauthService->isAuthenticated($this->getRequest())) {
            $this->getResponse()->setStatusCode(ApiHttpStatus::UNAUTHORIZED);
            throw new sfException('Invalide Authentication credentials');
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
        return $this->renderText(json_encode($data, null, JSON_PRETTY_PRINT));
    }

    /**
     * Retrieve a service by name
     * The service configurations is in /config/services.yml and in [plugin]/config/services.yml
     */
    public function getService($aServiceName)
    {
        return sfContext::getInstance()->getContainer()->get($aServiceName);
    }
}
