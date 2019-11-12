<?php declare(strict_types = 1);

namespace Contributte\Invoice\Templates;

use Contributte\Invoice\Formatter;
use Contributte\Invoice\IFormatter;
use Nette\SmartObject;
use Nette\Utils\Strings;
use Contributte\Invoice\Calculators\ICalculator;
use Contributte\Invoice\Components\Paginator;
use Contributte\Invoice\Data\Company;
use Contributte\Invoice\Data\Customer;
use Contributte\Invoice\Data\Item;
use Contributte\Invoice\Data\Order;
use Contributte\Invoice\ITranslator;
use Contributte\Invoice\Renderers\Color;
use Contributte\Invoice\Renderers\IRenderer;
use Contributte\Invoice\Renderers\Settings;
use Contributte\Invoice\Translator;

class DefaultTemplate implements ITemplate
{

	use SmartObject;

	private const ITEMS_PER_PAGE = 15;

	/** @var Color */
	private $primary;

	/** @var Color */
	private $font;

	/** @var Color */
	private $even;

	/** @var Color */
	private $odd;

	/** @var IRenderer */
	private $renderer;

	/** @var Customer */
	private $customer;

	/** @var Order */
	private $order;

	/** @var Company */
	private $company;

	/** @var ITranslator */
	private $translator;

	/** @var IFormatter */
	private $formatter;

	/** @var ICalculator */
	private $calculator;

	/** @var int */
	private $itemsPerPage = self::ITEMS_PER_PAGE;

	/** @var callable[] */
	public $onBuild = [];

	public function __construct(?ITranslator $translator = null, ?IFormatter $formatter = null)
	{
		$this->primary = new Color(6, 178, 194);
		$this->font = new Color(52, 52, 53);
		$this->even = new Color(241, 240, 240);
		$this->odd = Color::white();
		$this->translator = $translator ?: new Translator();
		$this->formatter = $formatter ?: new Formatter();
	}

	public function setItemsPerPage(int $itemsPerPage): self
	{
		$this->itemsPerPage = $itemsPerPage;

		return $this;
	}

	public function build(ICalculator $calculator, IRenderer $renderer, Customer $customer, Order $order, Company $company): string
	{
		$this->renderer = $renderer;
		$this->customer = $customer;
		$this->order = $order;
		$this->company = $company;
		$this->calculator = $calculator;

		$this->renderer->createNew();

		$this->renderer->addFont('sans', 'OpenSans-Regular.php');
		$this->renderer->addFont('sans', 'OpenSans-Semibold.php', Settings::FONT_STYLE_BOLD);
		$this->renderer->addFont('icons', 'pe.php');

		$paginator = new Paginator($this->order->getItems(), $this->itemsPerPage);

		while ($paginator->nextPage()) {
			if (!$paginator->isFirstPage()) {
				$this->renderer->addPage();
			}

			$this->buildHeader();
			$this->buildLeftPane();
			$this->buildRightPane();
			$this->buildMain();

			$offset = 337;
			$offset = $this->buildItems($offset, $paginator->getItems());

			if ($paginator->isLastPage()) {
				$this->buildTotal($offset);
			}

			$this->buildFooter($paginator);

			foreach ($this->onBuild as $build) {
				$build($paginator, $this->renderer, $this->formatter);
			}
		}

		return $this->renderer->output();
	}

	protected function buildTotal(int $offset): void
	{
		$renderer = $this->renderer;
		$half = ($renderer->width() - 553) / 2;

		$renderer->rect(553, $offset, $renderer->width() - 553, 29, function (Settings $settings): void {
			$settings->setFillDrawColor($this->even);
		});
		$renderer->polygon([
			553 + $half, $offset + 29,
			573 + $half, $offset,
			$renderer->width(), $offset,
			$renderer->width(), $offset + 29,
		], function (Settings $settings): void {
			$settings->setFillDrawColor($this->primary);
		});
		$renderer->cell(553, $offset, $half, 29.0, Strings::upper($this->translator->translate('totalPrice')) . ':', static function (Settings $settings): void {
			$settings->fontFamily = 'sans';
			$settings->fontStyle = $settings::FONT_STYLE_BOLD;
			$settings->align = $settings::ALIGN_CENTER;
		});
		$renderer->cell(553 + $half, $offset, $half - 4, 29.0, $this->formatter->formatMoney($this->order->getTotalPrice($this->calculator, $this->company->hasTax()), $this->order->getPayment()->getCurrency()), static function (Settings $settings): void {
			$settings->fontFamily = 'sans';
			$settings->fontStyle = $settings::FONT_STYLE_BOLD;
			$settings->fontColor = Color::white();
		});
	}

	/**
	 * @param Item[] $items
	 */
	protected function buildItems(int $offset, array $items): int
	{
		$renderer = $this->renderer;

		foreach ($items as $i => $item) {
			$renderer->rect(0, $offset, $renderer->width(), 29, function (Settings $settings) use ($i): void {
				$settings->setFillDrawColor($i % 2 === 1 ? $this->even : $this->odd);
			});
			$renderer->rect(0, $offset + 30, $renderer->width(), 0.1, function (Settings $settings): void {
				$settings->setFillDrawColor($this->even->darken(10));
			});

			// Data
			$renderer->cell(33, $offset, 360, 29.0, $item->getName(), function (Settings $settings): void {
				$settings->fontFamily = 'sans';
				$settings->fontColor = $this->font;
				$settings->fontStyle = $settings::FONT_STYLE_NONE;
				$settings->align = $settings::ALIGN_LEFT;
				$settings->fontSize = 5;
			});
			$renderer->cell(353, $offset, 80, 29.0, $this->formatter->formatNumber($item->getCount()), static function (Settings $settings): void {
				$settings->align = $settings::ALIGN_CENTER;
			});

			$renderer->cell(443, $offset, 160, 29.0, $this->formatter->formatMoney($item->getPrice(), $this->order->getPayment()->getCurrency()));

			if ($this->company->hasTax()) {
				$renderer->cell(593, $offset, 70, 29.0, $item->getTax() * 100 . '%');
			}

			$renderer->cell(670, $offset, 123, 29.0, $this->formatter->formatMoney($item->getTotalPrice($this->calculator, $this->company->hasTax()), $this->order->getPayment()->getCurrency()), static function (Settings $settings): void {
				$settings->fontFamily = 'sans';
				$settings->fontStyle = $settings::FONT_STYLE_BOLD;
			});

			$offset += 31;
		}

		return $offset;
	}

	protected function buildMain(): void
	{
		$renderer = $this->renderer;

		$renderer->rect(0, 307, $renderer->width(), 29, function (Settings $settings): void {
			$settings->setFillDrawColor($this->even);
		});
		$renderer->polygon([
			$renderer->width(), 307,
			$renderer->width(), 336,
			$renderer->width() - 124, 336,
			$renderer->width() - 100, 307,
		], function (Settings $settings) {
			$settings->setFillDrawColor($this->primary);
		});
		$renderer->rect(0, 336, $renderer->width(), 1);

		$renderer->cell(33, 307, 360, 29.0, Strings::upper($this->translator->translate('item')), function (Settings $settings): void {
			$settings->fontColor = $this->primary;
			$settings->fontStyle = $settings::FONT_STYLE_BOLD;
			$settings->fontSize = 9;
		});
		$renderer->cell(353, 307, 80, 29.0, Strings::upper($this->translator->translate('count')), static function (Settings $settings): void {
			$settings->align = $settings::ALIGN_CENTER;
		});

		$renderer->cell(443, 307, 160, 29.0, Strings::upper($this->translator->translate('pricePerItem')));

		if ($this->company->hasTax()) {
			$renderer->cell(593, 307, 70, 29.0, Strings::upper($this->translator->translate('tax')));
		}

		$renderer->cell($renderer->width() - 80, 307, 60, 29.0, Strings::upper($this->translator->translate('total')), function (Settings $settings): void {
			$settings->fontColor = $this->even;
		});
	}

	protected function buildHeader(): void
	{
		$renderer = $this->renderer;

		$renderer->polygon([
			0, 0,
			0, 100,
			245, 100,
			312, 0,
		], function (Settings $settings): void {
			$settings->setFillDrawColor($this->font);
		});

		$renderer->polygon([
			312, 0,
			245, 100,
			$renderer->width(), 100,
			$renderer->height(), 0,
		], function (Settings $settings): void {
			$settings->setFillDrawColor($this->primary);
		});

		$renderer->polygon([
			0, 100,
			0, 106,
			168, 106,
			188, 120,
			200, 100,
		]);

		$renderer->polygon([
			200, 100,
			188, 120,
			205, 125,
			222, 100,
		], function (Settings $settings): void {
			$settings->setFillDrawColor($this->primary->darken(15));
		});

		$renderer->polygon([
			222, 100,
			217, 106,
			242, 106,
			247, 100,
		], function (Settings $settings): void {
			$settings->setFillDrawColor($this->primary);
		});

		$y = 36;
		$renderer->cell(112, $y, 1, null, $this->company->getZip() . ' ' . $this->company->getTown(), static function (Settings $settings): void {
			$settings->align = $settings::ALIGN_RIGHT;
			$settings->fontSize = 6;
			$settings->fontFamily = 'sans';
			$settings->fontColor = Color::white();
		});
		$mul = 1;
		if ($this->company->getAddress()) {
			$renderer->cell(112, $y + 12, 1, null, $this->company->getAddress());
			$mul++;
		}
		if ($this->company->getCountry()) {
			$renderer->cell(112, $y + (12 * $mul), 1, null, $this->company->getCountry());
		}

		$multiplier = 0;
		if ($this->company->getTin()) {
			$renderer->cell(130, $y, 1, null, Strings::upper($this->translator->translate('vat')) . ': ' . $this->company->getTin(), static function (Settings $settings): void {
				$settings->align = $settings::ALIGN_LEFT;
			});
			$multiplier++;
		}

		if ($this->company->getVaTin()) {
			$renderer->cell(130, $y + ($multiplier * 12), 1, null, Strings::upper($this->translator->translate('vaTin')) . ': ' . $this->company->getVaTin(), static function (Settings $settings): void {
				$settings->align = $settings::ALIGN_LEFT;
			});
			$multiplier++;
		}

		$renderer->cell(130, $y + ($multiplier * 12), 1, null,
			$this->company->hasTax() ? $this->translator->translate('taxPay') : $this->translator->translate('notTax'));

		$renderer->cell(543, 25, 1, null, $this->company->getName(), static function (Settings $settings): void {
			$settings->align = $settings::ALIGN_CENTER;
			$settings->fontSize = 8;
		});

		$renderer->cell(525, 45, 1, null, $this->translator->translate('date') . ': ' . $this->formatter->formatDate($this->order->getCreated()), static function (Settings $settings): void {
			$settings->align = $settings::ALIGN_RIGHT;
			$settings->fontSize = 6;
		});
		$renderer->cell(525, 57, 1, null, $this->translator->translate('invoiceNumber') . ': ' . $this->order->getNumber());
		$renderer->cell(537, 52, 1, null, Strings::upper($this->translator->translate('invoice')), static function (Settings $settings): void {
			$settings->align = $settings::ALIGN_LEFT;
			$settings->fontSize = 18;
			$settings->fontStyle = $settings::FONT_STYLE_BOLD;
		});
	}

	protected function buildLeftPane(): void
	{
		$renderer = $this->renderer;
		$text = Strings::upper($this->translator->translate('subscriber'));

		$renderer->cell(33, 175, 1, null, $text, function (Settings $settings): void {
			$settings->fontSize = 16;
			$settings->fontColor = $this->primary;
			$settings->fontStyle = $settings::FONT_STYLE_BOLD;
		});

		$x = $renderer->textWidth($text) + 76;

		$x = max($x, $renderer->textWidth($this->customer->getName(), static function (Settings $settings): void {
				$settings->fontSize = 7;
			}) + 76);

		$renderer->polygon([
			$x, 190,
			$x, 200,
			$x - 7, 192,
			0, 192,
			0, 190,
		], function (Settings $settings) {
			$settings->setFillDrawColor($this->primary);
		});

		$renderer->cell(33, 210, 1, null, $this->customer->getName(), function (Settings $settings): void {
			$settings->fontSize = 10;
			$settings->fontColor = $this->font;
		});

		$multiplier = 0;
		$renderer->cell(33, 228, 1, null, $this->customer->getZip() . ', ' . $this->customer->getTown(), static function (Settings $settings): void {
			$settings->fontSize = 6;
			$settings->fontStyle = $settings::FONT_STYLE_NONE;
		});
		$multiplier++;

		if ($this->customer->getAddress()) {
			$renderer->cell(33, 228 + ($multiplier * 10), 1, null, $this->customer->getAddress());
			$multiplier++;
		}

		if ($this->customer->getCountry()) {
			$renderer->cell(33, 228 + ($multiplier * 10), 1, null, $this->customer->getCountry());
			$multiplier++;
		}

		if ($this->customer->getTin()) {
			$renderer->cell(33, 228 + ($multiplier * 10), 1, null, Strings::upper($this->translator->translate('vat')) . ': ' . $this->customer->getTin());
			$multiplier++;
		}

		if ($this->customer->getVaTin()) {
			$renderer->cell(33, 228 + ($multiplier * 10), 1, null, Strings::upper($this->translator->translate('vaTin')) . ': ' . $this->customer->getVaTin());
		}
	}

	protected function buildRightPane(): void
	{
		$renderer = $this->renderer;

		$renderer->cell(450, 145, 1, null, Strings::upper($this->translator->translate('paymentData')), function (Settings $settings): void {
			$settings->fontColor = $this->primary;
			$settings->align = $settings::ALIGN_LEFT;
			$settings->fontStyle = $settings::FONT_STYLE_BOLD;
			$settings->fontSize = 16;
		});

		$renderer->polygon([
			440, 160,
			440, 170,
			452, 162,
			$renderer->width(), 162,
			$renderer->width(), 160,
		]);

		$iconCb = function (Settings $settings): void {
			$settings->fontStyle = $settings::FONT_STYLE_NONE;
			$settings->fontColor = $this->primary;
			$settings->fontSize = 9;
			$settings->fontFamily = 'icons';
		};
		$sectionCb = static function (Settings $settings): void {
			$settings->fontSize = 6;
			$settings->fontFamily = 'sans';
		};
		$textCb = function (Settings $settings): void {
			$settings->fontColor = $this->font;
		};

		$multiplier = 0;
		// Account information
		if ($this->order->getAccount() !== null) {
			if ($this->order->getDueDate() !== null) {
				$renderer->cell(450, 190, 1, null, 'a', $iconCb);
				$renderer->cell(465, 189, 1, null, Strings::upper($this->translator->translate('dueDate')) . ':', $sectionCb);
				$renderer->cell(465 + 100, 189, 1, null, $this->formatter->formatDate($this->order->getDueDate()), $textCb);

				$multiplier++;
			}

			if ($this->order->getAccount()->getAccountNumber()) {
				$renderer->cell(450, 190 + ($multiplier * 15), 1, null, 'b', $iconCb);
				$renderer->cell(465, 189 + ($multiplier * 15), 1, null, Strings::upper($this->translator->translate('accountNumber')) . ':', $sectionCb);
				$renderer->cell(465 + 100, 189 + ($multiplier * 15), 1, null, $this->order->getAccount()->getAccountNumber(), $textCb);
				$multiplier++;
			}

			if ($this->order->getAccount()->getIBan()) {
				$renderer->cell(450, 190 + ($multiplier * 15), 1, null, 'b', $iconCb);
				$renderer->cell(465, 189 + ($multiplier * 15), 1, null, Strings::upper($this->translator->translate('iban')) . ':', $sectionCb);
				$renderer->cell(465 + 100, 189 + ($multiplier * 15), 1, null, $this->order->getAccount()->getIBan(), $textCb);
				$multiplier++;
			}
		}

		// Payment
		if ($this->order->getPayment()->getVariableSymbol()) {
			$renderer->cell(453, 190 + ($multiplier * 15), 1, null, 'c', $iconCb);
			$renderer->cell(465, 189 + ($multiplier * 15), 1, null, Strings::upper($this->translator->translate('varSymbol')) . ':', $sectionCb);
			$renderer->cell(465 + 100, 189 + ($multiplier * 15), 1, null, $this->order->getPayment()->getVariableSymbol(), $textCb);
			$multiplier++;
		}

		if ($this->order->getPayment()->getConstantSymbol()) {
			$renderer->cell(453, 190 + ($multiplier * 15), 1, null, 'c', $iconCb);
			$renderer->cell(465, 189 + ($multiplier * 15), 1, null, Strings::upper($this->translator->translate('constSymbol')) . ':', $sectionCb);
			$renderer->cell(465 + 100, 189 + ($multiplier * 15), 1, null, $this->order->getPayment()->getConstantSymbol(), $textCb);
			$multiplier++;
		}

		// Total price
		$renderer->cell(450, 190 + ($multiplier * 15), 1, null, 'd', $iconCb);
		$renderer->cell(465, 189 + ($multiplier * 15), 1, null, Strings::upper($this->translator->translate('totalPrice')) . ':', $sectionCb);
		$renderer->cell(465 + 100, 189 + ($multiplier * 15), 1, null, $this->formatter->formatMoney($this->order->getTotalPrice($this->calculator, $this->company->hasTax()), $this->order->getPayment()->getCurrency()), $textCb);
	}

	protected function buildFooter(Paginator $paginator): void
	{
		$renderer = $this->renderer;

		$renderer->rect(0, $renderer->height() - 20, $renderer->width(), 20, function (Settings $settings): void {
			$settings->setFillDrawColor($this->font);
		});

		$renderer->cell(0, -10, $renderer->width() - 10, null, $this->translator->translate('page') . ' ' . $paginator->getCurrentPage() . ' / ' . $paginator->getTotalPages(), static function (Settings $settings): void {
			$settings->fontColor = Color::white();
			$settings->fontFamily = 'sans';
			$settings->align = $settings::ALIGN_RIGHT;
			$settings->fontSize = 6;
		});
	}

}
