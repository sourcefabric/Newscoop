<?php

namespace spec\Newscoop\Tools\Console\Command;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;

class GenerateORMSchemaCommandSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Tools\Console\Command\GenerateORMSchemaCommand');
    }

    function it_accepts_a_single_entity()
    {
    	$input = new ArrayInput(
    		array(
    			'entity' => '\Newscoop\Entity\AutoId',
    			'alter'
    		),
    		new InputDefinition(array(
    			new InputArgument('entity', InputArgument::REQUIRED, 'Single or Multiple Entities'),
    			new InputOption('alter', null, InputOption::VALUE_NONE, 'If set, the task will output ALTER SQL', null)
    		))
    	);
    	$output = new NullOutput();
    	$this->execute($input, $output)->shouldReturn(true);
    }
}
