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
        //disable layout
        $this->setLayout(false);
        //json response header
        $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    }

    /**
     * 
     * @param array $data
     * @return string (sfView::NONE)
     */
    protected function createJsonResponse(array $data, $status = ApiHttpStatus::SUCCESS)
    {
        $this->getResponse()->setStatusCode($status);
        return $this->renderText(json_encode($data, null, JSON_PRETTY_PRINT));
    }
}
