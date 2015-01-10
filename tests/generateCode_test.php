<?php

/**
 * generateCode Test
 * @package OTPAuthenticate
 * @copyright (c) Marc Alexander <admin@m-a-styles.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OTPAuthenticat\tests;

require_once(dirname(__FILE__) . '/../lib/OTPAuthenticate.php');
require_once(dirname(__FILE__) . '/../vendor/christian-riesen/base32/src/Base32.php');

class generateCode extends \PHPUnit_Framework_TestCase
{
	protected $secret = "MRTGW2TEONWDQMR7";

	/** @var \OTPAuthenticate\OTPAuthenticate */
	protected $otp_auth;

	public function setUp()
	{
		parent::setUp();

		$this->otp_auth = new \OTPAuthenticate\OTPAuthenticate();
	}

	protected $hotp_codes = array(
		'020662',
		'297855',
		'293646',
		'438611',
		'795847',
		'381952',
		'900745',
		'565187',
	);

	protected $totp_codes = array(
		'049958',
		'522693',
		'483631',
		'747816',
		'894758',
		'279356',
		'227505',
		'515792',
	);

	public function testGenerateCodeHOTP()
	{
		$counter = 1;

		foreach ($this->hotp_codes as $code)
		{
			$this->assertSame($code, $this->otp_auth->generateCode($this->secret, $counter));
			$counter++;
		}
	}

	public function testGenerateCodeTOTP()
	{
		$start_time = 1420906262;

		foreach ($this->totp_codes as $code)
		{
			$this->assertSame($code, $this->otp_auth->generateCode($this->secret, $this->otp_auth->getTimestampCounter($start_time)));
			$start_time = $start_time + 30;
		}
	}
}