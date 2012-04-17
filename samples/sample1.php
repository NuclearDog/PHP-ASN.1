<?php

// Reads an ASN.1 message and outputs with the provided output class.

require('../asn.php');
ASN1\ASN1::load();

$data = base64_decode("YE0wS6ADCgEBoRIWEENyZWF0ZWQgd2l0aCBQSFCiMKEuMCwEBBERIiIEAQEEAwAAAQEB/xYVaHR0cDovL3d3dy5nb29nbGUuY29tgAIRIg==");

$reader = ASN1\ASN1::createReader();
$parser = ASN1\ASN1::createParser($reader);

$application = $parser->parse($data);

$output = ASN1\ASN1::createOutput($application);

$output->display();

?>
