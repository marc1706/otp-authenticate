<?php

/**
 * OTPHelper
 * @package OTPAuthenticate
 * @copyright (c) Marc Alexander <admin@m-a-styles.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OTPAuthenticate;

class OTPHelper
{
	/** @var array Allowed types of OTP */
	protected $allowedType = array(
		'hotp',
		'totp',
	);

	/** @var array Allowed algorithms */
	protected $allowedAlgorithm = array(
		'sha1',
		'sha256',
		'sha512',
	);

	/** @var string Label string for URI */
	protected $label;

	/** @var string Issuer string for URI */
	protected $issuer;

	/** @var string Additional parameters for URI */
	protected $parameters = '';

	/**
	 * Generate OTP key URI
	 *
	 * @param string $type OTP type
	 * @param string $secret Base32 encoded secret
	 * @param string $account Account name
	 * @param string $issuer Issuer name (optional)
	 * @param int $counter Counter for HOTP (optional)
	 * @param string $algorithm Algorithm name (optional)
	 * @param string $digits Number of digits for code (optional)
	 * @param string $period Period for TOTP codes (optional)
	 *
	 * @return string OTP key URI
	 */
	public function generateKeyURI($type, $secret, $account, $issuer = '', $counter = 0, $algorithm = '', $digits = '', $period = '')
	{
		// Check if type is supported
		$this->validateInput('type', $type);
		$this->validateInput('algorithm', $algorithm, false);

		// Format label string
		$this->formatLabel($issuer, 'issuer');
		$this->formatLabel($account, 'account');

		// Set additional parameters
		$this->setCounter($type, $counter);
		$this->setParameter($algorithm, 'algorithm');
		$this->setParameter($digits, 'digits');
		$this->setParameter($period, 'period');

		return 'otpauth://' . $type . '/' . $this->label . '?secret=' . $secret . $this->issuer . $this->parameters;
	}

	/**
	 * Check if input data is supported
	 *
	 * @param string $type Input type
	 * @param string $data Input data
	 * @param bool $required Whether input is required
	 */
	protected function validateInput($type, $data, $required = true)
	{
		$variable = 'allowed' . ucfirst($type);
		if ((empty($data) && $required) || (!empty($data) && !in_array($data, $this->$variable)))
		{
			throw new \InvalidArgumentException("The $type $data is not supported");
		}
	}

	/**
	 * Format label string according to expected urlencoded standards.
	 *
	 * @param string $string The label string
	 * @param string $part Part of label
	 *
	 * @throws \InvalidArgumentException When given account name is an empty string
	 */
	protected function formatLabel($string, $part)
	{
		$string = trim($string);

		if (empty($string) && $part === 'account')
		{
			throw new \InvalidArgumentException("Label can't contain empty strings");
		}

		if ($part === 'issuer' && !empty($string))
		{
			$this->label = rawurlencode($string) . ':';
			$this->issuer = '&issuer=' . rawurlencode($string);
		}
		else
		{
			$this->label .= str_replace('%40', '@', rawurlencode($string));
		}
	}

	/**
	 * Set parameter if it is defined
	 *
	 * @param string $data Data to set
	 * @param string $name Name of data
	 */
	protected function setParameter($data, $name)
	{
		if (!empty($data))
		{
			$this->parameters .= "&$name=" . rawurlencode($data);
		}
	}

	/**
	 * Set counter value if hotp is being used
	 *
	 * @param string $type Type of OTP auth, either HOTP or TOTP
	 * @param int $counter Counter value
	 *
	 * @throws \InvalidArgumentException If counter is empty while using HOTP
	 */
	protected function setCounter($type, $counter)
	{
		if ($type === 'hotp')
		{
			if ($counter !== 0 && empty($counter))
			{
				throw new \InvalidArgumentException("Counter can't be empty if HOTP is being used");
			}

			$this->parameters .= "&counter=$counter";
		}
	}
}
