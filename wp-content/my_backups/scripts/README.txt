/*******************************************************

	Backup2Mail v0.18, 30. 06. 2010.
	by Marko Dugonjic, http://www.backup2mail.com/

********************************************************

LEGAL STUFF
--------------------------------------------------------
	Released under the GNU GPL (General Public License).
	Read more at http://www.gnu.org/licenses/gpl.txt

	No warranties. No support. This is beta.

	You may use and/or modify any part of the code.


REQUIREMENTS
--------------------------------------------------------
	Apache
	PHP 4.1+
	Cron Jobs panel access


INSTALLATION INSTRUCTIONS
--------------------------------------------------------

1. 	Open 'index.php' in you text-editor and change settings.
	If you're not sure about something, leave the default values.
	Database settings and your e-mail are required.
	
2.	Upload folder in your public web folder to test it.
	Open the file in your browser. If you
	see the black page with green letters and there's no errors,
	you can proceed.

3.  Open .htaccess file provided in the package and remove '#'
    from the first line to enable script protection.

5.	Log in the Control Panel on your web site and
	enter the Cron Jobs area. It is suggested that
	you choose 'Advanced' interface.

6.	Configure your cron job as follows:

	minutes | hours | day_of_month | month | day_of_week | command
	----------------------------------------------------------------------------------------------------------------------
	0 		| 0		| *			   | * 	   | *			 | php /full_path_to_backup2mail/index.php >/dev/null 2>&1

	Time examples:

	| m | h | d/m | m | d/w		explanation:
	----------------------------------------------------------------------------------------------------------------------
	  *   *    *    *    * 		means every minute
	  0   0    *    *    * 		means every day at midnight
	  0   5    *    *    * 		means every day at five o'clock in the morning (11 = 11AM, 23 = 11PM)
	  0   0    *    *    0 		means every Sunday at midnight (0 = Sunday, 1 = Monday, ...)


	The 'php /full_path_to_backup2mail/index.php >/dev/null 2>&1'
	part (without the quotes) goes in the 'command' input field.
	
	
	
	
NEW IN v0.18
--------------------------------------------------------

*   Added $from variable in ‘E-mail settings’ to prevent
    errors when sending from non-existing e-mail address.	


NEW IN v0.17
--------------------------------------------------------

*   Fixed (mt) file permission issues.
*   The script now can be placed anywhere in public folder.


NEW IN v0.16
--------------------------------------------------------

*   initial backup_log.txt file is not mandatory.
    
	
	
