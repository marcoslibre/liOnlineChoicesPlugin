<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiManifestationService
 *
 * @author Glenn Cavarlé <glenn.cavarle@libre-informatique.fr>
 */
class ApiCartsService extends ApiEntityService
{

    const FIELD_MAPPING = [];

    /**
     * 
     * @param int $cart_id
     * @param int $query
     * @return array
     */
    public function findAll($cart_id, $query)
    {
        return [];
    }

    /**
     * 
     * @param int $cart_id
     * @param int $item_id
     * @return array|null
     */
    public function findOne($cart_id, $item_id)
    {
        return [];
    }

    /**
     * 
     * @param int $cart_id
     * @param int $item_id
     * @param array $data
     * @return boolean
     */
    public function updateCartItem($cart_id, $item_id, $data)
    {
        return true;
    }

    /**
     * 
     * @param int $cart_id
     * @param int $item_id
     * @return boolean
     */
    public function deleteCartItem($cart_id, $item_id)
    {
        return true;
    }

    public function buildInitialQuery()
    {
        return Doctrine_Query::create()
                ->from('OcTransaction root');
    }

    public function getFieldsEquivalents()
    {
        return static::FIELD_MAPPING;
    }
}
