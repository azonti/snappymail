<?php

namespace RainLoop\Providers\AddressBook;

use
	RainLoop\Providers\AddressBook\Enumerations\PropertyType,
	RainLoop\Providers\AddressBook\Classes\Property
;


class Utils
{
	/**
	 * @param mixed $oProp
	 */
	private static function yieldPropertyHelper($oArrayProp, int $iType) : iterable
	{
		$aTmp = [];
		foreach ($oArrayProp as $oProp) {
			$sValue = \trim($oProp->getValue());
			if (\strlen($sValue)) {
				$oTypes = $oProp['TYPE'];
				$aTypes = $oTypes ? $oTypes->getParts() : array();
				$pref = 100;
				if (0 < $oProp['PREF']) {
					$pref = (int) $oProp['PREF'];
				}
				$aTmp[\substr(1000+$pref,-3) . $sValue] = new Property($iType, $sValue, \implode(',', $aTypes));
			}
		}
		\ksort($aTmp);
		foreach ($aTmp as $oProp) {
			yield $oProp;
		}
	}

	/**
	 * @param mixed $oProp
	 */
	private static function getPropertyValueHelper($oProp, bool $bOldVersion) : string
	{
		$sValue = \trim($oProp);
		if ($bOldVersion && !isset($oProp->parameters['CHARSET'])) {
			if (\strlen($sValue)) {
				$sEncValue = \utf8_encode($sValue);
				if (\strlen($sEncValue)) {
					$sValue = $sEncValue;
				}
			}
		}
		return \MailSo\Base\Utils::Utf8Clear($sValue);
	}

	public static function VCardToProperties(\Sabre\VObject\Component\VCard $oVCard) : iterable
	{
		yield new Property(PropertyType::JCARD, \json_encode($oVCard));

		$bOldVersion = !empty($oVCard->VERSION) && \in_array((string) $oVCard->VERSION, array('2.1', '2.0', '1.0'));

		if (isset($oVCard->FN) && '' !== \trim($oVCard->FN)) {
			$sValue = static::getPropertyValueHelper($oVCard->FN, $bOldVersion);
			yield new Property(PropertyType::FULLNAME, $sValue);
		}

		if (isset($oVCard->N)) {
			$aNames = $oVCard->N->getParts();
			foreach ($aNames as $iIndex => $sValue) {
				$sValue = \trim($sValue);
				if ($bOldVersion && !isset($oVCard->N->parameters['CHARSET'])) {
					if (\strlen($sValue)) {
						$sEncValue = \utf8_encode($sValue);
						if (\strlen($sEncValue)) {
							$sValue = $sEncValue;
						}
					}
				}
				$sValue = \MailSo\Base\Utils::Utf8Clear($sValue);
				if ($sValue) {
					switch ($iIndex) {
						case 0:
							yield new Property(PropertyType::LAST_NAME, $sValue);
							break;
						case 1:
							yield new Property(PropertyType::FIRST_NAME, $sValue);
							break;
						case 2:
							yield new Property(PropertyType::MIDDLE_NAME, $sValue);
							break;
						case 3:
							yield new Property(PropertyType::NAME_PREFIX, $sValue);
							break;
						case 4:
							yield new Property(PropertyType::NAME_SUFFIX, $sValue);
							break;
					}
				}
			}
		}

		if (isset($oVCard->EMAIL)) {
			yield from static::yieldPropertyHelper($oVCard->EMAIL, PropertyType::EMAIl);
		}

		if (isset($oVCard->URL)) {
			yield from static::yieldPropertyHelper($oVCard->URL, PropertyType::WEB_PAGE);
		}

		if (isset($oVCard->TEL)) {
			yield from static::yieldPropertyHelper($oVCard->TEL, PropertyType::PHONE);
		}
	}

	private static $aMap = array(
		'title'               => 'FN',
		'name'                => 'FN',
		'fullname'            => 'FN',
		'displayname'         => 'FN',
		'last'                => 0,
		'lastname'            => 0,
		'givenname'           => 1,
		'first'               => 1,
		'firstname'           => 1,
		'middle'              => 2,
		'middlename'          => 2,
		'prefix'              => 3,
		'nameprefix'          => 3,
		'suffix'              => 4,
		'namesuffix'          => 4,
		'shortname'           => 'NICKNAME',
		'nickname'            => 'NICKNAME',
		'businessphone'       => array('TEL', 'WORK'),
		'businessphone2'      => array('TEL', 'WORK'),
		'businessphone3'      => array('TEL', 'WORK'),
		'companyphone'        => array('TEL', 'WORK'),
		'companymainphone'    => array('TEL', 'WORK'),
		'homephone'           => array('TEL', 'HOME'),
		'homephone2'          => array('TEL', 'HOME'),
		'homephone3'          => array('TEL', 'HOME'),
		'mobile'              => array('TEL', 'CELL'),
		'mobilephone'         => array('TEL', 'CELL'),
		'businessmobile'      => array('TEL', 'WORK,CELL'),
		'businessmobilephone' => array('TEL', 'WORK,CELL'),
		'otherphone'          => 'TEL',
		'primaryphone'        => array('TEL', 'PREF,HOME'),
		'email'               => array('EMAIL', 'HOME'),
		'email2'              => array('EMAIL', 'HOME'),
		'email3'              => array('EMAIL', 'HOME'),
		'homeemail'           => array('EMAIL', 'HOME'),
		'homeemail2'          => array('EMAIL', 'HOME'),
		'homeemail3'          => array('EMAIL', 'HOME'),
		'primaryemail'        => array('EMAIL', 'HOME'),
		'primaryemail2'       => array('EMAIL', 'HOME'),
		'primaryemail3'       => array('EMAIL', 'HOME'),
		'emailaddress'        => array('EMAIL', 'HOME'),
		'email2address'       => array('EMAIL', 'HOME'),
		'email3address'       => array('EMAIL', 'HOME'),
		'otheremail'          => 'EMAIL',
		'businessemail'       => array('EMAIL', 'WORK'),
		'businessemail2'      => array('EMAIL', 'WORK'),
		'businessemail3'      => array('EMAIL', 'WORK'),
		'personalemail'       => array('EMAIL', 'HOME'),
		'personalemail2'      => array('EMAIL', 'HOME'),
		'personalemail3'      => array('EMAIL', 'HOME'),
		'notes'               => 'NOTE',
		'web'                 => 'URL',
		'businessweb'         => array('URL', 'WORK'),
		'webpage'             => 'URL',
		'businesswebpage'     => array('URL', 'WORK'),
		'website'             => 'URL',
		'businesswebsite'     => array('URL', 'WORK'),
		'personalwebsite'     => 'URL',
		'birthday'            => 'BDAY'
	);

	public static function CsvArrayToContacts(array $aCsvData) : iterable
	{
		foreach ($aCsvData as $aItem) {
			\MailSo\Base\Utils::ResetTimeLimit();
			$iCount = 0;
			$oVCard = new \Sabre\VObject\Component\VCard;
			$aName = ['','','','',''];
			foreach ($aItem as $sItemName => $sItemValue) {
				$sItemName = \strtoupper(\trim(\preg_replace('/[\s\-]+/', '', $sItemName)));
				$sItemValue = \trim($sItemValue);
				if (!empty($sItemName) && !empty($sItemValue)) {
					if (\array_key_exists($sItemName, \Sabre\VObject\Component\VCard::$propertyMap)) {
						$mData = $sItemName;
					} else {
						$sItemName = \strtolower($sItemName);
						$mData = !empty($sItemName) && isset($aMap[$sItemName]) ? $aMap[$sItemName] : null;
					}
					if ($mData) {
						$mType = \is_array($mData) ? $mData[0] : $mData;
						++$iCount;
						if (\is_int($mType)) {
							$aName[$mType] = $sItemValue;
						} else if (\is_array($mData)) {
							$oVCard->add($mType, $sItemValue, ['type' => $mData[1]]);
						} else if ('FN' === $mType || 'NICKNAME' === $mType) {
							$oVCard->$mType = $sItemValue;
						} else {
							$oVCard->add($mType, $sItemValue);
						}
					}
				}
			}
			if ($iCount) {
				if ('' !== \implode('', $aName)) {
					$oVCard->N = $aName;
				}
				$oContact = new Classes\Contact();
				$oContact->setVCard($oVCard);
				yield $oContact;
			}
		}
	}

	public static function VCardToCsv($stream, Classes\Contact $oContact, bool $bWithHeader = false)/* : int|false*/
	{
		$aData = array();
		if ($bWithHeader) {
			\fputcsv($stream, array(
				'Title', 'First Name', 'Middle Name', 'Last Name', 'Nick Name', 'Display Name',
				'Company', 'Department', 'Job Title', 'Office Location',
				'E-mail Address', 'Notes', 'Web Page', 'Birthday', 'Mobile Phone',
				'Home Email', 'Home Phone',
				'Home Street', 'Home City', 'Home State', 'Home Postal Code', 'Home Country',
				'Business Email', 'Business Phone',
				'Business Street', 'Business City', 'Business State', 'Business Postal Code', 'Business Country'
			));
		}

		$oVCard = $oContact->vCard;

		$aName = isset($oVCard->N) ? $oVCard->N->getParts() : ['','','','',''];

		$adrHome = $oVCard->getByType('ADR', 'HOME');
		$adrHome = $adrHome ? $adrHome->getParts() : ['','','','','','',''];

		$adrWork = $oVCard->getByType('ADR', 'WORK');
		$adrWork = $adrWork ? $adrWork->getParts() : ['','','','','','',''];

		return \fputcsv($stream, array(
			(string) $oVCard->FN,       // Title
			$aName[1],                  // First Name
			$aName[2],                  // Middle Name
			$aName[0],                  // Last Name
			(string) $oVCard->NICKNAME, // Nick Name
			(string) $oVCard->FN,       // Display Name
			(string) $oVCard->ORG,      // Company
			'',                         // Department
			'',                         // Job Title
			'',                         // Office Location
			(string) $oVCard->EMAIL,    // E-mail Address
			(string) $oVCard->NOTE,     // Notes
			(string) $oVCard->URL,      // Web Page
			(string) $oVCard->BDAY,     // Birthday
			(string) $oVCard->getByType('TEL', 'CELL'),   // Mobile Phone
			// Home
			(string) $oVCard->getByType('EMAIL', 'HOME'), // Email
			(string) $oVCard->getByType('TEL', 'HOME'),   // Phone
			\trim($adrHome[1]."\n".$adrHome[2]), // extended address + street address
			$adrHome[3],                // City
			$adrHome[4],                // State
			$adrHome[5],                // Postal Code
			$adrHome[6],                // Country
			// Business
			(string) $oVCard->getByType('EMAIL', 'WORK'), // Email
			(string) $oVCard->getByType('TEL', 'WORK'),   // Phone
			\trim($adrWork[1]."\n".$adrWork[2]), // extended address + street address
			$adrWork[3],                // City
			$adrWork[4],                // State
			$adrWork[5],                // Postal Code
			$adrWork[6]                 // Country
		));
	}

	public static function VcfFileToContacts(string $sVcfData) : iterable
	{
		$sVcfData = \trim($sVcfData);
		if ("\xef\xbb\xbf" === \substr($sVcfData, 0, 3)) {
			$sVcfData = \substr($sVcfData, 3);
		}
		$oVCardSplitter = new \Sabre\VObject\Splitter\VCard($sVcfData);
		if ($oVCardSplitter) {
			while ($oVCard = $oVCardSplitter->getNext()) {
				if ($oVCard instanceof \Sabre\VObject\Component\VCard) {
					\MailSo\Base\Utils::ResetTimeLimit();
					$oContact = new Classes\Contact();
					$oContact->setVCard($oVCard);
					yield $oContact;
				}
			}
		}
	}
}
