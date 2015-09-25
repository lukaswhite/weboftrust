<?php namespace Lukaswhite\Weboftrust\Models;

/**
 * The Component class represents a component of a WebOfTrust site assessment, and is broken down into
 * reputation (the higher the value, the more reputable) and the confidence in that value (0-100).
 */
class Component {

	/**
	 * @var int
	 */
	public $reputation;

	/**
	 * @var int
	 */
	public $confidence;

	/**
	 * Constructpr
	 *
	 * @param int $reputation
	 * @param int $confidence
	 */
	public function __construct($reputation, $confidence)
	{
		$this->reputation = $reputation;
		$this->confidence = $confidence;
	}

}