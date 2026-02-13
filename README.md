# SMS Sending Case Study

This project is a console-based application developed to retrieve and mark SMS messages that should be sent within a specific local time window, taking time zones into account.

The application is containerized and can be started quickly using Docker.

---

## Requirements

- Docker  

No local PHP or MySQL installation is required.

---

## Installation

Clone the repository and start the containers:

```bash
docker-compose up -d
```

This command will build project and then start:

* PHP-FPM
* Nginx
* MySQL 8

## Database Setup

##### Run migrations:

```
docker exec -it yiicase php yii migrate
```

### Fetch Messages To Send

Retrieves 5 eligible SMS records, marks them as sent, and prints them.

##### Eligibility conditions:

- status = 0
- provider = inhousesms
- send_after < NOW()
- Local time between 09:00 – 23:00

Concurrency-safe selection using row-level locking

##### Run following command: 
```
docker exec -it yiicase php yii mobile/get-messages-to-send
```

### Technical Decisions & Optimizations
#### Timezone Performance Optimization

Instead of calculating timezone during query execution (which prevents index usage),
the local hour is precomputed and stored in the local_send_hour column.

This allows:

- Index-friendly filtering
- Millisecond-level query performance on large datasets

### Concurrency Safety

To prevent multiple workers from processing the same SMS:

**SELECT ... FOR UPDATE SKIP LOCKED is used**

- Ensures non-blocking parallel workers
- Eliminates race conditions during message retrieval

### Index Strategy

A composite index is used to optimize the critical query path:

- status
- provider
- send_after
- local_send_hour
- id

This enables:

- Efficient range scans
- Fast ordered retrieval with LIMIT 5

### Notes

Focus was placed on database performance, correctness, and concurrency handling,
as these are the most critical aspects of high-volume SMS systems.