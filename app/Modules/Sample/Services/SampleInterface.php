<?php namespace App\Modules\Sample\Services;

interface SampleInterface
{
    public function dataModifier($modelData);
    public function processData($prototypeData, $sysData);
}
