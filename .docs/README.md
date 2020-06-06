# Contributte / Invoice

## Content

- [Benchmark](#benchmark)
- [Setup](#setup)
- [Preview with minimal setup](#preview-with-minimal-setup)
- [Entities](#entities)
- [Customizing](#customizing)
- [Generating invoices](#generating-invoices)
- [Generating preview](#generating-preview)
- [Neon configuration](#neon-configuration)
- [Examples](#examples)

## Benchmark

Average output is ~20ms

## Setup

```php 
$company = new Contributte\Invoice\Data\Company('John Doe', 'Los Angeles', 'Cavetown', '720 55', 'USA', '0123456789', 'CZ0123456789');
$invoice = new Contributte\Invoice\Invoice($company);
```

## Preview with minimal setup

```php
$invoice = new Contributte\Invoice\Invoice(Contributte\Invoice\Preview\PreviewFactory::createCompany());

$invoice->send(Contributte\Invoice\Preview\PreviewFactory::createCustomer(), Contributte\Invoice\Preview\PreviewFactory::createOrder());
```

## Entities

We have following entities: Company (seller), Customer, Account (bank account), Payment Info, Order and Item.

### Company - seller

```php
$company = new Contributte\Invoice\Data\Company('John Doe', 'Los Angeles', 'Cavetown', '720 55', 'USA', '0123456789', 'CZ0123456789');
```

### Customer

```php
$customer = new Contributte\Invoice\Data\Customer('John Doe', 'Los Angeles', 'Cavetown', '720 55', 'USA');
```

### Account - bank account

```php
$account = new Contributte\Invoice\Data\Account('1111', 'CZ4808000000002353462015', 'GIGACZPX');
```

### Payment info

```php
$payment = new Contributte\Invoice\Data\PaymentInformation('Kč', '0123456789', '1234', 0.21);
```

### Order

```php
$order = new Contributte\Invoice\Data\Order('20160001', new \DateTime('+ 14 days'), $account, $payment);
```

### Item

```php
$order->addItem('Logitech G700s Rechargeable Gaming Mouse', 4, 1790);
```

## Customizing

Customize numbers, money or date

```php
use Contributte\Invoice\IFormatter;

class CustomFormatter implements IFormatter {
	
}
```

## Generating invoices

```php
$invoice = new Contributte\Invoice\Invoice($company);

header('Content-Type: application/pdf; charset=utf-8');
echo $invoice->create($customer, $order);
```

method `Invoice::send()` automatically sets content-type header

```php
$invoice = new Contributte\Invoice\Invoice($company);

$invoice->send($customer, $order);
```

if you use nette, recommended way is

```php
class CustomPresenter {
    
    public function actionPreview() {
        $invoice = new Contributte\Invoice\Invoice($company);
        
        $this->sendResponse($invoice->createResponse($customer, $order));
    }

}
```

## Generating preview

```php
$invoice->send(Contributte\Invoice\Preview\PreviewFactory::createCustomer(), Contributte\Invoice\Preview\PreviewFactory::createOrder());
```

## Neon configuration

```yaml
extensions:
	invoice: Contributte\Invoice\DI\InvoiceExtension

invoice:
	lang: en
	company:
		name: string
		town: string
		address: string
		zip: string|int
		country: string
		## Optional below
		tin: string|int
		vaTin: string|int
		isTax: bool
```

## Examples

First page:
![first page](http://i.imgbox.com/pwFByZ1L.jpg)

Second page:
![second page](http://i.imgbox.com/ebrwXldf.jpg)
