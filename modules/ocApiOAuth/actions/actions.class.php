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
class ocApiOAuthActions extends jsonActions
{
    /**
     * @param sfWebRequest $request
     * @todo move at least the protected functions into a service
     */
    public function executeToken(sfWebRequest $request)
    {
        // authenticates the app
        try {
            $app = $this->findApplication(
                $request->getParameter('client_id'),
                $request->getParameter('client_secret')
            );
        } catch ( liOnlineSaleException $e ) {
            OcLogger::log($e->getMessage(), $this);
            return $this->createJsonResponse([], ApiHttpStatus::UNAUTHORIZED);
        }
        
        // deal with the token
        if ( $refresh = $request->getParameter('refresh_token', false) )
        {
            try
            {
                $token = $this->refreshToken($refresh, $app);
            } catch ( liOnlineSaleException $e ) {
                OcLogger::log($e->getMessage(), $this);
                return $this->createJsonResponse([], ApiHttpStatus::UNAUTHORIZED);
            }
        }
        else
        {
            $token = $this->createToken($app);
        }
        
        // builds the result
        $result = [
            'access_token'  => $token->token,
            'expires_in'    => $this->getTokenLifetime(),
            'token_type'    => 'bearer',
            'scope'         => null,
            'refresh_token' => $token->refresh_token,
        ];
        
        // sends the result
        return $this->createJsonResponse($result);
    }
    
    protected function findApplication($client_id, $client_secret)
    {
        $q = Doctrine::getTable('OcApplication')->createQuery('app')
            ->andWhere('app.identifier = ?', $client_id)
            ->andWhere('app.secret     = ?', $this->encryptSecret($client_secret))
        ;
        
        $app = $q->fetchOne();
        if ( ! $app instanceof OcApplication )
            throw new liOnlineSaleException('Application not found.');
        
        return $app;
    }
    
    protected function createToken(OcApplication $app)
    {
        $token = new OcToken;
        
        $token->token = $this->generateToken();
        $token->refresh_token = $this->generateToken();
        $token->expires_at = $this->getExpirationTime();
        $token->oc_application_id = $app->id;
        $token->save();
        
        return $token;
    }
    
    protected function refreshToken($refresh, OcApplication $app)
    {
        $q = Doctrine::getTable('OcToken')->createQuery('ot')
            ->andWhere('ot.refresh_token = ?', $refresh)
            ->andWhere('ot.oc_application_id = ?', $app->id)
        ;
        
        $token = $q->fetchOne();
        if ( ! $token instanceof OcToken )
            throw new liOnlineSaleException('Refresh token not found.');
        
        $token->token = $this->generateToken();
        $token->refresh_token = $this->generateToken();
        $token->expires_at = $this->getExpirationTime();
        $token->save();
        
        return $token;
    }
    
    private function encryptSecret($secret)
    {
        return OcApplicationForm::encryptSecret($secret);
    }
    
    private function generateToken()
    {
        $date = str_replace('-', 'T', date('Ymd-HisP'));
        return OcApplicationForm::encryptSecret($date.'-'.rand(1000000,9999999));
    }
    
    private function getTokenLifetime()
    {
        return ini_get('session.gc_maxlifetime');
    }
    private function getExpirationTime()
    {
        return date('Y-m-d H:i:s',time()+$this->getTokenLifetime());
    }
}
