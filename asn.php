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

		/**
		* Load all TLVTypes from the tlv/ folder.
		*/
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

		/**
		* Search all loaded types and return the one corresponding with
		* the passed tag.
		*
		* @param integer $tag
		* @return \ASN1\TLVType
		*/
		public static function getType($tag)
		{
			if (!isset(self::$_types[$tag]))
				return self::$_types['dumb'];
			else
				return self::$_types[$tag];
		}

		/**
		* Load the rest of the ASN.1 library.
		*/
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

		/**
		* Create an instance of the output class to output the passed
		* TLV in some way.
		*
		* @param TLV $tlv
		* @return \ASN1\Output
		*/
		public static function createOutput(TLV $tlv)
		{
			return new Output($tlv);
		}

		/**
		* Create an instance of the parser class using the passed reader.
		* The parser class is responsible for converting a binary stream
		* into a series of TLV objects.
		*
		* @param \ASN1\TLVReader $reader
		* @return \ASN1\Parser
		*/
		public static function createParser(TLVReader $reader)
		{
			return new Parser($reader);
		}

		/**
		* Create an instance of the serializer class using the passed writer.
		* The serializer class is responsible for converting a tree of TLV
		* objects into a binary stream.
		*
		* @param \ASN1\TLVWriter $writer
		* @return \ASN1\Serializer
		*/
		public static function createSerializer(TLVWriter $writer)
		{
			return new Serializer($writer);
		}

		/**
		* Create an instance of the TLVReader which reads (but does not
		* in any way interpret) single TLV values from the stream.
		*
		* @return \ASN1\TLVReader
		*/
		public static function createReader()
		{
			return new TLVReader();
		}

		/**
		* Create an instance of the TLVWriter which serializes single
		* TLV values.
		*
		* @return \ASN1\TLVWriter
		*/
		public static function createWriter()
		{
			return new TLVWriter();
		}

	}

}

?>
