<?php declare(strict_types = 1);

namespace Contributte\Invoice\Components;

use Contributte\Invoice\Data\Item;
use Nette\SmartObject;

class Paginator
{

	use SmartObject;

	/** @var Item[] */
	private $items;

	/** @var int */
	protected $currentPage = 0;

	/** @var int */
	protected $totalPages;

	/** @var int */
	private $itemsPerPage;

	/**
	 * @param Item[] $items
	 */
	public function __construct(array $items, int $itemsPerPage)
	{
		$this->items = $items;
		$this->totalPages = (int) ceil(count($this->items) / $itemsPerPage);
		$this->itemsPerPage = $itemsPerPage;
	}

	public function getTotalPages(): int
	{
		return $this->totalPages;
	}

	/**
	 * @return Item[]
	 */
	public function getItems(): array
	{
		$page = $this->currentPage - 1;

		return array_slice($this->items, $page * $this->itemsPerPage, $page * $this->itemsPerPage + $this->itemsPerPage);
	}

	public function isFirstPage(): bool
	{
		return $this->currentPage === 1;
	}

	public function isLastPage(): bool
	{
		return $this->currentPage >= $this->getTotalPages();
	}

	public function getCurrentPage(): int
	{
		return $this->currentPage;
	}

	public function nextPage(): bool
	{
		if ($this->isLastPage()) {
			return false;
		}

		$this->currentPage++;

		return true;
	}

}
