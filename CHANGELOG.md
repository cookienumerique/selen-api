# CHANGELOG 

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
