<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1
{

	class ASN1
	{
		protected static $_types;

		public static function loadTypes()
		{
			$dir = dirname(__FILE__).'/tlv/';
			self::$_types = array();
			$files = glob($dir.'*');
			foreach ($files as $file)
			{
				require($file);
				$file = basename($file);
				$class = '\\ASN1\\TLV\\'.substr($file, 0, strpos($file, '.'));

				self::$_types[$class::getTag()] = $class;
			}
		}

		public static function getType($tag)
		{
			if (!isset(self::$_types[$tag]))
				return self::$_types['dumb'];
			else
				return self::$_types[$tag];
		}

		public static function load()
		{
			$dir = dirname(__FILE__).'/';
			require($dir.'parser.php');
			require($dir.'serializer.php');
			require($dir.'tlv.php');
			require($dir.'tlvtype.php');
			require($dir.'reader.php');
			require($dir.'writer.php');
			require($dir.'exceptions.php');
			require($dir.'query.php');
			require($dir.'output.php');

			self::loadTypes();
		}

		public static function createOutput(TLV $tlv)
		{
			return new Output($tlv);
		}

		public static function createParser(TLVReader $reader)
		{
			return new Parser($reader);
		}

		public static function createSerializer(TLVWriter $writer)
		{
			return new Serializer($writer);
		}

		public static function createReader()
		{
			return new TLVReader();
		}

		public static function createWriter()
		{
			return new TLVWriter();
		}

	}

}

?>
