`php artisan make:migration create_cities_table --create=cities`

`php artisan make:seeder CityTableSeeder`

`php artisan migrate:refresh --seed`

`php -S localhost:8000 -t public`

```
cat usa_census_tracts.geojson | jq -c -M '.features[]' | \
  while read line; do echo $line > tracts/$(uuidgen).json; done
```