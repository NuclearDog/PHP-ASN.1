<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1
{

	class Serializer
	{
		protected $_writer;

		/**
		* The writer to use to serialize individual TLVs.
		*
		* @param \ASN1\TLVWriter $writer
		*/
		public function __construct(TLVWriter $writer)
		{
			$this->_writer = $writer;
		}

		/**
		* Recursively serializes a TLV tree into a flat binary stream.
		*
		* @param \ASN1\TLV $tlv The root TLV node.
		* @return string The binary representation of the node and all it's children.
		*/
		public function serialize(TLV $tlv)
		{
			if ($tlv->isConstruct())
			{
				$data = '';
				foreach ($tlv as $child)
				{
					$data .= $this->serialize($child);
				}
				$tlv->write($data);
			}

			return $this->_writer->write($tlv);
		}

	}

}

?>
