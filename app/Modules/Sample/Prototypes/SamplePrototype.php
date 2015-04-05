<?php namespace App\Modules\Sample\Prototypes;

/**
 * The purpose of a prototype is to handle all buisness logic
 * enabling solid TDD.
 */
class SamplePrototype extends \Prototype {

    public $output;

    public function __construct()
    {
        parent::__construct();
    }

    public function dataModifier($modelData)
    {
        $data = [
            'superman' => $modelData->id,
            'birthday' => $modelData->created_at
        ];

        $this->output = $data;

        return $this;
    }

    public function processData($prototypeData, $sysData)
    {
        $data                   = $sysData['config'];
        $data['prototype']      = $prototypeData;
        $data['notification']   = $sysData['notification'];
        $data['welcome']        = $sysData['welcome'];

        $this->output = $data;

        return $this;
    }

}