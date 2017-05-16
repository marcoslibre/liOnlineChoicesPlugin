<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiEntityService
 *
 * @author Baptiste SIMON <baptiste.simon@libre-informatique.fr>
 */
abstract class ApiEntityService implements ApiEntityServiceInterface
{

    /**
     * @var ocApiOAuthService
     */
    protected $oauth;

    /**
     *
     * @var array 
     */
    protected static $HIDDEN_FIELD_MAPPING = [];

    /**
     *
     * @var array 
     */
    protected static $FIELD_MAPPING = [];

    /**
     * 
     * @param ApiOAuthService $oauth
     * @throws liOnlineSaleException
     */
    public function __construct(ApiOAuthService $oauth)
    {
        $this->oauth = $oauth;
        if ( !$oauth->isAuthenticated(sfContext::getInstance()->getRequest()) )
            throw new liOnlineSaleException('[services] API not authenticated.');
        sfContext::getInstance()->getUser()->signIn($oauth->getToken()->OcApplication->User, true);
    }

    /**
     *
     * @param Doctrine_Collection|Doctrine_Record $mixed
     *
     * */
    public function getFormattedEntities($mixed)
    {
        $r = [];

        // Doctrine_Record
        if ($mixed instanceof Doctrine_Record)
            $r = $this->getFormattedEntity($mixed);

        // Doctrine_Collection
        if ($mixed instanceof Doctrine_Collection)
            foreach ($mixed as $record)
                $r[] = $this->getFormattedEntity($record);

        return $r;
    }

    public function getFormattedEntity(Doctrine_Record $record)
    {
        if ($record === NULL)
            return [];

        $arr = [];
        foreach ($this->getFieldsEquivalents() as $api => $db) {
            // case of "not implemented" fields
            if ($db === NULL) {
                $arr = $this->setResultValue(NULL, $api, $arr);
                continue;
            }

            // direct fields from the root entity
            if (strpos($db, '.') === false) {
                $field = preg_replace('/^!/', '', $db);
                $value = $record->$field instanceof Doctrine_Collection || $record->$field instanceof Doctrine_Record ? $this->getDoctrineFlatData($record->$field) : $record->$field;
                $arr = $this->setResultValue(
                    $this->toggleBoolean($value, $field != $db), $api, $arr);
                continue;
            }

            // prepare data
            $subEntities = explode('.', preg_replace('/^!/', '', $db));
            $property = array_pop($subEntities);

            // get back the last Doctrine_Record child
            $rec = $record;
            foreach ($subEntities as $entity) {
                $rec = $rec->$entity;
            }

            // in case of unexistent property/object before getting back $property
            if (!( $rec instanceof Doctrine_Record || $rec instanceof Doctrine_Collection )) {
                $arr = $this->setResultValue(NULL, $api, $arr);
                continue;
            }

            $value = $rec->$property instanceof Doctrine_Collection || $rec->$property instanceof Doctrine_Record ? $this->getDoctrineFlatData($rec->$property) : $rec->$property;

            $value = $rec->$property;
            // find out the targeted property to render
            $arr = $this->setResultValue(
                $this->toggleBoolean($value, preg_match('/^!/', $db) === 1), $api, $arr
            );
        }

        return $this->postFormatEntity($arr);
    }

    /**
     * Post-process the formatted-as-expected-by-the-API results
     *
     * @param array $entity the pre-formatted entities
     * @return array post-formatted entities
     *
     */
    protected function postFormatEntity(array $entity)
    {
        return $entity;
    }

    public function buildQuery(array $query)
    {
        if (!is_array($query['criteria']))
            $query['criteria'] = [];

        $q = $this->buildInitialQuery();

        $this->buildQueryCondition($q, $query['criteria']);
        $this->buildQuerySorting($q, $query['sorting']);
        $this->buildQueryLimit($q, $query['limit']);
        $this->buildQueryPagination($q, $query['page']);

        return $q;
    }

    protected function buildQuerySorting(Doctrine_Query $q, array $sorting = [])
    {
        $orderBy = '';
        foreach ($sorting as $field => $direction) {
            if (!in_array($field, $this->getFieldsEquivalents()))
                continue;
            $orderBy .= array_search($field, $this->getFieldsEquivalents()) . ' ' . $direction . ' ';
        }

        return $orderBy ? $q->orderBy($orderBy) : $q;
    }

    protected function buildQueryLimit(Doctrine_Query $q, $limit = NULL)
    {
        if ($limit !== NULL)
            $q->limit($limit);
        return $q;
    }

    protected function buildQueryPagination(Doctrine_Query $q, $page = 1)
    {
        if ($page !== NULL)
            $q->offset($page - 1);
        return $q;
    }

    protected function buildQueryCondition(Doctrine_Query $q, array $criterias = [])
    {
        $fields = array_merge($this->getFieldsEquivalents(), $this->getHiddenFieldsEquivalents());
        $operands = $this->getOperandEquivalents();

        foreach ($criterias as $criteria => $search)
            if (isset($fields[$criteria]) && isset($search['value'])) {
                $field = strpos('.', $fields[$criteria]) === false ? $q->getRootAlias() . '.' . $fields[$criteria] . ' ' : $fields[$criteria] . ' ';
                $compare = $operands[$search['type']];
                $args = [$search['value']];
                $dql = '?';

                if (is_array($compare)) {
                    $args = $compare[1]($search['value']);
                    if (is_array($args)) {
                        $dql = [];
                        foreach ($args as $arg)
                            $dql[] = '?';
                        $dql = implode(',', $dql);
                    }
                }

                $q->andWhere($field . ' ' . $compare[0] . ' ' . $dql, $args);
            }

        return $q;
    }

    public function countResults(array $query)
    {
        return $this->buildQuery($query)->count();
    }

    public function getOperandEquivalents()
    {
        return [
            'contain' => ['ILIKE', function($s) {
                    return "%$s%";
                }],
            'not contain' => ['NOT ILIKE', function($s) {
                    return "%$s%";
                }],
            'equal' => '=',
            'not equal' => '!=',
            'start with' => ['ILIKE', function($s) {
                    return "$s%";
                }],
            'end with' => ['ILIKE', function($s) {
                    return "%$s";
                }],
            'empty' => ['=', function($s) {
                    return '';
                }],
            'not empty' => ['!=', function($s) {
                    return '';
                }],
            'in' => ['IN', function($s) {
                    return implode(',', $s);
                }],
            'not in' => ['NOT IN', function($s) {
                    return implode(',', $s);
                }],
            'greater' => '>',
            'greater or equal' => '>=',
            'lesser' => '<',
            'lesser or equal' => '<=',
        ];
    }

    /**
     * Sets a value in an array depending on its string description
     * using "." as a dimension separator
     *
     * @param mixed $value
     * @param string $key    description to the position in $result where to put $value
     * @param array $result  the array to modify
     *
     * */
    private function setResultValue($value, $key, array $result)
    {
        $tmp = &$result;
        foreach (explode('.', $key) as $field) {
            if (!isset($tmp[$field]))
                $tmp[$field] = [];
            $tmp = &$tmp[$field];
        }
        $tmp = $value;

        return $result;
    }

    private function toggleBoolean($value, $bool)
    {
        return $bool ? !$value : $value;
    }

    private function getDoctrineFlatData($data)
    {
        if (!$data instanceof Doctrine_Collection && !$data instanceof Doctrine_Record)
            throw new liOnlineSaleException('Doctrine_Collection or Doctrine_Record expected, ' . get_class($data) . ' given.');

        $fct = function(Doctrine_Record $rec) {
            $arr = [];
            foreach ($rec->getTable()->getColumns() as $colname => $coldef)
                if (!is_object($rec->$colname))
                    $arr[$colname] = $rec->$colname;
            return $arr;
        };

        $res = [];
        if ($data instanceof Doctrine_Collection) {
            foreach ($data as $rec) {
                $res[] = $fct($rec);
            }
        } else
            $res = $fct($rec);

        return $res;
    }

    public function getFieldsEquivalents()
    {
        return static::$FIELD_MAPPING;
    }

    public function getHiddenFieldsEquivalents()
    {
        return static::$HIDDEN_FIELD_MAPPING;
    }
}
