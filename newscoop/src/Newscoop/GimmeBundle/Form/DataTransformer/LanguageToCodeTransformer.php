<?php

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\GimmeBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class LanguageToCodeTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Transforms an object (language) to a string (code).
     *
     * @param Newscoop\Entity\Language|null $language
     *
     * @return string
     */
    public function transform($language = null)
    {
        if (null === $language) {
            return '';
        }

        return $language->getCode();
    }

    /**
     * Transforms a string (code) to an object (language).
     *
     * @param string $code
     *
     * @return Newscoop\Entity\Language|null
     *
     * @throws TransformationFailedException if object (language) is not found.
     */
    public function reverseTransform($code)
    {
        if (!$code) {
            return;
        }

        $language = $this->objectManager
            ->getRepository('Newscoop\Entity\Language')
            ->findOneByCode($code);

        if (null === $language) {
            throw new TransformationFailedException(sprintf(
                'Language with code "%s" does not exist!',
                $identifier
            ));
        }

        return $language;
    }
}
