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
class ocApiCheckoutsActions extends apiActions
{

    /**
     * 
     * @param sfWebRequest $request
     * @return array
     */
    public function getOne(sfWebRequest $request)
    {
        return array('message' => __METHOD__);
    }

    /**
     * 
     * @param sfWebRequest $request
     */
    public function executeComplete(sfWebRequest $request)
    {
        return $this->createJsonResponse(array('message' => __METHOD__));
    }

    /**
     * 
     * @param sfWebRequest $request
     */
    public function executePayments(sfWebRequest $request)
    {
        return $this->createJsonResponse(array('message' => __METHOD__));
    }

    /**
     * 
     * @param sfWebRequest $request
     */
    public function executeSelectPayments(sfWebRequest $request)
    {
        return $this->createJsonResponse(array('message' => __METHOD__));
    }

    /**
     * 
     * @param sfWebRequest $request
     */
    public function executeAddressing(sfWebRequest $request)
    {
        return $this->createJsonResponse(array('message' => __METHOD__));
    }
}
