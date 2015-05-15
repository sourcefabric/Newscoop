<?php

/**
 * @package Newscoop\GimmeBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\GimmeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class LanguageToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (language) to a string (id).
     *
     * @param  Newscoop\Entity\Language|null $language
     * @return string
     */
    public function transform($language = null)
    {
        if (null === $language) {
            return "";
        }

        return $language->getId();
    }

    /**
     * Transforms a string (id) to an object (language).
     *
     * @param string $id
     *
     * @return Newscoop\Entity\Language|null
     *
     * @throws TransformationFailedException if object (language) is not found.
     */
    public function reverseTransform($id)
    {
        if (!$id) {
            return;
        }

        $language = $this->om
            ->getReference('Newscoop\Entity\Language', (int) $id)
        ;

        if (null === $language) {
            throw new TransformationFailedException(sprintf(
                'Language with id "%s" does not exist!',
                $id
            ));
        }

        return $language;
    }
}
