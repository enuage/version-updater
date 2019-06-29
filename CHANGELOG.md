# Version updater command changelog

- [ ] TODO: Composer version updating
- [ ] TODO: .env file value updating

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
