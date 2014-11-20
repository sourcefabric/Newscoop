<?php
/**
 * @package Newscoop\Newscoop
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;
use Newscoop\Entity\Snippet;

interface SnippetControllerInterface
{
    /**
     * @return void
     */
    public function __construct(Snippet $snippet, $update = false);

    /**
     * @return Snippet
     */
    public function getSnippet();
    public function update($parameters);
    public function preProcess($parameters);
    public function Process($parameters);
    public function postProcess($parameters);
}