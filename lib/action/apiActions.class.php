<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of apiActions
 *
 * @author Glenn Cavarlé <glenn.cavarle@libre-informatique.fr>
 * @author Baptiste SIMON <baptiste.simon@libre-informatique.fr>
 */
class apiActions extends jsonActions
{

    /**
     * Action executed when requesting /[resource].
     *
     * @param sfWebRequest $request
     */
    public function executeIndex(sfWebRequest $request)
    {
        $response = null;
        $status = ApiHttpStatus::SUCCESS;
        $query = $this->buildQuery($request);

        switch (strtoupper($request->getMethod())) {
            case 'GET':
                // get all resources
                $response = $this->getAll($request, $query);
                break;
            case 'POST':
                // creates a resource
                $response = $this->create($request);
                $status = ApiHttpStatus::CREATED;
                break;
            default:
                $status = ApiHttpStatus::BAD_REQUEST;
                $response = array('error');
        }

        return $this->createJsonResponse($response, $status);
    }

    /**
     * Action executed when requesting /[resource]/[id].
     *
     * @param sfWebRequest $request
     */
    public function executeResource(sfWebRequest $request)
    {
        $response = null;
        $status = ApiHttpStatus::SUCCESS;

        switch (strtoupper($request->getMethod())) {
            case 'GET':
                // get one resource
                $response = $this->getOne($request);
                break;
            case 'POST':
                // update one resource
                $response = $this->update($request);
                break;
            case 'DELETE':
                // delete one resource
                $response = $this->delete($request);
                break;
            default:
                $status = ApiHttpStatus::BAD_REQUEST;
                $response = array('error');
        }

        return $this->createJsonResponse($response, $status);
    }

    /**
     * Action executed when requesting /[resource]/[action]/[id].
     *
     * @param sfWebRequest $request
     */
    public function executeAction(sfWebRequest $request)
    {
        $response = null;
        $status = ApiHttpStatus::SUCCESS;
        $query = $this->buildQuery($request);
        $doAction = ucfirst($request->getParameter('do_action'));

        // requirements for do_action must be defined in route configuration
        // example: do_action: action1|action2|action3
        if ($this->actionRequirementsIsEmpty($request)) {
            return $this->createJsonResponse(array('error')
                    , ApiHttpStatus::INTERNAL_SERVER_ERROR);
        }

        switch (strtoupper($request->getMethod())) {
            case 'GET':
                $response = $this->{$doAction . 'Action'}($request, $query);
                break;
            case 'POST':
            case 'DELETE':
            default:
                $status = ApiHttpStatus::BAD_REQUEST;
                $response = array('error');
        }

        return $this->createJsonResponse($response, $status);
    }

    /**
     * Action for a GET:/[resource]/[id] request
     * The specified id has to be retrieved from the $request
     * The id key is defined in routing.yml
     * 
     * @param sfWebRequest $request
     * @return array (a single entity)
     */
    public function getOne(sfWebRequest $request)
    {
        return array('message' => __METHOD__);
    }

    /**
     * Action for a GET:/[resource] request 
     * Criteria and filters can be retrieved in $query
     * 
     * @param sfWebRequest $request
     * @return array (a list of entities)
     */
    public function getAll(sfWebRequest $request, array $query)
    {
        return array('message' => __METHOD__);
    }

    /**
     * Action for a POST:/[resource] request
     * 
     * @param sfWebRequest $request
     * @return array
     */
    public function create(sfWebRequest $request)
    {
        return array('message' => __METHOD__);
    }

    /**
     * Action for a POST|PUT:/[resource]/id request
     * The specified id has to be retrieved from the $request
     * The id key is defined in routing.yml
     * 
     * @param sfWebRequest $request
     * @return array
     */
    public function update(sfWebRequest $request)
    {
        return array('message' => __METHOD__);
    }

    /**
     * Action for a DELETE:/[resource]/id request
     * The specified id has to be retrieved from the $request
     * The id key is defined in routing.yml
     * 
     * @param sfWebRequest $request
     * @return array
     */
    public function delete(sfWebRequest $request)
    {
        return array('message' => __METHOD__);
    }

    /**
     * Check if actions are specified in routing.yml
     * 
     * @param sfWebRequest $request
     * @return type
     */
    private function actionRequirementsIsEmpty(sfWebRequest $request)
    {
        $route = $request->getRequestParameters()['_sf_route'];
        $actionRequirements = $route->getRequirements()['do_action'];
        return empty($actionRequirements);
    }

    /**
     * Build the query parameters (criteria and filters) from the request
     * 
     * @param sfWebRequest $request
     * @return array
     */
    private function buildQuery(sfWebRequest $request)
    {
        $params = $request->getGetParameters();
        return [
            'page'      => isset($params['page'])     ? $this->buildSortingQuery($params['page'])      : NULL,
            'limit'     => isset($params['limit'])    ? $this->buildSortingQuery($params['limit'])     : NULL,
            'sorting'   => isset($params['sorting'])  ? $this->buildSortingQuery($params['sorting'])   : NULL,
            'criteria'  => isset($params['criteria']) ? $this->buildCriteriaQuery($params['criteria']) : NULL,
        ];
    }

    /**
     * 
     * @param array|null $params
     * @return array
     */
    private function buildSortingQuery($params = [])
    {
        $sortingParams = (null === $params ? [] : $params);
        return $sortingParams;
    }

    /**
     * 
     * @param array|null $params
     * @return array
     */
    private function buildCriteriaQuery($params = [])
    {
        $criteriaParams = !is_array($params) ? [] : $params;
        $result = [];

        $allowedCriteria = [];

        $allowedTypes = [
            'contain', 'not contain',
            'equal', 'not equal',
            'start with', 'end with',
            'empty', 'not empty',
            'in', 'not in',
            'greater', 'lesser',
            'greater or equal', 'lesser or equal',
        ];

        foreach ( $criteriaParams as $criteria => $options )
        {
            // this is done to limit allowed criterias, usually useless
            if ( is_array($allowedCriteria)
              && count($allowedCriteria) > 0
              && !in_array($criteria, $allowedCriteria) )
            {
                continue;
            }
            
            if (!( isset($options['type']) && $options['type'] ))
                $options['type'] = 'equal';

            if ( !in_array($options['type'], $allowedTypes) )
            {
                continue;
            }

            $result[$criteria] = [
                'value' => $options['value'],
                'type'  => $options['type'],
            ];
        }

        return $result;
    }
}
