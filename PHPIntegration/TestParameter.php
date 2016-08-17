<?php

namespace PHPIntegration;

class TestParameter
{
    private $id;
    private $validator;
    private $def;
    private $rawdef;
    private $builder;

    public function __construct(
        string $name,
        $default,
        string $rawDefault,
        callable $builder,
        callable $validator
    ) {
        $this->id = $name;
        $this->validator = $validator;
        $this->def = $default;
        $this->builder = $builder;
        $this->rawdef = $rawDefault;
    }

    public function name() : string
    {
        return $this->id;
    }
    
    public function default()
    {
        return $this->def;
    }

    public function rawDefault()
    {
        return $this->rawdef;
    }

    public function validate(string $value)
    {
        return call_user_func($this->validator, $value);
    }
    
    public function build(string $value)
    {
        return call_user_func($this->builder, $value);
    }
}
