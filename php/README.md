# PHP implementation

> Note: this app could be implemented in one simple script, but... I tried to implement production-like application: testing, abstractions, etc.

# What this app demonstrates?

Knowledge of:
* PSRs
* Docker
* Package Management in PHP (Composer)
* Testing
* Code Style

# How to run?

## Docker

```bash
docker build --tag=piano-test-task-php .
docker run -it --rm -v `pwd`/../_data:/tmp/_data piano-test-task-php ./bin/piano /tmp/_data/file_a.csv /tmp/_data/file_b.csv > results.csv
```

## Natively

```bash
composer i
./bin/piano -o results.csv file_a.csv file_b.csv
```
