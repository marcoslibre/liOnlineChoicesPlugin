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
class ocApiManifestationsActions extends apiActions
{

    /**
     * 
     * @param sfWebRequest $request
     * @param array $query
     * @return array
     */
    public function getAll(sfWebRequest $request, array $query)
    {
        /* @var $manifService ApiManifestationsService */
        $manifService = $this->getService('manifestations_service');
        $result = $manifService->findAll();

        return $result;
    }
}
