<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1
{

	class Query
	{
		protected $_values;
		protected $_exists;

		public function __construct($string)
		{
			$this->_values = array();
			$this->_exists = array();
			if (is_array($string))
			{
				foreach ($string as $k=>$v)
					$this->_values[$k] = $v;
			}
			else if (is_string($string))
			{
				$string = explode(',', $string);
				foreach ($string as $kvp)
				{
					$kvp = explode('=', $kvp);
					if (sizeof($kvp)==1)
						$this->_exists[] = $kvp[0];
					else if (sizeof($kvp)==2)
						$this->_values[$kvp[0]] = $kvp[1];
					else
						throw new QueryParseException(implode('=', $kvp));
				}
			}
			else
				throw new QueryParseException("Unknown format");
		}

		public function matches(TLV $tlv)
		{
			foreach ($this->_values as $k=>$v)
			{
				if (property_exists($tlv, $k))
				{
					if ($tlv->$k != $v)
						return false;
				}
				else if (method_exists($tlv, 'get'.$k))
				{
					$m = 'get'.$k;
					if ($tlv->$m()!=$v)
						return false;
				}
				else
					return false;
			}

			foreach ($this->_exists as $n)
			{
				if (property_exists($tlv, $n))
					continue;
				if (method_exists($tlv, 'is'.$n))
				{
					$m = 'is'.$n;
					if (!$tlv->$m())
						return false;
					continue;
				}
				return false;
			}

			return true;
		}

	}

}

?>
