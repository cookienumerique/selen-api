# CHANGELOG 

## [1.19.0] - 2022-04-12
### Added

- create journal entry 

## [1.18.0] - 2022-04-01
### Added

- create endpoint for capsules/ranked

## [1.17.0] - 2022-03-16
### Added

- update response ai when response is updated

## [1.16.1] - 2022-03-11
### Fixed

- remove sandbox logic for checking apple receipt
- rename SubscriptionBasePlanId with new offers
- add contrainte title capsule and code theme and sub theme

## [1.16.0] - 2022-03-11
### Added

- add endpoint for sub theme capsule progress

## [1.15.0] - 2022-03-06
### Added

- implement IA in capsule reponse

## [1.14.0] - 2022-03-05
### Added

- the capsule response is returned after creation

## [1.13.0] - 2022-03-03
### Added

- version android 1.13.0

## [1.12.0] - 2022-02-25
### Added

- Payment with apple store

## [1.11.0] - 2022-02-24
### Fixed

- docker compose config (docker)
- app version available for ios and android

## [1.10.0] - 2022-02-23
### Added

- a command was created for grant user to premium
## [1.9.1] - 2026-02-22

### Fixed
- fix payload operator google login if family_name, given_name ... are nulls

## [1.9.0] - 2026-02-11

### Added
- user can modify a capsule response
- purchase subscription on android
- stat nb capsules responses opened

## [1.8.0] - 2026-02-11

### Added
- user can delete his account

## [1.7.0] - 2026-02-10

### Added
- login with apple

## [1.6.0] - 2026-02-04

### Added
- api return sub theme capsule
- api return media from disk (image sub theme capsule)
- MOUTH_PATH in composer.yml
- symfony/mime library

## [1.5.0] - 2026-01-31

### Added
- Inner weather listing
- Inner weather response implementation (create and list)
- Debug message support in error responses

## [1.4.0] - 2026-01-30

### Added
- Added `theme_capsule` entity to structure capsules by main theme
- Added `sub_theme_capsule` entity linked to `theme_capsule`
- Linked `capsule` to `sub_theme_capsule`
- Added database constraints (FK + unique indexes) to ensure data integrity
- Seeded initial themes and sub-themes

## [1.3.0] - 2026-01-26

### Adeded 
- implement capsule response management with create, list, and get functionalities

## [1.2.0] – 2026-01-08

### Added

-   list of capsules

## [1.1.0] – 2026-01-08

### Added

-   save user on authentification with google

## [1.0.0] – 2026-01-07

### Added

-   init projet with docker
