<?php declare(strict_types = 1);

namespace Contributte\Invoice\Provider;

use BadMethodCallException;
use Contributte\Invoice\Data\Account;
use LogicException;
use Nette\Utils\Arrays;

final class InvoiceAccountsProvider
{

	/** @var Account[] */
	private array $accounts;

	/**
	 * @param Account[] $accounts
	 */
	public function __construct(array $accounts)
	{
		if (!$accounts) {
			throw new BadMethodCallException('Accounts array cannot be empty.');
		}

		$this->accounts = $accounts;
	}

	/**
	 * @return Account[]
	 */
	public function getAccounts(): array
	{
		return $this->accounts;
	}

	public function getAccount(): Account
	{
		return Arrays::first($this->accounts) ?? throw new LogicException('Unexpected error.');
	}

}
