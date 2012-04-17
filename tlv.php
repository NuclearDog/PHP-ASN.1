<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1
{

	define('TLV_CLASS_UNIVERSAL', 0);
	define('TLV_CLASS_APPLICATION', 1);
	define('TLV_CLASS_CONTEXT', 2);
	define('TLV_CLASS_PRIVATE', 3);

	define('TLV_TYPE_PRIMITIVE', 0);
	define('TLV_TYPE_CONSTRUCTED', 1);

	define('TLV_LENGTH_INDEFINITE', 0x80);

	define('TLV_TAG_EOC', 0x00);
	define('TLV_TAG_BOOLEAN', 0x01);
	define('TLV_TAG_INTEGER', 0x02);
	define('TLV_TAG_BITSTRING', 0x03);
	define('TLV_TAG_OCTETSTRING', 0x04);
	define('TLV_TAG_NULL', 0x05);
	define('TLV_TAG_OID', 0x06);
	define('TLV_TAG_OBJECTDESCRIPTOR', 0x07);
	define('TLV_TAG_EXTERNAL', 0x08);
	define('TLV_TAG_FLOAT', 0x09);
	define('TLV_TAG_ENUMERATED', 0x0A);
	define('TLV_TAG_EMBEDDEDPDV', 0x0B);
	define('TLV_TAG_UTF8STRING', 0x0C);
	define('TLV_TAG_RELATIVEOID', 0x0D);
	define('TLV_TAG_RESERVED_0E', 0x0E);
	define('TLV_TAG_RESERVED_0F', 0x0F);
	define('TLV_TAG_SEQUENCE', 0x10);
	define('TLV_TAG_SET', 0x11);
	define('TLV_TAG_NUMERICSTRING', 0x12);
	define('TLV_TAG_PRINTABLESTRING', 0x13);
	define('TLV_TAG_T61STRING', 0x14);
	define('TLV_TAG_VIDEOTEXSTRING', 0x15);
	define('TLV_TAG_IA5STRING', 0x16);
	define('TLV_TAG_UTCTIME', 0x17);
	define('TLV_TAG_GENERALIZEDTIME', 0x18);
	define('TLV_TAG_GRAPHICSTRING', 0x19);
	define('TLV_TAG_VISIBLESTRING', 0x1A);
	define('TLV_TAG_GENERALSTRING', 0x1B);
	define('TLV_TAG_UNIVERSALSTRING', 0x1C);
	define('TLV_TAG_CHARACTERSTRING', 0x1D);
	define('TLV_TAG_BMPSTRING', 0x1E);
	define('TLV_TAG_LONGFORM', 0x1F);

	/**
	* Represents a type-length-value object.
	*/
	class TLV implements \Iterator
	{
		protected $_Class, $_Type, $_Tag, $_Length, $_Value;
		protected $_Children;
		protected $_pointer;

		public function __construct(\stdClass $data = null)
		{
			if (isset($data))
			{
				if (isset($data->Class))
					$this->_Class = $data->Class;
				else
					$this->_Class = TLV_CLASS_UNIVERSAL;

				if (isset($data->Type))
				{
					$this->_Type = $data->Type;
					if ($this->_Type == TLV_TYPE_CONSTRUCTED)
					{
						$this->_Children = array();
						$this->_pointer = 0;
					}
				}
				else
					$this->_Type = TLV_TYPE_PRIMITIVE;

				if (isset($data->Tag))
					$this->_Tag = $data->Tag;

				if (isset($data->Length))
					$this->_Length = $data->Length;

				if (isset($data->Value))
					$this->_Value = $data->Value;
			}
		}

		public function isUniversal()
		{
			return $this->_Class == TLV_CLASS_UNIVERSAL;
		}

		public function isApplication()
		{
			return $this->_Class == TLV_CLASS_APPLICATION;
		}

		public function isContext()
		{
			return $this->_Class == TLV_CLASS_CONTEXT;
		}

		public function isPrivate()
		{
			return $this->_Class == TLV_CLASS_PRIVATE;
		}

		public function getType()
		{
			return $this->_Type;
		}

		public function getClass()
		{
			return $this->_Class;
		}
		
		public function getTag()
		{
			return $this->_Tag;
		}

		public function __toString()
		{
			return (string)$this->get();
		}

		public function makeConstruct()
		{
			$this->_Type = TLV_TYPE_CONSTRUCTED;
			$this->_Children = array();
			$this->_pointer = 0;
		}

		public function isConstruct()
		{
			return $this->_Type==TLV_TYPE_CONSTRUCTED;
		}

		public function isPrimitive()
		{
			return $this->_Type==TLV_TYPE_PRIMITIVE;
		}

		public function current()
		{
			if ($this->_Type!=TLV_TYPE_CONSTRUCTED)
				$this->makeConstruct();
			return $this->_Children[$this->_pointer];
		}

		public function key()
		{
			if ($this->_Type!=TLV_TYPE_CONSTRUCTED)
				$this->makeConstruct();
			return $this->_pointer;
		}

		public function next()
		{
			if ($this->_Type!=TLV_TYPE_CONSTRUCTED)
				$this->makeConstruct();
			$this->_pointer++;
		}

		public function rewind()
		{
			if ($this->_Type!=TLV_TYPE_CONSTRUCTED)
				$this->makeConstruct();
			$this->_pointer = 0;
		}

		public function valid()
		{
			if ($this->_Type!=TLV_TYPE_CONSTRUCTED)
				$this->makeConstruct();
			return isset($this->_Children[$this->_pointer]);
		}

		public function add(TLV $tlv)
		{
			if ($this->_Type!=TLV_TYPE_CONSTRUCTED)
				$this->makeConstruct();
			$this->_Children[] = $tlv;
		}

		public function addRange(array $tlvs)
		{
			if ($this->_Type!=TLV_TYPE_CONSTRUCTED)
				$this->makeConstruct();
			foreach ($tlvs as $tlv)
				$this->add($tlv);
		}

		public function find($query)
		{
			if (!$this->isConstruct())
				throw new TLVMisuseException("TLV is not a construct - cannot find children.");

			$q = new Query($query);
			foreach ($this->_Children as $child)
			{
				if ($q->matches($child))
					return $child;
			}

			return null;
		}

		public function first()
		{
			if (!$this->isConstruct())
				throw new TLVMisuseException("TLV is not a construct - cannot access children.");
			return $this->_Children[0];
		}

		public function set($value)
		{
			$type = $this->type();
			$type::write($this, $value);
			return $this;
		}

		public function get()
		{
			$type = $this->type();
			$type = ASN1::getType($this->_Tag);
			return $type::read($this);
		}

		public function type()
		{
			if ($this->isUniversal())
				return ASN1::getType($this->_Tag);
			else
				return ASN1::getType('dumb');
		}

		public function read()
		{
			return $this->_Value;
		}

		public function write($value)
		{
			$this->Value = $value;
		}

	}

}

?>
