<?php

namespace PHPIntegration;

class TestParameter
{
    private $id;
    private $validator;
    private $def;
    private $builder;

    public function __construct(string $name, $default, callable $builder, callable $validator)
    {
        $this->id = $name;
        $this->validator = $validator;
        $this->def = $default;
        $this->builder = $builder;
    }

    public function name() : string
    {
        return $this->id;
    }
    
    public function default()
    {
        return $this->def;
    }

    public function validate(string $value)
    {
        return $this->validator($value);
    }
    
    public function build(string $value)
    {
        return $this->builder($value);
    }
}
