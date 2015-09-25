<?php namespace Lukaswhite\Weboftrust\Models;

use Lukaswhite\Weboftrust\Models\Component;

/**
 * @class Lukaswhite\Weboftrust\Models\Website
 *
 * Represents a website, as described by the Web of Trust API. Encapsulates the data provided by the API 
 * about a website, i.e.:
 *  - its "trustworthiness"
 *  - child safety, i.e. is it suitable for children?
 *  - categories; negative (e.g. malware, phishing, scam), questionnable (e.g. spam, ads, popups) 
 *    or those categories related to child-safety (e.g. adult content)
 *  - whether it is on one or more third-party Blacklist
 */
class Website {

	/*********************************************
	*
	* CATEGORY CONSTANTS
	*
	**********************************************/

	// Negative categories
	const MALWARE_OR_VIRUSES 											= 101;
	const POOR_CUSTOMER_EXPERIENCE 								= 102;
	const PHISHING 																= 103;
	const SCAM 																		= 104;
	const POTENTIALLY_ILLEGAL 										= 105;
	
	// Questionable categories
	const MISLEADING_CLAIMS_OR_UNETHICAL 					= 201;
	const PRIVACY_RISKS 													= 202;
	const SUSPICIOUS 															= 203;
	const HATE_OR_DISCRIMINATION 									= 204;
	const SPAM 																		= 205;
	const POTENTIALLY_UNWANTED_PROGRAMS 					= 206;
	const ADS_OR_POPUPS 													= 207;
	
	// Neutral categories
	const ONLINE_TRACKING 												= 301;
	const ALTERNATIVE_OR_CONTROVERSIAL_MEDICINE 	= 302;
	const OPINIONS_RELIGION_POLITICS 							= 303;
	const OTHER 																	= 304;
	
	// Positive categories
	const GOOD_SITE 															= 501;

	// Child safety
	//  - Negative
	const ADULT_CONTENT 													= 401;
	//  - Questionnable
	const INCIDENTAL_NUDITY 											=	402;
	const GRUESOME_OR_SHOCKING 										= 403;
	//  - Positive
	const SITE_FOR_KIDS 													= 404;

	/*********************************************
	*
	* PROPERTIES
	*
	**********************************************

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var array
	 */
	private $categories = array();

	/**
	 * @var Lukaswhite\Weboftrust\Models\Component
	 */
	public $trustworthiness;

	/**
	 * @var Component
	 */
	public $childSafety;

	/**
	 * @var Lukaswhite\Weboftrust\Models\Component
	 */
	private $blacklists = array();

	/**
	 * Constructor.
	 *
	 * @param string
	 */
	public function __construct($url)
	{
		$this->url = $url;
	}

	/*********************************************
	*
	* API
	*
	**********************************************

	/**
	 * Is this site trusthworthy?
	 *
	 * Attempts to provide an indication of whether this site is "trustworthy", by checking whether its reputation
	 * on the "trustworthiness" axis is above the specified threshold. In order to return positive, it also requires 
	 * that the confidence in that rating is above a specified minimum value. WoT tend to issue warnings only when 
	 * the confidence value is above ten, but you should play around to try and find the appropriate value.
	 *
	 * @param int
	 * @param int
	 * @return boolean
	 */
	public function isTrustworthy($threshold = 50, $minConfidence = 10)
	{
		return (
			( $this->trustworthiness->reputation >= $threshold ) && 
			( $this->trustworthiness->confidence >= $minConfidence )
		);

	}

	/**
	 * Is this site suitable for children?
	 *
	 * Attempts to provide an indication of whether this site is suitable for children, by checking whether its reputation
	 * on the "child safety" axis is above the specified threshold. In order to return positive, it also requires 
	 * that the confidence in that rating is above a specified minimum value. WoT tend to issue warnings only when 
	 * the confidence value is above ten, but you should play around to try and find the appropriate value.
	 *
	 * @param int
	 * @param int
	 * @return boolean
	 */
	public function isSuitableForChildren($threshold = 50, $minConfidence = 10)
	{
		return (
			( $this->childSafety->reputation >= $threshold ) && 
			( $this->childSafety->confidence >= $minConfidence )
		);

	}

	/**
	 * Does this website belong to a particular category?
	 *
	 * Returns TRUE if Web Of Trust indicates this site belongs to a given category. Since categorisation
	 * also contains an indication of confidence (a value up to 100), the method also takes as an argument a 
	 * minimum confidence value.
	 */
	public function isInCategory($category, $minConfidence = 10)
	{
		if (!in_array($category, $this->categories)) {
			return FALSE;
		}

		return ($this->categories[$category] >= $minConfidence);
	}

	/**
	 * Determines whether this website belongs to a blacklist.
	 * Optionally, you can specify a type of blacklist (malware, phishing, scam or spam) - without specifying
	 * a particular type, it will return TRUE if it belongs to ANY blacklist.
	 *
	 * @param string
	 * @return boolean
	 */
	public function isBlacklisted($type = null)
	{
		if (!$type) {
			return (count($this->blacklists));
		}

		return in_array($type, $this->blacklists);
	}

	/*********************************************
	*
	* GETTERS 
	*
	**********************************************

	/**
	 * Get the URL this website refers to.
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/*********************************************
	*
	* SETTERS 
	*
	**********************************************

	/**
	 * Set the URL this website refers to.
	 *
	 * @param string
	 * @return Lukaswhite\Weboftrust\Models\Website
	 */
	public function setUrl($url)
	{
		$this->url = $url;
		return $this; // fluent interface
	}

	/**
	 * Set the "trustworthiness" component
	 *
	 * @param int
	 * @param int
	 * @return Lukaswhite\Weboftrust\Models\Website
	 */
	public function setTrustworthiness($reputation, $confidence)
	{
		$this->trustworthiness = new Component($reputation, $confidence);
		return $this; // fluent interface
	}

	/**
	 * Set the "child safety" component
	 *
	 * @param int
	 * @param int
	 * @return Lukaswhite\Weboftrust\Models\Website
	 */
	public function setChildSafety($reputation, $confidence)
	{
		$this->childSafety = new Component($reputation, $confidence);
		return $this; // fluent interface
	}

	/**
	 * Set the categories
	 *
	 * @param array
	 * @return Lukaswhite\Weboftrust\Models\Website
	 */
	public function setCategories($categories)
	{
		$this->categories = $categories;
		return $this; // fluent interface
	}

	/**
	 * Setter for the blacklists.
	 *
	 * @param array
	 * @return Lukaswhite\Weboftrust\Models\Website
	 */
	public function setBlacklists($blacklists)
	{
		$this->blacklists = $blacklists;
		return $this; // fluent interface
	}

}