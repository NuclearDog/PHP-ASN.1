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

		public function __construct(TLVWriter $writer)
		{
			$this->_writer = $writer;
		}

		public function serialize(TLV $tlv)
		{
			if ($tlv->isConstruct())
			{
				$data = '';
				foreach ($tlv as $child)
				{
					$data .= $this->serialize($child);
				}
				$d = $tlv->read();
				$tlv->write($data);
			}

			return $this->_writer->write($tlv);
		}

	}

}

?>
