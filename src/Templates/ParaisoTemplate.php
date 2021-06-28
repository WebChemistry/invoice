<?php declare(strict_types = 1);

namespace Contributte\Invoice\Templates;

use Contributte\Invoice\Data\IOrder;
use Contributte\Invoice\Templates\Template\ParaisoTemplateObject;
use Contributte\Invoice\Templates\Translator\ITranslator;
use Contributte\Invoice\Templates\Translator\ParaisoTemplateTranslator;
use WebChemistry\SvgPdf\Pdf\Color;
use WebChemistry\SvgPdf\PdfSvg;

class ParaisoTemplate extends Template
{

	/** @var Color[] */
	public array $colors = [];

	private ITranslator $translator;

	public function __construct(?ITranslator $translator = null)
	{
		$renderer = new PdfSvg();
		$renderer->addFont('Montserrat', __DIR__ . '/assets/font/Montserrat-Regular.php')
			->addFont('Montserrat', __DIR__ . '/assets/font/Montserrat-Bold.php', 'bold');

		$this->colors = [
			'text' => Color::fromString('#141618'),
			'lightText' => Color::fromString('#858788'),
			'primary' => Color::fromString('#d92b11'),
			'totalDue' => Color::fromString('#6e7072'),
		];

		$this->translator = $translator ?? new ParaisoTemplateTranslator();

		parent::__construct($renderer);
	}

	protected function getTemplate(): string
	{
		return __DIR__ . '/templates/paraiso.phtml';
	}

	/**
	 * @internal
	 */
	public function createTemplateObject(IOrder $order): ParaisoTemplateObject
	{
		return new ParaisoTemplateObject($order, $this->translator);
	}

}
