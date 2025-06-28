# AirPressure
This application gathers air pressure points from an API, saves it and displays information based on these points like chance of headache.

## Installing
1. `docker-compose up -d`
2. `crontab -e`
3. Edit `config/config.json` to fill in an API key, location and database connection configuration
3. Add `0 7 * * * docker exec air-pressure-php composer do-run -m gather` to crontab to gather air pressure points at regular intervals
4. Go to http://localhost or your configured host