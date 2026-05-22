# backend-statistics

A Symfony 8 microservice that receives podcast platform webhook events and exposes time-series download statistics for front-end charts.

## Architecture

The project follows **CQRS with a hexagonal (ports & adapters) layout** and three top-level layers:

```
src/
├── Domain/          # Pure domain objects — value objects, events, repository interfaces, exceptions
├── Application/     # Use-cases — commands, queries, handlers, DTOs
└── Infrastructure/  # Framework-specific wiring — HTTP controllers, Doctrine projections, repositories, projectors
    Shared/          # Cross-cutting primitives — bus interfaces, base value objects, exception listener
```

### Key flows

**Ingest (write side)**
1. `POST /webhook` → `WebhookController` → `RecordIncomingEventCommand`
2. `RecordIncomingEventCommandHandler` persists the raw `IncomingEvent` and dispatches `EpisodeDownloadRecorded`
3. `EpisodeDownloadProjector` (async Messenger consumer) writes a read-model row to `episode_download`

**Query (read side)**
1. `GET /podcasts/{podcastId}/episodes/{episodeId}/downloads` → `DownloadsController` → `GetDownloadsQuery`
2. `GetDownloadsQueryHandler` reads from the `episode_download` projection and returns a 7-day time series

### Infrastructure

| Component | Technology |
|-----------|-----------|
| Runtime   | PHP 8.5 / Symfony 8 |
| Database  | PostgreSQL 18 |
| Message bus | Symfony Messenger + RabbitMQ (AMQP) |
| Web server | Nginx + PHP-FPM |
| Container | Docker Compose |

## API

Interactive docs (Swagger UI) are available at **`http://localhost/api/doc`** after starting the stack.

The raw OpenAPI JSON spec is at `http://localhost/api/doc.json`.

### Endpoints

| Method | Path | Description |
|--------|------|-------------|
| `POST` | `/webhook` | Receive a platform event (`episode.downloaded`). Unknown types are acknowledged and ignored. |
| `GET`  | `/podcasts/{podcastId}/episodes/{episodeId}/downloads` | Daily download counts for an episode. Optional `from` / `to` query params (ISO date). Defaults to last 7 days. |

## Commands

```bash
make help          # List all available make targets

make start         # Build images and start the stack (dev, detached)
make start-prod    # Start with production compose override
make down          # Stop and remove containers
make logs          # Tail container logs
make bash          # Open a shell inside the php-fpm container
```

Inside the container (`make bash`) you can run the Composer scripts:

```bash
composer test         # Run PHPUnit with coverage
composer stan         # Run PHPStan static analysis
composer lint         # Fix code style with PHP-CS-Fixer
composer lint:check   # Check code style without fixing
composer mutation     # Run Infection mutation testing
```

Migrations run automatically on container start via the entrypoint script.
