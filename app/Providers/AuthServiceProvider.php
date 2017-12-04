<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Auth0\SDK\JWTVerifier;

class AuthServiceProvider extends ServiceProvider
{

    protected $token;
    protected $tokenInfo;

    /**
     * Register any application services.
     *
     * @return void
     */


    public function setCurrentToken($token)
    {   
        try {
            $verifier = new JWTVerifier([
              'supported_algs' => ['RS256'],
              'valid_audiences' => ['https://custom-jywf.frb.io/', "https://gates-data.auth0.com/userinfo"],
              'authorized_iss' => ['https://gates-data.auth0.com/']
            ]);

            $this->token = $token;
            $this->tokenInfo = $verifier->verifyAndDecode($token);
            return true;
        }
        catch(\Auth0\SDK\Exception\CoreException $e) {
            throw $e;
        }
    }


    public function checkHeader() {
      if (!function_exists('apache_request_headers')) { 
        function apache_request_headers() { 
          foreach($_SERVER as $key=>$value) { 
            if (substr($key,0,5)=="HTTP_") { 
                $key=str_replace(" ","-",ucwords(strtolower(str_replace("_"," ",substr($key,5))))); 
                $out[$key]=$value; 
            }else{ 
                $out[$key]=$value; 
            } 
          } 
          return $out; 
        } 
      } 


      $requestHeaders = apache_request_headers();

      if (!isset($requestHeaders['authorization']) && !isset($requestHeaders['Authorization'])) {
          header('HTTP/1.0 401 Unauthorized');
          echo "No token provided.";
          exit();
      }

      $authorizationHeader = isset($requestHeaders['authorization']) ? $requestHeaders['authorization'] : $requestHeaders['Authorization'];

      if ($authorizationHeader == null) {
        header('HTTP/1.0 401 Unauthorized');
        echo "No authorization header sent";
        exit();
      }

      $authorizationHeader = str_replace('bearer ', '', $authorizationHeader);
      $token = str_replace('Bearer ', '', $authorizationHeader);

      try {
          return $this->setCurrentToken($token);
      }
      catch(\Auth0\SDK\Exception\CoreException $e) {
        header('HTTP/1.0 401 Unauthorized');
        echo $e;
        exit();
      }
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['auth']->viaRequest('api', function ($request) {
            return $this->checkHeader();
            // return true;
        });
    }
}
