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
class ApiCustomersService extends ApiEntityService
{
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
    protected function getIdentifiedProfessional()
    {
        if ( !$this->isIdentificated() )
            return NULL;
        return $this->oauth->getToken()->OcTransaction[0]->OcProfessional->Professional;
    }
    
    /**
     *
     * @return array
     */
    public function getIdentifiedCustomer()
    {
        return $this->getFormattedEntity($this->getIdentifiedProfessional());
    }
    
    public function buildInitialQuery()
    {
        return Doctrine_Query::create()
            ->from('Professional root')
            ->leftJoin('root.Contact Contact')
            ->leftJoin('root.Organism Organism')
        ;
    }
    
    public function getFieldsEquivalents()
    {
        return [
            'id'            => 'root.id',
            'email'         => 'root.contact_email',
            'firstName'     => 'Contact.firstname',
            'lastName'      => 'Contact.name',
            'shortName'     => 'Contact.shortname',
            'address'       => 'Organism.address',
            'zip'           => 'Organism.postalcode',
            'city'          => 'Organism.city',
            'country'       => 'Organism.country',
            'phoneNumber'   => 'root.contact_number',
            'datesOfBirth'  => 'null 1',
            'locale'        => 'Contact.culture',
            'uid'           => 'Contact.vcard_uid',
            'subscribedToNewsletter' => '!root.contact_email_no_newsletter',
            //'password'      => 'Contact.password',
        ];
    }
}
