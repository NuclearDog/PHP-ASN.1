<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1\TLV
{

	class Integer extends \ASN1\TLVType
	{
		public static function create()
		{
			return new \ASN1\TLV((object)array(
				'Class'=>TLV_CLASS_UNIVERSAL,
				'Type'=>TLV_TYPE_PRIMITIVE,
				'Tag'=>TLV_TAG_INTEGER
				));
		}

		public static function write(\ASN1\TLV $tlv, $value)
		{
			$negative = $value < 0;
			$value = abs($negative ? $value + 1 : $value);

			// Figure out the number of bytes we need to encode it.
			$n = 1;
			for ($n=1; $value > (pow(256, $n)-($negative ? 0 : 1))/2; $n++) {}

			// Okay, it will take $n bytes. Convert it to hex. We can't
			// use dechex here, as it only supports up to the size of an
			// unsigned 32-bit integer.

			$hex = '';

			$val = pow(256, $n-1);
			for ($i = $n * 2; $i > 0 && $val >= 1; $i--)
			{
				$fit = floor($value / $val);

				$hex .= dechex($fit);
				$value -= ($fit * $val);

				$val /= 16;
			}

			if (strlen($hex)%2==1) $hex = '0'.$hex;

			if ($negative)
			{
				// Invert the entire number, set the MSB.
				for ($i=0; $i<strlen($hex); $i++)
				{
					$dec = hexdec($hex[$i]);
					$dec = 0xF - $dec;
					if ($i==0)
						$dec = $dec | 0x8;
					$hex[$i] = dechex($dec);
				}
			}

			$tlv->write(pack('H*', $hex));

		}

		public static function read(\ASN1\TLV $tlv)
		{
			$data = $tlv->read();
			$data = unpack('H*', $data);
			$data = $data[1];
			$sum = 0;
			$negative = false;
			for ($i=0; $i<strlen($data); $i++)
			{
				$d = hexdec($data[$i]);
				if ($i==0 && ($d & 0x8)==0x8)
					$negative = true;
				if ($negative)
					$d = 0xF - $d;
				$sum += pow(16, strlen($data)-$i-1) * $d;
			}
			if ($negative) $sum = -$sum - 1;
			return $sum;
		}

		public static function getTag()
		{
			return TLV_TAG_INTEGER;
		}
	}

}

?>
