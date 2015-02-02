<?php
/*
 * This file is part of the NewscooPluginGeneratorBundle package.
 *
 * (c) Mark Lewis <mark.lewis@sourcefabric.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Newscoop\PluginGeneratorBundle\Command;
use Newscoop\PluginGeneratorBundle\Command\GeneratorPluginCommand;
use Newscoop\PluginGeneratorBundle\Generator\BundleGenerator;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\HttpKernel\KernelInterface;

use Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator;
use Sensio\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;

/**
 * Generates bundles.
 *
 * @author Mark Lewis <mark.lewis@sourcefabric.org>
 */
class GeneratePluginBundleCommand extends GeneratorPluginCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputOption('vendor', '', InputOption::VALUE_REQUIRED, 'The vendor of the plugin to create'),
                new InputOption('namespace', '', InputOption::VALUE_REQUIRED, 'The namespace of the plugin to create'),
                new InputOption('dir', '', InputOption::VALUE_REQUIRED, 'The directory where to create the bundle'),
                new InputOption('bundle-name', '', InputOption::VALUE_REQUIRED, 'The required bundle name'),
                new InputOption('plugin-name', '', InputOption::VALUE_REQUIRED, 'The required plugin name'),
                new InputOption('format', '', InputOption::VALUE_REQUIRED, 'Use the format for configuration files (php, xml, yml, or annotation)'),
                new InputOption('structure', '', InputOption::VALUE_NONE, 'Whether to generate the whole directory structure'),
                new InputOption('admin', '', InputOption::VALUE_NONE, 'Whether to generate a plugin admin structure'),
                new InputOption('zip', '', InputOption::VALUE_NONE, 'Whether to generate a private plugin zip archive'),
            ))
            ->setDescription('Generates a Newscoop Plugin bundle')
            ->setHelp(<<<EOT
The <info>generate:plugin-bundle</info> command helps you generates new Newscoop Plugin bundles.

By default, the command interacts with the developer to tweak the generation.
Any passed option will be used as a default value for the interaction
(<comment>--namespace</comment> is the only one needed if you follow the
conventions):

<info>php app/console generate:plugin-bundle --plugin-name=NewFeature</info>

If you want to disable any user interaction, use <comment>--no-interaction</comment> but don't forget to pass all needed options:

<info>php app/console generate:plugin-bundle --plugin-name=NewFeature --dir=src [--bundle-name=...] --no-interaction</info>

Note that the plugin-name should NOT include the words "Plugin" or "Bundle".
EOT
            )
            ->setName('generate:plugin-bundle')
        ;
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When namespace doesn't end with Bundle
     * @throws \RuntimeException         When bundle can't be executed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        if ($input->isInteractive()) {
            if (!$dialog->askConfirmation($output, $dialog->getQuestion('Do you confirm generation', 'yes', '?'), true)) {
                $output->writeln('<error>Command aborted</error>');

                return 1;
            }
        }

        foreach (array('plugin-name', 'namespace', 'dir') as $option) {
            if (null === $input->getOption($option)) {
                throw new \RuntimeException(sprintf('The "%s" option must be provided.', $option));
            }
        }

        // validate the namespace
        $vendor = Validators::validateVendor($input->getOption('vendor'), false);
        $pluginName = Validators::validatePluginName($input->getOption('plugin-name'), false);
        $namespace = Validators::validateBundleNamespace($input->getOption('namespace'), false);
        if (!$bundle = $input->getOption('bundle-name')) {
            $bundle = strtr($namespace, array('\\' => ''));
        }
        $bundle = Validators::validateBundleName($bundle);
        $dir = Validators::validateTargetDir($input->getOption('dir'), $bundle, $namespace);
        if (null === $input->getOption('format')) {
            $input->setOption('format', 'annotation');
        }
        $format = Validators::validateFormat($input->getOption('format'));
        $structure = $input->getOption('structure');
        $admin = $input->getOption('admin');
        $zip = $input->getOption('zip');

        $dialog->writeSection($output, 'Bundle generation');

        if (!$this->getContainer()->get('filesystem')->isAbsolutePath($dir)) {
            $dir = getcwd().'/'.$dir;
        }

        $generator = $this->getGenerator();
        $generator->generate(
            $vendor,
            $pluginName, 
            $namespace, 
            $bundle, 
            $dir, 
            $format, 
            $structure, 
            $admin, 
            $zip
        );

        $output->writeln('Generating the bundle code: <info>OK</info>');

        $errors = array();
        $runner = $dialog->getRunner($output, $errors);

        $dialog->writeGeneratorSummary($output, $errors);

        $output->writeln('You must run the plugin install command manually: <comment>php application/console plugins:install '.strtolower($vendor).'/'.strtolower($pluginName).'-plugin-bundle</comment>');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the Newscoop Plugin bundle generator');

        // vendor 
        $vendor = null;
        try {
            // validate the plugin name
            $vendor = $input->getOption('vendor') ? Validators::validateVendor($input->getOption('vendor'), false) : null;
        } catch (\Exception $error) {
            $output->writeln($dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (null === $vendor) {
            $output->writeln(array(
                '',
                'Your application code must be written in <comment>bundles</comment>. This command helps',
                'you generate the bundle name and namespace easily.',
                '',
                'Each bundle is hosted under a namespace (like <comment>Google/YoutubePluginBundle</comment>).',
                'The vendor should be a "vendor" name like your company name',
                '(Google in the above example)',
                'It should be camel cased with no spaces, and should NOT contain the words "Plugin"',
                'or "Bundle" (these will be appended automatically)',
                '',
            ));

            $acceptedVendor = false;
            while (!$acceptedVendor) {
                $vendor = $dialog->askAndValidate(
                    $output,
                    $dialog->getQuestion('Vendor', $input->getOption('vendor')),
                    function ($vendor) use ($dialog, $output) {
                        return Validators::validateVendor($vendor, false);
                    },
                    false,
                    $input->getOption('vendor')
                );

                // mark as accepted, unless they want to try again below
                $acceptedVendor = true;
            }
            $input->setOption('vendor', $vendor);
        }

        // pluginName
        $pluginName = null;
        try {
            // validate the plugin name
            $pluginName = $input->getOption('plugin-name') ? Validators::validatePluginName($input->getOption('plugin-name'), false) : null;
        } catch (\Exception $error) {
            $output->writeln($dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (null === $pluginName) {
            $output->writeln(array(
                '',
                'Your application code must be written in <comment>bundles</comment>. This command helps',
                'you generate the bundle name and namespace easily.',
                '',
                'Each bundle is hosted under a namespace (like <comment>Newscoop/YoutubePluginBundle</comment>).',
                'The plugin name should be a project name, product name, or your client name.',
                'It should be camel cased with no spaces, and should NOT contain the words "Plugin"',
                'or "Bundle" (these will be appended automatically)',
                '',
            ));

            $acceptedPluginName = false;
            while (!$acceptedPluginName) {
                $pluginName = $dialog->askAndValidate(
                    $output,
                    $dialog->getQuestion('Plugin name', $input->getOption('plugin-name')),
                    function ($pluginName) use ($dialog, $output) {
                        return Validators::validatePluginName($pluginName, false);
                    },
                    false,
                    $input->getOption('plugin-name')
                );

                // mark as accepted, unless they want to try again below
                $acceptedPluginName = true;
            }
            $input->setOption('plugin-name', $pluginName);
        }

        // namespace
        $namespace = ucwords($vendor) ."/" . $pluginName . 'PluginBundle';
        $input->setOption('namespace', $namespace);

        // bundle name
        $bundle = ucwords($vendor) . $pluginName . 'PluginBundle';
        $input->setOption('bundle-name', $bundle);

        // target dir
        $dir = null;
        try {
            $dir = $input->getOption('dir') ? Validators::validateTargetDir($input->getOption('dir'), $bundle, $namespace) : null;
        } catch (\Exception $error) {
            $output->writeln($dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (null === $dir) {
            $dir = dirname($this->getContainer()->getParameter('kernel.root_dir')).'/plugins/'.ucwords($vendor);

            $output->writeln(array(
                '',
                'The bundle can be generated anywhere. The suggested default directory uses',
                'the plugin directory for this Newscoop instance.',
                '',
            ));
            $dir = $dialog->askAndValidate($output, $dialog->getQuestion('Target directory', $dir), function ($dir) use ($bundle, $namespace) { return Validators::validateTargetDir($dir, $bundle, $namespace); }, false, $dir);
            $input->setOption('dir', $dir);
        }

        // format
        $format = null;
        try {
            $format = $input->getOption('format') ? Validators::validateFormat($input->getOption('format')) : null;
        } catch (\Exception $error) {
            $output->writeln($dialog->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error'));
        }

        if (null === $format) {
            $output->writeln(array(
                '',
                'Determine the format to use for the generated configuration.',
                '',
            ));
            $format = $dialog->askAndValidate($output, $dialog->getQuestion('Configuration format (yml, xml, php, or annotation)', $input->getOption('format')), array('Newscoop\PluginGeneratorBundle\Command\Validators', 'validateFormat'), false, $input->getOption('format'));
            $input->setOption('format', $format);
        }

        // optional files to generate
        $output->writeln(array(
            '',
            'To help you get started faster, the command can generate some',
            'code snippets for you.',
            '',
        ));

        $structure = $input->getOption('structure');
        if (!$structure && $dialog->askConfirmation($output, $dialog->getQuestion('Do you want to generate the whole directory structure', 'no', '?'), false)) {
            $structure = true;
        }
        $input->setOption('structure', $structure);

        $admin = $input->getOption('admin');
        if (!$admin && $dialog->askConfirmation($output, $dialog->getQuestion('Do you want to generate an Admin structure', 'no', '?'), false)) {
            $admin = true;
        }
        $input->setOption('admin', $admin);

        $zip = $input->getOption('zip');
        if (!$zip && $dialog->askConfirmation($output, $dialog->getQuestion('Do you want to generate a private plugin zip', 'no', '?'), false)) {
            $zip = true;
        }
        $input->setOption('zip', $zip);

        // summary
        $output->writeln(array(
            '',
            $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
            '',
            sprintf("You are going to generate a \"<info>%s\\%s</info>\" bundle\nin \"<info>%s</info>\" using the \"<info>%s</info>\" format.", $namespace, $bundle, $dir, $format),
            '',
        ));
    }

    protected function createGenerator()
    {
        return new BundleGenerator($this->getContainer());
    }
}
