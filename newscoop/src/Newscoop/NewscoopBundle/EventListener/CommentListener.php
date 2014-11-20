<?php

namespace Newscoop\NewscoopBundle\EventListener;

use Newscoop\Entity\Publication;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

// define('ACTION_SUBMIT_COMMENT_ERR_INTERNAL', 'action_comment_submit_err_internal');
// define('ACTION_SUBMIT_COMMENT_ERR_NO_SUBJECT', 'action_comment_submit_err_no_subject');
// define('ACTION_SUBMIT_COMMENT_ERR_NO_CONTENT', 'action_comment_submit_err_no_content');
// define('ACTION_SUBMIT_COMMENT_ERR_NO_ARTICLE', 'action_comment_submit_err_no_article');
// define('ACTION_SUBMIT_COMMENT_ERR_NOT_ENABLED', 'action_comment_submit_err_not_enabled');
// define('ACTION_SUBMIT_COMMENT_ERR_NO_EMAIL', 'action_comment_submit_err_no_email');
// define('ACTION_SUBMIT_COMMENT_ERR_NO_PUBLIC', 'action_comment_submit_err_no_public');
// define('ACTION_SUBMIT_COMMENT_ERR_NO_CAPTCHA_CODE', 'action_comment_submit_err_no_captcha_code');
// define('ACTION_SUBMIT_COMMENT_ERR_INVALID_CAPTCHA_CODE', 'action_comment_submit_err_invalid_captcha_code');
// define('ACTION_SUBMIT_COMMENT_ERR_BANNED', 'action_comment_submit_err_banned');
// define('ACTION_SUBMIT_COMMENT_ERR_REJECTED', 'action_comment_submit_err_rejected');

require_once($GLOBALS['g_campsiteDir'].'/include/captcha/php-captcha.inc.php');

class CommentListener
{
    protected $log;
    protected $em;
    protected $input;
    protected $captchaEnabled;
    protected $article;
    protected $publication;

    public function __construct($logger, $em)
    {
        $this->log = $logger;
        $this->log->info('Comment!');
        $this->em = $em;
    }

    public function onCommentSubmit(GetResponseEvent $event)
    {
        $this->log->info('onCommentSubmit got fired!');
        $request = $event->getRequest();
        // var_dump($request);

        if ($request->getMethod() == 'POST') {
            $parameters = $request->request;
            // var_dump($parameters);

            $publicationId = $request->get('_newscoop_publication_metadata')['alias']['publication_id'];
            var_dump($publicationId);
            // $this->publication = new Publication($publicationId);
            $this->captchaEnabled = $this->em->getRepository('Newscoop\Entity\Publication')
                                ->findOneById($publicationId)->getCaptchaEnabled();
            // var_dump($this->publication);
            var_dump($this->captchaEnabled);
            $articleID = $request->get('_newscoop_article_metadata')['id'];

            if ($parameters->get('f_comment_email_protect') != '') {
                // $this->m_error = new PEAR_Error('The comment cannot be submitted.',
                // ACTION_SUBMIT_COMMENT_BOT_DETECTED);
                return false;
            }

            $this->input['nickname'] = $this::ifEmptyReturnString($parameters->get('f_comment_nickname'));
            $this->input['email']    = $this::ifEmptyReturnString($parameters->get('f_comment_reader_email'));
            $this->input['parent']   = (int) $this::ifEmptyReturnString($parameters->get('f_comment_parent'));
            $this->input['content']  = $this::ifEmptyReturnString($parameters->get('f_comment_content'));
            $this->input['article']  = $request->get('_newscoop_article_metadata')['id'];

            $this->input['captcha']['handler']   = $this::ifEmptyReturnString($parameters->get('f_captcha_handler'));
            $this->input['captcha']['challenge'] = $this::ifEmptyReturnString($parameters->get('recaptcha_challenge_field'));
            $this->input['captcha']['response']  = $this::ifEmptyReturnString($parameters->get('recaptcha_response_field'));

            if ($this->processCaptcha()) {
                var_dump($this->input);

                if (is_numeric($articleID)) {
                    // this is an article
                    var_dump('sup world');
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }

    private static function ifEmptyReturnString($input) {
        return (!empty($input) && !is_null($input)) ? $input : '';
    }

    private function processCaptcha()
    {
        if (!$this->captchaEnabled) {
            //if the Captcha is not enabled always return true
            return true;
        }
        return true;
    }
}