<?php

/**
 * @package OTPAuthenticate
 * @copyright (c) Marc Alexander <admin@m-a-styles.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace marc1706\OTPAuthenticate;

use Base32\Base32;

class OTPAuthenticate
{
	/** int sha1 digest length */
	const SHA1_DIGEST_LENGTH = 20;

	/** int verification code modulus */
	const VERIFICATION_CODE_MODULUS = 1e6;

	/** int Secret length */
	protected $secret_length;

	/** int code length */
	protected $code_length;

	/** \Base32\Base32 */
	protected $base32;

	/**
	 * Constructor for OTPAuthenticate
	 *
	 * @param int $code_length Code length
	 * @param int $secret_length Secret length
	 */
	public function __construct($code_length = 6, $secret_length = 10)
	{
		$this->code_length = $code_length;
		$this->secret_length = $secret_length;

		$this->base32 = new Base32();
	}

	/**
	 * Generates code based on timestamp and secret
	 *
	 * @param string $secret Secret shared with user
	 * @param int $time Timestamp
	 *
	 * @return string Generated TOTP code
	 */
	public function generateCode($secret, $time)
	{
		$key = $this->base32->decode($secret);

		if (strlen($key) !== $this->secret_length && empty($time))
		{
			return '';
		}

		$hash = hash_hmac('sha1', $this->getBinaryTimeCounter($time), $key, true);

		return str_pad($this->truncate($hash), $this->code_length, '0', STR_PAD_LEFT);
	}

	/**
	 * Truncate HMAC hash to binary for generating a TOTP code
	 *
	 * @param string $hash HMAC hash
	 *
	 * @return int Truncated binary hash
	 */
	protected function truncate($hash)
	{
		$truncated_hash = 0;
		$offset = ord($hash[self::SHA1_DIGEST_LENGTH - 1]) & 0xF;
		var_dump($offset);

		for ($i = 0; $i < 4; ++$i)
		{
			$truncated_hash <<= 8;
			$truncated_hash  |= ord($hash[$offset + $i]);
		}

		// Truncate to a smaller number of digits.
		$truncated_hash &= 0x7FFFFFFF;
		$truncated_hash %= self::VERIFICATION_CODE_MODULUS;

		return $truncated_hash;
	}

	/**
	 * Get binary version of time counter
	 *
	 * @param int $time Timestamp
	 *
	 * @return string Binary time counter
	 */
	protected function getBinaryTimeCounter($time)
	{
		return pack('N*', 0) . pack('N*', floor($time / 30));
	}
}
