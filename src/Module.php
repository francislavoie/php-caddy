<?php

namespace PhpCaddy;

interface Module
{
    /**
     * @return ModuleInfo
     */
    public static function module() : ModuleInfo;
}
