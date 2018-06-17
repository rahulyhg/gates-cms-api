
## Example command to design a new table

`php artisan make:migration create_cities_table --create=cities`

## Example command to make the new table

`php artisan migrate`

## Example command to make a new seeder

`php artisan make:seeder CityTableSeeder`

## run migration w seeds (needed for fresh start but takes a long time because of census)

`php artisan migrate:refresh --seed`

## run local server

`php -S localhost:8000 -t public`

## command used to beak up the massive geojson obj into smaller ones used in seeder

```
cat usa_census_tracts.geojson | jq -c -M '.features[]' | \
  while read line; do echo $line > tracts/$(uuidgen).json; done
```