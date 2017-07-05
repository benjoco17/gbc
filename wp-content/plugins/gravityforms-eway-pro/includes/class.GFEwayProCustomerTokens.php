<?php

if (!defined('ABSPATH')) {
	exit;
}

class GFEwayProCustomerTokens {

	const USER_OPTIONS_TOKENS		= 'eway_customer_tokens';

	/**
	* read tokens from user options
	* @param int $user_id
	* @return array
	*/
	protected function getUserTokens($user_id) {
		$tokens = false;

		if (empty($user_id)) {
			$user_id = get_current_user_id();
		}

		if ($user_id) {
			$tokens = get_user_option(self::USER_OPTIONS_TOKENS, $user_id);
		}

		return empty($tokens) ? array() : $tokens;
	}

	/**
	* get a list of tokens mapped to partial card numbers
	* @param int $user_id
	* @return array
	*/
	public function getTokenList($user_id = 0) {
		$list = array();

		foreach ($this->getUserTokens($user_id) as $token) {
			$list[$token['token']] = $token['card'];
		}

		return $list;
	}

	/**
	* check for valid token ID
	* @param string $token
	* @param int $user_id
	* @return bool
	*/
	public function hasToken($token, $user_id = 0) {
		$tokens = $this->getUserTokens($user_id);

		return isset($tokens[$token]);
	}

	/**
	* get partial card number for token
	* @param string $token
	* @param int $user_id
	* @return string
	*/
	public function getCardnumber($token, $user_id = 0) {
		$tokens = $this->getUserTokens($user_id);

		return empty($tokens[$token]['card']) ? false : $tokens[$token]['card'];
	}

	/**
	* add a new token with partial card number
	* @param string $token
	* @param string $card
	* @param int $user_id
	*/
	public function addToken($token, $card, $user_id = 0) {
		if (empty($user_id)) {
			$user_id = get_current_user_id();
		}

		if (empty($user_id)) {
			return;
		}

		$tokens = $this->getUserTokens($user_id);

		$tokens[$token] = array('token' => $token, 'card' => $card);

		// save as local user meta, i.e. not multisite cross-blog
		// TODO: allow multisite cross-blog tokens with add-on setting
		update_user_option($user_id, self::USER_OPTIONS_TOKENS, $tokens, false);
	}

	/**
	* remove a token
	* @param string $token
	* @param int $user_id
	*/
	public function removeToken($token, $user_id = 0) {
		if (empty($user_id)) {
			$user_id = get_current_user_id();
		}

		if (empty($user_id)) {
			return;
		}

		$tokens = $this->getUserTokens($user_id);

		if (isset($tokens[$token])) {
			unset($tokens[$token]);

			// save as local user meta, i.e. not multisite cross-blog
			// TODO: allow multisite cross-blog tokens with add-on setting
			update_user_option($user_id, self::USER_OPTIONS_TOKENS, $tokens, false);
		}
	}

}
