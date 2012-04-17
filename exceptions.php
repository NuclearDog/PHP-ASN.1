<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1
{

	class Exception extends \Exception {}

	class MalformedInputException extends Exception {}
	
		class SeekPastEndOfStreamException extends MalformedInputException {}

	class NotImplementedException extends Exception {}

	class TLVMisuseException extends Exception {}

	class QueryParseException extends Exception {}

}

?>
