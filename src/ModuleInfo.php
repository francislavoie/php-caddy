<?php

namespace PhpCaddy;

class ModuleInfo
{
    /**
     * The unique identifier for the module.
     *
     * e.g. "foo.bar"
     *
     * @var string
     */
    protected string $id;

    /**
     * ModuleInfo constructor.
     *
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function id() : string
    {
        return $this->id;
    }

    /**
     * The name of the module, i.e. the last element of the module ID
     *
     * @return string
     */
    public function name() : string
    {
        if ($this->id === "") {
            return "";
        }

        $parts = explode(".", $this->id);
        return $parts[array_key_last($parts)];
    }
}
