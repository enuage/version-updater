# Version updater command changelog

### v1.5.0

- [#7][3]: Added Git tag updating and parsing latest version from remote
repository

### v1.4.1

- Added feature for disabling colors in output

### v1.4.0

- Added executable file for using command from `vendor/bin` directory
- Updated files finder
- Updated command
    - improved output
    - add debug verbosity
- Improved and cleaned code
- Add configurations parser
- Update handlers and files formatter
- Update helpers
    - StringType improvements
    - Added BooleanType

### v1.3.1

##### 2019-07-06 00:28

- Fixed possible bug with `.yml` extension
- Code improvements

### v1.3.0

##### 2019-06-30 09:51

- [#2][2]: added handler for YAML files version updating by pattern

### v1.2.0

##### 2019-06-30 05:48

- [#1][1]: added handler for JSON files version updating by pattern

### v1.1.0

##### 2019-06-30 22:22

- Enable GitLab CI
- Update documentation
- Add version parser test
- Update code of pre-release versions data manipulations
- Code clean and improvements

##### 2019-06-29

- Fix bug on decreasing minor and patch version at the same time

##### 2019-06-23 17:36

- Add `command options parser`
- Add `files finder`, moved all files operations there
- Add `files array normalizer`
- Add `version options` DTO
- Updated `mutator`, moved there all code from command
- Add tests for mutator (Coverage 99.72%: 34 tests, 102 assertions) 

##### 2019-06-23 02:06

- Code clean and improvements
- Added prefix recognition
- Large code refactoring. Moved the code from command to separate
files

### v1.0.2

##### 2018-12-20 02:23

- Fix bugs
- Add tests (Coverage 100%: 26 tests, 65 assertions)

### v1.0.1

##### 2018-12-05 10:24:43

- Fix code

### v1.0.0

##### 2018-12-03 01:46:

- Version 1.0.0 release

### v0.1.1-alpha

##### 2018-12-02 22:35:

- Fixed cache issue

### v0.1.0-alpha

##### 2018-12-02 21:14:

- Moved version updater command from API project to separated bundle

[1]: https://gitlab.com/enuage/bundles/version-updater/issues/1
[2]: https://gitlab.com/enuage/bundles/version-updater/issues/2
[3]: https://gitlab.com/enuage/bundles/version-updater/issues/7
