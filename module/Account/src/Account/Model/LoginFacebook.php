<?php

namespace Account\Model;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\FacebookRequest;

/**
 * Description of LoginFacebook
 *
 * @author Pedro
 */
class LoginFacebook
{

    private $fb;
    private $loginHelper;
    private $permissions;

    public function __construct(array $dataConfig)
    {

        $config = ['app_id' => $dataConfig['app_id'],
                   'app_secret' => $dataConfig['app_secret'],
                   'default_graph_version' => $dataConfig['default_graph_version']];
        
              // print_r($config);die;
        $this->permissions = $dataConfig['permissions'];

       $this->fb = new Facebook($config);
      
    }
    
   
    public function accessToken()
    {
        $this->loginHelper = $this->fb->getRedirectLoginHelper();
        
        $accessToken = $this->loginHelper->getAccessToken();
    
        if (isset($accessToken)) {
            // Logged in!
           // $_SESSION['facebook_access_token'] = (string) $accessToken;
            
            return $accessToken;

            // Now you can redirect to another page and use the
            // access token from $_SESSION['facebook_access_token']
        } elseif ($this->loginHelper->getError()) {
            // The user denied the request
           return false;
        }
    }
    
     public function loginUrl($url, $permissions = null)
    {
        $this->loginHelper = $this->fb->getRedirectLoginHelper();
        
        if(is_null($permissions)){
            $permissions = $this->permissions;
        }
                
        $loginUrl = $this->loginHelper->getLoginUrl($url, $permissions);
        
        return $loginUrl;
    }
    
    
    public function getUser()
    {
        $accessToken = $this->accessToken();
        
        if(!$accessToken){
            
            return false;
        }

        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $this->fb->get('/me?fields=id,name,email', $accessToken->getValue());
            
        } catch (Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

       return $response->getGraphUser();
    }

    //put your code here
}
