<?php declare(strict_types = 1);

namespace Contributte\Invoice\DI;

use Contributte\Invoice\Data\Account;
use Contributte\Invoice\Data\Company;
use Contributte\Invoice\Invoice;
use Contributte\Invoice\Provider\InvoiceDataProvider;
use Nette\DI\CompilerExtension;
use Nette\DI\Definitions\Statement;
use Nette\Schema\Expect;
use Nette\Schema\Schema;
use stdClass;

class InvoiceExtension extends CompilerExtension
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

		$builder->addDefinition($this->prefix('dataProvider'))
			->setFactory(InvoiceDataProvider::class, [
				$this->getCompany(),
				$this->getAccounts(),
			]);

		$builder->addDefinition($this->prefix('invoice'))
			->setFactory(Invoice::class);
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
