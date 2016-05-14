IP list quiz access rule
========================

Requirements
------------
- Moodle 2.7 (build 2014051200) or later.

Installation
------------
Copy the ipaddresslist folder into your Moodle /mod/quiz/accessrule directory and 
visit your Admin Notification page to complete the installation.

Usage
-----
Visit admin page to configure IP adress list. When you setup quiz, you may select any named IP list. 
Selecting none means that this limitation will not apply.

Author
------
- Vadim Dvorovenko (Vadimon@mail.ru)

Links
-----
- Updates: https://moodle.org/plugins/view.php?plugin=quizaccess_ipaddresslist
- Latest code: https://github.com/vadimonus/moodle-quizaccess_ipaddresslist

Changes
-------
Release 0.9 (build 2016041900):
- Initial release.

Release 1.0 (build 2016041902):
- First stable version.

Release 1.1 (build 2016051400):
- Using checkboxes instead of multiselect list.
- Fixing prolem with empty new settings page on plugin install.