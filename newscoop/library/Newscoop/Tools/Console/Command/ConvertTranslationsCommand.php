<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Dumper;

/**
 * Convert old Newscoop translations Command
 */
class ConvertTranslationsCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('translation:convert')
            ->setDescription('Converts Newscoop old translations to Symfony2 translation format (yml).');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {

            $oldTranslationsPath = __DIR__.'/../../../../../admin-files/lang';
            $zip_file = __DIR__.'/../../../../../admin-files/oldNewscoopTranslations.zip';
            $this->backupOldTranslations($zip_file, $oldTranslationsPath, $output);

            $count = 0;
            $directoryIterator = new \RecursiveDirectoryIterator($oldTranslationsPath);
            foreach (new \RecursiveIteratorIterator($directoryIterator) as $filename => $file) {
                if (strtolower(substr($filename, -4)) == ".php") {
                    $locale = substr($file->getPath(), strlen($oldTranslationsPath)+1);
                    $translations = array();
                    $currentFile = fopen($filename, 'r');
                    $toRemove = array('\'', '\\');
                    while (!feof($currentFile))
                    {  
                        $line = fgets($currentFile);
                        $regex = '#\((([^()]+|(?R))*)\)#';
                        $double_quotes = '/"([^"]+)"/';
                        if (preg_match_all($regex, $line ,$matches)) {
                            $result = implode('', $matches[1]);
                            $resultArray = explode(',', $result);
                            preg_match($double_quotes, $resultArray[0], $matchKey);
                            preg_match($double_quotes, $resultArray[1], $matchValue);
                            if (!empty($matchValue[1])) {
                                $translations[str_replace($toRemove, '', $matchKey[1])] = str_replace($toRemove, '', $matchValue[1]);
                            }
                        }
                    }

                    fclose($currentFile);
                    $dumper = new Dumper();
                    if (!empty($translations)) {
                        $count++;
                        file_put_contents(__DIR__.'/../../../../../src/Newscoop/NewscoopBundle/Resources/translations/'.substr(basename($file), 0, -4).'.'.$locale.'.yml', $dumper->dump($translations, 2));
                    }
                }
            }

            $output->writeln('<info>Old Newscoop translations successfully converted!</info>');
            $output->writeln('<info>Converted '.$count.' files.</info>');
            if ($this->removeDirectory($oldTranslationsPath)) {
                $output->writeln('<info>Old Newscoop translations successfully removed!</info>');
            } else {
                $output->writeln('<error>Errors while removing old translations!</error>');
            }

        } catch (\Exception $e) {
            throw new \Exception('Something went wrong!');
        }
    }

    /**
     * Removes old translations directory
     *
     * @param string          $dir    Path to old translation files
     * @param OutputInterface $output Console output
     *
     * @return void
     */
    private function removeDirectory($dir) {

        if (!file_exists($dir)){ 
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if ($item == 'ge' || $item == 'kr' || $item == 'by' || $item == 'cz') {
                unlink($dir.DIRECTORY_SEPARATOR.$item);
            }

            if (!$this->removeDirectory($dir.DIRECTORY_SEPARATOR.$item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    /**
     * Makes backup of old translations files
     *
     * @param string          $zip_file            Path to backup file
     * @param string          $oldTranslationsPath Path to old translation files
     * @param OutputInterface $output              Console output
     *
     * @return void
     */
    private function backupOldTranslations($zip_file, $oldTranslationsPath, OutputInterface $output) {

            $zip = new \ZipArchive(); 
            $zip->open($zip_file, \ZipArchive::CREATE); 

            if (!is_dir($oldTranslationsPath)) { 
                $output->writeln('<error>Directory to old translations folder does not exist!</error>');
                exit;
            } 

            $oldTranslationsPath = realpath($oldTranslationsPath); 
            if (substr($oldTranslationsPath, -1) != '/') { 
                $oldTranslationsPath.= '/'; 
            } 

            $dirStack = array($oldTranslationsPath); 
            $cutFrom = strrpos(substr($oldTranslationsPath, 0, -1), '/')+1; 

            while (!empty($dirStack)) { 
                $currentDir = array_pop($dirStack); 
                $filesToAdd = array(); 

                $dir = dir($currentDir); 
                while (false !== ($node = $dir->read())) { 
                    if (($node == '..') || ($node == '.')) { 
                        continue; 
                    } 
                    if (is_dir($currentDir . $node)) { 
                        array_push($dirStack, $currentDir . $node . '/'); 
                    } 
                    if (is_file($currentDir . $node)) { 
                        $filesToAdd[] = $node; 
                    } 
                } 

                $localDir = substr($currentDir, $cutFrom); 
                $zip->addEmptyDir($localDir); 
                
                foreach ($filesToAdd as $file) { 
                    $zip->addFile($currentDir . $file, $localDir . $file); 
                } 
            } 

            $zip->close();

            $output->writeln('<info>Directory of old translations folder successfully back-up\'ed to ../admin-files/oldNewscoopTranslations.zip!</info>');
    }
}