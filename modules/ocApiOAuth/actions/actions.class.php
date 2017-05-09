<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of actions
 *
 * @author Glenn Cavarlé <glenn.cavarle@libre-informatique.fr>
 */
class ocApiOAuthActions extends jsonActions
{

    /**
     * 
     * @param sfWebRequest $request
     */
    public function executeToken(sfWebRequest $request)
    {
        $result = array('message' => 'oauth');

        return $this->createJsonResponse($result);
    }
}
