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
*    Copyright (c) 2006-2017 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2017 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/

$r = [];


// CUSTOMER
$r['oc_api_customers'] = [
  'class' => 'sfRequestRoute',
  'args'  => [
    'pattern'  => '/api/v2/customers',
    'defaults' => [
      'module' => 'ocApiCustomers',
      'action' => 'index',
    ],
  ],
];

$r['oc_api_customers'] = [
  'class' => 'sfRequestRoute',
  'args'  => [
    'pattern'  => '/api/v2/customers',
    'defaults' => [
      'module' => 'ocApiCustomers',
      'action' => 'index',
    ],
    'requirements' => [
      'sf_method' => ['GET', 'POST'],
    ],
  ],
];

$r['oc_api_customers_resource'] = [
  'class' => 'sfRequestRoute',
  'args'  => [
    'pattern' => '/api/v2/customers/:customer_id',
    'defaults' => [
      'module' => 'ocApiCustomers',
      'action' => 'resource'
    ],
    'requirements' => [
      'customer_id' => '\d+',
      'sf_method'   => ['GET', 'POST', 'DELETE'],
    ],
  ],
];

// CUSTOMER ORDER
$r['oc_api_customers_orders'] = [
  'class'   => 'sfRequestRoute',
  'args' => [
    'pattern' => '/api/v2/customers/:customer_id/orders',
    'defaults' => [
      'module' => 'ocApiCustomerOrders',
      'action' => 'index',
    ],
    'requirements' => [
      'sf_method' => ['GET'],
    ],
  ],
];

$r['oc_api_customers_orders_resource'] = [
  'class' => 'sfRequestRoute',
  'args'  => [
    'pattern' => '/api/v2/customers/:customer_id/orders/:order_id',
    'defaults' => [
      'module' => 'ocApiCustomerOrders',
      'action' => 'resource',
    ],
    'requirements' => [
      'customer_id' => '\d+',
      'order_id'    => '\d+',
      'sf_method'   => ['GET'],
    ],
  ],
];

// MANIFESTATION
$r['oc_api_manifestations'] = [
  'class' => 'sfRequestRoute',
  'args' => [
    'pattern' => '/api/v2/manifestations',
    'defaults' => [
      'module' => 'ocApiManifestations',
      'action' => 'index',
    ],
    'requirements' => [
      'sf_method' => ['GET'],
    ],
  ],
];

$r['oc_api_manifestations_resource'] = [
  'class' => 'sfRequestRoute',
  'args' => [
    'pattern' => '/api/v2/manifestations/:manifestation_id',
    'defaults' => [
      'module' => 'ocApiManifestations',
      'action' => 'resource',
    ],
    'requirements' => [
      'manifestation_id'  => '\d+',
      'sf_method'         => ['GET'],
    ],
  ],
];

// ORDERS
$r['oc_api_orders'] = [
  'class' => 'sfRequestRoute',
  'args' => [
    'pattern' => '/api/v2/orders',
    'defaults' => [
      'module' => 'ocApiOrders',
      'action' => 'index',
    ],
    'requirements' => [
      'sf_method' => ['GET'],
    ],
  ],
];

$r['oc_api_orders_resource'] = [
  'class' => 'sfRequestRoute',
  'args'  => [
    'pattern' => '/api/v2/orders/:order_id',
    'defaults' => [
      'module' => 'ocApiOrders',
      'action' => 'resource',
    ],
    'requirements' => [
      'order_id'  => '\d+',
      'sf_method' =>  ['GET']
    ],
  ],
];

$r['oc_api_orders_action'] = [
  'class' => 'sfRequestRoute',
  'args' => [
    'pattern' => '/api/v2/orders/:do_action/:order_id',
    'defaults' => [
      'module' => 'ocApiOrders',
      'action' => 'action',
    ],
    'requirements' => [
      'order_id'  => '\d+',
      'do_action' => 'ship|cancel|complete',
      'sf_method' => ['GET'],
    ],
  ],
];

// BACKEND
$r['oc_setup'] = [
  'class' => 'sfRequestRoute',
  'args'  => [
    'pattern'  => '/liOnlineChoicePlugin/setup',
    'defaults' => [
      'module' => 'ocSetup',
      'action' => 'index',
    ],
  ],
];


return $r;
