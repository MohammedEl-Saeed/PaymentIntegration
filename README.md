# Payment Integration — Symfony / PHP 8.3

A backend system for processing payments and refunds via Shift4 gateway.  
Built as a technical challenge to demonstrate clean architecture in PHP.

## What it does
- Process card payments via POST /api/charge
- Handle refunds via POST /api/refund
- Supports adding new payment providers without changing existing code

## Architecture
- **Provider Pattern** — each payment gateway is an isolated class
- **Registry** — auto-discovers providers, no manual registration needed
- **DTOs** — typed request/response objects for all operations
- **PHPUnit tests** — covers main charge and refund flows

## Tech
`Symfony 7.3` `PHP 8.3` `Docker` `PHPUnit` `Shift4 API`

## Run locally
composer install
cp .env .env.local
# Add SHIFT4_SECRET_KEY=your_key
php -S localhost:8000 -t public
php bin/phpunit
