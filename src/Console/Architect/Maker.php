<?php


namespace Robotism\Core\Console\Architect;


use PhpParser\PrettyPrinter\Standard;
use Robotism\Contract\Application\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Maker extends Command
{
    public Application $application;
    protected abstract function parameterCheck(InputInterface $parameter);
    protected abstract function getMakerConfigure(InputInterface $parameter);
    public function __construct(Application $application)
    {
        $this->application=$application;
        parent::__construct(null);
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->parameterCheck($input);
        $configure=$this->getMakerConfigure($input);
        $code_printer=new Standard();
        $user_namespace=$input->getArgument('namespace');
        $user_namespace=$user_namespace!=''?"\\".$user_namespace:'';
        $code = '<?php' ."\n" . $code_printer->prettyPrint([
            (new \PhpParser\Builder\Namespace_($configure['namespace'].$user_namespace))->getNode(),
            $configure['content']->getNode()
        ]);
        if(!file_exists($this->application->getAppRoot().$configure['directory'].'/'.$user_namespace))
            mkdir($this->application->getAppRoot().'/'.$configure['directory'].'/'.$user_namespace,true);
        if(file_exists($this->application->getAppRoot().$configure['directory'].''.$user_namespace.'/'.$input->getArgument('name').'.php') && !$input->getOption('force')){
            throw new \Exception('File existed.Use -f option to overwrite.');
        }
        file_put_contents($this->application->getAppRoot().$configure['directory'].''.$user_namespace.'/'.$input->getArgument('name').'.php',$code);
        $output->write('Command Created!');
        return 0;
    }
}