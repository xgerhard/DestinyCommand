<?php
namespace App\OAuth;

use Exception;
use Request;
use App\RequestHandler;
use App\OAuth\OAuthProvider;
use App\OAuth\OAuthSession;
use Carbon\Carbon;

class OAuthHandler
{
	public $provider;

	public function __construct($strService)
	{
		$this->provider = OAuthProvider::where('name', $strService)->firstOrFail();
	}

	public function runAuth($request)
	{
		// If code is set, user authorized or app
		if($request->input('code') !== null)
		{
			// Validate state
			if(
				$request->input('state') === null ||
				!$request->session()->has('state') ||
				$request->input('state') != $request->session()->pull('state')
			)
			throw new Exception('Invalid state parameter');

			// Get access tokens with access code
			$oTokens = $this->getTokens($request->input('code'));
			if(isset($oTokens->error)) $this->handleError($oTokens->error); // Bungie error field
			if(isset($oTokens->name)) $this->handleError($oTokens->name); // Nightbot error field

			// Save tokens
			$OAuthSession = new OAuthSession;
			$OAuthSession->access_token = $oTokens->access_token;
			$OAuthSession->refresh_token = $oTokens->refresh_token;
			$OAuthSession->expires_in = Carbon::now()->addSeconds($oTokens->expires_in);
			$OAuthSession->provider_id = $this->provider->id;

			// Bungie includes membershipId in Token response, I guess we should save it directly here..
			if(isset($oTokens->membership_id)) $OAuthSession->identifier = $oTokens->membership_id;

			$OAuthSession->save();
			$request->session()->put($this->provider->name .'-auth', $OAuthSession->id);
			echo 'Redirect to $this->provider->local_redirect';
			return $OAuthSession->access_token;
		}

		// If user denied authorization an error will be returned
		elseif($request->input('error') !== null)
		{
			$this->handleError($request->input('error'));
		}

		// Else it will be a new auth request
		else
		{
			// Create and save state
			$strState = $this->generateState();
			$request->session()->put('state', $strState);

			// Send user to authorization page
			$strUrl = $this->provider->auth_url .'&state='. $strState .'&client_id='. $this->provider->client_id 
			. (isset($this->provider->scope) ? '&scope='. $this->provider->scope : '')
			. (isset($this->provider->redirect_url) ? '&redirect_uri='. urlencode($this->provider->redirect_url) : '');

			// Go Auth!
			echo redirect($strUrl);
		}
		return false;
	}

	/*
	* generateState
	* Generate random string to validate user
	* return (string) random string
	*/
	private function generateState()
	{
		return sha1(time() . rand(146546546, 8789676764));
	}

	/*
	* getTokens
	* Requests tokens to auth server
	* required (string) code / refresh token
	* optional (boolean) refresh, true for refresh token, false for code
	*/
	private function getTokens($strCode, $bRefresh = false)
	{
		$oRH = new RequestHandler;
		$oRH->add(
			$this->provider->token_url,
			// Add header for Post
			array(),
			// Post values
			array(
				'grant_type' => $bRefresh === false ? 'authorization_code' : 'refresh_token',
				$bRefresh === false ? 'code' : 'refresh_token' => $strCode,
				'client_id'	=> $this->provider->client_id,
				'client_secret' => $this->provider->client_secret
			)
		);
		return json_decode($oRH->run()[0]);
	}

	private function isAuthValid($iAuthSessionId)
	{
		return true;
	}

	private function handleError($strError)
	{
		switch($strError)
		{
			case 'access_denied':
				$strError = 'Authorization was denied by client';
			break;
			
			case 'invalid_client':
				$strError = 'Something went wrong';
				// this should never happen, need to log this.
			break;

			case 'invalid_grant':
				$strError = 'Authorization code expired/invalid, please authorize again';
			break;
			
			default:
				$strError = 'Something went wrong';
		}
		// Todo: Log errors.
		throw new Exception($strError);
	}
}
?>