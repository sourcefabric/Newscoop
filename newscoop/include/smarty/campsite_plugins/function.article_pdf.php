<?php
/**
 * Newscoop customized Smarty plugin
 * @package Newscoop
 */

/**
 * Newscoop article_pdf function plugin
 *
 * Type:     function
 * Name:     article_pdf
 * Purpose:
 *
 * @param array
 *     $params[template]
 *     $params[prefix]
 * @param object
 *     $smarty The Smarty object
 */
function smarty_function_article_pdf($params, &$smarty)
{
    // gets the context variable
    $gimme = $smarty->getTemplateVars('gimme');

    if (isset($params['template'])) {
        $template = $params['template'];
    }

    $pdf_filename = '';
    if (isset($params['prefix'])) {
        $pdf_filename = $params['prefix'] . '-';
    }
    $publish = new DateTime($gimme->article->publish_date);
    $pdf_filename .= 'p' . $publish->format('Ymd') . '-n' . $gimme->article->number . '.pdf';
    $pdf_file = 'public/pdf/' . $pdf_filename;

    if (!file_exists($pdf_file) || $gimme->article->last_update > date('Y-m-d h:i:s', filemtime($pdf_file)))  {
        require('include/html2pdf/html2pdf.class.php');
        try {
            $content = $smarty->fetch($template);
            $html2pdf = new HTML2PDF('P', 'A4', 'de');
            $html2pdf->pdf->SetDisplayMode('real');
            $html2pdf->writeHTML($content);
            $html2pdf->Output($pdf_file, 'F');
        } catch (HTML2PDF_exception $e) {
            return '';
        }
    }

    return $pdf_file;
}

