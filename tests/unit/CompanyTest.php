<?php declare(strict_types = 1);

use Codeception\PHPUnit\TestCase;
use Contributte\Invoice\Data\Company;

final class CompanyTest extends TestCase
{

	public function testCompany(): void
	{
		$company = new Company('name', 'town', 'address', 'zip', 'country', 'vatNumber', 'id');

		self::assertSame('name', $company->getName());
		self::assertSame('town', $company->getTown());
		self::assertSame('address', $company->getAddress());
		self::assertSame('zip', $company->getZip());
		self::assertSame('country', $company->getCountry());
		self::assertSame('vatNumber', $company->getVatNumber());
		self::assertSame('id', $company->getId());
	}

}
