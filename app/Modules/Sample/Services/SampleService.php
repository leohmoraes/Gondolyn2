<?php namespace App\Modules\Sample\Services;

class SampleService implements SampleInterface
{
    public $output;

    public function dataModifier($modelData)
    {
        $data = [
            'superman' => $modelData->id,
            'birthday' => $modelData->created_at
        ];

        return $data;
    }

    public function processData($prototypeData, $sysData)
    {
        $data                   = $sysData['config'];
        $data['prototype']      = $prototypeData;
        $data['notification']   = $sysData['notification'];
        $data['welcome']        = $sysData['welcome'];

        return $data;
    }

}
