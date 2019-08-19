<?php


namespace App\helpers;


use \Exception;

class ValidateRequestHelper
{
    /**
     * @var array data from $_REQUEST
     */
    protected $request;
    /**
     * @var array required params
     */
    protected $required;
    /**
     * @var array errors
     */
    protected $errors = [];

    /**
     * ValidateRequestHelper constructor.
     * @param array $request
     */
    public function __construct(array $request)
    {
        $this->request = $request;

        $this->required = [
            'action' => [
                'isAvailable',
                'getShortages'
            ]
            ,
            'start' => 'date',
            'end' => 'date'
        ];

        if (!empty($this->request['action']) &&
            $this->request['action'] === 'isAvailable') {
            $this->required['equipmentId'] = 'int';
            $this->required['quantity'] = 'int';
        }
    }

    /**
     * @throws Exception
     */
    public function validate() :void
    {

        if (!empty($this->request['action']) &&
            !in_array($this->request['action'], $this->required['action'], false)
        ) {
            $this->errors[] = "Unsupported action '{$this->request['action']}'";
        }

        foreach ($this->required as $requiredParam => $type) {
            if (!in_array($requiredParam, array_keys($this->request), false)) {
                $this->errors[] = "Missing '{$requiredParam}' param";
                continue;
            }
            if ($type === 'date' && !strtotime($this->request[$requiredParam])) {
                $this->errors[] = "'{$requiredParam}' is not a valid date.";
            }

            if ($type === 'int' && (
                (int)$this->request[$requiredParam] === 0 ||
                filter_var($this->request[$requiredParam], FILTER_VALIDATE_INT) === false
                )
            ) {
                $this->errors[] = "'{$requiredParam}' should be a positive integer.";
            }
        }

        if (!empty($this->errors)) {
            throw new Exception(implode('; ', $this->errors), 100500);
        }
    }
}
