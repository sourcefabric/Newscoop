<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Plupload helper
 */
class Action_Helper_Plupload extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * @return Action_Helper_Plupload
     */
    public function init()
    {
        $request = $this->getRequest();
        if ($request->getParam('plupload', false)) {
            Plupload::OnMultiFileUpload(CS_TMP_TPL_DIR);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function direct()
    {
        return $this->getUploadedFiles();
    }

    /**
     * Get uploaded files
     *
     * @return array
     */
    public function getUploadedFiles()
    {
        $request = $this->getRequest();
        $count = (int) $request->getParam('uploader_count', 0);

        $files = array();
        if ($request->isPost() && $count) {
            for ($i = 0; $i < $count; $i++) {
                $tmpnameId = "uploader_{$i}_tmpname";
                $nameId = "uploader_{$i}_name";
                $statusId = "uploader_{$i}_status";
                if ($request->getParam($statusId) == 'done') {
                    $files[$request->getParam($nameId)] = CS_TMP_TPL_DIR . '/' . $request->getParam($tmpnameId);
                }
            }
        }

        return $files;
    }
}
