<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1
{

	abstract class TLVType
	{
		public abstract static function create();
		public abstract static function write(TLV $tlv, $value);
		public abstract static function read(TLV $tlv);
		public abstract static function getTag();

		public static function getName()
		{
			return get_called_class();
		}
	}

}

?>
