<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1
{

	class Parser
	{
		protected $_reader;

		/**
		*
		* @param \ASN1\TLVReader $reader The implementation to use to read TLV tuplets from a stream.
		*/
		public function __construct(TLVReader $reader)
		{
			$this->_reader = $reader;
		}

		/**
		* Parses the raw binary data into a tree of TLV objects.
		*
		* @param string data
		* @return TLV,array Either a single TLV or an array if there are multiple TLVs at the root level of the passed string.
		*/
		public function parse($data)
		{
			$tlvs = array();
			do
			{
				$tlv_data = $this->_reader->read($data);
				$tlv = new TLV($tlv_data);
				if ($tlv->isConstruct())
				{
					$children = $this->parse($tlv->read());
					if (isset($children))
					{
						if (is_array($children))
							$tlv->addRange($children);
						else
							$tlv->add($children);
					}
				}
				$tlvs[] = $tlv;
			} while (strlen($data)>0);

			if (sizeof($tlvs)==0)
				return null;
			else if (sizeof($tlvs)==1)
				return $tlvs[0];
			else
				return $tlvs;
		}

	}

}

?>
