<?php declare(strict_types = 1);

namespace Contributte\Invoice\Bridge\Nette\DI;

use Contributte\Invoice\Data\Account;
use Contributte\Invoice\Data\Company;
use Contributte\Invoice\Provider\InvoiceAccountsProvider;
use Contributte\Invoice\Provider\InvoiceCompanyProvider;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

final class InvoiceExtension extends CompilerExtension
{

	public function getConfigSchema(): Schema
	{
		return Expect::structure([
			'company' => Expect::structure([
				'name' => Expect::string()->required(),
				'town' => Expect::string()->required(),
				'address' => Expect::string()->required(),
				'zip' => Expect::string()->required(),
				'country' => Expect::string()->required(),
				'vatNumber' => Expect::string(),
				'id' => Expect::string(),
			]),
			'accounts' => Expect::arrayOf(Expect::structure([
				'iban' => Expect::string(),
			])),
		]);
	}

	public function loadConfiguration(): void
	{
		$builder = $this->getContainerBuilder();

		$company = $this->getCompany();
		if ($company) {
			$builder->addDefinition($this->prefix('companyProvider'))
				->setFactory(InvoiceCompanyProvider::class, [$company]);
		}

		$accounts = $this->getAccounts();
		if ($accounts) {
			$builder->addDefinition($this->prefix('accountsProvider'))
				->setFactory(InvoiceAccountsProvider::class, [$accounts]);
		}
	}

	private function getCompany(): ?Statement
	{
		/** @var stdClass $config */
		$config = $this->getConfig();

		/** @var stdClass $company */
		$company = $config->company;

		if (!$company->name) {
			return null;
		}

		return new Statement(Company::class, [
			$company->name,
			$company->town,
			$company->address,
			$company->zip,
			$company->country,
			$company->vatNumber,
			$company->id,
		]);
	}

	/**
	 * @return Statement[]
	 */
	private function getAccounts(): array
	{
		/** @var stdClass $config */
		$config = $this->getConfig();

		$accounts = [];
		/** @var stdClass $account */
		foreach ($config->accounts as $account) {
			$accounts[] = new Statement(Account::class, [$account->iban]);
		}

		return $accounts;
	}

}
