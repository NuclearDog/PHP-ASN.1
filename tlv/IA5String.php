<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1\TLV
{

	class IA5String extends \ASN1\TLVType
	{
		public static function create()
		{
			return new \ASN1\TLV((object)array(
				'Class'=>TLV_CLASS_UNIVERSAL,
				'Type'=>TLV_TYPE_PRIMITIVE,
				'Tag'=>TLV_TAG_IA5STRING
				));
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
			return TLV_TAG_IA5STRING;
		}
	}

}

?>
