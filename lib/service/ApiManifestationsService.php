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

    protected $FIELD_MAPPING = [
        'id' => 'id',
        'startsAt' => 'happens_at',
        'endsAt' => 'ends_at',
        'location' => 'Location', //object
        //'gauges' => null, //array
        'gauges.id' => 'Gauges.id',
        'gauges.translations' => 'Gauges.Translation',
        'gauges.availableUnits' => 'Gauges.free',
        //'gauges.prices' => null, //array
        'gauges.prices.id' => 'Gauges.Prices.id',
        'gauges.prices.translations' => 'Gauges.Prices.Translation',
        'gauges.prices.value' => 'Gauges.Prices.value',
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
        $manifDotrineRec = $this->buildQuery([
            'criteria' => [
                'id' => [
                    'value' => 'manif_id',
                    'type'  => 'equal',
                ],
            ]
        ])
        ->fetchOne();

        if (false === $manifDotrineRec)
        {
            return null;
        }

        return $this->getFormattedEntity($manifDotrineRec);
    }

    public function buildInitialQuery()
    {
        return Doctrine::getTable('Manifestation')->createQuery('root');
    }
}
