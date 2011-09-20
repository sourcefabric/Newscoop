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
    $pdf_file = 'pdf/' . $pdf_filename;

    if (!file_exists($pdf_file) || $gimme->article->last_update > date('Y-m-d h:i:s', filemtime($pdf_file)))  {
        require('include/html2fpdf/html2fpdf.php');
        $pdf = new HTML2FPDF;
        $pdf->AddPage();
        $content = $smarty->fetch($template);
        $pdf->WriteHTML($content);
        $pdf->Output($pdf_file);
    }

    return $pdf_file;
}

