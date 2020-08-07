**@FIXME:** 
- [ ] Run toc generator
- [ ] Generate a changelog
      `github_changelog_generator -u pdsinterop -p {{ project-slug }}`

# Example OAuth2 server and client

<!-- @TODO: Add project banner after images for the article has been created
![Project Banner](docs/banner.png)
-->

[![PDS Interop][pdsinterop-shield]][pdsinterop-site]
[![Project stage: Development][project-stage-badge: Development]][project-stage-page]
[![License][license-shield]][license-link]
[![Latest Version][version-shield]][version-link]
[![standard-readme compliant][standard-readme-shield]][standard-readme-link]
![Maintained][maintained-shield]

_Example OAuth2 implementation using the PHP League OAuth2 Server and Client packages_

This project contains a working example of how to create an OAuth2 server with
`league/oauth2-server` and use is with `league/oauth2-client`.

<!--

@TODO: Add link to article on Medium providing background information

[This article on Medium](https://medium.com/@potherca/...)
explains the implementation process in full detail.

-->

Besides boilerplating and infrastructure, this projects contains Entity,
Repository and Grant classes for `league/oauth2-server` and custom Provider for
`league/oauth2-server`.

## Table of Contents

<!-- toc -->
<!--
    Table of Contents is optional for READMEs shorter than 100 lines.
    Generate it using:

                        markdown-toc --bullets='-' -i -- README.md

    That will also remove these lines, as they are inside toc/tocstop
-->
<!-- tocstop -->

## Background

The code in this project was created in order to understand how OAuth2 and these 
libraries work, whilst implementing the Authentication and Authorization for
[the standalone PHP](https://pdsinterop.org/php-solid) and
[Nextcloud Solid](https://pdsinterop.org/solid-nextcloud) servers.

This project <!-- and accompanying article --> should make it more clear what sort of 
data is send across the network and could (or should) be stored by an 
application acting as an OAuth2 server.

The details of _how_ to store things depends on whatever platform, library or
framework integrating the OAuth2 server.

If you feel more comfortable looking at implementations for a specific framework
in order to understand how all of this works, take a look at one of the other 
existing integrations <!-- [listed at the end of the article]() -->.

## Installation

The advised way of running this project is by using the provided docker image.

To install the project locally, create a Git clone and run [Composer](https://getcomposer.org/)
to install the required dependencies: 

```
git clone https://github.com/pdsinterop/example-oauth2.git oauth2-example
cd $_
composer install
```

## Usage

The application runs an authorization server, client application, and resource 
server from the same webroot.

It makes calls to itself and _should_ run HTTPS (HTTP+TLS).

It can be run by using the provided docker image, a docker image of your choice 
or with the PHP development server.

### Provided docker image

The advised way of running this project is by using the provided docker image:

```sh
docker run pdsinterop/example-oauth2
```

### Custom docker image

The project can also be run by mounting it into any TLS enabled PHP+Apache
docker image, for instance using `pdsinterop/php-server`:

```sh
docker run                                     \
    -it                                        \
    --name=server                              \
    --network=host                             \
    --rm                                       \
    --volume /path/to/oauth2-server-stubs:/app \
    pdsinterop/php-server
```

### PHP development server

As this is meant as a runnable example and NOT production ready, HTTPS will be
disabled when run using the built-in PHP development server (as it does not 
support HTTP+TLS).

When using the PHP development server, all request to the server by the client 
will time-out unless it is called with more workers enabled:

```sh
PHP_CLI_SERVER_WORKERS=3    \
php                         \
    --docroot ./web/        \
    --server '0.0.0.0:8080' \
    ./web/index.php
```

## Contribute

Questions or feedback can be given by [opening an issue on GitHub](https://github.com/pdsinterop/example-oauth2/issues).

All PDS Interop projects are open source and community-friendly. 
Any contribution is welcome! 
For more details read the [contribution guidelines](CONTRIBUTING.md).

All PDS Interop projects adhere to [the Code Manifesto](http://codemanifesto.com)
as its [code-of-conduct](CODE_OF_CONDUCT.md). Contributors are expected to abide by its terms.

There is [a list of all contributors on GitHub][contributors-page]. 

For a list of changes see the [CHANGELOG](CHANGELOG.md) or [the GitHub releases page](https://github.com/pdsinterop/example-oauth2/releases).

## License

All code created by PDS Interop is licensed under the [MIT License][license-link].

[contributors-page]: https://github.com/pdsinterop/example-oauth2/contributors
[license-link]: ./LICENSE
[license-shield]: https://img.shields.io/github/license/pdsinterop/project-name.svg
[maintained-shield]: https://img.shields.io/maintenance/yes/2020
[pdsinterop-shield]: https://img.shields.io/badge/-PDS%20Interop-gray.svg?logo=data%3Aimage%2Fsvg%2Bxml%3Bbase64%2CPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9Ii01IC01IDExMCAxMTAiIGZpbGw9IiNGRkYiIHN0cm9rZS13aWR0aD0iMCI+CiAgICA8cGF0aCBkPSJNLTEgNTJoMTdhMzcuNSAzNC41IDAgMDAyNS41IDMxLjE1di0xMy43NWEyMC43NSAyMSAwIDAxOC41LTQwLjI1IDIwLjc1IDIxIDAgMDE4LjUgNDAuMjV2MTMuNzVhMzcgMzQuNSAwIDAwMjUuNS0zMS4xNWgxN2EyMiAyMS4xNSAwIDAxLTEwMiAweiIvPgogICAgPHBhdGggZD0iTSAxMDEgNDhhMi43NyAyLjY3IDAgMDAtMTAyIDBoIDE3YTIuOTcgMi44IDAgMDE2OCAweiIvPgo8L3N2Zz4K
[pdsinterop-site]: https://pdsinterop.org/
[project-stage-badge: Development]: https://img.shields.io/badge/Project%20Stage-Development-yellowgreen.svg
[project-stage-page]: https://blog.pother.ca/project-stages/
[standard-readme-link]: https://github.com/RichardLitt/standard-readme
[standard-readme-shield]: https://img.shields.io/badge/readme%20style-standard-brightgreen.svg
[version-link]: https://packagist.org/packages/pdsinterop/project-name
[version-shield]: https://img.shields.io/github/v/release/pdsinterop/project-name?sort=semver
