#Web of Trust API Library

The [https://www.mywot.com/wiki/API](Web of Trust API) provides reputation data about websites. 

It aims to try an answer questions such as;

* How trustworthy is this website?
* Is this website safe for children?
* Does this website pose a risk of phishing, malware or a scam?
* Could the content of this website be considered questionnable?
* Does this website contain adult content?
* Is this website on any blacklists and if so, for what risk?

With the exception of blacklist information, none of this data deals in absolutes (and even then, it cannot guaranntee that the
site in question *should* be on a blacklist, only that it is). However, it is useful as a starting point for trying to assess the suitablility - or otherwise - of a website.

##Before you Begin - Get an API Key

In order to use the Web Of Trust (WOT) API, you will need an API key which you can [request here](http://www.mywot.com/profile/api).

##Usage

```php
// Create a client
$client = \Lukaswhite\Weboftrust\WeboftrustClient::factory();

// IMPORTANT - set the API key
$client->setApiKey('PUT-YOUR-API-KEY-HERE');

// Call the API, passing in an array of URLs
$response = $client->lookup(array('www.bbc.co.uk', 'cnn.com'));
```

##Return Value

Calling `lookup()` returns an array of objects which represent the websites in question. The array is indexed by URL. Each object is an instance of `Lukaswhite\Weboftrust\Models\Website`. For example:

```php
$response = $client->lookup(array('www.bbc.co.uk', 'cnn.com'));

// Get the BBC website
$website = $response['www.bbc.co.uk'];

// Is the wesbite trustworthy?
if ($website->isTrustworthy()) {
	// it sure is, do something...
} else {
	// Uh-oh, perhaps we should be wary of this site. 
}
```

For the various methods you can call on the `Website` class, read on.

##Trustworthiness

Once you call the API, you should have an indication of the "trustworthiness" of a website. It's a slightly wooly concept, so it's really up to you how you interpret that. The "trustworthiness" is a value up to a hundred - "to what degree is this site trustworthy?" - and a second value measures the degree of confidence in that assessment. (This is covered later in the README.)

The "trustworthiness" is a property of the returned Website model, and contains two properties - `reputation` and `confidence`. So if you wanted to ask whether the trustworthiness is above fifty, and the confidence in that assessment is above ten, you could do this:

```php
$is_trustworthy = ( ( $website->trustworthiness->reputation >= 50 ) && ( $website->trustworthiness->confidence > 10 ) );
```

##Child Safety

The API may also provide an indication of how safe a website is for children. This works in the same way as trustworthiness, only this time the property is `childSafety`. E.g.:

```php
$suitable = ( ( $website->childSafety->reputation >= 50 ) && ( $website->childSafety->confidence > 10 ) );
```

##Categories

Web of Trust place websites in one or more categories. You can find a [list here](https://www.mywot.com/wiki/API#Categories). It isn't quite as simple as saying a website belongs in a certain category, or it doesn't. Instead, if the API indicates that a website belongs to a category it also incoporates a degree of certainty, as a value which goes up to a hundred. So rather than ask the question "does this website contain adult content" what you should really ask is "what is the likliehood that this website contains adult content?". We can try to "convert" that into a simple "yes/no" by specifying a minimum threshold for the certainty, for example:

```php
$is_adult = $website->isInCategory(Website::ADULT_CONTENT, 20);
```

Here, we're asking if the website contains adult content, but we're also specifying that in order to answer "YES" to the question, the certainty of that categorisation has to be at least twenty.

It's worth noting that not all categories are bad, or even questionnable. The category identified by 501 is a "good site". You can check that:

```php
$good = $website->isInCategory(Website::GOOD_SITE);
```

The Website class comtains a number of class constants, so that you don't have to use the underlying numeric category ID's.

##Blacklists

Sometimes, the API will indicate that a website is on a third-party blacklist. There are four types:

* malware
* phishing
* scam
* spam

To find out if a site has been blacklisted, use the `isBlacklisted()` method. 

To find out if a site is on ANY blacklist, call it without any arguments:

```php
$is_blacklisted = $website->isBlacklisted();
```

To check whether it's on a specific type of blacklist, specify it as an argument using one of the values above, for example:

```php
$is_blacklisted_for_spam = $website->isBlacklisted('spam');
```

##A Note on Confidence

The various components - trustworthiness and child safety, as well as categorisation - include a confidence value. This is a measure 
of the degree of certainty of this assessment. As such, it's not necessarily enough to ask a question like "How trustworthy is this 
website?". Rather, you would ask; "How trustworthy is this website, and how certain are you of the answer?".

As such, conside this method:

```php
$is_trustworthy = $website->isTrustworthy();
```

Without any arguments, this makes assumptions about the minimum threshold - remember, there are no absolutes, merely a degree of 
certainty in the form of a value between zero and one hundred - and also a threshold of certainty. Consider the following:

```php
$is_trustworthy = $website->isTrustworthy(50, 10);
```

This asks the same question, but explictly states two things. The first is the threshold for the value which rates the site's 
"trustworthiness". 50 is, well, about 50%. Whether that is a high enough threshold to make a judgement is a moot point, and it's up
to you to play around and try and decide what values to use, and what to do with them.

It also - in that second argument - indicates that in order to answer "YES" to the question, the level of certainty should be 
ten or higher. Ten is a good number because Web of Trust themselves will show a warning about a website only if the certainty is ten or
higher. Depending on your requirements, or experience of playing around with the data, you may want a higher certainty.

##Disclaimer

I have no affiliation with Web of Trust, and the data is crowd-sourced - so there are no guarrantees that it's correct. If you use this API and an unsuitable website is marked as suitable, or if a site you owned is marked as unsuitable, potentially dangerous, or containing questionnable material then I can't accept any responsbility.

##Questions

Your first port of call with any questions is probably the [Web Of Trust documentation](https://www.mywot.com/wiki/API). If your question or problem refers to the library, rather than the underling API, then leave an issue on Github.

Lukas White
hello@lukaswhite.com
