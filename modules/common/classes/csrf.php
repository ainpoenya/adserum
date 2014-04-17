<?php 

/**
 * Cross Site Request Forgery - basic system
 *
 * @see http://en.wikipedia.org/wiki/Cross-site_request_forgery
 *
 * @package    OC
 * @category   Text
 * @author     Chema <chema@garridodiaz.com>
 * @copyright  (c) 2012 AdSerum.com
 * @license    GPL v3
 */
class CSRF {

        /**
         * Returns the token in the session or generates a new one
         *
         * @param   string  $namespace - semi-unique name for the token (support for multiple forms)
         * @return  string
         */
        public static function token($namespace='default')
        {
                $token = Session::instance()->get('csrf-token-'.$namespace);

                // Generate a new token if no token is found
                if ( ! $token)
                {
                	$token = Text::random('alnum', rand(20, 30));
                    Session::instance()->set('csrf-token-'.$namespace, $token);
                }

                return $token;
        }

        /**
         * unsets the token
         *
         * @param   string  $namespace - semi-unique name for the token (support for multiple forms)
         */
        public static function unset_token($namespace='default')
        {
             Session::instance()->set('csrf-token-'.$namespace, NULL);
        }

        /**
         * Generates the CSRF form input
         * @uses    Form
         * @param   string  $namespace
         * @return  string  generated HTML
         */
        public static function form($namespace='default')
        {
                return Form::hidden('csrf_'.$namespace, CSRF::token($namespace));
        }
        
        /**
         * Validation rule for checking a valid token
         *
         * @param   string  $namespace - the token string to check for
         * @return  bool
         */
        public static function valid($namespace=NULL,$method='post')
        {
               /*
                $referer = Request::current()->referrer();
                $site_url = substr(URL::base('http'), 7);
                $preg = '/^https?:\/\/([^/]+\.)?'.str_replace('.','\.',$site_url);
                if ( ! preg_match($preg, $referer))
                  return FALSE; // invalid referer!!!
                */
        	if ($namespace===NULL)
        		$namespace = URL::title(Request::current()->uri());

            if ($method=='post')
        	   return Request::$current->post('csrf_'.$namespace) === self::token($namespace);
            else
               return Request::$current->query('csrf_'.$namespace) === self::token($namespace);  
        }
}