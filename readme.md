
    sketch:init make sketch schema yaml file
    sketch:generate (install|fresh|refresh|reset|rollback|status)
    sketch:config #stub out config



#Usage
sketch:generate #create [Models, Migrations, Factories, Seeds, Routes] from schema.yml

	# - php artisan make:model Dogs -a



  	- php artisan make:make:model
  	
  	#if table doesn't exsist
  	- php artisan make:migration create_{table}_table
  	# else
	- php artisan make:migration add_{field}_to_table --table=flights
  	
  	- php artisan make:controller
  	- php artisan make:factory
  	- php artisan make:seeder
  	
  	# if table observer: true
  	- php artisan make:observer
  	- 



make:cast              Create a new custom Eloquent cast class
make:channel           Create a new channel class
make:command           Create a new Artisan command
make:component         Create a new view component class
make:event             Create a new event class
make:exception         Create a new custom exception class
make:factory           Create a new model factory
make:job               Create a new job class
make:listener          Create a new event listener class
make:mail              Create a new email class
make:middleware        Create a new middleware class
make:migration         Create a new migration file
make:model             Create a new Eloquent model class
make:notification      Create a new notification class
make:observer          Create a new observer class
make:policy            Create a new policy class
make:provider          Create a new service provider class
make:request           Create a new form request class
make:resource          Create a new resource
make:rule              Create a new validation rule
make:seeder            Create a new seeder class
make:test              Create a new test class






// schema.yml
Dog:
id:
owner_id:foreign(columns, name)
name
birthday
breed ()
gender *(m/f)*

Owner:
id:i

































# Sketch

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require dwoodard/sketch
```

## Usage

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email dustin.woodard@gmail.com instead of using the issue tracker.

## Credits

- [Dustin Woodard][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/dwoodard/sketch.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dwoodard/sketch.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/dwoodard/sketch/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/dwoodard/sketch
[link-downloads]: https://packagist.org/packages/dwoodard/sketch
[link-travis]: https://travis-ci.org/dwoodard/sketch
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/dwoodard
[link-contributors]: ../../contributors
