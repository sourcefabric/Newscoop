<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Rating controller
 */

use Newscoop\Entity\Rating;

class RatingController extends Zend_Controller_Action
{
    public function init()
    {
        $this->getHelper('contextSwitch')->addActionContext('save', 'json')->initContext();
    }

    public function saveAction()
    {
		$this->_helper->layout->disableLayout();
		$params = $this->getRequest()->getParams();
        
        $errors = array();

		$auth = Zend_Auth::getInstance();

		if ($auth->getIdentity()) {
            $userRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\User');
			$ratingRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Rating');
            
            $user = $userRepository->find($auth->getIdentity());

            if ($ratingRepository->countBy(array('articleId' => $params['f_article_number'], 'userId' => $user->getId())) > 0) {
                $errors[] = $this->view->translate('You have already rated this article');   
            }

		} else {
			$errors[] = $this->view->translate('You are not logged in.');
		}

		if (empty($errors)) {
			$rating = new Rating();

			$values = array(
				'user_id' => $user->getId(),
				'article_id' => $params['f_article_number'],
				'rating_score' => $params['f_rating_score'],
				'time_created' => new DateTime()
			);

			$ratingRepository->save($rating, $values);
            $ratingRepository->flush();
            
            $this->view->response = $this->getArticleRating($params['f_article_number']);

		}
		else {
			$errors = implode('<br>', $errors);
			$this->view->response = array_merge($this->getArticleRating($params['f_article_number']), array('error' => $errors));
		}

        $this->_helper->json($this->view->response);
    }

    public function indexAction()
    {
        $this->view->param = $this->_getParam('switch');
    }

    public function showAction()
    {
        $this->_helper->layout->disableLayout();
        $articleId = $this->_getParam('f_article_number');

        $this->_helper->json($this->getArticleRating($articleId));
    }

    /**
     * Get rating stats for a given article
     *
     * @param int $articleId
     * @return array
     */
    protected function getArticleRating($articleId)
    {
	    $ratingRepository = $this->getHelper('entity')->getRepository('Newscoop\Entity\Rating');
        $ratingScores = $ratingRepository->getArticleRating($articleId);

         
        return array('widget_id' => $articleId,
                     'number_votes' => (int)$ratingScores[0]['number_votes'],
                     'total_score' => (int)$ratingScores[0]['total_score'],
                     'dec_avg' => (float)round($ratingScores[0]['avg_score'], 2),
                     'whole_avg' => (int)round($ratingScores[0]['avg_score'])
                    );
        
    }
}
