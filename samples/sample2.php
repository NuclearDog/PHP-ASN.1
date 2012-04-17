<?php

// Reads an ASN.1 message, finds a few values in it, then outputs them.

require('../asn.php');
ASN1\ASN1::load();

$data = base64_decode("YE0wS6ADCgEBoRIWEENyZWF0ZWQgd2l0aCBQSFCiMKEuMCwEBBERIiIEAQEEAwAAAQEB/xYVaHR0cDovL3d3dy5nb29nbGUuY29tgAIRIg==");

$reader = ASN1\ASN1::createReader();
$parser = ASN1\ASN1::createParser($reader);

$application = $parser->parse($data);

// There are several methods for navigating the resultant structure when you
// read a message.

// You can use the first method, which fetches the first child of a TLV.
$version = $application->first()->first()->first();

echo "Version: ".$version.PHP_EOL;

// You can manually iterate the children (the TLV implements the Iterator
// interface).

$sequence = $application->first();
foreach ($sequence as $child)
{
	if ($child->isContext() && $child->getTag()==1)
	{
		echo "Annotation: \"".$child->first()."\"".PHP_EOL;
	}
}

// Or you can use the `find` method. It passes its argument directly to the
// constructor of the `Query` class which supports a few different things:

// You can pass in an array of key-value pairs. The Query class will attempt
// to call `get$key` on the TLV and compare that value to the value you pass
// in.
$sequence = $application->find(array(
	'Class'=>TLV_CLASS_UNIVERSAL,
	'Type'=>TLV_TYPE_CONSTRUCTED,
	'Tag'=>TLV_TAG_SEQUENCE
	));

// You can pass in a string containing a comma-seperated list of assignment-
// like strings which is interpreted the equivilant to the previous.

$context = $sequence->find('Class=2,Type=1,Tag=2');

// You can do the same as the previous, but only putting in a name, no
// assignment. This causes the query to only match TLVs where a method
// named 'is$key' exists and returns true. (Check the TLV class for a
// list of available methods).

$context_inner = $context->first();
$sequence_inner = $context_inner->first();

$url = $sequence_inner->find('Universal,Primitive,Tag='.TLV_TAG_IA5STRING);

echo "Url: \"".$url."\"".PHP_EOL;

?>
