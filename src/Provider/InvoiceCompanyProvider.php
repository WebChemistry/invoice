<?php declare(strict_types = 1);

namespace Contributte\Invoice\Provider;

use Contributte\Invoice\Data\ICompany;

final class InvoiceCompanyProvider
{

	public function __construct(
		private ICompany $company
	)
	{
	}

	public function getCompany(): ICompany
	{
		return $this->company;
	}

}
