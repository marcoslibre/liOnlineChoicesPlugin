<?php
/**********************************************************************************
*
*	    This file is part of e-venement.
*
*    e-venement is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License.
*
*    e-venement is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with e-venement; if not, write to the Free Software
*    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*
*    Copyright (c) 2006-2016 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2016 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/

/*
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Baptiste SIMON <baptiste.simon@libre-informatique.fr>
 * @version    SVN: $Id: liOnlineChoiceRouting.class.php 25546 2017-05-03 16:05:04Z $
 */
class liOnlineChoiceRouting
{
  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   * @static
   */
  static public function listenToRoutingLoadConfigurationEvent(sfEvent $event)
  {
    $r = $event->getSubject();
    
    foreach ( require(__DIR__.'/../../config/routing.php') as $name => $params )
    {
      $rc = new ReflectionClass($params['class']);
      $args = [];
      $order = ['pattern', 'defaults', 'requirements', 'options'];
      
      foreach ( $params['args'] as $key => $arg )
      if ( in_array($key, $order) )
        $args[array_search($key, $order)] = $arg;
      
      ksort($args);
      $event->getSubject()->prependRoute($name, $rc->newInstanceArgs($args));
    }
  }
}
