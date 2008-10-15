<?php
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <devel@yellowsunshine.de>
 * @copyright 2008 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */

/**
 * This class provides easy-to-use mail functionality
 *
 */
class CampMail
{
    /**
     * Send an mime mail
     * possibly only text or text + html.
     *
     * @param string or array  $recipients
     * @param string $text
     * @param string $html
     * @param array $hdrs
     */
    static public function MailMime($recipients, $text=false, $html=false, $hdrs)
    {
        include_once 'Mail.php';
        include_once 'Mail/mime.php';
    
        $crlf = "\n";
    
        $mime = new Mail_mime($crlf);
        
        if (strlen($text)) {
            $mime->setTXTBody($text);
        }
        
        if (strlen($html)) {
            $mime->setHTMLBody($html);
        }
        
        $body = $mime->get(array('head_charset' => 'UTF-8', 'text_charset' => 'UTF-8', 'html_charset' => 'UTF-8'));
        $hdrs = $mime->headers($hdrs);
        
        $mail = Mail::factory('mail');
        
        if (is_array($recipients)) {
            foreach ($recipients as $recipient) {
                $mail->send($recipient, $hdrs, $body);
            }
        } else {
            $mail->send($recipients, $hdrs, $body);   
        }
    }
    
    /**
     * Validate the syntax of an email address
     *
     * @param string $p_email
     * @return boolean
     */
    static public function ValidateAddress($p_email)
    {
        if(eregi("^[a-z0-9]+([-_\.]?[a-z0-9])+@[a-z0-9]+([-_\.]?[a-z0-9])+\.[a-z]{2,4}$", $p_email)) {
            return true;
        }
        return false;      
    } 
}