<?php namespace App\Modules\Sample\Services;

interface SampleInterface
{
    public function getSamples($sortby);
    public function serviceInformation($config);
}
