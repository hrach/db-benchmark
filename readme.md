Database library benchmark based on Employees Sample Database

The task is:
> Select 500 employees from the Employees database, for each of them show all of their salaries and all the departments they belong to.

See [post by Tharos on Nette forum](http://forum.nette.org/cs/viewtopic.php?pid=106521#p106521) for original motivation.

Usage:
- Run `installall` - it installs all dependencies for each library
- Update `$config` in `bootstrap.php` - database driver / dbname / user / password
- Run `composer -d import install` - it installs dependencies for import script
- Run `php import/import.php` - it imports all needed SQL code
- Run `testall` or reach library individually `php run-employees.php`
