<?php

namespace ChainOfResponsibility;

/**
 * The Handler interface declares a method for building the chain of handlers.
 * It also declares a method for executing a request.
 */
interface Handler
{
    public function setNext(Handler $handler): Handler;
    public function getNext(): ?Handler;

    public function handle(): ?string;
}

/**
 * The default chaining behavior can be implemented inside a base handler class.
 */
abstract class AbstractHandler implements Handler
{
    /**
     * @var Handler
     */
    private $nextHandler;

    public function setNext(Handler $handler): Handler
    {
        $this->nextHandler = $handler;
        return $handler;
    }

    /**
     * Get the value of nextHandler
     *
     * @return  Handler
     */
    public function getNext(): ?Handler
    {
        return $this->nextHandler;
    }

    public function handle(): ?string
    {
        if ($this->nextHandler) {
            return $this->nextHandler->handle();
        }

        return null;
    }
}

/**
 * All Concrete Handlers either handle a request or pass it to the next handler
 * in the chain.
 */
class ServicoGerarNotaFiscal extends AbstractHandler
{
    public function handle(): ?string
    {
        echo get_class(). ": executei o servico GerarNotaFiscal.\n";
        return parent::handle();
    }
}

class ServicoConsultarSituacao extends AbstractHandler
{
    public function handle(): ?string
    {
        echo get_class() . ": executei o servico ConsultarSituacao.\n";
        return parent::handle();
    }
}

class ServicoConsultarLote extends AbstractHandler
{
    public function handle(): ?string
    {
        echo get_class() . ": executei o servico ConsultarLote.\n";
        return parent::handle();
    }
}

class ServicoConsultarRps extends AbstractHandler
{
    public function handle(): ?string
    {
        echo get_class() . ": executei o servico servicoConsultarRps.\n";
        return parent::handle();
    }
}

class ServicoCancelarNotaFiscal extends AbstractHandler
{
    public function handle(): ?string
    {
        echo get_class() . ": executei o servico servicoCancelarNotaFiscal.\n";
        return parent::handle();
    }
}

/**
 * - servicoGerarNotaFiscal
 * - servicoCancelarNotaFiscal
 * - servicoConsultar
 * - servicoConsultarRps
 */
function buildExecutionOrder(string $service): array
{
    $stepConsultar = [
        '0' => 'servicoConsultarLote',
        '1' => 'servicoConsultarSituacao'
    ];

    $stepConsultarRps = [
        '0' => 'servicoConsultarRps',
        '1' => 'servicoImprimir'
    ];

    $stepEmitir = [
        '0' => 'servicoGerarNotaFiscal',
        '1' => 'servicoConsultarSituacao',
        '2' => 'servicoConsultarLote',
        '3' => 'servicoImprimir'
    ];

    $stepCancelar = [
        '0' => 'servicoCancelarNotaFiscal',
        '1' => 'servicoConsultarSituacao',
        '2' => 'servicoConsultarLote',
        '3' => 'servicoImprimir'
    ];
    $stepsConfig = [
        'servicoGerarNotaFiscal' => array_unique($stepEmitir),
        'servicoCancelarNotaFiscal' => array_unique($stepCancelar),
        'servicoConsultar' => array_unique($stepConsultar),
        'servicoConsultarRps' => array_unique($stepConsultarRps)
    ];

    return $stepsConfig[$service];

}

function loadChainOfResponsability(array $chain): Handler
{
    $service = null;
    $parent = null;

    foreach ($chain as $value) {
        if (is_null($service)) {
            $service = $value->setNext($value);
            $parent = $service;
        } else {
            $service = $service->setNext($value);
        }
    }
    return $parent;
}

function stepsBuilder(array $step): array
{
    foreach ($step as $key => $value) {
        $load[$value] = stepFactory($value);
    }

    return array_filter($load);
}

function stepFactory(string $step): ?Handler
{
    $class = "ChainOfResponsibility\\" . $step;
    if (class_exists($class)) {
        return new $class;
    }
    return null;
}

/**
 * The client code is usually suited to work with a single handler. In most
 * cases, it is not even aware that the handler is part of a chain.
 */
function clientCode(Handler $handler): void
{
    echo "Emissor: Oi amigo, vou pedir pro serviço responsável conversar com a equipe! :)\n";
    $result = $handler->handle();
    echo "Emissor: Pronto! :)\n";
}
$order = buildExecutionOrder('servicoCancelarNotaFiscal');
$chain = stepsBuilder($order);
echo "Execution Order: " . json_encode($order) . "\n\n";
echo "Administrativo: Por favor, execute o " . $order[0] . "!!\n";

clientCode(loadChainOfResponsability($chain));
echo "Administrativo: Demoradinho neh? Ta entregando pouco";
echo "\n";
