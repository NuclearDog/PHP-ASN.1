<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1
{

	class TLVWriter
	{
		public static function write(TLV $tlv)
		{
			$data = $tlv->read();

			$length = strlen($data);

			if ($length > 127)
			{
				$len = self::packInteger($length);
				$length = chr(0x80 + strlen($len)).$len;
			}
			else
				$length = chr($length);

			$tag =
				str_pad(decbin($tlv->getClass()), 2, '0', STR_PAD_LEFT).
				decbin($tlv->getType()).
				str_pad(decbin($tlv->getTag()), 5, '0', STR_PAD_LEFT)
				;
			$tag = bindec($tag);

			return chr($tag).$length.$data;
		}

		protected static function packInteger($value)
		{
			$n = 1;
			for ($n=1; $value > (pow(256, $n)-1); $n++) {}

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

			return pack('H*', $hex);
		}
	}

}

?>
