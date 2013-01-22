<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Webcode;

require_once APPLICATION_PATH . '/../library/Newscoop/Webcode.php';
require_once APPLICATION_PATH . '/../library/Newscoop/Webcode/Mapper.php';

global $application;

$application->bootstrap('container');
$em = Zend_Registry::get('container')->getService('em');

$em->getConnection()->beginTransaction();
$query = $em->getRepository('Newscoop\Entity\Article')->createQueryBuilder('a')
    ->select('a.number, l.id as language')
    ->join('a.language', 'l')
    ->getQuery();

$result = $query->getResult();
$webcodes = array();
foreach ($result as $row) {
    $webcode = trim(Newscoop\Webcode\Mapper::encode($row['number']), '@+');
    while (array_key_exists($webcode, $webcodes)) {
        $webcode .= $row['language'];
    }

    $webcodes[$webcode] = $row;

    $em->createQuery("UPDATE Newscoop\Entity\Article a SET a.webcode = :webcode WHERE a.number = :number AND a.language = :language")
        ->execute(array(
        'webcode' => $webcode,
        'number' => $row['number'],
        'language' => $row['language'],
    ));

    $article = $em->getReference('Newscoop\Entity\Article', $row);
    $webcode = new Webcode($webcode, $article);
    $em->persist($webcode);
    $em->flush();
}

$em->getConnection()->commit();
