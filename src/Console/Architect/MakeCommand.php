<?php

namespace Robotism\Core\Console\Architect;

use PhpParser\Builder\Class_;
use PhpParser\Builder\Method;
use PhpParser\Builder\Param;
use PhpParser\Comment;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Stmt\Nop;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

class MakeCommand extends Maker
{
    protected function getMakerConfigure(InputInterface $parameter): array
    {
        return [
            'directory' => '\\app\\Command',
            'namespace' => 'App\\Command',
            'content' => (new Class_($parameter->getArgument('name')))
                ->extend('\\Robotism\\CommandParser\\Command')
                ->addStmt(
                    (new Method('configure'))
                        ->addStmt(
                            new Expression(
                                new MethodCall(
                                    new Variable('this'),
                                    'setSignature',
                                    [
                                        new Arg(
                                            new \PhpParser\Node\Scalar\String_('命令 [param:参数]')
                                        )
                                    ]
                                )
                            )
                        )
                )
                ->addStmt((new Method('execute'))
                    ->addParam(
                        (new Param('input'))
                            ->setType(
                                new Identifier('\\Robotism\\CommandParser\\Input\\InputInterface')
                            )
                    )
                    ->addParam(
                        (new Param('output'))
                            ->setType(
                                new Identifier('\\Robotism\\CommandParser\\Output\\OutputInterface')
                            )
                    )
                    ->addStmt(
                        (new Nop([
                            'comments' => [
                                new Comment('//Do something here!')
                            ]
                        ]))
                    ))
        ];
    }

    protected function configure()
    {
        $this
            ->setName('make:command')
            ->setDescription('Make a new chat-command for the bot')
            ->addArgument('name', InputArgument::REQUIRED, 'The command name you want to make')
            ->addArgument('namespace', InputArgument::OPTIONAL, 'Which sub-namespace you want to place the file to')
            ->addOption('force','f');
    }

    protected function parameterCheck(InputInterface $parameter)
    {
        $namespace=$parameter->getArgument('namespace');
        if($namespace!=null and preg_match("/^[A-Za-z\\\\]+$/",$parameter)===false){
            throw new \Exception('Invalid namespace.');
        }
        $class_name=$parameter->getArgument('name');
        if(preg_match("/^[A-Za-z]+$/",$class_name)===false){
            throw new \Exception('Invalid name.');
        }
    }
}