<?php
/**
 * @author   Demin Yin <deminy@deminy.net>
 * @license  MIT license
 */

use Behat\Behat\Context\ClosuredContextInterface;
use Behat\Behat\Context\BehatContext;
use Symfony\Component\Finder\Finder;
use Behat\MinkExtension\Context\MinkContext;

require_once __DIR__ . '/RestContext.php';

/**
 * Features context.
 */
class FeatureContext extends MinkContext implements ClosuredContextInterface
{

    /**
     * @var array
     */
    protected $parameters;

    /**
     * Store data used across different subcontexts and steps.
     *
     * @var array
     */
    protected $data = array();

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters Context parameters (set them up through behat.yml)
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __construct(array $parameters)
    {
        if (empty($parameters)) {
            throw new \InvalidArgumentException('Parameters not loaded.');
        }

        $this->parameters = $parameters;

        $this->useContext('RestContext', new RestContext($parameters));

        /**
         * You may chain other contexts as sub-contexts of this main context via parameters. In this way all the
         * context classes may communicate with each other.
         */
        if (array_key_exists('subContexts', $parameters) && is_array($parameters['subContexts'])) {
            $this->loadBootstrapScripts($this->getResourcePath('bootstrap'));

            foreach ($parameters['subContexts'] as $subContext) {
                if (class_exists($subContext)) {
                    $this->useContext($subContext, new $subContext());
                } else {
                    throw new \Exception("Context '{$subContext}' doesn't exist.");
                }
            }
        }
    }

    /**
     * Returns array of step definition files (*.php).
     *
     * @return array
     */
    public function getStepDefinitionResources()
    {
        $path = $this->getResourcePath('steps') ?: (__DIR__ . '/../steps');

        return $this->getFiles($path);
    }

    /**
     * Returns array of hook definition files (*.php).
     *
     * @return array
     * @throws \RuntimeException
     */
    public function getHookDefinitionResources()
    {
        $path = $this->getResourcePath('hooks') ?: (__DIR__ . '/../support');

        return $this->getFiles($path);
    }

    /**
     * Get data by field name, or return all data if no field name provided.
     *
     * @param string $name Field name.
     * @return mixed
     * @throws \Exception
     */
    public function getData($name = null)
    {
        if (!isset($name)) {
            return $this->data;
        } elseif (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        throw new \Exception('Requested data not exist.');
    }

    /**
     * Set value on given field name.
     *
     * @param string $name Field name.
     * @param mixed $value Field value.
     * @return void
     */
    public function setData($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Check if specified field name exists or not.
     *
     * @param string $name Field name.
     * @return mixed
     */
    public function dataExists($name)
    {
        return array_key_exists($name, $this->data);
    }

    /**
     * This public method is also for other context(s) to set parameter(s) into this context.
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function setParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Get context parameter.
     *
     * @param string $name Parameter name.
     * @return mixed
     */
    public function getParameter($name)
    {
        return array_key_exists($name, $this->parameters) ? $this->parameters[$name] : null;
    }

    /**
     * Returns path that points to specified resources.
     *
     * @param string $type Resource type. Either 'boostrap', 'steps' or 'hooks'.
     * @return string Return path back.
     * @throws \RuntimeException
     */
    protected function getResourcePath($type)
    {
        $paths = $this->getParameter('paths');

        if (array_key_exists($type, $paths)) {
            $pathBase = array_key_exists('base', $paths) ? $paths['base'] : '';
            $pathType = $paths[$type];

            // Check if it's an absolute path.
            if (substr($pathType, 0, 1) == DIRECTORY_SEPARATOR) {
                if (empty($pathBase)) {
                    return $pathType;
                } else {
                    throw new \RuntimeException(
                        sprintf('You may only use relative path for type "%s" when base path is presented.', $type)
                    );
                }
            } else {
                // TODO: check if there is a trailing directory separator in the base path.
                return ($pathBase ? ($pathBase . DIRECTORY_SEPARATOR) : '') . $pathType;
            }
        }

        return '';
    }

    /**
     * Get files of certain type under specified directory.
     *
     * @param string $dir A directory.
     * @param string $ext File extension.
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function getFiles($dir, $ext = 'php')
    {
        if (!is_dir($dir)) {
            throw new \InvalidArgumentException(sprintf('Given path "%s" is not a directory.', $dir));
        }

        if (!is_readable($dir)) {
            throw new \InvalidArgumentException(sprintf('Given path "%s" is not readable.', $dir));
        }

        if (!preg_match('/^[0-9a-z]+$/i', $ext)) {
            throw new \InvalidArgumentException(
                sprintf('Given file extension "%s" is invalid (may only contain digits and/or letters).', $dir)
            );
        }

        $finder = new Finder;

        return $finder->files()->name('*.' . $ext)->in($dir);
    }

    /**
     * Requires *.php scripts from bootstrap/ folder.
     *
     * @param string $path
     * @see Behat\Behat\Console\Processor\LocatorProcessor::loadBootstrapScripts()
     */
    protected function loadBootstrapScripts($path)
    {
        $iterator = Finder::create()
            ->files()
            ->name('*.php')
            ->sortByName()
            ->in($path)
        ;

        foreach ($iterator as $file) {
            include_once (string) $file;
        }
    }
}