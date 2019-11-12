<?php declare(strict_types = 1);

namespace Contributte\Invoice\Renderers;

use FPDF;

class PDF extends FPDF
{

	public function __construct(string $orientation = 'P', string $unit = 'mm', string $size = 'A4')
	{
		$px = false;
		if ($unit === 'px') {
			$unit = 'pt';
			$px = true;
		}
		parent::__construct($orientation, $unit, $size);

		if ($px) {
			$this->k = 72 / 96;

			$this->wPt = $this->w * $this->k;
			$this->hPt = $this->h * $this->k;
		}
	}

	public function SetFontPath(string $fontPath): void
	{
		$this->fontpath = $fontPath;
	}

	/**
	 * @param float[] $points
	 */
	public function Polygon(array $points, string $style = 'D'): void
	{
		//Draw a polygon
		if ($style === 'F') {
			$op = 'f';
		} elseif ($style === 'FD' || $style === 'DF') {
			$op = 'b';
		} else {
			$op = 's';
		}

		$h = $this->h;
		$k = $this->k;

		$points_string = '';
		for ($i = 0; $i < count($points); $i += 2) {
			$points_string .= sprintf('%.2F %.2F', $points[$i] * $k, ($h - $points[$i + 1]) * $k);
			if ($i == 0) {
				$points_string .= ' m ';
			} else {
				$points_string .= ' l ';
			}
		}
		$this->_out($points_string . $op);
	}

}
