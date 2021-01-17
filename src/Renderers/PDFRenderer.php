<?php declare(strict_types = 1);

namespace Contributte\Invoice\Renderers;

use Nette\SmartObject;

class PDFRenderer implements IRenderer
{

	use SmartObject;

	/** @var PDF */
	protected $pdf;

	/** @var mixed[] */
	protected $cache = [
		'family' => null,
		'size' => 15,
		'color' => null,
		'align' => Settings::ALIGN_JUSTIFY,
	];

	public function x(): int
	{
		return (int) $this->pdf->GetX();
	}

	public function y(): int
	{
		return (int) $this->pdf->GetY();
	}

	public function textWidth(string $text, ?callable $setCallback = null): float
	{
		$settings = $this->extractSettings($setCallback);
		$this->setFont($settings);

		return $this->pdf->GetStringWidth($text);
	}

	public function width(): float
	{
		return $this->pdf->GetPageWidth();
	}

	public function height(): float
	{
		return $this->pdf->GetPageHeight();
	}

	public function createNew(): void
	{
		$this->pdf = new PDF('P', 'px', 'A4');
		$this->pdf->SetFontPath(IRenderer::ASSETS_PATH);
		$this->pdf->SetAutoPageBreak(false);

		$this->addPage();
	}

	public function addPage(): void
	{
		$this->pdf->AddPage('P', 'A4');
	}

	public function addFont(string $family, string $file, string $fontStyle = Settings::FONT_STYLE_NONE): void
	{
		$this->pdf->AddFont($family, $fontStyle, $file);
	}

	public function rect(float $x, float $y, float $width, float $height, ?callable $setCallback = null): void
	{
		$settings = $this->extractSettings($setCallback);

		$this->setDrawing($settings);

		$this->pdf->Rect($x, $y, $width, $height, 'DF');
	}

	/**
	 * @param mixed[] $points
	 */
	public function polygon(array $points, ?callable $setCallback = null): void
	{
		$settings = $this->extractSettings($setCallback);

		$this->setDrawing($settings);

		$this->pdf->Polygon($points, 'DF');
	}

	/**
	 * (border, fill, align)
	 */
	public function cell(float $x, float $y, float $width, ?float $height, ?string $text, ?callable $setCallback = null): void
	{
		$text = iconv('UTF-8', 'WINDOWS-1250//TRANSLIT', (string) $text);

		$settings = $this->extractSettings($setCallback);
		$this->restoreSettings($settings, 'align');

		$this->pdf->SetXY($x, $y);
		$this->setFont($settings);

		if ($height) {
			$this->pdf->MultiCell($width, $height, $text, $settings->border, $settings->align, $settings->fill);
		} else {
			$this->pdf->Cell($width, 0, $text, $settings->border, 0, $settings->align, $settings->fill);
		}
	}

	public function output(): string
	{
		return $this->pdf->Output('S');
	}

	protected function setFont(Settings $settings): void
	{
		if ($settings->fontFamily === null && $settings->fontSize === null && $settings->fontColor === null) {
			return;
		}

		$this->restoreSettings($settings, 'fontFamily');
		$this->restoreSettings($settings, 'fontSize');

		$this->pdf->SetFont($settings->fontFamily, $settings->fontStyle, $settings->fontSize);

		if ($settings->fontColor) {
			$color = $settings->fontColor;
			$this->pdf->SetTextColor($color->getRed(), $color->getGreen(), $color->getBlue());
		}
	}

	protected function setDrawing(Settings $settings): void
	{
		if ($settings->drawColor !== null) {
			$color = $settings->drawColor;

			$this->pdf->SetDrawColor($color->getRed(), $color->getGreen(), $color->getBlue());
		}

		if ($settings->fillColor !== null) {
			$color = $settings->fillColor;

			$this->pdf->SetFillColor($color->getRed(), $color->getGreen(), $color->getBlue());
		}
	}

	protected function extractSettings(?callable $callback = null): Settings
	{
		$settings = new Settings();
		if ($callback !== null) {
			$callback($settings);
		}

		return $settings;
	}

	protected function restoreSettings(Settings $settings, string $name): void
	{
		if ($settings->$name === null) {
			$settings->$name = $this->cache[$name];
		} else {
			$this->cache[$name] = $settings->$name;
		}
	}

	public function getSource(): PDF
	{
		return $this->pdf;
	}

}
