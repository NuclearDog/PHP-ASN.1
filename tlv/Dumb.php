<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1\TLV
{

	class Dumb extends \ASN1\TLVType
	{
		public static function create()
		{
			return new \ASN1\TLV();
		}

		public static function write(\ASN1\TLV $tlv, $value)
		{
			$tlv->write($value);
		}

		public static function read(\ASN1\TLV $tlv)
		{
			return $tlv->read();
		}

		public static function getTag()
		{
			return 'dumb';
		}
	}

}

?>
