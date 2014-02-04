<?php
/**
 * @package Newscoop
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Connection;

/**
 * Install newscoop with command line
 */
class GenerateORMSchemaCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('newscoop:generateOrmSchema')
            ->setDescription('Generates SQL for an ORM Entity')
            ->addOption('alter', null, InputOption::VALUE_NONE, 'If set, the task will output ALTER SQL')
            ->addArgument('entity', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Single or Multiple Entities');
    }

    /**
     * @see Console\Command\Command
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $em = $container->getService('em');

        $entityMetaClasses = array();

        foreach ($input->getArgument('entity') as $entity) {
            $entityMetaClasses[] = $em->getClassMetadata($entity);
        }

        $tool = new \Doctrine\ORM\Tools\SchemaTool($em);

        if ($input->getOption('alter')) {
            $schema = $tool->getUpdateSchemaSql($entityMetaClasses, true);
        } else {
            $schema = $tool->getCreateSchemaSql($entityMetaClasses);
        }

        $output->writeln($schema);
    }
}
