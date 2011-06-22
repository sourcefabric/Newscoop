<?php
/**
 * In the begining this was build for
 *         extending and getting the protected method from Gravatar _getAvatarUrl
 * But it can be used like a proxy helper to get the avatar link from different sources
 *
 */
if(Zend_Version::compareVersion('1.11.0') === 1)
{
    class Admin_View_Helper_GetAvatar
    {
        public function getAvatar($email = "", $options = array()) {

        }
        public function __toString()
        {
            return '';
        }
    }
}
else {
    class Admin_View_Helper_GetAvatar extends Zend_View_Helper_Gravatar
    {

        /**
         * The main helper class witch is trigger back to gravatar method
         *
         * @param string $email
         * @param array $options
         * @return Zend_View_Helper_Gravatar
         */
        public function getAvatar($email = "", $options = array()) {
            $this->gravatar($email, $options);
            //parent::gravatar($email, $options);
            return $this;
        }

        /**
         * Rewrite the __tostring class to get the avatar url
         *         this was thought as a little hack to acces the protected _getAvatarUrl Method
         *
         * @return string
         */
        public function __toString()
        {
            return $this->_getAvatarUrl();
        }
    }
}