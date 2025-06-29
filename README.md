# AirPressure
This application gathers air pressure points from an API, saves it and displays information based on these points like chance of headache.

## Installing
1. `docker-compose up -d`
2. Edit `config/config.json` to fill in an API key, location and database connection configuration
3. Open a terminal to the php container
4. `composer install`
5. `composer phinx:migrate`
6. Exit the container
7. `crontab -e`
8. Add `0 7 * * * docker exec air-pressure-php composer do-run -m gather` to crontab to gather air pressure points at regular intervals
9. Go to http://localhost or your configured host