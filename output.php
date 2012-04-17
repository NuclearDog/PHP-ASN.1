<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1
{

	class Output
	{
		protected $_tlv;

		public function __construct(TLV $tlv)
		{
			$this->_tlv = $tlv;
		}

		public function display()
		{
			echo self::dump($this->_tlv);
		}

		public static function dump(TLV $tlv, $depth=0)
		{
			$ret = '';
			$type = $tlv->type();
			$ret .= str_repeat('	', $depth)."[".$type::getName()." 0x".dechex(ord($tlv->getTag()))."";
			if ($tlv->isContext())
				$ret .= " (".$tlv->getTag().")";
			if ($tlv->isConstruct())
			{
				$ret .= PHP_EOL;
				foreach ($tlv as $child)
				{
					$ret .= self::dump($child, $depth+1);
				}
				$ret .= str_repeat('	', $depth).']'.PHP_EOL;
			}
			else
			{
				$ret .= ": \"";
				$str = (string)$tlv;
				$str = preg_replace_callback("/[^([:print:])]/", function($matches) {
					return '['.rtrim(base64_encode($matches[0]), '=').']';
				}, $str);
				$ret .= $str;
				$ret .= "\"]".PHP_EOL;
			}

			return $ret;
		}

	}

}

?>
