Changelog
=========

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.9.2] - 2022-01-17
### Fixed
- Parameter placeholders containing hyphens in names would break the query on Mysql 8, changed name format to camelcase

## [1.9.1] - 2021-02-01
### Fixed
- The netgen.default.information_collection.form.use_csrf parameter was directly injected into service, now it is retrieved via ConfigResolver

## [1.9.0] - 2020-03-24
### Changed
- Parameters are now consumed through ConfigResolver (instead of being injected)
- Removed service definition class parameters

## [1.8.1] - 2020-03-19
### Fixed
- Referencing nonexistent variable in the form template

## [1.8.0] - 2020-03-19
### Added
- CSV delimiters can now be customized with semantic config - thx @darinda
- Export to Microsoft Excel (.xls) functionality
- Extensible export formatters via DIC tags

### Changed
- Form ID is now dynamically generated - tnx @RandyCupic
- Bumped versions of frontend dependencies (jquery, fstream, underscore.string, lodash.mergewith, cached-path-relative and lodash) 

### Fixed 
- Accessing collections created by nonexistent users
- Translate ContentType name with TranslationHelper in tree
- URL's generated without siteaccess context on the overview page

## [1.7.1] - 2020-01-24
### Added
- Exposed default_variables to email templates

## [1.7.0] - 2020-01-24
### Added
- Implemented Information collection policy provider
- Enabled download controller for enhanced binary file

## [1.6.8] - 2019-04-26
### Added
- A bit more looser coupling to `doctrine/orm` version
- Documented Google invisible captcha - tnx @ludwig031

## [1.6.7] - 2019-04-16
### Fixed
- Legacy controller should also handle captcha errors
- Check if DateAndTime and Date are null before using them

## [1.6.6] - 2019-03-05
### Added
- Export all functionality - tnx @ludwig031

## [1.6.5] - 2019-02-28
### Fixed
- Exporter did not took into account date filtering  - tnx @ludwig031

## [1.6.4] - 2019-02-20
### Added
- Display form error when captcha validatio fails - tnx @bvrbanec
- Captcha documentation - tnx @bvrbanec

### Fixed
- Documentation typo

## [1.6.3] - 2019-02-08
### Fixed
- `prototype('array')` is now used instead of `arrayPrototype`
- Twig function is now instance of `Twig_SimpleFunction` instead of `Twig_Function`

## [1.6.2] - 2019-02-04
### Fixed
- Add jQuery to global scope on standalone admin

## [1.6.1] - 2019-02-04
### Fixed
- MySQL handing of GROUP BY when ONLY_FULL_GROUP_BY mode is on (tnx @darinda for reporting the issue)

## [1.6.0] - 2019-01-23
### Added
- Admin can now display collected info for contents without location
- ReCaptcha support

## [1.5.4] - 2018-10-24
### Fixed
- Breaks in older versions of eZ

## [1.5.3] - 2018-08-16
### Fixed
- Spelling in translation
- Return empty array when there is no collected information contents

## [1.5.2] - 2018-08-13
### Fixed
- Use ez_urlalias rather than legacy route

## [1.5.1] - 2018-08-13
### Fixed
- Padding on `ic-content` div

## [1.5.0] - 2018-08-10
### Added
- Symfony based admin interface for managing collected information
- Admin interface can work standalone or integrated into Netgen AdminUI
- GDPR compliant removal and anonymizatio of collected information
- Console command for anonymization
- Collected information export to CSV

## [1.4.1] - 2018-03-09
### Fixed
- Continue with fallback logic if subject/recipient/sender template blocks do not return value

## [1.4.0] - 2018-02-28
### Added
- Custom field hadlers for Date, Time and DateAndTime field values

## [1.3.3] - 2018-01-05
### Fixed
- Fixed compatibility with Swiftmailer 6

## [1.3.2] - 2017-11-23
### Added
- Sometimes field value can be null, added defensive check

## [1.3.1] - 2017-11-8
### Added
- Fixed problem with `AutoResponderAction` configuration in context of Symfony 3.3

## [1.3.0] - 2017-11-07
### Added
- Implemented `AutoResponderAction` for sending auto respond a.k.a. `thank you` emails

## [1.2.0] - 2017-10-05
### Added
- `EmailAction` now supports sending attachments

## [1.1.0] - 2017-09-15
### Added
- Implemented custom field handlers for checkbox, integer and float field value handlers
- Created `CustomLegacyFieldHandlerInterface` for customizing field values
 
## [1.0.6] - 2017-08-17
### Added
- Use hasParameter before fetching it from container fix (by @emodric )

## [1.0.5] - 2017-06-29 
### Added
- Email templates were not properly resolved

## [1.0.4] - 2017-06-27 
### Added
- InformationCollectionMapper, InformationCollectionStruct and InformationCollectionType
- Bundle requires EzFormsBundle in version 1.3.2 or greater
- Enabled Scrutinizer service
- Fixed problem with action configuration by content type

## [1.0.3] - 2017-04-21
### Added
- Symfony 3.x compatibility

## [1.0.2] - 2017-04-18
### Added
- marked bundle as `ezplatform-bundle` in composer.json

### Changed
- allowed eZ Forms Bundle 2.0 to be installed

## [1.0.1] - 2017-02-03
### Added
- `ezinfocollection` and `ezinfocollection_attribute` table indexes inside Entity classes
- bundle now requires `doctrine/orm` by default
- updated install doc

## [1.0] - 2017-17-02
### Added
- ability to render recipient, sender and subject from template.
- more descriptive exception classes.
- more descriptive error logging.
- extracted mailer to a separate class.
- Events class is set as final.
- factory classes moved to separate services .yml file.
- InformationCollectionTrait.
- EmailDataFactory does not require ContentTypeService anymore.
- InformationCollectionController implements ContainerAwareInterface.
- Actions priority.
- Action can be marked as crucial with CrucialActionInterface.
- eZ 5 compatibility (InformationCollectionLegacyController and InformationCollectionLegacyTrait).
- exposed collected data to email and full view template.
- disabled http cache in controllers (thanks @emodric).
- CSRF token enabled by default (thanks @emodric).
- ActionRegistry relies on kernel.debug parameter to throw or ignore exceptions.

## [0.8] - 2017-13-02
### Added
- documentation update.
- every action throws ActionFailedException on failure.
- Start following [SemVer](http://semver.org) properly.
- Changelog.
- Project cleanup.

## [0.7.1] - 2017-22-01
### Changed
- Removed config resolver from EmailDataFactory.

## [0.7.0] - 2016-29-12
### Added
- New configuration for actions.

### Changed
- Tweaked action registry.
- Removed unused email templates.
