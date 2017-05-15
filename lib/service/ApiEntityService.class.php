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
        $r = [];
        
        // Doctrine_Record
        if ( $mixed instanceof Doctrine_Record )
            $r = $this->getFormattedEntity($mixed);
        
        // Doctrine_Collection
        if ( $mixed instanceof Doctrine_Collection )
        foreach ( $mixed as $record )
            $r[] = $this->getFormattedEntity($record);
        
        return $r;
    }
    
    public function getFormattedEntity(Doctrine_Record $record)
    {
        if ( $record === NULL )
            return [];
        
        $arr = [];
        foreach ( $this->getFieldsEquivalents() as $api => $db )
        {
            // case of "not implemented"  fields
            if ( $db === NULL )
            {
                $arr = $this->setResultValue(NULL, $api, $arr);
                continue;
            }
            
            // direct fields from the root entity
            if ( strpos($db, '.') === false )
            {
                $field = preg_replace('/^!/', '', $db);
                $arr = $this->setResultValue(
                    $this->toggleBoolean($record->$field, $field != $db),
                    $api,
                    $arr);
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
            $arr = $this->setResultValue(
                $this->toggleBoolean($rec->$property, preg_match('/^!/', $db) === 1),
                $api,
                $arr
            );
        }
        
        return $arr;
    }
    
    public function buildQuery(array $query)
    {
        if ( !is_array($query['criteria']) )
            $query['criteria'] = [];
        
        $q = $this->buildInitialQuery();
        
        $this->buildQueryCondition($q, $query['criteria']);
        $this->buildQuerySorting($q, $query['sorting']);
        $this->buildQueryLimit($q, $query['limit']);
        $this->buildQueryPagination($q, $query['page']);
        
        return $q;
    }
    
    protected function buildQuerySorting(Doctrine_Query $q, array $sorting = [])
    {
        $orderBy = '';
        foreach ( $sorting as $field => $direction )
        {
            if ( !in_array($field, $this->getFieldsEquivalents()) )
                continue;
            $orderBy .= array_search($field, $this->getFieldsEquivalents()).' '.$direction.' ';
        }
        
        return $orderBy ? $q->orderBy($orderBy) : $q;
    }
    
    protected function buildQueryLimit(Doctrine_Query $q, $limit = NULL)
    {
        if ( $limit !== NULL )
            $q->limit($limit);
        return $q;
    }
    
    protected function buildQueryPagination(Doctrine_Query $q, $page = 1)
    {
        if ( $page !== NULL )
            $q->offset($page-1);
        return $q;
    }
    
    protected function buildQueryCondition(Doctrine_Query $q, array $criterias = [])
    {
        $fields   = array_merge($this->getFieldsEquivalents(), $this->getHiddenFieldsEquivalents());
        $operands = $this->getOperandEquivalents();
        
        foreach ( $criterias as $criteria => $search )
        if ( isset($fields[$criteria]) && isset($search['value']) )
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
    
    public function countResults(array $query)
    {
        return $this->buildQuery($query)->count();
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

    public function getHiddenFieldsEquivalents()
    {
        return isset($this->hiddenFieldsEquivalents) ? $this->hiddenFieldsEquivalents : [];
    }
    
    public function getFieldsEquivalents()
    {
        return isset($this->fieldsEquivalents) ? $this->fieldsEquivalents : [];
    }

    
    private function setResultValue($value, $key, array $result)
    {
        $tmp = &$result;
        foreach ( explode('.', $key) as $field )
        {
            if ( !isset($tmp[$field]) )
                $tmp[$field] = [];
            $tmp = &$tmp[$field];
        }
        $tmp = $value;
        
        return $result;
    }
    
    private function toggleBoolean($value, $bool)
    {
        return $bool ? !$value : $value;
    }
}
