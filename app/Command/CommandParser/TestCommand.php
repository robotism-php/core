<?php
namespace App\Command\CommandParser;

class TestCommand extends \Robotism\CommandParser\Command
{
    function configure()
    {
        $this->setSignature('命令 [param:参数]');
    }
    function execute(\Robotism\CommandParser\Input\InputInterface $input, \Robotism\CommandParser\Output\OutputInterface $output)
    {
        //Do something here!
    }
}