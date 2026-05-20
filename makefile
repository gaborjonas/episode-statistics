run:
	RUN SERVER_NAME=localhost \
        APP_SECRET=ChangeMe \
        docker compose -f compose.yaml -f compose.prod.yaml up --wait
