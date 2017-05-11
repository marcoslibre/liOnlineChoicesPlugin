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
class ocApiCustomersActions extends apiActions
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
     * @param array $query
     * @return array
     */
    public function getAll(sfWebRequest $request, array $query)
    {
        $customers = $this->getService('customers_service');
        if ( !$customers->isIdentificated() && !$query['criteria'] )
            return [];
        
        $customer = $customers->identify($query);
        if ( !$customer )
            return [];
        
        echo "TODO - https://github.com/betaglop/e-venement/blob/master/doc/api/customers.rst#collection-of-customers\n";
        return ['success'];
    }
    
    public function buildQuery(sfWebRequest $request)
    {
        $params = parent::buildQuery($request);
    }
}
