<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiHttpStatus
 *
 * @author Baptiste SIMON <baptiste.simon@libre-informatique.fr>
 */

class OcLogger
{
    public function log($message, apiAction $object = NULL)
    {
        $cat = is_object($object) ? preg_replace('/Actions$/', '', get_class($object)) : 'liOnlineChoicePlugin';
        error_log('['.$cat'] '.$message);
    }
}
