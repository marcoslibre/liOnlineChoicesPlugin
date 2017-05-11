<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiCustomersService
 *
 * @author Baptiste SIMON <baptiste.simon@libre-informatique.fr>
 */
class ApiCustomersService
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
    
    /**
     * 
     * @return boolean
     */
    public function isIdentificated()
    {
        $token = $this->oauth->getToken();
        return $token instanceof OcToken && $token->OcTransaction[0]->oc_professional_id !== NULL;
    }
    
    /**
     * 
     * @return NULL|boolean  NULL if no email nor password given, else boolean
     */
    public function identify(array $query)
    {
        // prerequisites
        if (!( isset($query['criteria']['password']) && $query['criteria']['password']
            && isset($query['criteria']['email']) && $query['criteria']['email'] ))
            return NULL;
        
        if ( $pro = $this->buildQuery($query)->fetchOne() )
        {
            $token = $this->oauth->getToken();
            $transaction = $token->OcTransaction->count() == 0
                ? new OcTransaction
                : $token->OcTransaction[0];
            
            if ( !$transaction->oc_professional_id )
                $transaction->OcProfessional = new OcProfessional;
            $transaction->OcProfessional->Professional = $pro;
            
            $transaction->OcToken = $token;
            
            $transaction->save();
            return true;
        }
        return false;
    }
    
    /**
     * 
     * @return NULL|OcProfessional
     */
    public function getCurrentOcProfessional()
    {
        if ( !$this->isIdentificated() )
            return NULL;
        return $this->oauth->getToken()->OcTransaction[0]->OcProfessional;
    }
    
    public function buildQuery(array $query)
    {
        if ( !is_array($query['criteria']) )
            $query['criteria'] = [];
        
        $q = Doctrine_Query::create()
            ->from('Professional p')
            ->leftJoin('p.Contact c')
            ->leftJoin('p.Organism o')
        ;
        
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
    
    protected function getFieldsEquivalents()
    {
        return [
            'id'            => 'p.id',
            'email'         => 'p.contact_email',
            'phonenumber'   => 'p.phonenumber',
            'address'       => 'o.address',
            'zip'           => 'o.postalcode',
            'city'          => 'o.city',
            'country'       => 'o.country',
            'firstName'     => 'c.firstname',
            'lastName'      => 'c.name',
            'shortName'     => 'c.shortname',
            'locale'        => 'c.culture',
            'uid'           => 'c.vcard_uid',
            'password'      => 'c.password',
        ];
    }
    
    protected function getOperandEquivalents()
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
