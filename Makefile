YEAR    ?= 2025
DAY     ?= 1
HALF    ?= 1
TEST    ?= 1
DEBUG   ?= false
DOCKER  := docker compose exec php

## Muestra esta ayuda
help:
	@grep -E '^## ' Makefile | sed 's/## /  /' && echo "" && \
	grep -E '^[a-zA-Z_-]+:' Makefile | grep -v '^\.PHONY' | \
	awk -F: '{printf "    make %-10s\n", $$1}'

## Ejecutar un reto:          make run DAY=3 HALF=1 [TEST=1] [DEBUG=false]
run:
	$(DOCKER) php bin/console txurdi:challenge-launch $(YEAR) $(DAY) $(HALF) $(TEST) $(DEBUG)

## Ejecutar con datos ejemplo: make example DAY=3 HALF=1  (usa TEST=0)
example:
	$(DOCKER) php bin/console txurdi:challenge-launch $(YEAR) $(DAY) $(HALF) 0 $(DEBUG)

## Ejecutar con debug:         make debug DAY=3 HALF=1
debug:
	$(DOCKER) php bin/console txurdi:challenge-launch $(YEAR) $(DAY) $(HALF) $(TEST) true

## Crear fichero para un nuevo reto: make new DAY=4 [YEAR=2025]
new:
	@CLASS=Year$(YEAR)Day$(DAY)Challenge; \
	FILE=src/Challenges/$$CLASS.php; \
	DATA_DIR=src/Challenges/data/$(YEAR); \
	if [ -f $$FILE ]; then echo "Ya existe: $$FILE"; exit 1; fi; \
	sed "s/Year2025DayXChallenge/$$CLASS/g" src/Challenges/Year2025DayXChallenge.php > $$FILE; \
	mkdir -p $$DATA_DIR; \
	touch $$DATA_DIR/day$(DAY)H1T0.txt $$DATA_DIR/day$(DAY)H1T1.txt \
	      $$DATA_DIR/day$(DAY)H2T0.txt $$DATA_DIR/day$(DAY)H2T1.txt; \
	echo "Creado: $$FILE"; \
	echo "Datos:  $$DATA_DIR/day$(DAY)H{1,2}T{0,1}.txt"

## Iniciar Docker
up:
	docker compose up --wait

## Parar Docker
down:
	docker compose down --remove-orphans

## Reconstruir imagen Docker
build:
	docker compose build --pull --no-cache

.PHONY: help run example debug new up down build
