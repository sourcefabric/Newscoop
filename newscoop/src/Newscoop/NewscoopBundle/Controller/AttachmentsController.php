<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Newscoop\Entity\Attachment;

class AttachmentsController extends Controller
{
    /**
     * @Route("attachment/{id}/{name}")
     */
    public function downloadAttachmentAction(Request $request, $id, $name)
    {
        $em = $this->get('em');
        $attachmentService = $this->get('attachment');
        $download = $request->query->get('g_download');
        $showInBrowser = $request->query->get('g_show_in_browser');

        $attachment = $em->getRepository('Newscoop\Entity\Attachment')
            ->getAttachment($id)
            ->getOneOrNullResult();

        if (!$attachment) {
            throw new ResourceNotFoundException("Attachment not found");
        }

        if ($download) {
            $response = $this->sendFileAsAttachment($attachment, $attachmentService);
        } elseif ($showInBrowser) {
            $response = $this->openFileInBrowser($attachment, $attachmentService);
        } else {
            if ($attachment->getContentDisposition() == Attachment::CONTENT_DISPOSITION) {
                $response = $this->sendFileAsAttachment($attachment, $attachmentService);
            } else {
                $response = $this->openFileInBrowser($attachment, $attachmentService);
            }
        }

        return $response;
    }

    private function sendFileAsAttachment($attachment, $attachmentService)
    {
        $file = $attachmentService->getStorageLocation($attachment);
        $fileSystemService = $this->get('newscoop.filesystem');
        $response = new BinaryFileResponse($file);
        $d = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $attachment->getName(),
            $fileSystemService->sanitizeFileName($attachment->getName())
        );

        $response->headers->set('Content-Disposition', $d);

        return $response;
    }

    private function openFileInBrowser($attachment, $attachmentService)
    {
        $file = $attachmentService->getStorageLocation($attachment);
        $response = new BinaryFileResponse($file);

        return $response;
    }
}
