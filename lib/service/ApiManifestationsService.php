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
class ApiManifestationsService extends ApiEntityService
{

    const FIELD_MAPPING = [
        'id' => null,
        'startsAt' => null,
        'endsAt' => null,
        'location' => null, //object
        'gauges' => null, //array
        'gauges.id' => null,
        'gauges.translations' => null,
        'gauges.availableUnits' => null,
        'gauges.prices' => null, //array
        'gauges.prices.id' => null,
        'gauges.prices.translations' => null,
        'gauges.prices.value' => null,
        'gauges.prices.currencyCode' => null,
    ];

    /**
     * 
     * @return array
     */
    public function findAll($query)
    {
        $q = $this->buildQuery($query);
        $manifDotrineCol = $q->execute();

        return $this->getFormattedEntities($manifDotrineCol);
    }

    /**
     * 
     * @param int $manif_id
     * @return array | null
     */
    public function findOneById($manif_id)
    {
        $manifDotrineRec = $this->buildQuery(
                ['criteria' => ['root.id' => $manif_id]])
            ->fetchOne();

        if (false === $manifDotrineRec) {
            return null;
        }

        return $this->getFormattedEntity($manifDotrineRec);
    }

    public function buildInitialQuery()
    {
        return Doctrine_Query::create()
                ->from('Manifestation root')
                ->leftJoin('root.EventCategory EventCategory');
    }

    public function getFieldsEquivalents()
    {
        return static::FIELD_MAPPING;
    }
}
