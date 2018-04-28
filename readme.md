# Laravel Resource Controller
[![CircleCI](https://circleci.com/gh/ericdowell/resource-controller.svg?style=svg)](https://circleci.com/gh/ericdowell/resource-controller)
[![StyleCI](https://styleci.io/repos/130137009/shield?branch=master)](https://styleci.io/repos/130137009)
[![Maintainability](https://api.codeclimate.com/v1/badges/9667d6f991e0b1573e99/maintainability)](https://codeclimate.com/github/ericdowell/resource-controller/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/9667d6f991e0b1573e99/test_coverage)](https://codeclimate.com/github/ericdowell/resource-controller/test_coverage)
[![Issue Count](https://codeclimate.com/github/ericdowell/resource-controller/badges/issue_count.svg)](https://codeclimate.com/github/ericdowell/resource-controller)


[![License](https://poser.pugx.org/ericdowell/resource-controller/license?format=flat-square)](https://packagist.org/packages/ericdowell/resource-controller)
[![Latest Stable Version](https://poser.pugx.org/ericdowell/resource-controller/version?format=flat-square)](https://packagist.org/packages/ericdowell/resource-controller)
[![Latest Unstable Version](https://poser.pugx.org/ericdowell/resource-controller/v/unstable?format=flat-square)](https://packagist.org/packages/ericdowell/resource-controller)
[![Total Downloads](https://poser.pugx.org/ericdowell/resource-controller/downloads?format=flat-square)](https://packagist.org/packages/ericdowell/resource-controller)

## Installation
Install using composer by running:
```bash
composer require ericdowell/resource-controller ^1.3
```

Full Documentation can be found in the [Wiki](https://github.com/ericdowell/resource-controller/wiki/Index)

## Planned Improvements
- [ ] Add resource templates to package, templates will be namespaced.
- [ ] Add [view()->first()](https://laravel-news.com/viewfirst) support so applications can define overriding top-level templates.
    - [ ] Add something similar to blade, something like `@include_first` if it doesn't exist yet.
- [ ] Add optional gate methods in resource methods where applications can add calls to `abort_unless` or similar helper functions.
    - [ ] Get parameter injection for gate methods working.
- [ ] Add generators to make setup easier for the morph model resource controller.
