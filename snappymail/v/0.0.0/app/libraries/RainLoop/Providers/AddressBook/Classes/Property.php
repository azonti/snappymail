<?php

namespace RainLoop\Providers\AddressBook\Classes;

use RainLoop\Providers\AddressBook\Enumerations\PropertyType;

class Property implements \JsonSerializable
{
	/**
	 * @var int
	 */
	public $IdProperty = 0;

	/**
	 * @var int
	 */
	public $Type;

	/**
	 * @var string
	 */
	public $TypeStr;

	/**
	 * @var string
	 */
	public $Value;

	/**
	 * @var string
	 */
	public $ValueLower = '';

	/**
	 * @var string
	 */
	public $ValueCustom = '';

	/**
	 * @var int
	 */
	public $Frec = 0;

	public function __construct(int $iType = PropertyType::UNKNOWN, string $sValue = '', string $sTypeStr = '')
	{
		$this->Type = $iType;
		$this->Value = $sValue;
		$this->TypeStr = $sTypeStr;
	}

	public function IsName() : bool
	{
		return \in_array($this->Type, array(PropertyType::FULLNAME, PropertyType::FIRST_NAME,
			PropertyType::LAST_NAME, PropertyType::MIDDLE_NAME, PropertyType::NICK_NAME));
	}

	public function IsEmail() : bool
	{
		return PropertyType::EMAIl === $this->Type;
	}

	public function IsPhone() : bool
	{
		return PropertyType::PHONE === $this->Type;
	}

	public function IsWeb() : bool
	{
		return PropertyType::WEB_PAGE === $this->Type;
	}

	public function IsValueForLower() : bool
	{
		return $this->IsEmail() || $this->IsName() || $this->IsWeb();
	}

	public function TypesAsArray() : array
	{
		$aResult = array();
		if (!empty($this->TypeStr))
		{
			$sTypeStr = \preg_replace('/[\s]+/', '', $this->TypeStr);
			$aResult = \explode(',', $sTypeStr);
		}

		return $aResult;
	}

	public function TypesUpperAsArray() : array
	{
		return \array_map('strtoupper', $this->TypesAsArray());
	}

	public function UpdateDependentValues() : void
	{
		$this->Value = \trim($this->Value);
		$this->ValueCustom = \trim($this->ValueCustom);
		$this->TypeStr = \trim($this->TypeStr);
		$this->ValueLower = '';

		if (\strlen($this->Value))
		{
			// lower
			if ($this->IsEmail())
			{
				$this->Value = \MailSo\Base\Utils::StrMailDomainToLower($this->Value);
			}

			if ($this->IsName())
			{
				$this->Value = \MailSo\Base\Utils::StripSpaces($this->Value);
			}

			// lower value for searching
			if ($this->IsValueForLower())
			{
				$this->ValueLower = (string) \mb_strtolower($this->Value, 'UTF-8');
			}

			// phone value for searching
			if ($this->IsPhone())
			{
				$sPhone = \trim($this->Value);
				$sPhone = \preg_replace('/^[+]+/', '', $sPhone);
				$sPhone = \preg_replace('/[^\d]/', '', $sPhone);

				$this->ValueCustom = \trim($sPhone);
			}
		}
	}

	#[\ReturnTypeWillChange]
	public function jsonSerialize()
	{
		// Simple hack
		if ($this && $this->IsWeb())
		{
			$this->Value = \preg_replace('#(https?)\\\://#i', '$1://', $this->Value);
		}
		return array(
			'@Object' => 'Object/ContactProperty',
			'id' => $this->IdProperty,
			'type' => $this->Type,
			'typeStr' => $this->TypeStr,
			'value' => \MailSo\Base\Utils::Utf8Clear($this->Value)
		);
	}
}
