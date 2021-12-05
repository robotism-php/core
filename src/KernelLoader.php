<?php


namespace Robotism\Core;


use Robotism\Contract\Registry\Kernel;

class KernelLoader
{
    protected array $kernels=[];
    public function add($kernel){
        if(is_subclass_of($kernel,Kernel::class)){
            if(!in_array($kernel,$this->kernels)){
                array_push($this->kernels,$kernel);
            }
        }
    }
    public function getComponent($component_type):array
    {
        $result=$this->getComponents([$component_type])[$component_type];
        return is_array($result)?$result:[];
    }
    public function getComponents($component_types): array
    {
        $components=[];
        foreach ($component_types as $type){
            $components[$type]=[];
        }
        $all_components=array_unique(self::getComponentsRecursion($this->kernels));

        foreach ($all_components as $component){
            foreach ($component_types as $type){
                if(is_subclass_of($component,$type)){
                    array_push($components[$type],$component);
                }
            }
        }
        return $components;
    }
    protected static function getComponentsRecursion($kernels): array
    {
        $components_cache=[];
        foreach ($kernels as $kernel){
            if(is_subclass_of($kernel,Kernel::class)){

                $kernel_components=$kernel::getComponents();
                foreach($kernel_components as $component){
                    if(is_subclass_of($component,Kernel::class)){
                        $components_cache=array_merge($components_cache,self::getComponentsRecursion($component));
                    }else{
                        array_push($components_cache,$component);
                    }
                }
            }
        }
        return $components_cache;
    }
}