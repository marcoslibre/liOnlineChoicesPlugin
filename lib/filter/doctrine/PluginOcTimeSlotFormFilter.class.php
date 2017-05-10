<?php

/**
 * PluginOcTimeSlot form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormFilterPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginOcTimeSlotFormFilter extends BaseOcTimeSlotFormFilter
{
  public function configure()
  {
    parent::configure();
    $this->widgetSchema->getFormFormatter()->setTranslationCatalogue('li_oc');
    
    $this->widgetSchema['starts_before'] = new liWidgetFormJQueryDateText([
    ]);
    $this->widgetSchema['ends_after'] = new liWidgetFormJQueryDateText([
    ]);
  }
}
