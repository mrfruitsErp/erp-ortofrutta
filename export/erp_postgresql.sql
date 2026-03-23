-- PostgreSQL conversion from MariaDB dump
BEGIN;

DROP TABLE IF EXISTS cache CASCADE;
DROP TABLE IF EXISTS cache_locks CASCADE;
DROP TABLE IF EXISTS client_delivery_prefs CASCADE;

CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);
CREATE INDEX cache_expiration_index ON cache(expiration);

CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);
CREATE INDEX cache_locks_expiration_index ON cache_locks(expiration);

CREATE TABLE client_delivery_prefs (
    id SERIAL PRIMARY KEY,
    client_id BIGINT NOT NULL,
    delivery_time_slot_id BIGINT NOT NULL,
    preferito SMALLINT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT unique_client_delivery UNIQUE(client_id, delivery_time_slot_id)
);

INSERT INTO client_delivery_prefs VALUES (8,1,3,1,'2026-03-20 08:52:19','2026-03-20 08:52:19');

COMMIT;
