# Requirement Checklist

Dokumen ini digunakan untuk memastikan kebutuhan final project sudah terpenuhi.

## Checklist Umum

| Requirement            | Status    | Bukti Implementasi                                                                 |
| ---------------------- | --------- | ---------------------------------------------------------------------------------- |
| Microservices          | Terpenuhi | auth-service, member-service, field-service, booking-service, notification-service |
| Docker                 | Terpenuhi | docker-compose.yml dan Dockerfile pada setiap service                              |
| Database terpisah      | Terpenuhi | auth_db, member_db, field_db, booking_db, notification_db                          |
| RESTful API            | Terpenuhi | routes/api.php pada setiap service                                                 |
| Message broker         | Terpenuhi | Redis dan notification-worker                                                      |
| Web UI                 | Terpenuhi | web-client                                                                         |
| Auth admin             | Terpenuhi | auth-service dan web-client admin login                                            |
| Auth member            | Terpenuhi | auth-service dan web-client member login                                           |
| OTP email              | Terpenuhi | Auth Service, Redis, Notification Service                                          |
| Booking flow           | Terpenuhi | Booking Service dan Web Client                                                     |
| Notification log       | Terpenuhi | Notification Service                                                               |
| GraphQL manual         | Terpenuhi | graphql-gateway berbasis Laravel                                                   |
| Hasura GraphQL         | Terpenuhi | hasura local dan hasura_db PostgreSQL                                              |
| Dokumentasi testing    | Terpenuhi | docs/final-testing.md                                                              |
| Dokumentasi deployment | Terpenuhi | docs/deployment-guide.md                                                           |

## Backend Framework

GraphQL Gateway manual dibuat menggunakan Laravel sebagai backend framework.

Bukti:

```text
graphql-gateway/
```

Endpoint:

```text
http://localhost:8010/api/graphql
```

## Hasura

Hasura dijalankan menggunakan Docker Compose.

Bukti:

```text
hasura:
  image: hasura/graphql-engine:v2.38.0
```

Endpoint:

```text
http://localhost:8080
```

Database:

```text
hasura_db
```

## RESTful API

RESTful API tersedia pada service berikut:

```text
auth-service/routes/api.php
member-service/routes/api.php
field-service/routes/api.php
booking-service/routes/api.php
notification-service/routes/api.php
```

## Message Broker

Redis digunakan sebagai message broker untuk mendukung proses OTP dan notifikasi.

Bukti service:

```text
redis
notification-worker
```

## Database Per Service

| Service              | Database        |
| -------------------- | --------------- |
| Auth Service         | auth_db         |
| Member Service       | member_db       |
| Field Service        | field_db        |
| Booking Service      | booking_db      |
| Notification Service | notification_db |

## Hasura Reporting Database

Hasura menggunakan database terpisah:

```text
hasura_db
```

Alasannya adalah Hasura digunakan sebagai reporting layer, bukan sebagai database transaksi utama.

## Testing yang Disiapkan

| Area Testing            | Dokumen                  |
| ----------------------- | ------------------------ |
| Testing flow utama      | docs/final-testing.md    |
| Testing REST API        | docs/api-endpoints.md    |
| Testing GraphQL Gateway | docs/graphql-testing.md  |
| Testing Hasura          | docs/hasura-testing.md   |
| Deployment              | docs/deployment-guide.md |
| Arsitektur              | docs/architecture.md     |

## Kesimpulan

Berdasarkan checklist ini, project Retobluto Arena Microservices sudah memenuhi kebutuhan utama final project, yaitu microservices, Docker, RESTful API, GraphQL manual, Hasura GraphQL, database terpisah, message broker, dan dokumentasi pengujian.
