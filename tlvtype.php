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
		/**
		* Create a new, empty TLV of this type.
		*
		* @return \ASN1\TLV
		*/
		public abstract static function create();

		/**
		* Write the value into the TLV, encoding it as necessitated by the type.
		*
		* @param \ASN1\TLV $tlv
		* @param mixed $value
		*/
		public abstract static function write(TLV $tlv, $value);

		/**
		* Read the value from the TLV, converting it into a workable format
		* as necessitated by the type.
		*
		* @param \ASN1\TLV $tlv
		* @return mixed
		*/
		public abstract static function read(TLV $tlv);

		/**
		* Returns the tag number this type represents.
		*
		* @return integer
		*/
		public abstract static function getTag();

		/**
		* Returns the name of the implementing class.
		*
		* @return string
		*/
		public static function getName()
		{
			return get_called_class();
		}
	}

}

?>
