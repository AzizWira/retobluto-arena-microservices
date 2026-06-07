# Retobluto Arena Microservices

Retobluto Arena Microservices adalah sistem backend berbasis microservices untuk mengelola proses booking lapangan olahraga.

## Deskripsi Singkat

Sistem ini mendukung:

- Autentikasi admin dan member
- Registrasi member dengan OTP email
- Pengelolaan data member
- Pengelolaan data lapangan
- Pengajuan booking oleh member
- Approval dan rejection booking oleh admin
- Cancel booking oleh member
- Notifikasi berbasis message broker
- RESTful API
- GraphQL manual
- GraphQL dengan Hasura
- Deployment menggunakan Docker

## Daftar Service

| Service              | Deskripsi                                                  | Penanggung Jawab            |
| -------------------- | ---------------------------------------------------------- | --------------------------- |
| auth-service         | Login admin/member, register member, OTP, token validation | Aura Iftitah                |
| member-service       | Manajemen profil member                                    | Ryan Alvin Saputra          |
| field-service        | Manajemen data lapangan                                    | MUhammad Agil Hidayahtullah |
| booking-service      | Pengajuan booking, approve, reject, cancel                 | Ahmad Aziz Wira Widodo      |
| notification-service | OTP email dan notifikasi booking                           | Ryan Alvin Saputra          |
| graphql-gateway      | GraphQL manual gateway                                     | Ahmad Aziz Wira Widodo      |
| hasura               | GraphQL otomatis untuk reporting                           | Ahmad Aziz Wira Widodo      |

## Teknologi

- Laravel
- MySQL
- PostgreSQL
- Redis
- Docker
- RESTful API
- GraphQL
- Hasura

## Struktur Project

retobluto-arena-microservices/
├── auth-service/
├── member-service/
├── field-service/
├── booking-service/
├── notification-service/
├── graphql-gateway/
├── hasura/
├── docs/
├── docker/
├── docker-compose.yml
└── README.md

## Flow Utama

1. Member register menggunakan OTP email.
2. Member login.
3. Member mengajukan booking lapangan.
4. Booking masuk dengan status pending.
5. Admin approve atau reject booking.
6. Jika approved, booking menjadi jadwal resmi.
7. Member dapat cancel booking.
8. Notification Service mengirim notifikasi berdasarkan event dari Redis.

## Status Booking

- pending
- approved
- rejected
- canceled
