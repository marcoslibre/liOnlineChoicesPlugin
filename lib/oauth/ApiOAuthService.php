<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiOAuthService
 *
 * @author Glenn Cavarlé <glenn.cavarle@libre-informatique.fr>
 */
class ApiOAuthService
{

    /**
     * 
     * @param sfWebRequest $request
     * @return boolean
     */
    public function isAuthenticated(sfWebRequest $request)
    {
        $key = $request->getHttpHeader('Authorization');
        return null !== $this->findOneByApiKey($key);
    }

    public function findOneByApiKey($key)
    {
        //return Doctrine::getTable('myTable')->find...
        return array();
    }
}
