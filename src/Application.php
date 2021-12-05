<?php


namespace Robotism\Core;


use Robotism\Contract\Application\Wrapper;
use Robotism\Contract\Registry\Kernel;
use RobotismPhp\Command\Command as ChatCommand;
use RobotismPhp\Command\Commander;
use RobotismPhp\Command\Factory\TransportFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Application implements \Robotism\Contract\Application\Application
{
    protected string $app_root='';
    protected Wrapper $wrapper;
    protected KernelLoader $loader;
    protected EventDispatcher $dispatcher;
    public function __construct(){
        $this->loader=new KernelLoader();
        $this->loader->add(\Robotism\Core\Console\Kernel::class);
    }
    public static function create(): self
    {
        return new self();
    }

    public function withWrapper(Wrapper $wrapper):self
    {
        $this->wrapper=$wrapper;
        return $this;
    }

    public function withKernel(Kernel $kernel):self
    {
        $this->loader->add($kernel);
        return $this;
    }

    public function at(string $directory):self
    {
        $this->app_root=$directory;
        return $this;
    }

    public function run()
    {
        $this->wrapper->mount($this);
        $components=$this->loader->getComponents([
            EventSubscriberInterface::class,
            ChatCommand::class
        ]);
        foreach($components[EventSubscriberInterface::class] as $subscriber){
            $this->dispatcher->addSubscriber(new $subscriber($this));
        }
        if($this->wrapper instanceof TransportFactory){
            $commander=new Commander($this->wrapper);
            foreach($components[ChatCommand::class] as $command){
                $commander->add(new $command($this));
            }
            $this->dispatcher->addListener('message',[$commander,'run'],-9999);
        }
        $this->wrapper->run();

    }

    public function runConsole(){
        $console=new \Symfony\Component\Console\Application();
        $commands=$this->loader->getComponent(Command::class);
        $console->setName('Robotism Framework');
        $console->setVersion('Beta');

        $command_instances=[];
        foreach($commands as $command){
            array_push($command_instances,new $command($this));
        }
        $console->addCommands($command_instances);
        $console->run();
    }

    public function getAppRoot(): string
    {
        return $this->app_root;
    }
}