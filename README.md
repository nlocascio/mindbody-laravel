# Laravel MINDBODY
Access the MINDBODY API from your Laravel application.

[![Latest Stable Version](https://img.shields.io/github/release/nlocascio/mindbody-laravel.svg?style=flat-square)](https://packagist.org/packages/nlocascio/mindbody-laravel)
[![Packagist Downloads](https://img.shields.io/packagist/dt/nlocascio/mindbody-laravel.svg?style=flat-square)](https://packagist.org/packages/nlocascio/mindbody-laravel)
[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://img.shields.io/travis/nlocascio/mindbody-laravel/master.svg?style=flat-square)](https://travis-ci.org/nlocascio/mindbody-laravel)

## Requirements
This package requires:
- PHP __7.0__+
- Laravel __5.1__+

You will also need the following credentials to access the MINDBODY API:
- __SourceCredentials__ consisting of your __SourceName__ and __Password__
- __Site ID__ (or multiple Site IDs) corresponding to the MINDBODY site(s) you are connecting to 

For API credentials and documentation, visit the [MINDBODY Developers site](https://developers.mindbodyonline.com/).

## Installation
Install the package through Composer:
```
composer require nlocascio/mindbody-laravel
```
#### Registering the Service Provider
Append the service provider to the `providers` key in  `config/app.php`:
```php
Nlocascio\Mindbody\MindbodyServiceProvider::class
```
#### Configuring API Credentials
Configure your API credentials by defining the following environment variables in `.env`:
```
MINDBODY_SOURCENAME=                // Your Source Name
MINDBODY_SOURCEPASSWORD=            // Your Source Password
MINDBODY_SITEIDS=                   // Site ID. (Also accepts a comma-delimitted list of IDs)
```

## Usage
#### Option 1: Type-hinting
You may type-hint the `Mindbody` class in methods of classes which are resolved by the service container:
```php
public function index(Mindbody $mindbody)
{
    $response = $mindbody->GetClients();
}
```

#### Option 2: Use Laravel's helper method
```php
use Nlocascio\Mindbody\Mindbody;

public function index()
{
    $mindbody = resolve(Mindbody::class);
    
    $mindbody->GetClients();
}
```
#### Running API functions
Examples:

```php
$mindbody = resolve(Mindbody::class);

$result = $mindbody->GetSites();
```

With arguments:
```php
$mindbody = resolve(Mindbody::class);

$result = $mindbody->GetClients([
    'XMLDetail'         => 'Bare',
    'Fields'            => [
                                'Clients.FirstName',
                                'Clients.LastName'
                            ],
    'PageSize'          => 500,
    'CurrentPageIndex'  => 1,
    'SearchText'        => 'example@email.com'
]);
```
