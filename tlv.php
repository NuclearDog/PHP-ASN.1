<?php

/*
 * PHP ASN.1 - ASN.1 BER Library for PHP
 * Adam Pippin (GP, Inc.) <adam@gp-inc.ca>
 *
 * Please see the LICENSE file for the license terms.
 */

namespace ASN1
{

	// As per the ASN.1 BER specs:

	/**
	* UNIVERSAL - A type native to ASN.1.
	*/
	define('TLV_CLASS_UNIVERSAL', 0);
	/**
	* APPLICATION - Valid within the specifying application.
	*/
	define('TLV_CLASS_APPLICATION', 1);
	/**
	* CONTEXT - Meaning depends on context (such as place within a sequence, etc).
	*/
	define('TLV_CLASS_CONTEXT', 2);
	/**
	* PRIVATE - Defined in a private specification.
	*/
	define('TLV_CLASS_PRIVATE', 3);

	/**
	* PRIMITIVE - The value is the value of this TLV.
	*/
	define('TLV_TYPE_PRIMITIVE', 0);
	/**
	* CONSTRUCTED - The value contains more TLV nodes.
	*/
	define('TLV_TYPE_CONSTRUCTED', 1);

	/**
	* If the TLV length is TLV_LENGTH_INDEFINITE, we continue reading until
	* we hit an EOD TLV.
	*/
	define('TLV_LENGTH_INDEFINITE', 0x80);

	// These are the various tags for the UNIVERSAL types.
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

		/**
		* Initializes a new TLV object if desired.
		*
		* @param \stdClass $data An object containing any of Class, Type, Tag, Length, Value, Children.
		*/
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

		/**
		* Does the tag specifies this is a UNIVERSAL type?
		*
		* @return bool
		*/
		public function isUniversal()
		{
			return $this->_Class == TLV_CLASS_UNIVERSAL;
		}

		/**
		* Does the tag specify this is an APPLICATION type?
		*
		* @return bool
		*/
		public function isApplication()
		{
			return $this->_Class == TLV_CLASS_APPLICATION;
		}

		/**
		* Does the tag specify this is a CONTEXT type?
		*
		* @return bool
		*/
		public function isContext()
		{
			return $this->_Class == TLV_CLASS_CONTEXT;
		}

		/**
		* Does the tag specify this is a PRIVATE type?
		*
		* @return bool
		*/
		public function isPrivate()
		{
			return $this->_Class == TLV_CLASS_PRIVATE;
		}

		/**
		* Gets the underlying type number.
		*
		* @return integer One of TLV_TYPE_PRIMITIVE or TLV_TYPE_CONSTRUCTED
		*/
		public function getType()
		{
			return $this->_Type;
		}

		/**
		* Gets the underlying class number.
		*
		* @return integer One of TLV_CLASS_UNIVERSAL, TLV_CLASS_APPLICATION, TLV_CLASS_CONTEXT or TLV_CLASS_PRIVATE.
		*/
		public function getClass()
		{
			return $this->_Class;
		}
		
		/**
		* Gets the tag number.
		*
		* @return integer
		*/
		public function getTag()
		{
			return $this->_Tag;
		}

		/**
		* Return the 'friendly' value of this TLV as determined by its type.
		*/
		public function __toString()
		{
			return (string)$this->get();
		}

		/**
		* Convert a primitive to a construct.
		*/
		public function makeConstruct()
		{
			if ($this->_Type == TLV_TYPE_PRIMITIVE)
			{
				$this->_Type = TLV_TYPE_CONSTRUCTED;
				$this->_Children = array();
				$this->_pointer = 0;
			}
		}

		/**
		* Is this TLV a CONSTRUCT (has children)?
		*
		* @return bool
		*/
		public function isConstruct()
		{
			return $this->_Type==TLV_TYPE_CONSTRUCTED;
		}

		/**
		* Is this TLV a PRIMITIVE (has no children)?
		*
		* @return bool
		*/
		public function isPrimitive()
		{
			return $this->_Type==TLV_TYPE_PRIMITIVE;
		}

		// For the Iterator interface:

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

		/**
		* Add a child TLV. Converts this TLV to a construct if necessary.
		*
		* @param \ASN1\TLV $tlv
		*/
		public function add(TLV $tlv)
		{
			if ($this->_Type!=TLV_TYPE_CONSTRUCTED)
				$this->makeConstruct();
			$this->_Children[] = $tlv;
		}

		/**
		* Adds an array of child TLVs. Converts this TLV to a construct if
		* necessary.
		*
		* @param array $tlvs
		*/
		public function addRange(array $tlvs)
		{
			if ($this->_Type!=TLV_TYPE_CONSTRUCTED)
				$this->makeConstruct();
			foreach ($tlvs as $tlv)
				$this->add($tlv);
		}

		/**
		* Converts 'query' to a query object and finds the first child matching
		* it.
		*
		* @param string $query
		* @return \ASN1\TLV
		*/
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

		/**
		* Returns the first child of this TLV.
		*
		* @return \ASN!\TLV
		*/
		public function first()
		{
			if (!$this->isConstruct())
				throw new TLVMisuseException("TLV is not a construct - cannot access children.");
			return $this->_Children[0];
		}

		/**
		* Set the value of this TLV, filtering it through the type (if existing)
		* first to handle conversions.
		*
		* @param mixed $value
		* @return \ASN1\TLV This object.
		*/
		public function set($value)
		{
			$type = $this->type();
			$type::write($this, $value);
			return $this;
		}

		/**
		* Get the value of this TLV, filtering it through the type (if existing)
		* first to handle conversions.
		*
		* @return mixed
		*/
		public function get()
		{
			$type = $this->type();
			$type = ASN1::getType($this->_Tag);
			return $type::read($this);
		}

		/**
		* Return the class name of the TLVType representing this TLV.
		*
		* @return string
		*/
		public function type()
		{
			if ($this->isUniversal())
				return ASN1::getType($this->_Tag);
			else
				return ASN1::getType('dumb');
		}

		/**
		* Provides raw access to read the value of this TLV.
		*
		* @return mixed
		*/
		public function read()
		{
			return $this->_Value;
		}

		/**
		* Provides raw access to write the value of this TLV.
		*
		* @param mixed $value
		*/
		public function write($value)
		{
			$this->_Value = $value;
		}

	}

}

?>
