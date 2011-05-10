<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Annotations;

use Doctrine\Common\Annotations\Annotation;

class Acl extends Annotation
{
    public $resource;

    public $action;

    public $ignore;
}
