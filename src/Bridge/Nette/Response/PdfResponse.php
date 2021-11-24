<?php declare(strict_types = 1);

namespace Contributte\Invoice\Bridge\Nette\Response;

use Nette\Application\Response;
use Nette\Http\IRequest;
use Nette\Http\IResponse;

final class PdfResponse implements Response
{

	public function __construct(
		private string $content,
	)
	{
	}

	public function send(IRequest $httpRequest, IResponse $httpResponse): void
	{
		$httpResponse->setContentType('application/pdf', 'utf-8');

		echo $this->content;
	}

}
