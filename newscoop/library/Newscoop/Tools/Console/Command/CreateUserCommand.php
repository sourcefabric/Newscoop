<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create oauth client
 * Example usage: user:create test@example.org testpassword testuser "Test Name" "Test Surname" true null 1
 */
class CreateUserCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('user:create')
            ->setDescription('Create New user.')
            ->addArgument('email', InputArgument::REQUIRED, 'User email')
            ->addArgument('pasword', InputArgument::REQUIRED, 'User password')
            ->addArgument('username', InputArgument::REQUIRED, 'User login name')
            ->addArgument('firstName', InputArgument::OPTIONAL, 'User first name')
            ->addArgument('lastName', InputArgument::OPTIONAL, 'User last name')
            ->addArgument('is_public', InputArgument::OPTIONAL, 'User is public', true)
            ->addArgument('publication', InputArgument::OPTIONAL, 'Publication number assigned to user')
            ->addArgument('roles', InputArgument::IS_ARRAY | InputArgument::OPTIONAL, 'User roles (separate multiple names with a space)')
            ->addOption('isAdmin', null, InputOption::VALUE_NONE, 'If user should have access to admin interface');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $userService = $container->getService('user');
        $userService->createUser(
            $input->getArgument('email'),
            $input->getArgument('pasword'),
            $input->getArgument('username'),
            $input->getArgument('firstName'),
            $input->getArgument('lastName'),
            $input->getArgument('publication'),
            $input->getArgument('is_public'),
            $input->getArgument('roles'),
            $input->getOption('isAdmin')
        );
    }
}
