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
 */
class apiActions extends sfActions
{

    /**
     * 
     */
    public function preExecute()
    {
        //disable layout
        $this->setLayout(false);
        //json response header
        $this->getResponse()->setHttpHeader('Content-type', 'application/json');
    }

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
     * 
     * @param sfWebRequest $request
     * @return array
     */
    public function getOne(sfWebRequest $request)
    {
        return array('message' => __METHOD__);
    }

    /**
     * 
     * @param sfWebRequest $request
     * @return array
     */
    public function getAll(sfWebRequest $request, array $query)
    {
        return array('message' => __METHOD__);
    }

    /**
     * 
     * @param sfWebRequest $request
     * @return array
     */
    public function create(sfWebRequest $request)
    {
        return array('message' => __METHOD__);
    }

    /**
     * 
     * @param sfWebRequest $request
     * @return array
     */
    public function update(sfWebRequest $request)
    {
        return array('message' => __METHOD__);
    }

    /**
     * 
     * @param sfWebRequest $request
     * @return array
     */
    public function delete(sfWebRequest $request)
    {
        return array('message' => __METHOD__);
    }

    /**
     * 
     * @param array $data
     * @return string (sfView::NONE)
     */
    protected function createJsonResponse(array $data, $status = ApiHttpStatus::SUCCESS)
    {
        $this->getResponse()->setStatusCode($status);
        return $this->renderText(json_encode($data, null, JSON_PRETTY_PRINT));
    }

    /**
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
     * 
     * @param sfWebRequest $request
     * @return array
     */
    private function buildQuery(sfWebRequest $request)
    {
        $params = $request->getGetParameters();
        return array(
            'sorting' => $this->buildSortingQuery($params['sorting']),
            'criteria' => $this->buildCriteriaQuery($params['criteria'])
        );
    }

    /**
     * 
     * @param array|null $params
     * @return array
     */
    private function buildSortingQuery($params = array())
    {
        $sortingParams = (null === $params ? array() : $params);
        return $sortingParams;
    }

    /**
     * 
     * @param array|null $params
     * @return array
     */
    private function buildCriteriaQuery($params = array())
    {
        $criteriaParams = (null === $params ? array() : $params);
        $result = array();

        $allowedCriteria = array('search');

        $allowedTypes = array(
            'contain', 'not contain',
            'equal', 'not equal',
            'start with', 'end with',
            'empty', 'not empty',
            'in', 'not in'
        );

        foreach ($criteriaParams as $criteria => $options) {
            if (!in_array($criteria, $allowedCriteria)) {
                continue;
            }

            if (!in_array($options['type'], $allowedTypes)) {
                continue;
            }

            $result[$criteria] = $options['value'];
        }

        return $result;
    }
}
