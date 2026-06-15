# =============================================================================
#  GastroMap — керування локальною БД та запуском застосунку
#
#  Корінь проблеми "Cannot modify header information / headers already sent":
#  SESSION_DRIVER=database, CACHE_STORE=database, QUEUE_CONNECTION=database,
#  але БД не піднята/не змігрована -> немає таблиці `sessions`. Laravel падає
#  при збереженні сесії вже ПІСЛЯ відправки відповіді -> цей фатал.
#
#  Лікування одним рядком:   make setup
#  (підняти БД -> дочекатись готовності -> .env -> ключ -> міграції -> сіди)
# =============================================================================

# --- Налаштування (можна перевизначити: `make up DB_PORT=3307`) --------------
COMPOSE      ?= docker compose
PHP          ?= php
ARTISAN      ?= $(PHP) artisan
DB_CONTAINER ?= gastromap_db

DB_CONNECTION ?= pgsql
DB_HOST       ?= 127.0.0.1
# Локальний порт публікації БД на хост. 5432 часто зайнятий іншим Postgres-проєктом,
# тож за замовчуванням беремо 5433. Усередині контейнера Postgres усе одно на 5432.
DB_PORT       ?= 5433
DB_DATABASE   ?= gastromap
DB_USERNAME   ?= gastromap
DB_PASSWORD   ?= secret

.DEFAULT_GOAL := help
.PHONY: help setup up down destroy restart logs ps wait-db check env key \
        migrate seed fresh db-shell serve

help: ## Показати список команд
	@echo "GastroMap — доступні команди:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| sort | awk 'BEGIN{FS=":.*?## "}{printf "  \033[36m%-12s\033[0m %s\n", $$1, $$2}'

# --- Головна команда ---------------------------------------------------------
setup: env up wait-db check key migrate seed ## Повне налаштування з нуля (фікс падіння застосунку)
	@echo ""
	@echo "✅ Готово. БД піднята й змігрована. Запустіть застосунок: make serve"

# --- Docker / БД -------------------------------------------------------------
up: ## Підняти контейнер БД (та Adminer) у фоні
	$(COMPOSE) up -d

down: ## Зупинити контейнери (дані БД зберігаються у volume)
	$(COMPOSE) down

destroy: ## Зупинити та ВИДАЛИТИ дані БД (volume) — повне очищення
	$(COMPOSE) down -v

restart: down up ## Перезапустити контейнери

logs: ## Дивитися логи БД у реальному часі
	$(COMPOSE) logs -f db

ps: ## Статус контейнерів
	$(COMPOSE) ps

wait-db: ## Чекати, доки БД стане healthy
	@echo "⏳ Чекаємо готовності БД ($(DB_CONTAINER))..."
	@until [ "$$(docker inspect -f '{{.State.Health.Status}}' $(DB_CONTAINER) 2>/dev/null)" = "healthy" ]; do \
		printf '.'; sleep 2; \
	done
	@echo " ✅ БД готова."

db-shell: ## Відкрити psql-консоль усередині контейнера
	docker exec -it -e PGPASSWORD=$(DB_PASSWORD) $(DB_CONTAINER) psql -U $(DB_USERNAME) -d $(DB_DATABASE)

# --- Laravel / застосунок ----------------------------------------------------
env: ## Створити .env (якщо немає) і вписати під'єднання до docker-БД
	@test -f .env || cp .env.example .env
	@sed -i -E 's/^#? *DB_CONNECTION=.*/DB_CONNECTION=$(DB_CONNECTION)/' .env
	@sed -i -E 's|^#? *DB_HOST=.*|DB_HOST=$(DB_HOST)|'                   .env
	@sed -i -E 's/^#? *DB_PORT=.*/DB_PORT=$(DB_PORT)/'                   .env
	@sed -i -E 's/^#? *DB_DATABASE=.*/DB_DATABASE=$(DB_DATABASE)/'       .env
	@sed -i -E 's/^#? *DB_USERNAME=.*/DB_USERNAME=$(DB_USERNAME)/'       .env
	@sed -i -E 's/^#? *DB_PASSWORD=.*/DB_PASSWORD=$(DB_PASSWORD)/'       .env
	@echo "✅ .env налаштовано на $(DB_CONNECTION)://$(DB_HOST):$(DB_PORT)/$(DB_DATABASE)"

key: ## Згенерувати APP_KEY, якщо порожній
	@grep -qE '^APP_KEY=.+' .env || $(ARTISAN) key:generate

check: ## Перевірити, що PHP має драйвер pdo_pgsql
	@$(PHP) -m | grep -qi pdo_pgsql || { \
		echo "❌ У PHP немає розширення pdo_pgsql — без нього застосунок не з'єднається з PostgreSQL."; \
		echo "   Pop!_OS / Ubuntu:  sudo apt install php8.4-pgsql"; \
		echo "   Далі перезапустіть сервер: Ctrl+C і знову make serve"; \
		exit 1; }
	@echo "✅ pdo_pgsql присутній."

migrate: check ## Запустити міграції (створює sessions/cache/jobs + таблиці застосунку)
	$(ARTISAN) migrate --force

seed: ## Заповнити БД демо-даними (користувачі + заклади)
	$(ARTISAN) db:seed --force

fresh: ## Перестворити схему з нуля та засідити (УВАГА: стирає дані)
	$(ARTISAN) migrate:fresh --seed --force

serve: ## Запустити застосунок (php artisan serve)
	$(ARTISAN) serve
