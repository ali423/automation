start:
	@if [ ! -f .env ]; then \
		echo ".env not found. Copying from .env.example..."; \
		cp .env.example .env; \
	fi
	docker-compose up

.PHONY: rebuild

rebuild:
		echo ".env not found. Copying from .env.example..."; \
		cp .env.example .env; \
	    docker-compose down && docker-compose build --no-cache && docker-compose up
