<?php

/**
 * PluginOcApplication form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginOcApplicationForm extends BaseOcApplicationForm
{
  public function configure()
  {
    $this->widgetSchema->getFormFormatter()->setTranslationCatalogue('li_oc');

    $this->widgetSchema['user_id']->setOption('add_empty', true);
    unset($this->widgetSchema['secret'], $this->validatorSchema['secret']);
    
    $this->widgetSchema   ['secret_new'] = new sfWidgetFormInputText(['label' => 'Secret']);
    $this->validatorSchema['secret_new'] = new sfValidatorString(['required' => false]);
  }
  
  public function doSave($con = null)
  {
    if ( $this->values['secret_new'] || $this->object->isNew() )
      $this->values['secret'] = $this->encryptSecret($this->values['secret_new']);
    unset($this->values['secret_new']);
    
    parent::doSave($con);
  }
  
  public static function encryptSecret($secret)
  {
    $salt = sfConfig::get('project_eticketting_salt', '123456789azerty');
    return md5($secret.$salt);
  }
}
