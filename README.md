# laravel-blueprint

How I structure a Laravel app: layering, domain modeling, testing, tooling.

![CI](https://github.com/abdulrahmanelnegery/laravel-blueprint/actions/workflows/ci.yml/badge.svg)

This is a small, honest reference, not a product. The domain is deliberately
generic (invoicing: customers, invoices, invoice lines, a per year number
sequence). One feature, "create an invoice", is wired end to end through every
layer so the boundaries are visible in real code rather than described in prose.
Every body is small but real; nothing is a stub.

## The layers

### HTTP

Parses and shape checks the request, then gets out of the way. No business
rules, no Eloquent, no error handling. Domain exceptions are translated to a
consistent `422` payload centrally in `bootstrap/app.php`.

- `app/Http/Requests/StoreInvoiceRequest.php`: types, presence, structure only.
- `app/Http/Controllers/InvoiceController.php`: validate, build a DTO, call the
  action, return a resource.
- `app/Http/Resources/InvoiceResource.php`: the response shape.

The request deliberately does **not** enforce "at least one line", "amounts
positive" or "customer active". Those are business rules and live in the
application layer, where they can be tested without an HTTP request. Keeping
that split honest is the main thing this repo is trying to show.

### Application

The use case and its business rules. One write path, no framework types beyond
an injected event dispatcher and repository interfaces.

- `app/Actions/CreateInvoice.php`: lines non-empty, every amount positive,
  customer active, allocate a number, persist, raise a domain event.
- `app/DTO/`: `NewInvoice` / `NewInvoiceLine` in, `InvoiceSummary` /
  `InvoiceLineSummary` out. Nothing crosses a boundary as an anonymous array.

### Domain

Framework free. Value objects that enforce their own invariants, plus the
domain event.

- `app/Domain/Money.php`: integer minor units plus currency, immutable, never
  negative, arithmetic returns new instances, currency mismatch throws.
- `app/Domain/InvoiceNumber.php`: a validated "INV-2026-0007" value.
- `app/Domain/Events/InvoiceCreated.php` with
  `app/Listeners/LogInvoiceCreated.php`: a side effect (an audit log line) hung
  off an event instead of wired into the action. Wiring is explicit in
  `app/Providers/EventServiceProvider.php`.
- `app/Exceptions/DomainException.php`: base for recoverable business errors
  (`CustomerInactiveException`, `EmptyInvoiceException`,
  `NonPositiveAmountException`, `CurrencyMismatchException`). Programming errors
  throw SPL exceptions instead and are left to surface as `500`s.

### Infrastructure

Persistence and other outward facing details, behind interfaces the inner
layers own.

- `app/Repositories/InvoiceRepository.php` (interface) and
  `app/Repositories/Eloquent/EloquentInvoiceRepository.php` (implementation),
  bound in `app/Providers/RepositoryServiceProvider.php`. `nextNumber()` takes a
  row lock while it increments `InvoiceNumberSequence`.
- `app/Support/SlugGenerator.php`, `app/Support/MoneyFormatter.php`: small pure
  helpers with their own unit tests.
- `app/Rendering/`: a `MessageRenderer` interface with `PlainTextRenderer` and
  `MarkdownRenderer`, both consuming the `InvoiceSummary` DTO.

## Request flow

```
POST /api/invoices
  -> StoreInvoiceRequest        (shape validation)
  -> InvoiceController::store    (map to NewInvoice DTO)
  -> CreateInvoice              (business rules, Money math)
       -> CustomerRepository     (load + active check)
       -> InvoiceRepository::nextNumber   (locked sequence)
       -> InvoiceRepository::save         (transaction)
       -> event(InvoiceCreated) -> LogInvoiceCreated
  -> InvoiceResource            (201 response)
```

## Run

```sh
composer install
composer test
```

Other checks (also run in CI on PHP 8.4):

```sh
composer lint    # Pint, formatting
composer stan    # PHPStan / Larastan, level 8
composer check   # all three
```

Tests use an in memory SQLite database; no services to start.

## Tooling

- **Pint** (`pint.json`): Laravel preset plus `declare_strict_types` and ordered
  imports and class elements.
- **PHPStan + Larastan** (`phpstan.neon`): level 8, `app/`, `routes/` and
  `database/factories/`. Level 8 is realistic here only because the codebase is
  small and clean; on a legacy codebase you would start lower and ratchet up.
- **CI** (`.github/workflows/ci.yml`): Pint, PHPStan, Pest on PHP 8.4.
- **`hooks/pre-commit`**: the same three checks locally. Enable with
  `ln -sf ../../hooks/pre-commit .git/hooks/pre-commit` (see `hooks/README.md`).

## Layout

```
app/
  Actions/            CreateInvoice
  Domain/             Money, InvoiceNumber, Events/InvoiceCreated
  DTO/                NewInvoice, InvoiceSummary, ...
  Exceptions/         DomainException + Domain/*
  Http/               Requests, Controllers, Resources
  Listeners/          LogInvoiceCreated
  Models/             Customer, Invoice, InvoiceLine, InvoiceNumberSequence
  Providers/          EventServiceProvider, RepositoryServiceProvider
  Rendering/          MessageRenderer + PlainText / Markdown
  Repositories/       interfaces + Eloquent/*
  Support/            SlugGenerator, MoneyFormatter
tests/
  Unit/               Domain, Support, Rendering   (mirrors app/)
  Feature/Invoice/    CreateInvoiceTest
```
