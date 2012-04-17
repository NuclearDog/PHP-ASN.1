<?php

// Create an ASN.1 message, then parse and dump it.

require('../asn.php');
ASN1\ASN1::load();

// Most stuff doesn't have a 'Helper' implemented, and must be initialized
// completely by hand.
$app = new \ASN1\TLV((object)array(
	'Class'=>TLV_CLASS_CONTEXT,
	'Type'=>TLV_TYPE_CONSTRUCTED,
	'Tag'=>0
));


// Some stuff does have a helper. They have a `create` method for easy creation.

$name = \ASN1\TLV\IA5String::create();
$name->set('Adam Pippin');

// We explicitly set the '$app' TLV to a CONSTRUCTED TLV before. If we hadn't,
// adding a child would automatically cause a conversion whether it was valid
// or not.
$app->add($name);

$email = \ASN1\TLV\IA5String::create();
$email->set('adam@gp-inc.ca');
$app->add($email);


// Grab a writer...
$writer = \ASN1\ASN1::createWriter();
// Create a serializer
$serializer = \ASN1\ASN1::createSerializer($writer);

// Serializer the top level TLV.
$data = $serializer->serialize($app);

// Output it
echo base64_encode($data).PHP_EOL;

// Now let's read it back to ensure it's valid.
$reader = \ASN1\ASN1::createReader();
$parser = \ASN1\ASN1::createParser($reader);
$app = $parser->parse($data);
$output = \ASN1\ASN1::createOutput($app);
$output->display();


?>
