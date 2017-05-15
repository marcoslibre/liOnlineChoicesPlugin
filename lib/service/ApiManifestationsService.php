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
    public function findAll()
    {
        $manifDotrineCol = $this->buildInitialQuery()
            ->orderBy('root.happens_at, EventCategory.name')
            ->execute();

        return $this->getFormattedEntities($manifDotrineCol);
    }

    /**
     * 
     * @param int $manif_id
     * @return array | null
     */
    public function findOneById($manif_id)
    {
        $manifDotrineCol = $this->buildInitialQuery()
            ->orderBy('root.happens_at, EventCategory.name')
            ->andWhere('root.id = ?', $manif_id)
            ->execute();

        if ($manifDotrineCol->count() == 0) {
            return null;
        }

        return $this->getFormattedEntity($manifDotrineCol->getFirst());
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
