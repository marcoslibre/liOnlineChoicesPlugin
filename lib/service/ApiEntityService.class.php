<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiEntityService
 *
 * @author Baptiste SIMON <baptiste.simon@libre-informatique.fr>
 */
abstract class ApiEntityService implements ApiEntityServiceInterface
{
    /**
     * @var ocApiOAuthService
     */
    protected $oauth;
    
    public function __construct(ApiOAuthService $oauth)
    {
        $this->oauth = $oauth;
        if ( !$oauth->isAuthenticated(sfContext::getInstance()->getRequest()) )
            throw new liOnlineSaleException('[customers] API not authenticated.');
    }
    
    public function getFormattedEntities($mixed)
    {
        if ( $mixed instanceof Doctrine_Record )
        {
            
        }
        
        if ( $mixed instanceof Doctrine_Collection )
        {
        }
        
        return [];
    }
    
    protected function getFormattedEntity(Doctrine_Record $record)
    {
        if ( $record === NULL )
            return [];
        
        $arr = [];
        $matches = array_flip($this->getFieldsEquivalents());
        foreach ( $matches as $db => $api )
        {
            // case of "not implemented"  fields
            if ( preg_match('/^null /', $db) === 1 )
            {
                $arr[$api] = NULL;
                continue;
            }
            
            // direct fields from the root entity
            if ( preg_match('/^!?root\.(.*)$/', $db, $result) === 1 )
            {
                $arr[$api] = $this->toggleBoolean($record->$result[1], preg_match('/^!/', $db) === 1);
                continue;
            }
            
            // prepare data
            $subEntities = explode('.', preg_replace('/^!/', '', $db));
            $property = array_pop($subEntities);
            
            // get back the last Doctrine_Record child
            $rec = $record;
            foreach ( $subEntities as $entity )
                $rec = $rec->$entity;
            
            // find out the targeted property to render
            $arr[$api] = $this->toggleBoolean($rec->$property, preg_match('/^!/', $db) === 1);
        }
        
        return $arr;
    }
    
    private function toggleBoolean($value, $bool)
    {
        return $bool ? !$value : $value;
    }
    
    public function buildQuery(array $query)
    {
        if ( !is_array($query['criteria']) )
            $query['criteria'] = [];
        
        $q = $this->buildInitialQuery();
        
        $fields   = $this->getFieldsEquivalents();
        $operands = $this->getOperandEquivalents();
        
        foreach ( $query['criteria'] as $criteria => $search )
        if ( isset($fields[$criteria]) )
        {
            $where   = $fields[$criteria].' ';
            $compare = $operands[$search['type']];
            $args    = [$search['value']];
            $dql     = '?';
            
            if ( is_array($compare) )
            {
                $args = $compare[1]($search['value']);
                if ( is_array($args) )
                {
                    $dql = [];
                    foreach ( $args as $arg )
                        $dql[] = '?';
                    $dql = implode(',', $dql);
                }
            }
            
            $q->andWhere($fields[$criteria].' '.$compare[0].' '.$dql, $args);
        }
        
        return $q;
    }
    
    public function getOperandEquivalents()
    {
        return [
            'contain'           => ['ILIKE',    function($s){ return "%$s%"; }],
            'not contain'       => ['NOT ILIKE',function($s){ return "%$s%"; }],
            'equal'             => '=',
            'not equal'         => '!=',
            'start with'        => ['ILIKE',    function($s){ return "$s%"; }],
            'end with'          => ['ILIKE',    function($s){ return "%$s"; }],
            'empty'             => ['=',        function($s){ return ''; }],
            'not empty'         => ['!=',       function($s){ return ''; }],
            'in'                => ['IN',       function($s){ return implode(',', $s); }],
            'not in'            => ['NOT IN',   function($s){ return implode(',', $s); }],
            'greater'           => '>',
            'greater or equal'  => '>=',
            'lesser'            => '<',
            'lesser or equal'   => '<=',
        ];
    }
}
