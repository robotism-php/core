<?php


namespace Robotism\Core\Console;



class Kernel implements \Robotism\Contract\Registry\Kernel
{
    public static function getComponents(): array
    {
        return [
            \Robotism\Core\Console\Architect\MakeCommand::class
        ];
    }
}