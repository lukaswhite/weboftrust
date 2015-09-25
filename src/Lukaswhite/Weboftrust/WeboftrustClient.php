<?php namespace Lukaswhite\Weboftrust;

use Guzzle\Service\Client;
use Guzzle\Service\Description\ServiceDescription;
use Guzzle\Common\Collection;

/**
 * @author Lukas White <hello@lukaswhite.com>
 */
class WeboftrustClient extends Client
{
	/**
	 * The Base Url
	 * 
	 * @var string
	 */
	private static $baseUrl = 'http://api.mywot.com/';

	/**
	 * The API Key
	 * 
	 * @var string
	 */
	private $apiKey;

 	/**
	 * {@inheritdoc}
   *
   * @return static
   */
  public static function factory($config = array())
  {
  	$default = array(
			'base_url' => self::$baseUrl,
		);
		$config = Collection::fromConfig($config, $default, array());

		$client = new static($config->get('base_url'), $config);

		$description = ServiceDescription::factory(__DIR__.'/Resources/client.json');
		$client->setDescription($description);

		return $client;
	}

	/**
	 * Set the API key.
	 *
	 * @param string $apiKey
	 * @return void
	 */
	public function setApiKey($apiKey)
	{
  	$this->apiKey = $apiKey;
	}

	public function lookup($hosts)
  {
  	return parent::lookup(array(
				'hosts' => 	implode('/', $hosts) . '/',
				'key'		=>	$this->apiKey,
		));
	}

}
