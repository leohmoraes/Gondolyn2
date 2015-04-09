<?php namespace Prototype;

class Prototype
{
    public $output;

    public function __construct()
    {
        // nothing here yet
    }

    public function toString($delimiter = ',')
    {
        return (string) implode($delimiter, $this->output);
    }

    public function toJson()
    {
        return (string) json_encode($this->output);
    }

    public function toArray()
    {
        return (array) $this->output;
    }

}
