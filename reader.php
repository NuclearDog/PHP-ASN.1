<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1
{

	class TLVReader
	{
		/**
		* Read a single TLV tuplet from the stream and shorten the stream by
		* the consumed amount.
		*
		* @param string $stream
		* @return stdClass An object containing Class, Type, Tag, Length and Value properties.
		*/
		public static function read(&$stream)
		{
			$tlv_data = new \stdClass();

			$type = ord($stream[0]);
			$tlv_data->Class = (($type & 0x40) ? 1 : 0) + (($type & 0x80) ? 2 : 0);
			$tlv_data->Type = ($type & 0x20) ? 1 : 0;
			$tlv_data->Tag = $type & 0x1F;

			$length = ord($stream[1]);
			if ($length==0xFF)
			{
				throw new MalformedInputException("TLV length is specified as 0xFF - defined as reserved");
			}
			else if ($length==0x80)
			{
				// Okay, here's some fun.
				// We need to keep reading until we hit a EOC marker, then we know our length.
				// This could be implemented better, I'm sure, but this will work for anything that
				// isn't a huge data set.

				// So, copy the stream as not to mangle it.
				$lstream = $stream;

				// Read tlvs from it until we find one that's TLV_TAG_EOC.
				do
				{
					$tlv = self::read($lstream);
				} while ($tlv->Tag != TLV_TAG_EOC);

				// Now, subtract to figure out how much we consumed.
				$length = strlen($stream) - strlen($lstream);
			}
			else if ($length > 0x80)
			{
				// Next bytes specify length
				$numBytes = $length - 0x80;
				if (strlen($stream) < 2+$numBytes)
					throw new SeekPastEndOfStreamException();
				$length = hexdec(bin2hex(substr($stream, 2, $numBytes)));
			}
			else
			{
				$numBytes = 0;
			}

			if (2+$numBytes+$length > strlen($stream))
				throw new SeekPastEndOfStreamException();

			$tlv_data->Length = $length;

			$tlv_data->Value = substr($stream, 2 + $numBytes, $length);

			$total = 2 + $numBytes + $length;
			if ($total==strlen($stream))
				$stream = null;
			else
				$stream = substr($stream, $total);

			return $tlv_data;
		}
	}

}

?>
