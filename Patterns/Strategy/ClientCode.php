<?php
namespace Strategy;

use Strategy\Context;

class ClientCode{
    /**
     * The client code picks a concrete strategy and passes it to the context. The
     * client should be aware of the differences between strategies in order to make
     * the right choice.
     */
    
    public function doAlgorithm()
    {
        $context = new Context(new ConcreteStrategyA());
        echo "Client: Strategy is set to normal sorting.\n";
        $context->doSomeBusinessLogic();
        
        echo "\n";
        
        echo "Client: Strategy is set to reverse sorting.\n";
        $context->setStrategy(new ConcreteStrategyB());
        $context->doSomeBusinessLogic();

        return true;
    }
    
}